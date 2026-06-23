<x-layouts.admin>
    <x-slot:title>الاشتراكات</x-slot>

    <div x-data="{ showModal: false }" class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/5 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-900 dark:text-white">إدارة الباقات والاشتراكات</h3>
            <button @click="showModal = true" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة باقة جديدة
            </button>
        </div>
        <div class="p-10 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">قائمة الباقات</h4>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mb-6">يمكنك من هنا إضافة، تعديل، أو حذف باقات الاشتراك للعيادات.</p>
            
            <div class="w-full max-w-4xl text-right">
                @php
                    $plans = \App\Models\Plan::all();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                    <div class="border border-slate-200 dark:border-white/5 rounded-2xl p-5 bg-slate-50 dark:bg-white/5 text-right relative group">
                        <h5 class="font-bold text-lg text-slate-900 dark:text-white">{{ $plan->name }}</h5>
                        <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-2">${{ $plan->price }} <span class="text-sm font-normal text-slate-500">/ {{ $plan->duration_in_days }} يوم</span></p>
                        <div class="mt-4 space-y-2">
                            @foreach($plan->features ?? [] as $key => $feature)
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ $key }}: {{ $feature === 'unlimited' ? 'غير محدود' : $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="absolute top-4 left-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                            <button class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 bg-white dark:bg-slate-800 p-1 rounded-md shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button class="text-red-600 hover:text-red-800 dark:text-red-400 bg-white dark:bg-slate-800 p-1 rounded-md shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Add Plan Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
            <div @click.away="showModal = false" class="bg-white dark:bg-[#0f1115] w-full max-w-lg rounded-2xl shadow-xl overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">إضافة باقة جديدة</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">اسم الباقة</label>
                            <input type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white" placeholder="مثال: الباقة المتقدمة">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">السعر ($)</label>
                                <input type="number" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white" placeholder="99.00">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">المدة (بالأيام)</label>
                                <input type="number" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white" placeholder="30">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">المميزات (صيغة JSON)</label>
                            <textarea class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white h-24 text-left font-mono" dir="ltr" placeholder='{"max_patients": 500, "support": "24/7"}'></textarea>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/5 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">إلغاء</button>
                    <button @click="showModal = false" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-colors shadow-sm">حفظ الباقة</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
