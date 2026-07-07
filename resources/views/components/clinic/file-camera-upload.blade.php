@props(['name' => 'file', 'required' => true])

<div>
    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
        الملف المرفق @if($required)<span class="text-red-500">*</span>@endif
    </label>
    
    {{-- Choice Tabs --}}
    <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 dark:bg-white/5 rounded-xl mb-3 border border-gray-200/60 dark:border-white/10">
        <button type="button" id="tab-upload-file" class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold bg-white dark:bg-white/10 text-brand-600 dark:text-brand-400 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span>رفع ملف من الجهاز</span>
        </button>
        <button type="button" id="tab-take-photo" class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>التصوير بالكاميرا</span>
        </button>
    </div>

    {{-- Hidden actual file input --}}
    <input type="file" id="record-file-input" name="{{ $name }}" @if($required) required @endif class="hidden" accept="image/*,application/pdf,.doc,.docx">

    {{-- Panel A: File Upload from Device --}}
    <div id="panel-upload-file" class="block">
        <div id="dropzone-area" class="border-2 border-dashed border-gray-200 dark:border-white/10 hover:border-brand-500 dark:hover:border-brand-400 rounded-xl p-6 text-center cursor-pointer bg-gray-50/50 dark:bg-white/[0.02] transition-all group">
            <div class="w-12 h-12 rounded-full bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
            </div>
            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">اضغط لاختيار ملف أو اسحب الملف هنا</p>
            <p class="text-[10px] text-gray-400">يدعم الصور، PDF، ملفات Word (الحد الأقصى 10 ميجابايت)</p>
        </div>
    </div>

    {{-- Panel B: Camera Capture --}}
    <div id="panel-take-photo" class="hidden space-y-3">
        <div class="relative rounded-xl overflow-hidden bg-black border border-gray-200 dark:border-white/10 aspect-video flex items-center justify-center shadow-inner">
            <video id="camera-stream" autoplay playsinline class="w-full h-full object-cover"></video>
            <div id="camera-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-white p-4 text-center">
                <div class="w-8 h-8 border-3 border-brand-500 border-t-transparent rounded-full animate-spin mb-2"></div>
                <p class="text-xs font-bold">جاري تشغيل الكاميرا...</p>
            </div>
            <div id="camera-error" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-black/90 text-red-400 p-4 text-center">
                <svg class="w-8 h-8 mb-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-xs font-bold mb-1">تعذر الوصول إلى الكاميرا</p>
                <p class="text-[10px] text-gray-400">يرجى التأكد من منح متصفحك صلاحية استخدام الكاميرا أو توصيل كاميرا بالجهاز.</p>
            </div>
        </div>
        
        <div class="flex justify-center gap-2">
            <button type="button" id="btn-capture-photo" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-brand-500/30 flex items-center gap-2 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>التقاط صورة الآن 📸</span>
            </button>
            <button type="button" id="btn-switch-camera" class="px-3 py-2.5 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl transition-all" title="تبديل الكاميرا">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
        <canvas id="camera-canvas" class="hidden"></canvas>
    </div>

    {{-- Shared File Preview --}}
    <div id="file-preview-box" class="hidden mt-3 p-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl flex items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3 min-w-0">
            <div id="preview-icon-wrapper" class="w-11 h-11 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="min-w-0">
                <p id="preview-filename" class="text-xs font-bold text-emerald-900 dark:text-emerald-300 truncate">item.webp</p>
                <p id="preview-filesize" class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-bold">جاهز للرفع ✔️</p>
            </div>
        </div>
        <button type="button" id="btn-remove-file" class="p-2 rounded-lg text-emerald-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors shrink-0" title="حذف وإعادة اختيار">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
    </div>
