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

    function pickArabicVoice() {
        const voices = window.speechSynthesis.getVoices() || [];
        return voices.find((v) => (v.lang || '').toLowerCase().startsWith('ar'))
            || voices.find((v) => (v.lang || '').toLowerCase().includes('arab'))
            || null;
    }

    function buildPhrase(name, queueNumber) {
        const safeName = (name || '').trim();
        const number = queueNumber ?? '';
        if (safeName && number !== '') {
            return `${safeName}، الدور رقم ${number}، يتفضل إلى غرفة الكشف.`;
        }
        if (safeName) {
            return `${safeName}، يتفضل إلى غرفة الكشف.`;
        }
        return `الدور رقم ${number}، يتفضل إلى غرفة الكشف.`;
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

        speakTimer = setTimeout(() => {
            speakTimer = null;

            try {
                window.speechSynthesis.resume();
            } catch (e) {
                // ignore
            }

            const utterance = new SpeechSynthesisUtterance(buildPhrase(name, queueNumber));
            utterance.lang = 'ar-EG';
            utterance.rate = 0.95;
            utterance.volume = 1;

            const voice = pickArabicVoice();
            if (voice) {
                utterance.voice = voice;
                utterance.lang = voice.lang || 'ar-EG';
            }

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
                name = data.appointment?.patient_name || name;
                queueNumber = data.appointment?.queue_number ?? queueNumber;
            }

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
                name: appointment.patient_name || data.patient_name,
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
        window.speechSynthesis.getVoices();
        window.speechSynthesis.onvoiceschanged = () => pickArabicVoice();
    }

    window.ClinoraQueueVoice = { announce, stop, supportsSpeech };
})();
