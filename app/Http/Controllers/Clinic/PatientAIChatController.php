<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientAIChatController extends Controller
{
    public function chat(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

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
                $systemContext,
                $request->input('message'),
                $request->input('history', [])
            );

            return response()->json(['success' => true, 'message' => $response]);
        } catch (\Exception $e) {
            Log::error('Patient AI Chat error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    private function buildSystemContext(Patient $patient): string
    {
        $age = $patient->dob
            ? $patient->dob->diffInYears(now()).' سنة'
            : 'غير محدد';

        $gender = match ($patient->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => 'غير محدد',
        };

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

        return <<<PROMPT
أنت مساعد طبي ذكي (ClinoraAI) متخصص في مساعدة الأطباء داخل نظام إدارة العيادات "كلينورا".

أنت الآن تساعد الطبيب في مراجعة وتحليل الملف الطبي الكامل للمريض التالي:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 بيانات المريض الأساسية:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• الاسم: {$patient->name}
• العمر: {$age}
• الجنس: {$gender}
• رقم الهاتف: {$patientPhone}
• ملاحظات عامة: {$patientNotes}

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
    private function callGemini(string $systemContext, string $userMessage, array $history): string
    {
        $apiKey = env('GEMINI_API');

        if (! $apiKey) {
            return 'خدمة الذكاء الاصطناعي غير متاحة حالياً. يرجى التأكد من إضافة GEMINI_API في إعدادات النظام.';
        }

        // Build conversation history for multi-turn
        $contents = [];
        foreach ($history as $turn) {
            $contents[] = [
                'role' => $turn['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $turn['message']]],
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(30)
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

        Log::warning('Gemini AI response failed', ['body' => $response->body()]);

        return 'لم أتمكن من معالجة طلبك في الوقت الحالي. يرجى المحاولة مرة أخرى.';
    }
}
