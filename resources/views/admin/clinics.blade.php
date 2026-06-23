<x-layouts.admin>
    <x-slot:title>العيادات</x-slot>

    <div x-data="{ showModal: false }" class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/5 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-900 dark:text-white">إدارة العيادات</h3>
            <button @click="showModal = true" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة عيادة
            </button>
        </div>
        <div class="p-10 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">لا توجد عيادات في الوقت الحالي</h4>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">صفحة العيادات ستعرض البيانات والمعلومات المتعلقة بإدارة العيادات المسجلة قريباً.</p>
        </div>

        <!-- Add Clinic Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
            <div @click.away="showModal = false" class="bg-white dark:bg-[#0f1115] w-full max-w-lg rounded-2xl shadow-xl overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">إضافة عيادة جديدة</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">اسم العيادة</label>
                            <input type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white" placeholder="مثال: عيادة الأمل">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">البريد الإلكتروني للعيادة</label>
                            <input type="email" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white" placeholder="clinic@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">الباقة المختارة</label>
                            <select class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white">
                                <option>الباقة المجانية الأساسية</option>
                                <option>الباقة المتقدمة</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/5 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">إلغاء</button>
                    <button @click="showModal = false" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-colors shadow-sm">حفظ وإضافة</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
