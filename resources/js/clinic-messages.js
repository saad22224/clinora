/**
 * Clinic staff messaging — header panel + full-screen alert for incoming messages.
 */
(() => {
    const root = document.querySelector('[data-staff-messages]');
    if (!root) {
        return;
    }

    const listUrl = root.getAttribute('data-messages-url');
    const storeUrl = root.getAttribute('data-messages-store-url');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const listEl = root.querySelector('[data-messages-list]');
    const form = root.querySelector('[data-messages-form]');
    const input = root.querySelector('[data-messages-input]');
    const sendBtn = root.querySelector('[data-messages-send]');
    const statusEl = root.querySelector('[data-messages-status]');
    const toggleBtn = document.getElementById('staff-msg-toggle-btn');
    const panel = document.getElementById('staff-msg-panel');
    const badgeEl = document.getElementById('staff-msg-badge');
    const seenKey = 'clinora_staff_msg_seen';
    const POLL_MS = 8000;
    const ALERT_MS = 10000;

    let latestId = 0;
    let timer = null;
    let inFlight = false;
    let sending = false;
    let unread = 0;
    let alertTimer = null;
    let alertHost = null;

    const getSeenId = () => Number(localStorage.getItem(seenKey) || 0);
    const setSeenId = (id) => localStorage.setItem(seenKey, String(id));

    const updateBadge = () => {
        if (!badgeEl) {
            return;
        }
        if (unread > 0) {
            badgeEl.textContent = unread > 9 ? '9+' : String(unread);
            badgeEl.classList.remove('hidden');
        } else {
            badgeEl.classList.add('hidden');
        }
    };

    const markSeen = () => {
        unread = 0;
        if (latestId > 0) {
            setSeenId(latestId);
        }
        updateBadge();
    };

    const panelOpen = () => panel && !panel.classList.contains('hidden');

    const setStatus = (text, isError = false) => {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = text || '';
        statusEl.classList.toggle('text-rose-600', isError);
        statusEl.classList.toggle('text-slate-400', !isError);
    };

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');

    const ensureAlertHost = () => {
        if (alertHost) {
            return alertHost;
        }

        alertHost = document.createElement('div');
        alertHost.id = 'staff-msg-alert-host';
        alertHost.setAttribute('aria-live', 'assertive');
        alertHost.className = 'pointer-events-none fixed inset-0 z-[100000] flex items-center justify-center p-4 sm:p-6';
        document.body.appendChild(alertHost);
        return alertHost;
    };

    const hideStaffAlert = () => {
        if (alertTimer) {
            clearTimeout(alertTimer);
            alertTimer = null;
        }
        const host = ensureAlertHost();
        const card = host.querySelector('[data-staff-alert-card]');
        if (!card) {
            host.classList.add('pointer-events-none');
            host.innerHTML = '';
            return;
        }
        card.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            host.classList.add('pointer-events-none');
            host.innerHTML = '';
        }, 220);
    };

    const showStaffAlert = (message) => {
        const host = ensureAlertHost();
        host.classList.remove('pointer-events-none');
        host.innerHTML = `
            <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm transition-opacity duration-200" data-staff-alert-backdrop></div>
            <div data-staff-alert-card
                 class="relative w-full max-w-lg origin-center rounded-[1.75rem] border border-white/20 bg-white p-6 shadow-2xl transition-all duration-200 dark:border-white/10 dark:bg-[#141420] sm:p-8 opacity-0 scale-95">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-wide text-brand-600 dark:text-brand-400">رسالة من الفريق</p>
                            <p class="text-base font-black text-gray-900 dark:text-white">${escapeHtml(message.from_name || 'عضو الفريق')}</p>
                        </div>
                    </div>
                    <button type="button" data-staff-alert-close
                            class="rounded-xl p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-white/10 dark:hover:text-white"
                            aria-label="إخفاء الرسالة">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="whitespace-pre-wrap break-words text-lg font-bold leading-8 text-gray-800 dark:text-gray-100">${escapeHtml(message.body || '')}</p>
                <div class="mt-6 flex justify-end">
                    <button type="button" data-staff-alert-close
                            class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-brand-500">
                        فهمت
                    </button>
                </div>
            </div>
        `;

        requestAnimationFrame(() => {
            host.querySelector('[data-staff-alert-card]')?.classList.remove('opacity-0', 'scale-95');
        });

        host.querySelectorAll('[data-staff-alert-close], [data-staff-alert-backdrop]').forEach((el) => {
            el.addEventListener('click', () => {
                hideStaffAlert();
                markSeen();
            });
        });

        if (alertTimer) {
            clearTimeout(alertTimer);
        }
        alertTimer = setTimeout(() => {
            hideStaffAlert();
            markSeen();
        }, ALERT_MS);
    };

    const appendMessage = (message, { fromPoll = false } = {}) => {
        if (!listEl || !message?.id) {
            return;
        }

        if (listEl.querySelector(`[data-message-id="${message.id}"]`)) {
            latestId = Math.max(latestId, message.id);
            return;
        }

        const row = document.createElement('div');
        row.setAttribute('data-message-id', String(message.id));
        row.className = message.is_mine
            ? 'mr-6 rounded-2xl bg-brand-600 text-white px-3 py-2 text-sm'
            : 'ml-6 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-800 dark:text-slate-100 px-3 py-2 text-sm';
        row.innerHTML = `
            <div class="text-[11px] font-bold opacity-80 mb-0.5">${escapeHtml(message.from_name || '')} · ${escapeHtml(message.created_at || '')}</div>
            <div class="font-semibold whitespace-pre-wrap break-words">${escapeHtml(message.body || '')}</div>
        `;
        listEl.appendChild(row);
        listEl.scrollTop = listEl.scrollHeight;
        latestId = Math.max(latestId, message.id);

        if (fromPoll && !message.is_mine && message.id > getSeenId()) {
            unread += 1;
            updateBadge();
            showStaffAlert(message);
            if (panelOpen()) {
                setSeenId(latestId);
            }
        }
    };

    const poll = async () => {
        if (inFlight || document.hidden) {
            return;
        }
        inFlight = true;
        try {
            const url = new URL(listUrl, window.location.origin);
            if (latestId > 0) {
                url.searchParams.set('since_id', String(latestId));
            }
            const res = await fetch(url.toString(), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) {
                throw new Error('poll failed');
            }
            const data = await res.json();
            const isInitial = latestId === 0;
            (data.messages || []).forEach((message) => appendMessage(message, { fromPoll: !isInitial }));
            if (isInitial) {
                setSeenId(latestId);
                unread = 0;
                updateBadge();
            }
        } catch (e) {
            // Retry on next interval.
        }
        inFlight = false;
    };

    const start = () => {
        stop();
        timer = setInterval(poll, POLL_MS);
    };

    const stop = () => {
        if (timer) {
            clearInterval(timer);
        }
        timer = null;
    };

    toggleBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        if (!panel) {
            return;
        }
        const willOpen = panel.classList.contains('hidden');
        panel.classList.toggle('hidden');
        document.getElementById('notif-dropdown-menu')?.classList.add('hidden');
        if (willOpen) {
            hideStaffAlert();
            markSeen();
            listEl && (listEl.scrollTop = listEl.scrollHeight);
            input?.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!panel || panel.classList.contains('hidden')) {
            return;
        }
        if (panel.contains(event.target) || toggleBtn?.contains(event.target)) {
            return;
        }
        panel.classList.add('hidden');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideStaffAlert();
        }
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (sending) {
            return;
        }
        const body = (input?.value || '').trim();
        if (!body) {
            return;
        }

        sending = true;
        if (sendBtn) {
            sendBtn.disabled = true;
        }
        setStatus('جاري الإرسال…');

        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ body }),
            });
            if (!res.ok) {
                throw new Error('send failed');
            }
            const data = await res.json();
            appendMessage(data.message);
            if (input) {
                input.value = '';
            }
            setStatus('تم الإرسال');
            markSeen();
        } catch (e) {
            setStatus('تعذّر الإرسال', true);
        }

        sending = false;
        if (sendBtn) {
            sendBtn.disabled = false;
        }
    });

    root.querySelectorAll('[data-quick-message]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (input) {
                input.value = btn.getAttribute('data-quick-message') || '';
                input.focus();
            }
        });
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
        } else {
            poll();
            start();
        }
    });

    updateBadge();
    poll().then(start);
})();