</div>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabUploadFile = document.getElementById('tab-upload-file');
        const tabTakePhoto = document.getElementById('tab-take-photo');
        const panelUploadFile = document.getElementById('panel-upload-file');
        const panelTakePhoto = document.getElementById('panel-take-photo');
        const fileInput = document.getElementById('record-file-input');
        const dropzone = document.getElementById('dropzone-area');
        const previewBox = document.getElementById('file-preview-box');
        const filenameEl = document.getElementById('preview-filename');
        const filesizeEl = document.getElementById('preview-filesize');
        const iconWrapper = document.getElementById('preview-icon-wrapper');
        const removeBtn = document.getElementById('btn-remove-file');
        const captureBtn = document.getElementById('btn-capture-photo');
        const switchBtn = document.getElementById('btn-switch-camera');
        const canvas = document.getElementById('camera-canvas');

        let videoStream = null;
        let facingMode = 'environment';

        function stopCameraStream() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
            const video = document.getElementById('camera-stream');
            if (video) video.srcObject = null;
        }

        async function startCameraStream() {
            const video = document.getElementById('camera-stream');
            const loading = document.getElementById('camera-loading');
            const error = document.getElementById('camera-error');
            if (!video) return;

            if (loading) loading.style.display = 'flex';
            if (error) error.style.display = 'none';

            stopCameraStream();

            try {
                const constraints = {
                    video: {
                        facingMode: facingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                videoStream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = videoStream;
                video.onloadedmetadata = () => {
                    video.play();
                    if (loading) loading.style.display = 'none';
                };
            } catch (err) {
                console.error("Camera access error:", err);
                if (loading) loading.style.display = 'none';
                if (error) error.style.display = 'flex';
            }
        }

        if (tabUploadFile && tabTakePhoto) {
            tabUploadFile.addEventListener('click', () => {
                stopCameraStream();
                panelTakePhoto?.classList.add('hidden');
                if (!fileInput?.value) panelUploadFile?.classList.remove('hidden');
                tabUploadFile.className = "flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold bg-white dark:bg-white/10 text-brand-600 dark:text-brand-400 shadow-sm transition-all";
                tabTakePhoto.className = "flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all";
            });

            tabTakePhoto.addEventListener('click', () => {
                panelUploadFile?.classList.add('hidden');
                if (!fileInput?.value) {
                    panelTakePhoto?.classList.remove('hidden');
                    startCameraStream();
                }
                tabTakePhoto.className = "flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold bg-white dark:bg-white/10 text-brand-600 dark:text-brand-400 shadow-sm transition-all";
                tabUploadFile.className = "flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all";
            });
        }

        if (switchBtn) {
            switchBtn.addEventListener('click', () => {
                facingMode = (facingMode === 'environment') ? 'user' : 'environment';
                startCameraStream();
            });
        }

        function showFilePreview(file, dataUrl = null) {
            if (!previewBox || !file) return;
            filenameEl.textContent = file.name;
            const sizeKB = Math.round(file.size / 1024);
            filesizeEl.textContent = `${sizeKB > 1024 ? (sizeKB/1024).toFixed(2) + ' MB' : sizeKB + ' KB'} — جاهز للرفع ✔️`;

            if (dataUrl || file.type.startsWith('image/')) {
                const url = dataUrl || URL.createObjectURL(file);
                iconWrapper.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
            } else {
                iconWrapper.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            }

            previewBox.classList.remove('hidden');
            panelUploadFile?.classList.add('hidden');
            panelTakePhoto?.classList.add('hidden');
            stopCameraStream();
        }

        if (captureBtn && canvas && fileInput) {
            captureBtn.addEventListener('click', () => {
                const video = document.getElementById('camera-stream');
                if (!video || !videoStream) return;

                canvas.width = video.videoWidth || 1280;
                canvas.height = video.videoHeight || 720;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    if (!blob) return;
                    const timestamp = new Date().toISOString().replace(/[-:T.]/g, '').slice(0, 14);
                    const filename = `camera-capture-${timestamp}.jpg`;
                    const file = new File([blob], filename, { type: 'image/jpeg' });

                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;

                    showFilePreview(file, canvas.toDataURL('image/jpeg'));
                }, 'image/jpeg', 0.90);
            });
        }

        if (dropzone && fileInput) {
            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-brand-500', 'bg-brand-50/10');
            });
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-brand-500', 'bg-brand-50/10');
            });
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-brand-500', 'bg-brand-50/10');
                if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    showFilePreview(fileInput.files[0]);
                }
            });
            fileInput.addEventListener('change', () => {
                if (fileInput.files && fileInput.files.length > 0) {
                    showFilePreview(fileInput.files[0]);
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                if (fileInput) fileInput.value = '';
                previewBox?.classList.add('hidden');
                const isCameraTab = tabTakePhoto?.classList.contains('text-brand-600') || tabTakePhoto?.classList.contains('dark:text-brand-400');
                if (isCameraTab) {
                    panelTakePhoto?.classList.remove('hidden');
                    startCameraStream();
                } else {
                    panelUploadFile?.classList.remove('hidden');
                }
            });
        }

        // Stop camera when modal closes
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', stopCameraStream);
        });
        document.querySelectorAll('[data-modal]').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) stopCameraStream();
            });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') stopCameraStream();
        });
    });
</script>
@endpush
@endonce
