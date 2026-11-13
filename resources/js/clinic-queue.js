/**
 * Arabic voice announcements for clinic queue management.
 * Includes Chrome speechSynthesis workarounds (cancel/speak race + stuck paused state).
 */
(() => {
    const supportsSpeech = typeof window !== 'undefined'
        && 'speechSynthesis' in window
        && typeof SpeechSynthesisUtterance !== 'undefined';

    let speaking = false;
    let speakTimer = null;
    let resumeTimer = null;
    let currentUtterance = null;
    let voicesReady = null;

    const arabicDigitWords = {
        0: 'صفر',
        1: 'واحد',
        2: 'اثنين',
        3: 'ثلاثة',
        4: 'أربعة',
        5: 'خمسة',
        6: 'ستة',
        7: 'سبعة',
        8: 'ثمانية',
        9: 'تسعة',
        10: 'عشرة',
    };

    function scoreArabicVoice(voice) {
        const lang = (voice.lang || '').toLowerCase().replace('_', '-');
        const name = (voice.name || '').toLowerCase();
        const uri = String(voice.voiceURI || '').toLowerCase();
        const blob = `${lang} ${name} ${uri}`;
        let score = 0;

        if (lang.startsWith('ar-eg') || name.includes('hoda') || blob.includes('egypt')) {
            score += 100;
        } else if (lang.startsWith('ar-sa') || name.includes('naayf') || blob.includes('saudi')) {
            score += 90;
        } else if (lang.startsWith('ar') || /(^|[^a-z])ar([^a-z]|$)/.test(lang)) {
            score += 80;
        } else if (blob.includes('arab') || blob.includes('عربي')) {
            score += 70;
        } else {
            return -1;
        }

        if (voice.localService) {
            score += 15;
        }

        if (name.includes('microsoft') || name.includes('google')) {
            score += 5;
        }

        return score;
    }

    function pickArabicVoice() {
        const voices = window.speechSynthesis.getVoices() || [];
        let best = null;
        let bestScore = -1;

        voices.forEach((voice) => {
            const score = scoreArabicVoice(voice);
            if (score > bestScore) {
                best = voice;
                bestScore = score;
            }
        });

        return best;
    }

    function waitForVoices() {
        if (!supportsSpeech) {
            return Promise.resolve([]);
        }

        const existing = window.speechSynthesis.getVoices() || [];
        if (existing.length > 0) {
            return Promise.resolve(existing);
        }

        if (voicesReady) {
            return voicesReady;
        }

        voicesReady = new Promise((resolve) => {
            let settled = false;
            const finish = () => {
                if (settled) {
                    return;
                }
                settled = true;
                window.speechSynthesis.onvoiceschanged = null;
                resolve(window.speechSynthesis.getVoices() || []);
            };

            window.speechSynthesis.onvoiceschanged = finish;
            window.speechSynthesis.getVoices();
            setTimeout(finish, 700);
        });

        return voicesReady;
    }

    function arabicQueueNumber(value) {
        const raw = String(value ?? '').trim();
        if (raw === '') {
            return '';
        }

        const num = Number.parseInt(raw, 10);
        if (Number.isFinite(num) && Object.prototype.hasOwnProperty.call(arabicDigitWords, num)) {
            return arabicDigitWords[num];
        }

        // Keep multi-digit numbers as spoken Arabic digits to avoid English TTS saying "One".
        return raw.replace(/\d/g, (digit) => arabicDigitWords[Number(digit)] || digit);
    }

    function buildPhrase(name, queueNumber) {
        const safeName = String(name || '').trim();
        const spokenNumber = arabicQueueNumber(queueNumber);

        // Prefer the patient name — queue numbers alone are a fallback when the name is missing.
        if (safeName) {
            return `${safeName}، يتفضل بالدخول لغرفة الكشف.`;
        }

        if (spokenNumber !== '') {
            return `الدور رقم ${spokenNumber}، يتفضل بالدخول لغرفة الكشف.`;
        }

        return 'يتفضل بالدخول لغرفة الكشف.';
    }

    function clearBusy() {
        document.querySelectorAll('[data-announce-busy]').forEach((el) => {
            el.removeAttribute('data-announce-busy');
            el.classList.remove('opacity-60');
            el.removeAttribute('disabled');
        });
    }

    function stopResumeWatch() {
        if (resumeTimer) {
            clearInterval(resumeTimer);
            resumeTimer = null;
        }
    }

    function startResumeWatch() {
        stopResumeWatch();
        // Chrome sometimes pauses speechSynthesis mid-utterance.
        resumeTimer = setInterval(() => {
            if (!supportsSpeech || !speaking) {
                stopResumeWatch();
                return;
            }
            if (window.speechSynthesis.paused) {
                window.speechSynthesis.resume();
            }
        }, 200);
    }

    function stop() {
        if (speakTimer) {
            clearTimeout(speakTimer);
            speakTimer = null;
        }
        stopResumeWatch();
        if (supportsSpeech) {
            window.speechSynthesis.cancel();
        }
        speaking = false;
        currentUtterance = null;
        clearBusy();
    }

    function announce({ name, queueNumber, onEnd } = {}) {
        if (!supportsSpeech) {
            onEnd?.({ ok: false, reason: 'unsupported' });
            return { ok: false, reason: 'unsupported' };
        }

        if (speakTimer) {
            clearTimeout(speakTimer);
            speakTimer = null;
        }

        // cancel() then immediate speak() is unreliable in Chromium.
        window.speechSynthesis.cancel();
        speaking = true;

        waitForVoices().then(() => {
            if (!speaking) {
                return;
            }

            speakTimer = setTimeout(() => {
                speakTimer = null;

                try {
                    window.speechSynthesis.resume();
                } catch (e) {
                    // ignore
                }

                const voice = pickArabicVoice();
                const utterance = new SpeechSynthesisUtterance(buildPhrase(name, queueNumber));
                // Always announce. Prefer a dedicated Arabic voice; otherwise keep ar-EG so
                // Chromium/Edge can still synthesize Arabic text when possible.
                utterance.lang = voice?.lang || 'ar-EG';
                if (voice) {
                    utterance.voice = voice;
                }
                utterance.rate = 0.92;
                utterance.volume = 1;

                const finish = (result) => {
                    speaking = false;
                    currentUtterance = null;
                    stopResumeWatch();
                    clearBusy();
                    onEnd?.(result);
                };

                utterance.onend = () => finish({ ok: true });
                utterance.onerror = (event) => {
                    // "interrupted" / "canceled" are expected when re-triggering.
                    const err = event?.error || 'error';
                    if (err === 'interrupted' || err === 'canceled') {
                        return;
                    }
                    finish({ ok: false, reason: err });
                };

                currentUtterance = utterance;
                window.speechSynthesis.speak(utterance);
                startResumeWatch();

                // Nudge Chromium if it queued but never started.
                setTimeout(() => {
                    if (speaking && window.speechSynthesis.paused) {
                        window.speechSynthesis.resume();
                    }
                }, 80);
            }, 80);
        });

        return { ok: true };
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async function postJson(url, body = {}) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify(body),
            credentials: 'same-origin',
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            const message = data?.errors?.queue?.[0] || data?.message || 'تعذّر تنفيذ المناداة';
            throw new Error(message);
        }
        return data;
    }

    async function postForm(form) {
        // Never use form.action — an input named "action" shadows the URL property.
        const url = form.getAttribute('action');
        const res = await fetch(url, {
            method: (form.getAttribute('method') || 'POST').toUpperCase(),
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: new FormData(form),
            credentials: 'same-origin',
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            const message = data?.errors?.queue?.[0] || data?.message || 'تعذّر تنفيذ المناداة';
            throw new Error(message);
        }
        return data;
    }

    function announceThenReload({ name, queueNumber }) {
        if (supportsSpeech && (name || queueNumber)) {
            announce({
                name,
                queueNumber,
                onEnd: () => window.location.reload(),
            });
            setTimeout(() => window.location.reload(), 8000);
            return;
        }
        window.location.reload();
    }

    document.addEventListener('click', async (event) => {
        const btn = event.target.closest('[data-announce-patient]');
        if (!btn) {
            return;
        }

        event.preventDefault();

        if (btn.hasAttribute('data-announce-busy')) {
            return;
        }

        btn.setAttribute('data-announce-busy', '1');
        btn.classList.add('opacity-60');
        btn.setAttribute('disabled', 'disabled');

        const callUrl = btn.getAttribute('data-call-url');
        let name = btn.getAttribute('data-patient-name');
        let queueNumber = btn.getAttribute('data-queue-number');

        try {
            if (callUrl) {
                const data = await postJson(callUrl, { action: 'call' });
                const apiName = String(data.appointment?.patient_name || '').trim();
                if (apiName) {
                    name = apiName;
                }
                queueNumber = data.appointment?.queue_number ?? queueNumber;
            }

            name = String(name || '').trim();

            if (!supportsSpeech) {
                window.alert('تم تحديث النداء. المتصفح لا يدعم الصوت — جرّب Chrome أو Edge.');
                window.location.reload();
                return;
            }

            announceThenReload({ name, queueNumber });
        } catch (error) {
            window.alert(error.message || 'تعذّر تنفيذ المناداة');
            clearBusy();
        }
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-queue-voice-form]');
        if (!form) {
            return;
        }

        event.preventDefault();

        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60');
        }

        try {
            const data = await postForm(form);
            const appointment = data.appointment;

            if (!appointment) {
                window.alert(data.message || 'لا يوجد مرضى في الانتظار.');
                window.location.reload();
                return;
            }

            announceThenReload({
                name: String(appointment.patient_name || data.patient_name || '').trim(),
                queueNumber: appointment.queue_number ?? data.queue_number,
            });
        } catch (error) {
            window.alert(error.message || 'تعذّر تنفيذ المناداة');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-60');
            }
        }
    });

    window.addEventListener('beforeunload', stop);

    if (supportsSpeech) {
        waitForVoices().then(() => pickArabicVoice());
    }

    window.ClinoraQueueVoice = { announce, stop, supportsSpeech, pickArabicVoice, waitForVoices };
})();
