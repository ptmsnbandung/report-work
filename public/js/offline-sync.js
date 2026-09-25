/**
 * ═══════════════════════════════════════════════════════════════════
 * PT MEDIA SOLUSI NETWORK (MSN) - OFFLINE-FIRST & AUTO-SYNC ENGINE
 * ═══════════════════════════════════════════════════════════════════
 * Menyediakan kapabilitas:
 * 1. Deteksi status jaringan realtime (Online / Offline + Heartbeat Ping)
 * 2. Penyimpanan antrean lokal berkinerja tinggi (IndexedDB) untuk Form Data, Chat, Foto, dan GPS
 * 3. Sinkronisasi otomatis (Auto-Sync) saat jaringan internet kembali terhubung
 * 4. UI Indikator modern (Floating Offline Banner, Status Badge, Modal Antrean)
 */

(function(window) {
    'use strict';

    const DB_NAME = 'PTMSN_OfflineDB';
    const DB_VERSION = 1;
    const QUEUE_STORE = 'offline_queue';

    let dbInstance = null;
    let isSyncing = false;
    let isCurrentlyOnline = navigator.onLine;

    // ── 1. INISIALISASI INDEXEDDB ──
    function openDatabase() {
        return new Promise((resolve, reject) => {
            if (dbInstance) return resolve(dbInstance);

            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function(e) {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(QUEUE_STORE)) {
                    const store = db.createObjectStore(QUEUE_STORE, { keyPath: 'id', autoIncrement: true });
                    store.createIndex('status', 'status', { unique: false });
                    store.createIndex('created_at', 'created_at', { unique: false });
                    store.createIndex('tiket_id', 'meta.tiket_id', { unique: false });
                }
            };

            request.onsuccess = function(e) {
                dbInstance = e.target.result;
                resolve(dbInstance);
            };

            request.onerror = function(e) {
                console.error('[OfflineSync] Gagal membuka IndexedDB:', e);
                reject(e);
            };
        });
    }

    // ── 2. SERIALISASI & DESERIALISASI FORM DATA ──
    async function serializeFormData(formData) {
        const fields = [];
        for (const [key, value] of formData.entries()) {
            if (value instanceof Blob || value instanceof File) {
                const base64 = await blobToBase64(value);
                fields.push({
                    key,
                    type: 'file',
                    name: value.name || 'upload.jpg',
                    mimeType: value.type || 'image/jpeg',
                    data: base64
                });
            } else {
                fields.push({
                    key,
                    type: 'text',
                    value: String(value)
                });
            }
        }
        return fields;
    }

    function deserializeFormData(fields) {
        const formData = new FormData();
        fields.forEach(field => {
            if (field.type === 'file') {
                const blob = base64ToBlob(field.data, field.mimeType);
                formData.append(field.key, blob, field.name);
            } else {
                formData.append(field.key, field.value);
            }
        });
        return formData;
    }

    function blobToBase64(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    function base64ToBlob(base64, mimeType) {
        const byteCharacters = atob(base64.split(',')[1] || base64);
        const byteArrays = [];
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            const slice = byteCharacters.slice(offset, offset + 512);
            const byteNumbers = new Array(slice.length);
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }
        return new Blob(byteArrays, { type: mimeType });
    }

    // ── 3. OPERASI QUEUE INDEXEDDB ──
    async function enqueueItem(item) {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readwrite');
            const store = tx.objectStore(QUEUE_STORE);
            const record = {
                url: item.url,
                method: item.method || 'POST',
                headers: item.headers || {},
                fields: item.fields,
                meta: item.meta || {},
                status: 'pending',
                retry_count: 0,
                created_at: Date.now()
            };
            const req = store.add(record);
            req.onsuccess = function(e) {
                const id = e.target.result;
                record.id = id;
                updateUiState();
                window.dispatchEvent(new CustomEvent('offline-sync:enqueued', { detail: record }));
                resolve(record);
            };
            req.onerror = function(e) {
                reject(e);
            };
        });
    }

    async function getAllQueueItems() {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readonly');
            const store = tx.objectStore(QUEUE_STORE);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result || []);
            req.onerror = (e) => reject(e);
        });
    }

    async function deleteQueueItem(id) {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readwrite');
            const store = tx.objectStore(QUEUE_STORE);
            const req = store.delete(id);
            req.onsuccess = () => {
                updateUiState();
                resolve();
            };
            req.onerror = (e) => reject(e);
        });
    }

    async function clearAllQueue() {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readwrite');
            const store = tx.objectStore(QUEUE_STORE);
            const req = store.clear();
            req.onsuccess = () => {
                updateUiState();
                resolve();
            };
            req.onerror = (e) => reject(e);
        });
    }

    // ── 4. AUTO-SYNC PROCESSOR (SINKRONISASI OTOMATIS) ──
    async function processQueue() {
        if (isSyncing) return;
        if (!navigator.onLine) {
            updateUiState();
            return;
        }

        const items = await getAllQueueItems();
        if (!items || items.length === 0) {
            updateUiState();
            return;
        }

        isSyncing = true;
        showSyncingBanner(items.length);

        let successCount = 0;
        let failCount = 0;

        for (const item of items) {
            try {
                // Siapkan FormData
                const formData = deserializeFormData(item.fields);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                };

                const response = await fetch(item.url, {
                    method: item.method,
                    headers: headers,
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json().catch(() => ({ success: true }));
                    await deleteQueueItem(item.id);
                    successCount++;
                    window.dispatchEvent(new CustomEvent('offline-sync:item-synced', {
                        detail: { item, result }
                    }));
                } else if (response.status >= 400 && response.status < 500) {
                    // Validasi gagal atau unauthorized - hapus dari antrean agar tidak looping terus
                    console.warn(`[OfflineSync] Item ${item.id} ditolak server (HTTP ${response.status})`);
                    await deleteQueueItem(item.id);
                    failCount++;
                } else {
                    // Server 5xx atau masalah jaringan, hentikan loop sementara
                    console.warn(`[OfflineSync] Server error HTTP ${response.status}, antrean ditangguhkan.`);
                    failCount++;
                    break;
                }
            } catch (err) {
                console.warn('[OfflineSync] Gagal mengirim item saat sync:', err);
                failCount++;
                break; // Terputus kembali, hentikan proses antrean
            }
        }

        isSyncing = false;
        updateUiState();

        if (successCount > 0) {
            showSyncSuccessToast(successCount);
            window.dispatchEvent(new CustomEvent('offline-sync:all-synced', {
                detail: { successCount, failCount }
            }));
        }
    }

    // ── 5. HEARTBEAT & NETWORK DETECTION ──
    async function checkInternetConnection() {
        if (!navigator.onLine) {
            setOnlineStatus(false);
            return false;
        }
        try {
            // Heartbeat ringan dengan timestamp query
            const response = await fetch('/manifest.json?_hb=' + Date.now(), {
                method: 'HEAD',
                cache: 'no-store'
            });
            const online = response.ok;
            setOnlineStatus(online);
            return online;
        } catch (e) {
            setOnlineStatus(false);
            return false;
        }
    }

    function setOnlineStatus(online) {
        const wasOnline = isCurrentlyOnline;
        isCurrentlyOnline = online;

        if (wasOnline !== online) {
            if (online) {
                console.log('[OfflineSync] Jaringan terhubung kembali! Memulai auto-sync...');
                window.dispatchEvent(new CustomEvent('offline-sync:online'));
                processQueue();
            } else {
                console.log('[OfflineSync] Mode offline aktif! Data akan disimpan secara lokal.');
                window.dispatchEvent(new CustomEvent('offline-sync:offline'));
            }
            updateUiState();
        }
    }

    // ── 6. UI BANNER & TOAST CONTROLLER ──
    function createOfflineUiElements() {
        if (document.getElementById('msnOfflineFloatingBar')) return;

        const html = `
            <!-- Floating Offline Indicator Capsule -->
            <div id="msnOfflineFloatingBar" class="d-none position-fixed top-0 start-50 translate-middle-x p-2" style="z-index: 99999; max-width: 95vw;">
                <div class="card border-0 shadow-lg rounded-pill px-3 py-2 d-flex flex-row align-items-center gap-2" id="msnOfflineBarContent" style="background: rgba(15, 23, 42, 0.92); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.35) !important;">
                    <div id="msnOfflineStatusIcon" class="d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-warning" role="status"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size: 0.8rem; font-weight: 500;">
                        <span id="msnOfflineStatusText">Mode Offline (Tanpa Jaringan)</span>
                        <span class="badge rounded-pill bg-warning text-dark px-2 py-0.5" id="msnOfflineQueueBadge" style="font-size: 0.72rem; font-weight: 700; display: none;">0 Antrean</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-2.5 py-0.5 fw-bold" id="btnMsnForceSync" style="font-size: 0.72rem; background: #0284c7; border: none;">
                            <i class="bi bi-arrow-repeat me-0.5"></i> Sinkronkan
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-2 py-0.5" id="btnMsnViewQueue" style="font-size: 0.72rem; border-color: rgba(255,255,255,0.25);">
                            <i class="bi bi-list-check"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Manajemen Antrean Offline -->
            <div class="modal fade" id="msnOfflineQueueModal" tabindex="-1" aria-hidden="true" style="z-index: 100000;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-navy text-white rounded-top-4 py-3">
                            <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-cloud-arrow-up-fill text-warning"></i> Antrean Data Offline Lapangan
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-3">
                            <p class="text-muted small mb-3">
                                Data di bawah ini tersimpan aman di memori perangkat Anda dan akan otomatis terkirim ke server saat ponsel kembali terhubung ke jaringan internet.
                            </p>
                            <div id="msnOfflineQueueList" class="d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto;">
                                <!-- Diisi via JS -->
                            </div>
                        </div>
                        <div class="modal-footer bg-light rounded-bottom-4 py-2 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" id="btnMsnClearAllQueue">
                                <i class="bi bi-trash3"></i> Hapus Semua
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" id="btnMsnModalForceSync">
                                    <i class="bi bi-arrow-repeat me-1"></i> Sinkronkan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);

        // Pasang event listener tombol
        document.getElementById('btnMsnForceSync')?.addEventListener('click', () => processQueue());
        document.getElementById('btnMsnModalForceSync')?.addEventListener('click', () => {
            const modalEl = document.getElementById('msnOfflineQueueModal');
            if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
            processQueue();
        });
        document.getElementById('btnMsnViewQueue')?.addEventListener('click', () => openQueueModal());
        document.getElementById('btnMsnClearAllQueue')?.addEventListener('click', async () => {
            if (confirm('Yakin ingin menghapus seluruh antrean data offline di perangkat ini?')) {
                await clearAllQueue();
                renderQueueModalList([]);
            }
        });
    }

    async function updateUiState() {
        const floatingBar = document.getElementById('msnOfflineFloatingBar');
        const statusIcon = document.getElementById('msnOfflineStatusIcon');
        const statusText = document.getElementById('msnOfflineStatusText');
        const queueBadge = document.getElementById('msnOfflineQueueBadge');
        const barContent = document.getElementById('msnOfflineBarContent');
        const btnForceSync = document.getElementById('btnMsnForceSync');

        if (!floatingBar) return;

        const items = await getAllQueueItems().catch(() => []);
        const count = items.length;

        if (!isCurrentlyOnline) {
            floatingBar.classList.remove('d-none');
            barContent.style.background = 'rgba(15, 23, 42, 0.92)';
            statusIcon.innerHTML = '<i class="bi bi-wifi-off text-danger fs-6"></i>';
            statusText.textContent = 'Mode Offline (Tanpa Jaringan)';
            if (count > 0) {
                queueBadge.style.display = 'inline-block';
                queueBadge.textContent = `${count} Tersimpan`;
                queueBadge.className = 'badge rounded-pill bg-warning text-dark px-2 py-0.5';
            } else {
                queueBadge.style.display = 'none';
            }
            btnForceSync.style.display = 'inline-block';
        } else if (count > 0) {
            floatingBar.classList.remove('d-none');
            barContent.style.background = 'rgba(2, 132, 199, 0.95)';
            statusIcon.innerHTML = '<i class="bi bi-cloud-arrow-up text-white fs-6"></i>';
            statusText.textContent = 'Data Offline Siap Dikirim';
            queueBadge.style.display = 'inline-block';
            queueBadge.textContent = `${count} Antrean`;
            queueBadge.className = 'badge rounded-pill bg-white text-primary px-2 py-0.5';
            btnForceSync.style.display = 'inline-block';
        } else {
            floatingBar.classList.add('d-none');
        }
    }

    function showSyncingBanner(count) {
        const floatingBar = document.getElementById('msnOfflineFloatingBar');
        const statusIcon = document.getElementById('msnOfflineStatusIcon');
        const statusText = document.getElementById('msnOfflineStatusText');
        const queueBadge = document.getElementById('msnOfflineQueueBadge');
        const barContent = document.getElementById('msnOfflineBarContent');

        if (!floatingBar) return;
        floatingBar.classList.remove('d-none');
        barContent.style.background = 'rgba(15, 118, 110, 0.95)';
        statusIcon.innerHTML = '<span class="spinner-border spinner-border-sm text-white" role="status"></span>';
        statusText.textContent = `Menyinkronkan ${count} data ke server...`;
        queueBadge.style.display = 'none';
    }

    function showSyncSuccessToast(count) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Auto-Sync Berhasil!`,
                text: `${count} catatan & foto lapangan berhasil dikirim ke server.`,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
    }

    async function openQueueModal() {
        const modalEl = document.getElementById('msnOfflineQueueModal');
        if (!modalEl) return;
        const items = await getAllQueueItems();
        renderQueueModalList(items);
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }

    function renderQueueModalList(items) {
        const listEl = document.getElementById('msnOfflineQueueList');
        if (!listEl) return;

        if (items.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-3 mb-2 d-block"></i>
                    <div class="fw-bold small">Tidak Ada Antrean Offline</div>
                    <div style="font-size: 0.75rem;">Semua data lapangan sudah sinkron ke server.</div>
                </div>
            `;
            return;
        }

        listEl.innerHTML = items.map(item => {
            const timeStr = new Date(item.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            const label = item.meta?.label || 'Data Lapangan';
            const infoText = item.meta?.text || 'Pesan/Update status';
            const hasPhoto = item.meta?.has_photo ? '<span class="badge bg-info-subtle text-info"><i class="bi bi-image"></i> Foto</span>' : '';
            const hasLoc = item.meta?.has_location ? '<span class="badge bg-danger-subtle text-danger"><i class="bi bi-geo-alt"></i> GPS</span>' : '';

            return `
                <div class="p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between gap-2" id="queue-row-${item.id}">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">${label}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">${infoText}</div>
                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                <span class="text-muted" style="font-size: 0.68rem;"><i class="bi bi-clock"></i> ${timeStr}</span>
                                ${hasPhoto}
                                ${hasLoc}
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-danger btn-sm p-1" onclick="window.OfflineSync.deleteItem(${item.id})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `;
        }).join('');
    }

    // ── 7. PUBLIC API EKSTERNAL ──
    const OfflineSync = {
        init: function() {
            createOfflineUiElements();
            openDatabase().then(() => {
                updateUiState();
                if (navigator.onLine) {
                    processQueue();
                }
            });

            window.addEventListener('online', () => setOnlineStatus(true));
            window.addEventListener('offline', () => setOnlineStatus(false));

            // Heartbeat berkala setiap 30 detik untuk memastikan konektivitas nyata
            setInterval(checkInternetConnection, 30000);
        },

        enqueueRequest: async function({ url, method = 'POST', headers = {}, formData, meta = {} }) {
            const fields = formData instanceof FormData ? await serializeFormData(formData) : [];
            const record = await enqueueItem({
                url,
                method,
                headers,
                fields,
                meta
            });

            // Jika sedang online, langsung coba sinkronkan
            if (navigator.onLine) {
                processQueue();
            }

            return record;
        },

        processQueue: processQueue,
        getAllItems: getAllQueueItems,
        deleteItem: async function(id) {
            await deleteQueueItem(id);
            const items = await getAllQueueItems();
            renderQueueModalList(items);
        },
        clearAll: clearAllQueue,
        isOnline: () => isCurrentlyOnline
    };

    window.OfflineSync = OfflineSync;

    // Inisialisasi saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => OfflineSync.init());
    } else {
        OfflineSync.init();
    }

})(window);
