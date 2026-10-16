<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\StaffMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMessageController extends Controller
{
    public function index(Request $request)
    {
        $clinicId = $this->currentClinicId();
        $this->assertClinicStaff();

        $validated = $request->validate([
            'since_id' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = $validated['limit'] ?? 50;
        $sinceId = (int) ($validated['since_id'] ?? 0);

        $messages = StaffMessage::query()
            ->with('sender:id,name,role')
            ->where('clinic_id', $clinicId)
            ->when($sinceId > 0, fn ($q) => $q->where('id', '>', $sinceId))
            ->orderBy('id')
            ->when($sinceId === 0, fn ($q) => $q->latest('id')->limit($limit))
            ->get();

        if ($sinceId === 0) {
            $messages = $messages->sortBy('id')->values();
        }

        return response()->json([
            'messages' => $messages->map(fn (StaffMessage $message) => $this->serialize($message, Auth::id())),
            'quick_messages' => config('clinora.staff_quick_messages', []),
        ]);
    }

    public function store(Request $request)
    {
        $clinicId = $this->currentClinicId();
        $this->assertClinicStaff();

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:500'],
            'quick' => ['nullable', 'string', 'max:500'],
        ]);

        $body = trim((string) ($validated['quick'] ?? $validated['body'] ?? ''));

        if ($body === '') {
            return response()->json([
                'message' => 'نص الرسالة مطلوب.',
                'errors' => ['body' => ['نص الرسالة مطلوب.']],
            ], 422);
        }

        $quickList = config('clinora.staff_quick_messages', []);
        $isQuick = in_array($body, $quickList, true);

        $message = StaffMessage::query()->create([
            'clinic_id' => $clinicId,
            'from_user_id' => Auth::id(),
            'body' => $body,
            'is_quick' => $isQuick,
        ]);

        $message->load('sender:id,name,role');

        return response()->json([
            'message' => $this->serialize($message, Auth::id()),
        ], 201);
    }

    private function assertClinicStaff(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless((int) $user->clinic_id === $this->currentClinicId(), 403);
        abort_unless(in_array($user->role, ['owner', 'doctor', 'secretary', 'superadmin'], true), 403);

        if (in_array($user->role, ['owner', 'doctor', 'superadmin'], true)) {
            return;
        }

        abort_unless($user->can('use_staff_messages'), 403, 'ليس لديك صلاحية لتواصل الفريق.');
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(StaffMessage $message, ?int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_quick' => $message->is_quick,
            'from_user_id' => $message->from_user_id,
            'from_name' => $message->sender?->name,
            'from_role' => $message->sender?->role,
            'is_mine' => (int) $message->from_user_id === (int) $currentUserId,
            'created_at' => optional($message->created_at)?->timezone(config('clinora.clinic_timezone', 'Africa/Cairo'))->format('H:i'),
            'created_at_iso' => optional($message->created_at)?->toIso8601String(),
        ];
    }
}
