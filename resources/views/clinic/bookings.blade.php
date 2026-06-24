<x-layouts.clinic>
    <x-slot:title>طلبات الحجز</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">طلبات الحجز من الصفحة العامة</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $bookings->total() }} طلب حجز مستلم</p>
            </div>
            <a href="{{ route('clinic.public.show', auth()->user()->clinic->slug) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl border border-brand-200 text-brand-600 hover:bg-brand-50 dark:border-brand-700/40 dark:text-brand-400 dark:hover:bg-brand-900/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                صفحتك العامة
            </a>
        </div>

        @if($bookings->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد طلبات حجز بعد</h4>
                <p class="text-sm text-gray-400 max-w-xs">شارك رابط صفحتك العامة مع مرضاك ليتمكنوا من حجز مواعيد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">المريض</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الهاتف</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الموعد المفضل</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الحالة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">تاريخ الطلب</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                        @foreach($bookings as $booking)
                            @php
                                $statusMap = [
                                    'new'       => ['label' => 'جديد',    'class' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'],
                                    'contacted' => ['label' => 'تم التواصل', 'class' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'],
                                    'confirmed' => ['label' => 'مؤكد',    'class' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'],
                                    'rejected'  => ['label' => 'مرفوض',   'class' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'],
                                ];
                                $s = $statusMap[$booking->status] ?? ['label' => $booking->status, 'class' => ''];
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-sm shrink-0">
                                            {{ mb_substr($booking->patient_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $booking->patient_name }}</p>
                                            @if($booking->patient_email)
                                                <p class="text-xs text-gray-400">{{ $booking->patient_email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300 dir-ltr">{{ $booking->patient_phone }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $booking->preferred_date->format('Y/m/d') }}</p>
                                    @if($booking->preferred_time)
                                        <p class="text-xs text-gray-400">{{ $booking->preferred_time }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $s['class'] }}">{{ $s['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-xs">{{ $booking->created_at->format('Y/m/d H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($booking->status === 'new')
                                        <form method="POST" action="{{ route('clinic.bookings.update-status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="contacted">
                                            <button type="submit" class="px-2 py-1 text-xs font-bold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors">تواصل</button>
                                        </form>
                                        @endif
                                        @if(in_array($booking->status, ['new', 'contacted']))
                                        <form method="POST" action="{{ route('clinic.bookings.update-status', $booking) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="px-2 py-1 text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors">تأكيد</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $bookings->links() }}</div>
            @endif
        @endif
    </div>
</x-layouts.clinic>
