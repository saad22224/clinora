<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PatientAIChatController extends Controller
{
    public function chat(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id != Auth::user()->clinic_id, 403);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array'],
        ]);

        $patient->load([
            'medicalFiles' => fn ($q) => $q->latest(),
            'prescriptions' => fn ($q) => $q->with('items')->latest(),
            'diagnoses',
        ]);

        $systemContext = $this->buildSystemContext($patient);

        try {
            $response = $this->callGemini(
                $patient,
                $systemContext,
                $request->input('message'),
                $request->input('history', [])
            );

            return response()->json(['success' => true, 'message' => $response]);
        } catch (\Exception $e) {
            Log::error('Patient AI Chat error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'عذراً دكتور، حدث عائق مؤقت في معالجة الطلب. يرجى إعادة المحاولة خلال ثوانٍ.',
            ], 500);
        }
    }

    private function buildSystemContext(Patient $patient): string
    {
        $gender = match ($patient->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => 'غير محدد',
        };

        // Age: prefer dob-calculated age, fallback to explicit age field
        if ($patient->dob) {
            $age = $patient->dob->diffInYears(now()).' سنة';
        } elseif ($patient->age) {
            $age = $patient->age.' سنة';
        } else {
            $age = 'غير محدد';
        }

        // Medical files summary
        $filesText = $patient->medicalFiles->isEmpty()
            ? 'لا توجد ملفات طبية مرفوعة.'
            : $patient->medicalFiles->map(fn ($f) => "- {$f->type} (مرفوع في: {$f->created_at->format('Y/m/d')})")->join("\n");

        // Prescriptions summary
        $prescriptionsText = $patient->prescriptions->isEmpty()
            ? 'لا توجد وصفات طبية.'
            : $patient->prescriptions->map(function ($p) {
                $date = $p->created_at->format('Y/m/d');
                $items = $p->items->map(fn ($i) => "  • {$i->medicine_name}".($i->dosage ? " ({$i->dosage})" : '').($i->instructions ? " — {$i->instructions}" : ''))->join("\n");
                $notes = $p->notes ? "\n  ملاحظات: {$p->notes}" : '';

                return "وصفة بتاريخ {$date}:\n{$items}{$notes}";
            })->join("\n\n");

        // Diagnoses summary
        $diagnosesText = $patient->diagnoses->isEmpty()
            ? 'لا توجد تشخيصات مسجلة.'
            : $patient->diagnoses->map(fn ($d) => "- [{$d->diagnosed_at->format('Y/m/d')}] {$d->diagnosis}".($d->notes ? "\n  ملاحظات: {$d->notes}" : ''))->join("\n");

        $patientNotes = $patient->notes ?? 'لا يوجد';
        $patientPhone = $patient->phone ?? 'غير محدد';
        $medicalHistoryText = $patient->medical_history ?? 'لا يوجد تاريخ مرضي مسجل.';
        $surgicalHistoryText = $patient->surgical_history ?? 'لا يوجد تاريخ جراحي مسجل.';

        $name = $patient->name;

        return <<<PROMPT
أنت مساعد طبي ذكي (ClinoraAI) متخصص في مساعدة الأطباء داخل نظام إدارة العيادات "كلينورا".

أنت الآن تساعد الطبيب في مراجعة وتحليل الملف الطبي الكامل للمريض التالي:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 بيانات المريض الأساسية:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• الاسم: {$name}
• العمر: {$age}
• الجنس: {$gender}
• رقم الهاتف: {$patientPhone}
• ملاحظات عامة: {$patientNotes}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🏥 التاريخ المرضي (الأمراض المزمنة):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$medicalHistoryText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔪 التاريخ الجراحي (العمليات السابقة):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$surgicalHistoryText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔬 الأشعة والتحاليل المرفوعة:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$filesText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
💊 الوصفات الطبية (الروشتات):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$prescriptionsText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🩺 التشخيصات والملاحظات:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$diagnosesText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
تعليمات مهمة:
- أنت تتحدث مع طبيب أو كادر طبي متخصص، ليس مع مريض عام.
- استخدم المصطلحات الطبية المناسبة واللغة العربية الواضحة.
- قدم تحليلات مبنية على بيانات المريض المذكورة أعلاه.
- يمكنك اقتراح تشخيصات تفاضلية، أدوية محتملة، أو فحوصات إضافية بناءً على الحالة.
- أشر دائماً إلى أن القرار النهائي للطبيب المعالج.
- كن موجزاً ومفيداً في إجاباتك.
PROMPT;
    }

    /**
     * @param  array<int, array{role: string, message: string}>  $history
     */
    private function callGemini(Patient $patient, string $systemContext, string $userMessage, array $history): string
    {
        $apiKeys = array_filter([
            env('GEMINI_API'),
            env('GEMINI_API2'),
            env('GEMINI_API3'),
            env('GEMINI_API4'),
        ]);

        if (empty($apiKeys)) {
            return 'عذراً، خدمة المساعد الذكي غير مفعلة حالياً في النظام.';
        }

        // Build conversation history for multi-turn
        $contents = [];
        foreach ($history as $turn) {
            $contents[] = [
                'role' => $turn['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $turn['message']]],
            ];
        }

        // Gather attached medical files (images/PDFs up to 10 files <= 10MB each)
        $userParts = [];
        foreach ($patient->medicalFiles->take(10) as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                $size = Storage::disk('public')->size($file->file_path);
                if ($size <= 10 * 1024 * 1024) {
                    $mimeType = Storage::disk('public')->mimeType($file->file_path);
                    if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'application/pdf'])) {
                        $userParts[] = [
                            'text' => "[مرفق صورة/ملف طبي: {$file->type} - تاريخ المرفق: {$file->created_at->format('Y/m/d')}]",
                        ];
                        $userParts[] = [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => base64_encode(Storage::disk('public')->get($file->file_path)),
                            ],
                        ];
                    }
                }
            }
        }
        $userParts[] = ['text' => $userMessage];

        $contents[] = [
            'role' => 'user',
            'parts' => $userParts,
        ];

        $lastError = '';
        foreach ($apiKeys as $index => $apiKey) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(60)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}", [
                        'system_instruction' => [
                            'parts' => [['text' => $systemContext]],
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'topK' => 40,
                            'topP' => 0.95,
                            'maxOutputTokens' => 2000,
                        ],
                        'safetySettings' => [
                            ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',  'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return $data['candidates'][0]['content']['parts'][0]['text'];
                    }
                }

                $lastError = $response->body();
                Log::warning("Gemini AI response failed with key index {$index}", ['status' => $response->status(), 'body' => $lastError]);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini AI request exception with key index {$index}", ['error' => $lastError]);
            }
        }

        Log::error('All Gemini AI API keys failed', ['last_error' => $lastError]);

        return 'عذراً دكتور، المساعد الذكي يشهد ضغطاً عالياً حالياً. يرجى إعادة المحاولة خلال ثوانٍ قليلة.';
    }
}
