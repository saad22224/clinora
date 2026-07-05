<x-layouts.clinic>
    <x-slot:title>الإشعارات والتنبيهات</x-slot:title>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-2xl text-sm font-semibold text-emerald-700 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-100 dark:border-white/[0.06]">
            <div>
                <h1 class="text-xl font-black text-gray-900 dark:text-white mb-1">سجل الإشعارات والتنبيهات</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">سجل كامل لجميع الرسائل والتنبيهات والتحديثات المرسلة لعيادتك من إدارة المنصة.</p>
            </div>
            @if(\App\Models\ClinicNotification::where('clinic_id', Auth::user()->clinic_id)->where('is_read', false)->exists())
                <form method="POST" action="{{ route('clinic.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-brand-500/10 hover:bg-brand-500/20 border border-brand-500/20 text-brand-600 dark:text-brand-400 font-bold text-xs rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>تحديد كل الإشعارات كمقروءة</span>
                    </button>
                </form>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="py-20 text-center">
                <div class="w-20 h-20 rounded-3xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-1">لا توجد أي إشعارات حتى الآن</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto leading-relaxed">لم تقم إدارة المنصة بإرسال أي تنبيهات أو رسائل لعيادتك بعد.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notif)
                    <div class="p-5 rounded-2xl border border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] hover:bg-white dark:hover:bg-white/[0.04] transition-all shadow-sm flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="shrink-0 mt-1">
                                @switch($notif->type)
                                    @case('success')
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 dark:text-emerald-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        @break
                                    @case('warning')
                                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        @break
                                    @case('danger')
                                        <div class="w-10 h-10 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 dark:text-red-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 dark:text-blue-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                @endswitch
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white">{{ $notif->title }}</h3>
                                    <span class="text-[11px] text-gray-400">• {{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl">{{ $notif->message }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-white/[0.06]">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.clinic>
