<x-layouts.clinic>
    <x-slot:title>إعدادات العيادة</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Sidebar: Preview Card --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6 shadow-sm text-center">
                <div class="relative inline-block mb-4">
                    @if($clinic->doctor_image)
                        <img src="{{ Storage::url($clinic->doctor_image) }}" alt="{{ $clinic->name }}"
                             class="w-24 h-24 rounded-2xl object-cover mx-auto shadow-lg">
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center mx-auto shadow-lg shadow-brand-500/20">
                            <span class="text-3xl font-black text-white">{{ mb_substr($clinic->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">{{ $clinic->name }}</h3>
                @if($clinic->doctor_title)
                    <p class="text-sm text-brand-600 dark:text-brand-400 font-semibold mt-1">{{ $clinic->doctor_title }}</p>
                @endif
                @if($clinic->phone)
                    <p class="text-sm text-gray-500 mt-1 dir-ltr">{{ $clinic->phone }}</p>
                @endif
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/[0.06]">
                    <a href="{{ route('clinic.public.show', $clinic->slug) }}" target="_blank"
                       class="inline-flex items-center gap-2 text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        عرض الصفحة العامة
                    </a>
                </div>
            </div>

            {{-- Theme Color --}}
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6 shadow-sm">
                <h4 class="font-bold text-gray-900 dark:text-white mb-3">لون الثيم</h4>
                <div id="color-preview" class="w-full h-12 rounded-xl mb-3 transition-colors" style="background-color: {{ $clinic->theme_color ?? '#3b5bdb' }}"></div>
                <input type="color" id="color-picker" value="{{ $clinic->theme_color ?? '#3b5bdb' }}"
                       class="w-full h-10 rounded-xl border border-gray-200 dark:border-white/10 p-1"
                       onchange="document.getElementById('color-preview').style.backgroundColor=this.value; document.getElementById('theme-color-input').value=this.value">
            </div>
        </div>

        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('clinic.settings.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="theme_color" id="theme-color-input" value="{{ $clinic->theme_color ?? '#3b5bdb' }}">

                {{-- Basic Info --}}
                <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                        <h3 class="font-black text-gray-900 dark:text-white">معلومات العيادة</h3>
                    </div>
                    <div class="p-6 space-y-5">

                        @if($errors->any())
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                                @foreach($errors->all() as $error)<p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>@endforeach
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">اسم العيادة <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">لقب الطبيب</label>
                                <input type="text" name="doctor_title" value="{{ old('doctor_title', $clinic->doctor_title) }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                       placeholder="مثال: طبيب أسنان أو أخصائي قلب">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">رقم الهاتف</label>
                                <input type="tel" name="phone" value="{{ old('phone', $clinic->phone) }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                       placeholder="05xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">العنوان</label>
                                <input type="text" name="address" value="{{ old('address', $clinic->address) }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                       placeholder="المدينة، الحي، الشارع">
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 dark:border-white/[0.06] bg-gray-50/70 dark:bg-white/[0.03] p-4">
                            <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <label class="block text-sm font-black text-gray-800 dark:text-gray-200">موقع العيادة على الخريطة</label>
                                    <p class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">اضغط على الخريطة لتحديد مكان العيادة، أو استخدم موقعك الحالي.</p>
                                </div>
                                <button type="button" id="use-current-location" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-xs font-black text-white transition hover:bg-brand-500">
                                    استخدام موقعي الحالي
                                </button>
                            </div>
                            <div id="clinic-map" class="h-80 rounded-2xl border border-gray-200 dark:border-white/10 z-0"></div>
                            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-gray-500 dark:text-gray-400">خط العرض</label>
                                    <input id="clinic-latitude" type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $clinic->latitude) }}"
                                           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 dark:border-white/10 dark:bg-white/5 dark:text-white dir-ltr">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-gray-500 dark:text-gray-400">خط الطول</label>
                                    <input id="clinic-longitude" type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $clinic->longitude) }}"
                                           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 dark:border-white/10 dark:bg-white/5 dark:text-white dir-ltr">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">نبذة عن الطبيب / العيادة</label>
                            <textarea name="doctor_bio" rows="4"
                                      class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none"
                                      placeholder="اكتب نبذة عن خبرتك وتخصصك...">{{ old('doctor_bio', $clinic->doctor_bio) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">صورة الطبيب / الشعار</label>
                            <input type="file" name="doctor_image" accept="image/*"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-100 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-400 outline-none dark:text-white">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">عنوان الهيرو (الرئيسي)</label>
                                <input type="text" name="hero_title" value="{{ old('hero_title', $clinic->hero_title) }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                       placeholder="مثال: مرحباً بك في عيادتنا">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">عنوان الهيرو (الفرعي)</label>
                                <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $clinic->hero_subtitle) }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                       placeholder="رسالة ترحيبية قصيرة">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">من نحن؟</label>
                            <textarea name="about_us" rows="4"
                                      class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none"
                                      placeholder="أخبر مرضاك عن تاريخ العيادة وخدماتها...">{{ old('about_us', $clinic->about_us) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Social Links --}}
                <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                        <h3 class="font-black text-gray-900 dark:text-white">روابط التواصل الاجتماعي</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @php $socials = ['facebook' => 'فيسبوك', 'twitter' => 'تويتر', 'instagram' => 'انستجرام', 'linkedin' => 'لينكد إن']; @endphp
                        @foreach($socials as $key => $label)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">{{ $label }}</label>
                                <input type="url" name="social_links[{{ $key }}]" value="{{ old("social_links.$key", $clinic->social_links[$key] ?? '') }}"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white dir-ltr text-left"
                                       placeholder="https://...">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Working Hours --}}
                <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                        <h3 class="font-black text-gray-900 dark:text-white">أوقات الدوام</h3>
                        <p class="text-sm text-gray-400 mt-0.5">حدد أيام وأوقات عمل العيادة</p>
                    </div>
                    <div class="p-6 space-y-3">
                        @php
                            $days = [
                                'saturday'  => 'السبت',
                                'sunday'    => 'الأحد',
                                'monday'    => 'الاثنين',
                                'tuesday'   => 'الثلاثاء',
                                'wednesday' => 'الأربعاء',
                                'thursday'  => 'الخميس',
                                'friday'    => 'الجمعة',
                            ];
                            $workingHours = $clinic->working_hours ?? [];
                        @endphp
                        @foreach($days as $key => $label)
                            @php
                                $dayData = $workingHours[$key] ?? [];
                                $enabled = !empty($dayData['enabled']);
                                $periods = $dayData['periods'] ?? [];
                                
                                // Fallback for old single-period data
                                if (empty($periods) && !empty($dayData['open']) && !empty($dayData['close'])) {
                                    $periods[] = ['open' => $dayData['open'], 'close' => $dayData['close']];
                                }
                                if (empty($periods)) {
                                    $periods[] = ['open' => '09:00', 'close' => '17:00'];
                                }
                            @endphp
                            <div class="flex items-start gap-4 py-4 border-b border-gray-50 dark:border-white/[0.04] last:border-0" data-day="{{ $key }}">
                                <label class="flex items-center gap-2.5 min-w-[110px] mt-1.5">
                                    <input type="hidden" name="working_hours[{{ $key }}][enabled]" value="0">
                                    <input type="checkbox" name="working_hours[{{ $key }}][enabled]" value="1"
                                           {{ $enabled ? 'checked' : '' }}
                                           class="w-4 h-4 rounded accent-brand-600"
                                           onchange="toggleDayPeriods(this, '{{ $key }}')">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                                
                                <div class="flex-1 space-y-2 periods-container" id="periods-{{ $key }}">
                                    @foreach($periods as $index => $period)
                                        <div class="flex items-center gap-2 period-row">
                                            <input type="time" name="working_hours[{{ $key }}][periods][{{ $index }}][open]" value="{{ $period['open'] ?? '09:00' }}"
                                                   {{ !$enabled ? 'disabled' : '' }} required
                                                   class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white disabled:opacity-40 transition-opacity">
                                            <span class="text-gray-400 text-xs font-bold">—</span>
                                            <input type="time" name="working_hours[{{ $key }}][periods][{{ $index }}][close]" value="{{ $period['close'] ?? '17:00' }}"
                                                   {{ !$enabled ? 'disabled' : '' }} required
                                                   class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white disabled:opacity-40 transition-opacity">
                                            
                                            <button type="button" onclick="removePeriod(this)" class="p-1.5 text-gray-400 hover:text-red-500 {{ $loop->first ? 'hidden' : '' }} {{ !$enabled ? 'opacity-40 pointer-events-none' : '' }}" title="حذف هذه الفترة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                    <button type="button" onclick="addPeriod('{{ $key }}')" class="add-period-btn text-xs font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 mt-2 flex items-center gap-1 {{ !$enabled ? 'hidden' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        فترة دوام أخرى
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-8 py-3 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-md hover:shadow-brand-500/30 hover:shadow-xl active:scale-95">
                        حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        // ── Working Hours helpers ──────────────────────────────────────
        function toggleDayPeriods(checkbox, dayKey) {
            const container = document.getElementById(`periods-${dayKey}`);
            const inputs    = container.querySelectorAll('input[type=time]');
            const removeBtns = container.querySelectorAll('button[onclick^="removePeriod"]');
            const addBtn    = container.querySelector('.add-period-btn');
            inputs.forEach(i => i.disabled = !checkbox.checked);
            if (checkbox.checked) {
                addBtn.classList.remove('hidden');
                removeBtns.forEach(b => b.classList.remove('opacity-40', 'pointer-events-none'));
            } else {
                addBtn.classList.add('hidden');
                removeBtns.forEach(b => b.classList.add('opacity-40', 'pointer-events-none'));
            }
        }
        function addPeriod(dayKey) {
            const container = document.getElementById(`periods-${dayKey}`);
            const idx       = container.querySelectorAll('.period-row').length;
            const row       = document.createElement('div');
            row.className   = 'flex items-center gap-2 period-row mt-2';
            row.innerHTML   = `
                <input type="time" name="working_hours[${dayKey}][periods][${idx}][open]" value="09:00" required class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                <span class="text-gray-400 text-xs font-bold">—</span>
                <input type="time" name="working_hours[${dayKey}][periods][${idx}][close]" value="17:00" required class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                <button type="button" onclick="removePeriod(this)" class="p-1.5 text-gray-400 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
            container.insertBefore(row, container.querySelector('.add-period-btn'));
        }
        function removePeriod(btn) { btn.closest('.period-row').remove(); }

        // ── Leaflet Map Picker ─────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('clinic-latitude');
            const lngInput = document.getElementById('clinic-longitude');
            const mapEl    = document.getElementById('clinic-map');
            if (!mapEl || typeof L === 'undefined') return;

            const savedLat = parseFloat(latInput.value) || 30.0444;
            const savedLng = parseFloat(lngInput.value) || 31.2357;

            const map    = L.map('clinic-map').setView([savedLat, savedLng], latInput.value ? 15 : 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const icon = L.divIcon({
                className: '',
                html: '<div style="width:36px;height:36px;border-radius:50%;background:#3b5bdb;border:3px solid white;box-shadow:0 4px 12px rgba(59,91,219,.4);display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
            });

            let marker = null;
            function placeMarker(lat, lng) {
                if (marker) marker.setLatLng([lat, lng]);
                else marker = L.marker([lat, lng], { icon, draggable: true }).addTo(map);
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
                marker.on('dragend', e => {
                    const p = e.target.getLatLng();
                    latInput.value = p.lat.toFixed(7);
                    lngInput.value = p.lng.toFixed(7);
                });
            }

            if (latInput.value && lngInput.value) placeMarker(savedLat, savedLng);

            map.on('click', e => { placeMarker(e.latlng.lat, e.latlng.lng); map.setView(e.latlng, 15); });

            document.getElementById('use-current-location')?.addEventListener('click', function () {
                if (!navigator.geolocation) return alert('المتصفح لا يدعم تحديد الموقع.');
                navigator.geolocation.getCurrentPosition(
                    pos => { placeMarker(pos.coords.latitude, pos.coords.longitude); map.setView([pos.coords.latitude, pos.coords.longitude], 16); },
                    () => alert('تعذّر الحصول على موقعك الحالي.')
                );
            });
        });
    </script>
    @endpush
</x-layouts.clinic>
