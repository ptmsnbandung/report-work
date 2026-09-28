<script>
// Zoom Photo in Lightbox Modal
window.zoomPhoto = function(url, title) {
    if (!url || url === '#' || url === 'null' || url === 'undefined') return;

    const zoomImg = document.getElementById('photoZoomImg');
    const zoomTitle = document.getElementById('photoZoomTitle');
    const zoomDownloadBtn = document.getElementById('photoZoomDownloadBtn');
    const modalEl = document.getElementById('photoZoomModal');

    if (modalEl) {
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        if (zoomImg) zoomImg.src = url;
        if (zoomTitle) zoomTitle.textContent = title || 'Foto Dokumentasi Kronologis';
        if (zoomDownloadBtn) {
            zoomDownloadBtn.href = url;
            zoomDownloadBtn.setAttribute('data-download-url', url);

            if (!zoomDownloadBtn._downloadAttached) {
                zoomDownloadBtn._downloadAttached = true;
                zoomDownloadBtn.addEventListener('click', async function(e) {
                    const downloadUrl = this.getAttribute('data-download-url') || this.href;
                    if (!downloadUrl || downloadUrl === '#' || downloadUrl.startsWith('javascript:')) return;
                    e.preventDefault();

                    const icon = this.querySelector('i');
                    const prevClass = icon ? icon.className : 'bi bi-download';
                    if (icon) icon.className = 'spinner-border spinner-border-sm';

                    try {
                        const response = await fetch(downloadUrl);
                        const blob = await response.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = blobUrl;
                        const cleanName = (zoomTitle?.textContent || 'foto-lapangan')
                            .replace(/[^a-zA-Z0-9_\-\.]/g, '_') + '.jpg';
                        a.download = cleanName;
                        document.body.appendChild(a);
                        a.click();
                        setTimeout(() => {
                            document.body.removeChild(a);
                            window.URL.revokeObjectURL(blobUrl);
                        }, 1000);
                    } catch (err) {
                        const a = document.createElement('a');
                        a.href = downloadUrl;
                        a.download = 'foto-lapangan.jpg';
                        a.target = '_blank';
                        document.body.appendChild(a);
                        a.click();
                        setTimeout(() => document.body.removeChild(a), 500);
                    } finally {
                        if (icon) icon.className = prevClass;
                    }
                });
            }
        }
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    }
};

// Global Delegated Click Listener for any chat media card
document.addEventListener('click', function(e) {
    const mediaCard = e.target.closest('.wa-media-card');
    if (mediaCard) {
        const img = mediaCard.querySelector('.wa-media-img');
        if (img && img.src && img.src !== '#' && !img.src.endsWith('/#')) {
            const bubbleEl = mediaCard.closest('.wa-bubble');
            const titleEl = bubbleEl ? bubbleEl.querySelector('.wa-bubble-sender') : null;
            const timeEl = bubbleEl ? bubbleEl.querySelector('.wa-msg-time') : null;
            const title = (titleEl ? titleEl.textContent.trim() : 'Foto Lapangan') + (timeEl ? ' - ' + timeEl.textContent.trim() : '');
            window.zoomPhoto(img.src, title);
        }
    }
});

// Format file size helper
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

/**
 * Kompresi Gambar Otomatis Berbasis HTML5 Canvas
 * Mengubah foto beresolusi tinggi (5MB-25MB) dari kamera HP menjadi ~150KB - 450KB
 * dengan kualitas visual tajam dan siap diupload tanpa kendala ukuran.
 */
// ═══════════════════════════════════════════════════════════════════
// GPS TIMESTAMP CAMERA WATERMARK & AUTO-COMPRESSION ENGINE
// Menyematkan Logo MSN (kanan atas), Mini Map Lokasi (kiri bawah),
// dan Waktu + Koordinat + Alamat Lengkap otomatis (kanan bawah)
// ═══════════════════════════════════════════════════════════════════
const MSN_LOGO_SRC = "{{ asset('assets/logo-msn BG Trans - Copy2.png') }}";
const REVERSE_GEOCODE_URL = "{{ route('api.reverse-geocode') }}";

let cachedMsnLogo = null;
function getMsnLogo() {
    if (cachedMsnLogo) return Promise.resolve(cachedMsnLogo);
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => { cachedMsnLogo = img; resolve(img); };
        img.onerror = () => resolve(null);
        img.src = MSN_LOGO_SRC;
    });
}

// Global cached GPS for instant watermark acquisition
let _lastDetectedGps = null;
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            localStorage.setItem('perm_geo_granted', '1');
            _lastDetectedGps = {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
                accuracy: pos.coords.accuracy,
                timestamp: Date.now()
            };
        },
        () => {},
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
    );
}

function getDeviceCoordinates(timeoutMs = 7000) {
    return new Promise((resolve) => {
        // 1. Cek apakah ada input koordinat terpilih dari form
        const latInput = document.getElementById('latitude') || document.getElementById('waChatLatitude');
        const lngInput = document.getElementById('longitude') || document.getElementById('waChatLongitude');
        if (latInput && lngInput && latInput.value && lngInput.value) {
            const latVal = parseFloat(latInput.value);
            const lngVal = parseFloat(lngInput.value);
            if (!isNaN(latVal) && !isNaN(lngVal)) {
                return resolve({ latitude: latVal, longitude: lngVal, accuracy: 10 });
            }
        }

        // 2. Cek apakah ada cache GPS aktif (< 2 menit)
        if (_lastDetectedGps && (Date.now() - _lastDetectedGps.timestamp < 120000)) {
            return resolve(_lastDetectedGps);
        }

        // 3. Request fresh GPS location
        if (!navigator.geolocation) return resolve(_lastDetectedGps || null);
        
        let isResolved = false;
        const timer = setTimeout(() => {
            if (!isResolved) {
                isResolved = true;
                resolve(_lastDetectedGps || null);
            }
        }, timeoutMs);

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                if (!isResolved) {
                    isResolved = true;
                    clearTimeout(timer);
                    _lastDetectedGps = {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        accuracy: pos.coords.accuracy,
                        timestamp: Date.now()
                    };
                    resolve(_lastDetectedGps);
                }
            },
            (err) => {
                if (!isResolved) {
                    isResolved = true;
                    clearTimeout(timer);
                    resolve(_lastDetectedGps || null);
                }
            },
            { enableHighAccuracy: true, timeout: timeoutMs, maximumAge: 60000 }
        );
    });
}

async function getReverseGeocodeLines(lat, lng) {
    try {
        const res = await fetch(`${REVERSE_GEOCODE_URL}?lat=${lat}&lng=${lng}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (res.ok) {
            const data = await res.json();
            if (data.formatted_lines && data.formatted_lines.length > 0) {
                return data.formatted_lines;
            }
        }
    } catch (e) {}
    return ['Titik Lokasi Lapangan', 'Indonesia'];
}



function formatGpsDateTime(date = new Date()) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();
    const pad = (n) => String(n).padStart(2, '0');
    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());
    const seconds = pad(date.getSeconds());
    return `${month} ${day}, ${year} ${hours}:${minutes}:${seconds}`;
}

function formatGpsCoords(lat, lng) {
    const latStr = Math.abs(lat).toFixed(5) + (lat < 0 ? 'S' : 'N');
    const lngStr = Math.abs(lng).toFixed(5) + (lng >= 0 ? 'E' : 'W');
    return `${latStr} ${lngStr}`;
}

function compressImageFile(file, customOptions = {}) {
    return new Promise(async (resolve) => {
        if (!file || !file.type.startsWith('image/')) {
            return resolve(file);
        }

        const options = {
            maxWidth: 1600,
            maxHeight: 1600,
            quality: 0.82,
            withWatermark: true,
            ...customOptions
        };

        // Mulai ambil logo dan GPS secara paralel saat foto dimuat
        const logoPromise = options.withWatermark ? getMsnLogo() : Promise.resolve(null);
        let gpsPromise = null;
        if (options.withWatermark) {
            if (options.latitude && options.longitude) {
                gpsPromise = Promise.resolve({ latitude: options.latitude, longitude: options.longitude });
            } else {
                gpsPromise = getDeviceCoordinates(7000);
            }
        }

        const reader = new FileReader();
        reader.onload = async function(e) {
            const img = new Image();
            img.onload = async function() {
                let width = img.naturalWidth || img.width;
                let height = img.naturalHeight || img.height;

                if (width > options.maxWidth || height > options.maxHeight) {
                    if (width > height) {
                        height = Math.round((height * options.maxWidth) / width);
                        width = options.maxWidth;
                    } else {
                        width = Math.round((width * options.maxHeight) / height);
                        height = options.maxHeight;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');

                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, width, height);

                // ── WATERMARK OVERLAY (LOGO MSN, MINI MAP, GPS TIMESTAMP & ALAMAT) ──
                if (options.withWatermark) {
                    const [logoImg, gpsCoords] = await Promise.all([logoPromise, gpsPromise]);

                    // 1. Logo MSN di Pojok Kanan Atas
                    if (logoImg) {
                        const logoW = Math.max(120, Math.min(260, Math.round(width * 0.17)));
                        const logoH = Math.round(logoW * (logoImg.naturalHeight / (logoImg.naturalWidth || 1)));
                        const logoX = width - logoW - Math.round(width * 0.03);
                        const logoY = Math.round(width * 0.03);

                        ctx.save();
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
                        ctx.shadowBlur = 8;
                        ctx.drawImage(logoImg, logoX, logoY, logoW, logoH);
                        ctx.restore();
                    }

                    // Koordinat default (jika GPS tidak aktif gunakan koordinat Kota Bandung / Jawa Barat)
                    const lat = gpsCoords ? gpsCoords.latitude : -6.91746;
                    const lng = gpsCoords ? gpsCoords.longitude : 107.61912;

                    // Ambil Alamat Reverse Geocode
                    const addrLines = await getReverseGeocodeLines(lat, lng);

                    // 2. Teks Timestamp, Koordinat & Alamat di Pojok Kanan Bawah
                    const textRight = width - Math.round(width * 0.04);
                    // Perbesar ukuran font (sebelumnya ~0.021, kini diperbesar menjadi ~0.034 agar jelas dan terbaca tajam)
                    const baseFontSize = Math.max(18, Math.min(46, Math.round(width * 0.034)));
                    const lineHeight = Math.round(baseFontSize * 1.36);

                    const fullLines = [
                        formatGpsDateTime(),
                        formatGpsCoords(lat, lng),
                        ...addrLines
                    ];

                    ctx.save();
                    ctx.textAlign = 'right';
                    ctx.textBaseline = 'bottom';
                    // Shadow kuat agar teks kontras di latar terang maupun gelap
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
                    ctx.shadowBlur = Math.max(6, Math.round(baseFontSize * 0.35));
                    ctx.shadowOffsetX = Math.max(1.5, Math.round(baseFontSize * 0.08));
                    ctx.shadowOffsetY = Math.max(1.5, Math.round(baseFontSize * 0.08));

                    let currentBottomY = height - Math.round(width * 0.04);

                    // Gambar dari baris terbawah ke atas
                    for (let idx = fullLines.length - 1; idx >= 0; idx--) {
                        const lineText = fullLines[idx];
                        if (!lineText) continue;

                        if (idx === 0 || idx === 1) {
                            ctx.font = `700 ${baseFontSize}px 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`;
                        } else {
                            ctx.font = `600 ${baseFontSize}px 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`;
                        }

                        ctx.fillStyle = '#ffffff';
                        ctx.fillText(lineText, textRight, currentBottomY);
                        currentBottomY -= lineHeight;
                    }
                    ctx.restore();

                    // Kirim info koordinat yang terdeteksi jika ada callback
                    if (gpsCoords && options.onLocationDetected) {
                        try {
                            options.onLocationDetected(gpsCoords);
                        } catch(e) {}
                    }
                }

                // Convert to JPEG blob
                canvas.toBlob(
                    function(blob) {
                        if (!blob) return resolve(file);

                        let cleanName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        const compressedFile = new File([blob], cleanName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });

                        resolve(compressedFile);
                    },
                    'image/jpeg',
                    options.quality
                );
            };
            img.onerror = function() { resolve(file); };
            img.src = e.target.result;
        };
        reader.onerror = function() { resolve(file); };
        reader.readAsDataURL(file);
    });
}

/**
 * Buat thumbnail frame pertama dari file video untuk preview
 */
function getVideoThumbnail(file) {
    return new Promise((resolve) => {
        if (!file) return resolve(null);
        const url = URL.createObjectURL(file);
        const video = document.createElement('video');
        video.muted = true;
        video.playsInline = true;
        video.src = url;
        video.onloadeddata = () => {
            video.currentTime = Math.min(0.5, (video.duration || 1) / 2);
        };
        video.onseeked = () => {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = Math.min(160, video.videoWidth || 160);
                canvas.height = Math.round(canvas.width * ((video.videoHeight || 120) / (video.videoWidth || 160)));
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const thumbData = canvas.toDataURL('image/jpeg', 0.7);
                URL.revokeObjectURL(url);
                resolve(thumbData);
            } catch(e) {
                URL.revokeObjectURL(url);
                resolve(null);
            }
        };
        video.onerror = () => {
            URL.revokeObjectURL(url);
            resolve(null);
        };
        setTimeout(() => {
            URL.revokeObjectURL(url);
            resolve(null);
        }, 3000);
    });
}

/**
 * Kompresi dan optimasi file video agar ringan (< 3MB) dan cepat diunggah dari lapangan.
 * Menggunakan HTML5 Canvas + MediaRecorder API dengan target bitrate ~1Mbps dan resolusi 720p/480p.
 */
function compressVideoFile(file, customOptions = {}) {
    return new Promise(async (resolve) => {
        if (!file || (!file.type.startsWith('video/') && !/\.(mp4|webm|mov|m4v|3gp|avi)$/i.test(file.name))) {
            return resolve(file);
        }

        const options = {
            maxDimension: 854, // 480p / 720p scaling for fast mobile bandwidth
            bitrate: 1000000,   // ~1.0 Mbps target
            fps: 24,
            maxDuration: 60,    // max 60 seconds per clip
            onProgress: null,
            ...customOptions
        };

        // Jika ukuran video sudah sangat kecil (< 2.5MB), langsung kirim tanpa re-encoding
        if (file.size <= 2.5 * 1024 * 1024) {
            return resolve(file);
        }

        // Cek dukungan MediaRecorder dan captureStream
        const canCaptureCanvas = typeof HTMLCanvasElement !== 'undefined' && HTMLCanvasElement.prototype.captureStream;
        const canRecord = typeof MediaRecorder !== 'undefined';

        if (!canCaptureCanvas || !canRecord) {
            console.warn('Browser tidak mendukung MediaRecorder canvas stream, menggunakan file video original.');
            return resolve(file);
        }

        const videoUrl = URL.createObjectURL(file);
        const video = document.createElement('video');
        video.muted = true;
        video.playsInline = true;
        video.preload = 'auto';
        video.src = videoUrl;

        const cleanup = () => {
            try {
                video.pause();
                video.removeAttribute('src');
                video.load();
                URL.revokeObjectURL(videoUrl);
            } catch(e) {}
        };

        video.onerror = () => {
            cleanup();
            resolve(file);
        };

        video.onloadedmetadata = async () => {
            try {
                const origW = video.videoWidth || 640;
                const origH = video.videoHeight || 480;
                const duration = video.duration || 10;

                // Hitung skala resolusi agar ringan
                let targetW = origW;
                let targetH = origH;
                const maxDim = options.maxDimension;

                if (targetW > maxDim || targetH > maxDim) {
                    if (targetW >= targetH) {
                        targetH = Math.round((targetH * maxDim) / targetW);
                        targetW = maxDim;
                    } else {
                        targetW = Math.round((targetW * maxDim) / targetH);
                        targetH = maxDim;
                    }
                }
                // Pastikan ukuran genap untuk codec video
                targetW = targetW % 2 === 0 ? targetW : targetW + 1;
                targetH = targetH % 2 === 0 ? targetH : targetH + 1;

                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d', { alpha: false });

                // Stream dari canvas
                const stream = canvas.captureStream(options.fps);

                // Tambahkan audio track jika ada
                try {
                    const audioStream = video.captureStream ? video.captureStream() : (video.mozCaptureStream ? video.mozCaptureStream() : null);
                    if (audioStream && audioStream.getAudioTracks().length > 0) {
                        stream.addTrack(audioStream.getAudioTracks()[0]);
                    }
                } catch (e) {}

                // Tentukan supported mimeType
                let mimeType = 'video/webm;codecs=vp8,opus';
                if (MediaRecorder.isTypeSupported('video/mp4;codecs=avc1,mp4a.40.2')) {
                    mimeType = 'video/mp4;codecs=avc1,mp4a.40.2';
                } else if (MediaRecorder.isTypeSupported('video/mp4')) {
                    mimeType = 'video/mp4';
                } else if (MediaRecorder.isTypeSupported('video/webm;codecs=vp8')) {
                    mimeType = 'video/webm;codecs=vp8';
                } else if (MediaRecorder.isTypeSupported('video/webm')) {
                    mimeType = 'video/webm';
                }

                let recorder;
                try {
                    recorder = new MediaRecorder(stream, {
                        mimeType: mimeType,
                        videoBitsPerSecond: options.bitrate
                    });
                } catch(err) {
                    cleanup();
                    return resolve(file);
                }

                const chunks = [];
                recorder.ondataavailable = (e) => {
                    if (e.data && e.data.size > 0) chunks.push(e.data);
                };

                recorder.onstop = () => {
                    cleanup();
                    if (chunks.length === 0) {
                        return resolve(file);
                    }
                    const blob = new Blob(chunks, { type: mimeType.split(';')[0] });
                    const isMp4 = mimeType.includes('mp4');
                    const extension = isMp4 ? '.mp4' : '.webm';
                    const cleanName = (file.name.replace(/\.[^.]+$/, '') || 'rekaman_lapangan') + extension;

                    const compressedFile = new File([blob], cleanName, {
                        type: blob.type,
                        lastModified: Date.now()
                    });

                    // Hanya gunakan jika hasil kompresi lebih kecil dari aslinya
                    if (compressedFile.size < file.size) {
                        resolve(compressedFile);
                    } else {
                        resolve(file);
                    }
                };

                let isRendering = true;
                const drawFrame = () => {
                    if (!isRendering) return;
                    if (video.currentTime >= options.maxDuration || video.ended || video.paused) {
                        if (recorder.state === 'recording') {
                            recorder.stop();
                        }
                        isRendering = false;
                        return;
                    }
                    ctx.drawImage(video, 0, 0, targetW, targetH);

                    if (typeof options.onProgress === 'function' && duration > 0) {
                        const pct = Math.min(99, Math.round((video.currentTime / Math.min(duration, options.maxDuration)) * 100));
                        options.onProgress(pct);
                    }

                    if ('requestVideoFrameCallback' in video) {
                        video.requestVideoFrameCallback(drawFrame);
                    } else {
                        requestAnimationFrame(drawFrame);
                    }
                };

                recorder.start(100);
                video.playbackRate = 1.0;
                await video.play();
                drawFrame();

                video.onended = () => {
                    if (recorder.state === 'recording') {
                        recorder.stop();
                    }
                    isRendering = false;
                };
            } catch(e) {
                console.warn('Video compression exception:', e);
                cleanup();
                resolve(file);
            }
        };
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // ── 1. MODAL CLOSING TIKET LIVE MTTR CALCULATION ──
    const modalCloseDateInput = document.getElementById('modal_tanggal_close');
    const modalMttrPreview = document.getElementById('modalMttrPreview');
    const modalSlaPreview = document.getElementById('modalSlaPreview');

    if (modalCloseDateInput && modalMttrPreview && modalSlaPreview) {
        const openTime = new Date("{{ $tiket->tanggal_open->toIso8601String() }}").getTime();
        const slaMinutes = {{ (int) ($tiket->sla_target_minutes ?? 360) }};

        function calculateModalMttr() {
            if (!modalCloseDateInput.value) return;

            const closeTime = new Date(modalCloseDateInput.value).getTime();
            const diffMinutes = Math.max(0, Math.round((closeTime - openTime) / (1000 * 60)));

            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;

            const durText = (hours > 0 ? `${hours} jam ` : '') + (minutes > 0 || hours === 0 ? `${minutes} menit` : '');
            modalMttrPreview.textContent = `${durText} (${diffMinutes} menit)`;

            if (diffMinutes <= slaMinutes) {
                modalSlaPreview.className = 'badge bg-success';
                modalSlaPreview.innerHTML = '<i class="bi bi-shield-check me-1"></i> TEPAT SLA';
            } else {
                modalSlaPreview.className = 'badge bg-danger';
                modalSlaPreview.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> MELEBIHI SLA';
            }
        }

        modalCloseDateInput.addEventListener('change', calculateModalMttr);
        modalCloseDateInput.addEventListener('input', calculateModalMttr);
        calculateModalMttr();
    }

    // ── 2. PREVIEW UPLOAD FOTO KRONOLOGIS (WITH AUTO-COMPRESS) ──
    const fotoInput = document.getElementById('foto');
    const fotoPreviewContainer = document.getElementById('fotoPreviewContainer');
    const fotoPreview = document.getElementById('fotoPreview');

    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (file) {
                const compressed = await compressImageFile(file, {
                    maxWidth: 1600,
                    maxHeight: 1600,
                    quality: 0.82,
                    withWatermark: true,
                    onLocationDetected: (coords) => {
                        if (latInput && !latInput.value) latInput.value = coords.latitude.toFixed(7);
                        if (lngInput && !lngInput.value) lngInput.value = coords.longitude.toFixed(7);
                        if (locationStatus) locationStatus.innerHTML = '<span class="text-success"><i class="bi bi-geo-alt-fill"></i> Lokasi GPS otomatis terdeteksi dari kamera</span>';
                    }
                });
                if (window.DataTransfer) {
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    fotoInput.files = dt.files;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                    fotoPreviewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(compressed);
            } else {
                fotoPreviewContainer.classList.add('d-none');
            }
        });
    }

    // ── 3. GEOLOCATION API (LOKASI SAYA) ──
    const btnGetLocation = document.getElementById('btnGetLocation');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locationStatus = document.getElementById('locationStatus');

    if (btnGetLocation && latInput && lngInput) {
        btnGetLocation.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
                return;
            }

            locationStatus.innerHTML = '<span class="text-primary"><i class="spinner-border spinner-border-sm"></i> Mendeteksi koordinat GPS...</span>';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    latInput.value = pos.coords.latitude.toFixed(6);
                    lngInput.value = pos.coords.longitude.toFixed(6);
                    locationStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Lokasi GPS berhasil diambil.</span>';
                },
                function(err) {
                    locationStatus.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Gagal mendeteksi lokasi: ' + err.message + '</span>';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // ── 4. LEAFLET.JS MAP PICKER MODAL FOR KRONOLOGIS ──
    let mapPickerInstance = null;
    let mapMarker = null;
    const btnOpenMapPicker = document.getElementById('btnOpenMapPicker');
    const mapPickerModal = document.getElementById('mapPickerModal');
    const pickedCoordsText = document.getElementById('pickedCoordsText');
    const btnApplyPickedCoords = document.getElementById('btnApplyPickedCoords');

    let currentLat = -6.917464; // Default Bandung
    let currentLng = 107.619123;

    if (btnOpenMapPicker && mapPickerModal) {
        const bsMapModal = new bootstrap.Modal(mapPickerModal);

        btnOpenMapPicker.addEventListener('click', function() {
            if (latInput.value && lngInput.value) {
                currentLat = parseFloat(latInput.value);
                currentLng = parseFloat(lngInput.value);
            }
            bsMapModal.show();
        });

        mapPickerModal.addEventListener('shown.bs.modal', function () {
            if (!mapPickerInstance) {
                mapPickerInstance = L.map('mapPicker').setView([currentLat, currentLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapPickerInstance);

                mapMarker = L.marker([currentLat, currentLng], { draggable: true }).addTo(mapPickerInstance);

                mapMarker.on('dragend', function (e) {
                    const pos = mapMarker.getLatLng();
                    updatePickedCoords(pos.lat, pos.lng);
                });

                mapPickerInstance.on('click', function (e) {
                    mapMarker.setLatLng(e.latlng);
                    updatePickedCoords(e.latlng.lat, e.latlng.lng);
                });
            } else {
                mapPickerInstance.invalidateSize();
                mapPickerInstance.setView([currentLat, currentLng], 14);
                mapMarker.setLatLng([currentLat, currentLng]);
            }
            updatePickedCoords(currentLat, currentLng);
        });

        function updatePickedCoords(lat, lng) {
            currentLat = lat;
            currentLng = lng;
            pickedCoordsText.textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        btnApplyPickedCoords.addEventListener('click', function() {
            latInput.value = currentLat.toFixed(6);
            lngInput.value = currentLng.toFixed(6);
            locationStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Koordinat dari peta diterapkan.</span>';
            bsMapModal.hide();
        });
    }

    // ── 5. DYNAMIC TEAM OM INPUT (FASE 4) ──
    const btnAddTeamMember = document.getElementById('btnAddTeamMember');
    const teamOmContainer = document.getElementById('teamOmContainer');

    if (btnAddTeamMember && teamOmContainer) {
        btnAddTeamMember.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'input-group input-group-sm mb-2 team-om-row';
            row.innerHTML = `
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="team_om[]" class="form-control" placeholder="Nama Teknisi" required>
                <button type="button" class="btn btn-outline-danger btn-remove-team">
                    <i class="bi bi-dash"></i>
                </button>
            `;
            teamOmContainer.appendChild(row);
            updateRemoveTeamButtons();
        });

        teamOmContainer.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-team')) {
                const row = e.target.closest('.team-om-row');
                if (document.querySelectorAll('.team-om-row').length > 1) {
                    row.remove();
                    updateRemoveTeamButtons();
                }
            }
        });

        function updateRemoveTeamButtons() {
            const rows = document.querySelectorAll('.team-om-row');
            rows.forEach(r => {
                const btn = r.querySelector('.btn-remove-team');
                if (btn) btn.disabled = (rows.length <= 1);
            });
        }
    }

    // ── 6. GEOLOCATION & MAP PICKER FOR TITIK PERBAIKAN (FASE 4) ──
    const btnGetLocationTitik = document.getElementById('btnGetLocationTitik');
    const latTitikInput = document.getElementById('latitude_titik');
    const lngTitikInput = document.getElementById('longitude_titik');
    const btnOpenMapPickerTitik = document.getElementById('btnOpenMapPickerTitik');
    const mapPickerTitikModal = document.getElementById('mapPickerTitikModal');
    const pickedCoordsTitikText = document.getElementById('pickedCoordsTitikText');
    const btnApplyPickedCoordsTitik = document.getElementById('btnApplyPickedCoordsTitik');

    let mapPickerTitikInstance = null;
    let mapMarkerTitik = null;
    let currentLatTitik = -6.917464;
    let currentLngTitik = 107.619123;

    if (btnGetLocationTitik && latTitikInput && lngTitikInput) {
        btnGetLocationTitik.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    latTitikInput.value = pos.coords.latitude.toFixed(6);
                    lngTitikInput.value = pos.coords.longitude.toFixed(6);
                },
                function(err) {
                    alert('Gagal mendeteksi lokasi: ' + err.message);
                }
            );
        });
    }

    if (btnOpenMapPickerTitik && mapPickerTitikModal) {
        const bsMapTitikModal = new bootstrap.Modal(mapPickerTitikModal);

        btnOpenMapPickerTitik.addEventListener('click', function() {
            if (latTitikInput.value && lngTitikInput.value) {
                currentLatTitik = parseFloat(latTitikInput.value);
                currentLngTitik = parseFloat(lngTitikInput.value);
            }
            bsMapTitikModal.show();
        });

        mapPickerTitikModal.addEventListener('shown.bs.modal', function () {
            if (!mapPickerTitikInstance) {
                mapPickerTitikInstance = L.map('mapPickerTitik').setView([currentLatTitik, currentLngTitik], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapPickerTitikInstance);

                mapMarkerTitik = L.marker([currentLatTitik, currentLngTitik], { draggable: true }).addTo(mapPickerTitikInstance);

                mapMarkerTitik.on('dragend', function () {
                    const pos = mapMarkerTitik.getLatLng();
                    currentLatTitik = pos.lat;
                    currentLngTitik = pos.lng;
                    pickedCoordsTitikText.textContent = `Koordinat: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                });

                mapPickerTitikInstance.on('click', function (e) {
                    mapMarkerTitik.setLatLng(e.latlng);
                    currentLatTitik = e.latlng.lat;
                    currentLngTitik = e.latlng.lng;
                    pickedCoordsTitikText.textContent = `Koordinat: ${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
                });
            } else {
                mapPickerTitikInstance.invalidateSize();
                mapPickerTitikInstance.setView([currentLatTitik, currentLngTitik], 14);
                mapMarkerTitik.setLatLng([currentLatTitik, currentLngTitik]);
            }
        });

        btnApplyPickedCoordsTitik.addEventListener('click', function() {
            latTitikInput.value = currentLatTitik.toFixed(6);
            lngTitikInput.value = currentLngTitik.toFixed(6);
            bsMapTitikModal.hide();
        });
    }

    // ── 7. LEAFLET MAP VIEW FOR ALL TITIK PERBAIKAN (TAB 3) ──
    const titikMapEl = document.getElementById('titikPerbaikanMap');
    const materialTabBtn = document.getElementById('material-tab');

    @php
        $titikListJson = $tiket->titikPerbaikans->map(function($tp) {
            return [
                'nama' => $tp->nama_titik,
                'lat'  => (float) $tp->latitude,
                'lng'  => (float) $tp->longitude,
                'ket'  => $tp->keterangan ?: '',
            ];
        })->toJson();
    @endphp

    const titikData = {!! $titikListJson !!};
    let titikMapInstance = null;

    function initTitikMap() {
        if (!titikMapEl || titikData.length === 0) return;

        if (!titikMapInstance) {
            const first = titikData[0];
            titikMapInstance = L.map('titikPerbaikanMap').setView([first.lat, first.lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(titikMapInstance);

            const bounds = [];
            titikData.forEach(t => {
                const marker = L.marker([t.lat, t.lng]).addTo(titikMapInstance);
                marker.bindPopup(`<strong>${t.nama}</strong><br>${t.ket}<br><small>${t.lat}, ${t.lng}</small>`);
                bounds.push([t.lat, t.lng]);
            });

            if (bounds.length > 1) {
                titikMapInstance.fitBounds(bounds, { padding: [30, 30] });
            }
        } else {
            titikMapInstance.invalidateSize();
        }
    }

    if (materialTabBtn) {
        materialTabBtn.addEventListener('shown.bs.tab', function () {
            setTimeout(initTitikMap, 200);
        });
    }

    // ── 8. REALTIME AJAX POLLING FOR TIMELINE (EVERY 5 SECONDS) ──
    const tiketId = {{ $tiket->id }};
    const timelineApiUrl = "{{ route('tiket.kronologis.index', $tiket->id) }}";
    let knownCount = {{ $totalKronologis ?? $tiket->kronologis()->count() }};
    let isLoadingOlder = false;

    function pollTimeline() {
        // Ambil ID pesan terakhir yang ada di DOM saat ini
        const allMsgRows = document.querySelectorAll('.wa-msg-row');
        let latestId = null;
        if (allMsgRows.length > 0) {
            const lastRow = allMsgRows[allMsgRows.length - 1];
            const idMatch = lastRow.id ? lastRow.id.match(/\d+/) : null;
            if (idMatch) latestId = parseInt(idMatch[0], 10);
        }

        const pollUrl = latestId ? `${timelineApiUrl}?after_id=${latestId}` : timelineApiUrl;

        fetch(pollUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                if (res.total_count !== undefined && res.total_count !== knownCount) {
                    knownCount = res.total_count;
                    const badgeEl = document.getElementById('kronologisCountBadge');
                    if (badgeEl) badgeEl.textContent = knownCount;
                }
                if (res.views) {
                    window.TICKET_USER_VIEWS = res.views;
                }
                if (res.data && res.data.length > 0) {
                    updateTimelineFromData(res.data);
                }

                // Perbarui status centang 2 biru secara realtime jika sudah dibuka/dibaca oleh pengguna lain
                if (res.is_closed_or_verified || (res.max_read_timestamp && res.max_read_timestamp > 0)) {
                    document.querySelectorAll('.wa-msg-outgoing').forEach(row => {
                        const checkIcon = row.querySelector('.wa-status-sent');
                        if (checkIcon) {
                            const rawTs = row.getAttribute('data-timestamp');
                            if (!rawTs) return;
                            const msgTimestamp = parseInt(rawTs, 10);
                            if (msgTimestamp > 0 && (res.is_closed_or_verified || (res.max_read_timestamp && msgTimestamp <= res.max_read_timestamp))) {
                                checkIcon.className = 'bi bi-check2-all wa-status-icon wa-status-read';
                                checkIcon.title = 'Dilihat oleh tim';
                            }
                        }
                    });
                }
            }
        })
        .catch(err => console.debug('Timeline polling error:', err));
    }

    // Fungsi muat riwayat pesan terdahulu (Pagination ke atas)
    async function loadOlderMessages() {
        const btn = document.getElementById('btnLoadOlderKrono');
        const loadWrapper = document.getElementById('loadOlderKronoWrapper');
        if (!btn || isLoadingOlder) return;

        const oldestId = btn.getAttribute('data-oldest-id');
        if (!oldestId) return;

        isLoadingOlder = true;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Memuat riwayat pesan...';

        try {
            const res = await fetch(`${timelineApiUrl}?before_id=${oldestId}&limit=40`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.success && data.data && data.data.length > 0) {
                if (data.views) {
                    window.TICKET_USER_VIEWS = data.views;
                }
                const stream = document.getElementById('timelineList');
                if (stream) {
                    // Simpan posisi scroll sebelum prepend
                    const prevScrollHeight = stream.scrollHeight;
                    const prevScrollTop = stream.scrollTop;

                    let olderHtml = '';
                    let lastDateKey = null;

                    data.data.forEach(k => {
                        if (k.date_key !== lastDateKey) {
                            olderHtml += `
                                <div class="wa-date-divider">
                                    <span class="wa-date-chip">
                                        <i class="bi bi-calendar3 me-1"></i> ${k.formatted_date}
                                    </span>
                                </div>`;
                            lastDateKey = k.date_key;
                        }
                        olderHtml += buildSingleKronoHtml(k);
                    });

                    if (loadWrapper) {
                        loadWrapper.insertAdjacentHTML('afterend', olderHtml);
                    } else {
                        stream.insertAdjacentHTML('afterbegin', olderHtml);
                    }

                    // Pertahankan posisi scroll agar tidak meloncat
                    const newScrollHeight = stream.scrollHeight;
                    stream.scrollTop = prevScrollTop + (newScrollHeight - prevScrollHeight);

                    // Update oldest id
                    const newOldestId = data.oldest_id || data.data[0].id;
                    btn.setAttribute('data-oldest-id', newOldestId);

                    const renderedCount = document.querySelectorAll('.wa-msg-row').length;
                    const total = data.total_count || knownCount;
                    if (!data.has_more || renderedCount >= total) {
                        if (loadWrapper) loadWrapper.remove();
                    } else {
                        btn.disabled = false;
                        const remaining = Math.max(0, total - renderedCount);
                        btn.innerHTML = `<i class="bi bi-clock-history me-1.5 text-primary"></i> Muat Pesan Sebelumnya (<span id="olderKronoCount">${remaining}</span> lagi)`;
                    }
                }
            } else {
                if (loadWrapper) loadWrapper.remove();
            }
        } catch (err) {
            console.error('Error loading older messages:', err);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1 text-danger"></i> Gagal memuat. Klik untuk coba lagi.';
        } finally {
            isLoadingOlder = false;
        }
    }

    // ── HELPER FUNCTIONS (scope luar agar bisa diakses form submit) ──
    const nameColors = ['#075e54', '#128c7e', '#0284c7', '#7c3aed', '#d97706', '#059669', '#2563eb'];
    function getSenderColor(name) {
        let hash = 0;
        for (let i = 0; i < (name || 'User').length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        return nameColors[Math.abs(hash) % nameColors.length];
    }

    window.TICKET_USER_VIEWS = @json($ticketViews ?? []);
    const MENTIONABLE_USERS = @json($mentionableUsers ?? []);
    const isTiketClosed = {{ $tiket->status === 'CLOSE' ? 'true' : 'false' }};
    const canChat = {{ auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']) ? 'true' : 'false' }};
    const currentUserId = {{ auth()->id() ?? 0 }};
    const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
    const destroyUrlBase = "{{ url('/tiket/' . $tiket->id . '/kronologis') }}";
    const csrfToken = "{{ csrf_token() }}";

    function rawEscape(str) {
        if (!str) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(str).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function formatMessageWithMentions(text) {
        if (!text) return '';
        let str = text;
        let quoteHtml = '';

        // Deteksi format kutipan balasan: [Membalas Sender]: Pesan Asli\n\n (atau legacy > [Membalas...])
        const quoteMatch = str.match(/^>?\s*\[Membalas\s+([^\]]+)\]:\s*([^\n]+)\n+/i);
        if (quoteMatch) {
            const sender = rawEscape(quoteMatch[1]);
            const quoteContent = rawEscape(quoteMatch[2]);
            quoteHtml = `<div class="wa-quote-box"><div class="wa-quote-sender"><i class="bi bi-reply-fill me-1"></i>${sender}</div><div class="wa-quote-text">${quoteContent}</div></div>`;
            str = str.substring(quoteMatch[0].length);
        }

        str = str.trim().replace(/(\r?\n\s*){2,}/g, '\n');

        // Otomatis ubah format menit mentah (misal: 751 menit) menjadi jam dan menit yang rapi
        str = str.replace(/(Durasi Jeda|Total Jeda SLA Tiket|Total Stop Clock):\s*(\d+)\s*menit/gi, function(match, label, mins) {
            const m = parseInt(mins, 10) || 0;
            if (m <= 0) return `${label}: 0 menit`;
            const jam = Math.floor(m / 60);
            const sisa = m % 60;
            if (jam > 0 && sisa > 0) return `${label}: ${jam} jam ${sisa} menit`;
            if (jam > 0) return `${label}: ${jam} jam`;
            return `${label}: ${sisa} menit`;
        });

        let safe = rawEscape(str).replace(/\n/g, '<br>');
        let formatted = safe.replace(/(@[a-zA-Z0-9_\.\-]+(?:\s+[a-zA-Z0-9_\.\-]+)?)/g, function(match) {
            return `<span class="wa-mention-tag-highlight">${match}</span>`;
        });

        return quoteHtml + formatted;
    }

    function escapeHtml(text) {
        return rawEscape(text);
    }

    function buildSingleKronoHtml(k, isNew = false) {
            const isMe = (k.user_id === currentUserId);
            const animClass = isNew ? (isMe ? 'wa-msg-anim-send' : 'wa-msg-anim-receive') : '';
            const initials = (k.user_name || 'U').substring(0, 2).toUpperCase();
            const senderColor = getSenderColor(k.user_name);
            const userAvatar = k.user_avatar || null;
            const senderDisplayName = isMe ? 'Anda' : (k.user_name || 'User');
            const sentMoment = k.created_at ? new Date(k.created_at) : (k.timestamp ? new Date(k.timestamp) : new Date());
            const diffMinutes = (Date.now() - sentMoment.getTime()) / (1000 * 60);
            const canEdit = !isTiketClosed && (isMe || isAdmin) && (diffMinutes <= 5);
            const canDelete = isAdmin || (!isTiketClosed && isMe);
            const canReply = !isTiketClosed && canChat;
            const safeInfoAttr = rawEscape(k.informasi || '');

            const kTimestampUnix = k.timestamp ? Math.floor(new Date(k.timestamp).getTime() / 1000) : Math.floor(sentMoment.getTime() / 1000);

            return `
                <div class="wa-msg-row ${isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming'} ${animClass}" id="krono-item-${k.id}" data-id="${k.id}" data-timestamp="${kTimestampUnix}">
                    ${!isMe ? `
                    <div class="wa-avatar" style="background-color: ${userAvatar ? 'transparent' : senderColor};" title="${k.user_name}">
                        ${userAvatar ? `<img src="${userAvatar}" alt="${k.user_name}" class="wa-avatar-img">` : initials}
                    </div>` : ''}

                    <div class="wa-bubble ${isMe ? 'wa-bubble-outgoing' : 'wa-bubble-incoming'}">
                        <!-- Bubble Header: Sender, Role & 3-Dots Action Menu -->
                        <div class="wa-bubble-header">
                            <div class="wa-sender-info">
                                <span class="wa-sender-name" style="color: ${isMe ? '#0f766e' : senderColor};">
                                    ${senderDisplayName}
                                </span>
                                <span class="wa-role-pill">${k.user_role || '-'}</span>
                            </div>

                            <!-- 3-Dots Action Menu -->
                            <div class="dropdown wa-bubble-menu-wrapper">
                                <button type="button" class="wa-msg-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan pesan">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end wa-msg-dropdown-menu shadow border-0">
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-copy" data-id="${k.id}" data-text="${safeInfoAttr}">
                                            <i class="bi bi-clipboard text-primary"></i> Salin
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-msg-info"
                                                data-id="${k.id}"
                                                data-user-id="${k.user_id || ''}"
                                                data-sender="${rawEscape(senderDisplayName)}"
                                                data-sender-role="${k.user_role || '-'}"
                                                data-time="${k.formatted_time}"
                                                data-text="${safeInfoAttr}"
                                                data-photo="${k.foto_url || ''}"
                                                data-timestamp="${kTimestampUnix}">
                                            <i class="bi bi-info-circle-fill text-info"></i> Info Pesan
                                        </button>
                                    </li>
                                    ${k.foto_url && !isTiketClosed && canChat ? `
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-forward-doc text-success"
                                                onclick="forwardPhotoToDoc('${k.foto_url}', '${k.latitude || ''}', '${k.longitude || ''}', '${k.timestamp ? k.timestamp.substring(0,16) : ''}', '${k.kategori || ''}')">
                                            <i class="bi bi-folder-plus text-success"></i> Simpan ke Dokumentasi
                                        </button>
                                    </li>` : ''}
                                    ${canReply ? `
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-reply" data-id="${k.id}" data-sender="${rawEscape(senderDisplayName)}" data-text="${safeInfoAttr}">
                                            <i class="bi bi-reply-fill text-info"></i> Balas
                                        </button>
                                    </li>` : ''}
                                    ${canEdit ? `
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-edit" data-id="${k.id}" data-text="${safeInfoAttr}">
                                            <i class="bi bi-pencil-square text-warning"></i> Edit
                                        </button>
                                    </li>` : ''}
                                    ${canDelete ? `
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item wa-msg-dropdown-item text-danger btn-action-delete" data-id="${k.id}">
                                            <i class="bi bi-trash3-fill"></i> Hapus
                                        </button>
                                    </li>` : ''}
                                </ul>
                            </div>
                        </div>

                        <div class="wa-msg-text">${formatMessageWithMentions(k.informasi || '')}</div>

                        ${k.is_video || k.video_url ? `
                        <div class="wa-media-card wa-video-card">
                            <video src="${k.video_url || k.foto_url}" controls playsinline preload="metadata" class="wa-video-player"></video>
                        </div>` : (k.foto_url ? `
                        <div class="wa-media-card" onclick="zoomPhoto('${k.foto_url}', '${k.kategori} - ${k.formatted_time}')">
                            <img src="${k.foto_url}" alt="Foto Kronologis" class="wa-media-img" width="280" height="158" loading="lazy" decoding="async">
                            <div class="wa-media-badge">
                                <i class="bi bi-arrows-fullscreen"></i>
                                <span>Klik untuk memperbesar</span>
                            </div>
                        </div>` : '')}

                        ${k.has_coordinates ? `
                        <div class="wa-location-card">
                            <div class="wa-loc-icon">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                            </div>
                            <div class="wa-loc-info">
                                <div class="wa-loc-title">Lokasi Titik Lapangan</div>
                                <div class="wa-loc-coords">${k.latitude}, ${k.longitude}</div>
                            </div>
                            <a href="${k.google_maps_url}" target="_blank" class="wa-loc-btn" title="Buka di Google Maps">
                                <span>Peta</span>
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>` : ''}

                        <div class="wa-bubble-footer">
                            <span class="wa-time">${k.formatted_time}</span>
                            ${isMe ? `<i class="bi bi-check2-all wa-status-icon wa-status-sent" title="Terkirim (Belum dilihat)"></i>` : ''}
                        </div>
                    </div>
                </div>`;
    }

    function appendSingleKronoToTimeline(k, isSelf = true) {
        if (!k || !k.id) return;

        // Jangan append jika sudah ada di DOM
        if (document.getElementById('krono-item-' + k.id)) return;

        const wrapper = document.getElementById('timelineWrapper');
        let stream = document.getElementById('timelineList');

        const emptyEl = document.getElementById('emptyTimeline');
        if (emptyEl) emptyEl.remove();

        if (!stream && wrapper) {
            wrapper.innerHTML = '<div class="wa-chat-stream" id="timelineList"></div>';
            stream = document.getElementById('timelineList');
            attachStreamScrollListener(stream);
        }

        if (!stream) return;

        // Jika ada pesan baru masuk dari anggota tim lain, ubah status pesan outgoing yang dikirim sebelum/saat pesan ini menjadi ceklis biru (Read)
        if (k.user_id !== currentUserId) {
            const newKronoTs = k.timestamp ? Math.floor(new Date(k.timestamp).getTime() / 1000) : Math.floor(Date.now() / 1000);
            document.querySelectorAll('.wa-bubble-outgoing .wa-status-sent').forEach(el => {
                const row = el.closest('.wa-msg-row');
                const rowTs = row ? parseInt(row.getAttribute('data-timestamp') || '0', 10) : 0;
                if (rowTs > 0 && rowTs <= newKronoTs) {
                    el.className = 'bi bi-check2-all wa-status-icon wa-status-read';
                    el.title = 'Dilihat oleh tim';
                }
            });
        }

        // Cek apakah posisi scroll stream saat ini sedang berada di dekat bawah
        const isNearBottom = (stream.scrollHeight - stream.scrollTop - stream.clientHeight) < 200;

        // Cek apakah perlu menambahkan date divider baru
        const allDividers = stream.querySelectorAll('.wa-date-divider .wa-date-chip');
        const lastDivider = allDividers.length > 0 ? allDividers[allDividers.length - 1] : null;
        const lastDateText = lastDivider ? lastDivider.textContent.trim() : '';
        if (k.formatted_date && (!lastDateText || !lastDateText.includes(k.formatted_date))) {
            stream.insertAdjacentHTML('beforeend', `
                <div class="wa-date-divider">
                    <span class="wa-date-chip">
                        <i class="bi bi-calendar3 me-1"></i> ${k.formatted_date}
                    </span>
                </div>`);
        }

        // Simpan posisi scroll window agar tidak melompat
        const savedWindowY = window.scrollY || window.pageYOffset;

        stream.insertAdjacentHTML('beforeend', buildSingleKronoHtml(k, true));

        // Pertahankan posisi scroll window
        window.scrollTo(0, savedWindowY);

        // Hanya auto-scroll stream jika user sedang di bawah atau pengirim adalah diri sendiri
        if (isNearBottom || isSelf) {
            scrollChatToBottom(true);
            requestAnimationFrame(() => {
                scrollChatToBottom(true);
                window.scrollTo(0, savedWindowY);
            });
        }

        // Highlight pesan baru
        const newEl = document.getElementById('krono-item-' + k.id);
        if (newEl) {
            newEl.classList.add('wa-bubble-new-highlight');
            setTimeout(() => newEl.classList.remove('wa-bubble-new-highlight'), 4000);
        }

        // Fokus ke textarea hanya jika user sendiri yang baru mengirim pesan (desktop)
        if (isSelf) {
            const waChatInput = document.getElementById('waChatTextInput');
            if (waChatInput && window.innerWidth >= 768) {
                waChatInput.focus({ preventScroll: true });
            }
        }

        checkStreamScroll(stream);
    }

    function updateTimelineFromData(items) {
        if (!items || items.length === 0) return;

        const stream = document.getElementById('timelineList');
        if (!stream) {
            renderTimelineFromData(items);
            return;
        }

        items.forEach(k => {
            if (!document.getElementById('krono-item-' + k.id)) {
                appendSingleKronoToTimeline(k, false);
            }
        });
    }

    function renderTimelineFromData(items) {
        const wrapper = document.getElementById('timelineWrapper');
        if (!wrapper || !items) return;

        if (items.length === 0) {
            wrapper.innerHTML = `
                <div class="wa-empty-state py-5 text-center" id="emptyTimeline">
                    <div class="wa-empty-icon mb-3">
                        <i class="bi bi-chat-square-dots-fill text-muted opacity-50"></i>
                    </div>
                    <h6 class="fw-bold text-navy mb-1">Belum Ada Catatan Koordinasi</h6>
                    <p class="text-muted small mb-3 px-3 mx-auto" style="max-width: 420px;">
                        Mulai percakapan perkembangan update teknis di lapangan. Seluruh aktivitas perbaikan akan tercatat secara kronologis.
                    </p>
                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                    <button class="btn btn-cjp-teal btn-sm rounded-pill px-4 shadow-xs" data-bs-toggle="modal" data-bs-target="#addKronologisModal">
                        <i class="bi bi-plus-circle me-1"></i> Mulai Catatan Kronologis
                    </button>
                    @endif
                </div>`;
            return;
        }

        let html = '<div class="wa-chat-stream" id="timelineList">';
        let lastDateKey = null;

        items.forEach(k => {
            if (k.date_key !== lastDateKey) {
                html += `
                    <div class="wa-date-divider">
                        <span class="wa-date-chip">
                            <i class="bi bi-calendar3 me-1"></i> ${k.formatted_date}
                        </span>
                    </div>`;
                lastDateKey = k.date_key;
            }
            html += buildSingleKronoHtml(k);
        });

        html += '</div>';

        // Kunci tinggi wrapper agar window tidak loncat saat DOM diganti
        const prevHeight = wrapper.offsetHeight;
        if (prevHeight > 0) wrapper.style.minHeight = prevHeight + 'px';

        wrapper.innerHTML = html;

        setTimeout(() => { wrapper.style.minHeight = ''; }, 150);

        const stream = document.getElementById('timelineList');
        if (stream) {
            scrollChatToBottom(true);
            attachStreamScrollListener(stream);
            checkStreamScroll(stream);
        }
    }

    setInterval(pollTimeline, 5000);

    // ── 9. DOKUMENTASI CATEGORY FILTER (FASE 5) ──
    const filterBtns = document.querySelectorAll('.doc-filter-btn');
    const galleryItems = document.querySelectorAll('.doc-gallery-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'active');

            const filter = this.getAttribute('data-filter');
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    });

    // ── 10. MULTI-PHOTO UPLOAD INSTANT THUMBNAIL PREVIEW (FASE 5 - WITH AUTO-COMPRESS) ──
    const photosInput = document.getElementById('photosInput');
    const docThumbnailsContainer = document.getElementById('docThumbnailsContainer');

    if (photosInput && docThumbnailsContainer) {
        photosInput.addEventListener('change', async function() {
            docThumbnailsContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                docThumbnailsContainer.classList.remove('d-none');
                docThumbnailsContainer.innerHTML = '<div class="small text-muted py-2"><span class="spinner-border spinner-border-sm text-primary me-1"></span> Mengompres foto otomatis...</div>';

                const rawFiles = Array.from(this.files);
                const compressedFiles = await Promise.all(
                    rawFiles.map(f => compressImageFile(f, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 }))
                );

                if (window.DataTransfer) {
                    const dt = new DataTransfer();
                    compressedFiles.forEach(f => dt.items.add(f));
                    photosInput.files = dt.files;
                }

                docThumbnailsContainer.innerHTML = '';
                compressedFiles.forEach((file, idx) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative border rounded p-1 bg-white shadow-xs';
                        wrapper.style.width = '80px';
                        wrapper.style.height = '80px';
                        wrapper.innerHTML = `
                            <img src="${e.target.result}" class="w-100 h-100 rounded" style="object-fit: cover;" alt="Preview ${idx + 1}">
                            <span class="badge bg-navy position-absolute bottom-0 end-0 m-1" style="font-size: 0.55rem;">#${idx + 1} (${formatFileSize(file.size)})</span>
                        `;
                        docThumbnailsContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                docThumbnailsContainer.classList.add('d-none');
            }
        });
    }

    // ── 11. DOKUMENTASI CATEGORY & MANUVER QUICK PRESETS ──
    // Kategori chips preset
    document.querySelectorAll('.doc-cat-preset').forEach(chip => {
        chip.addEventListener('click', function() {
            const cat = this.getAttribute('data-cat');
            const selectEl = document.getElementById('doc_kategori');
            if (selectEl) selectEl.value = cat;
        });
    });

    // Manuver titik preset
    document.querySelectorAll('.titik-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const titik = this.getAttribute('data-titik');
            const inputEl = document.getElementById('titik_manuver');
            if (inputEl) inputEl.value = titik;
        });
    });

    // Core Asal preset
    document.querySelectorAll('.core-asal-preset').forEach(chip => {
        chip.addEventListener('click', function() {
            const val = this.getAttribute('data-val');
            const inputEl = document.getElementById('core_asal');
            if (inputEl) inputEl.value = val;
        });
    });

    // Core Tujuan preset
    document.querySelectorAll('.core-tujuan-preset').forEach(chip => {
        chip.addEventListener('click', function() {
            const val = this.getAttribute('data-val');
            const inputEl = document.getElementById('core_tujuan');
            if (inputEl) inputEl.value = val;
        });
    });

    // ── 12. DOKUMENTASI GEOLOCATION & MAP PICKER (FASE 5) ──
    const btnGetLocationDoc = document.getElementById('btnGetLocationDoc');
    const latDocInput = document.getElementById('latitude_doc');
    const lngDocInput = document.getElementById('longitude_doc');
    const btnOpenMapPickerDoc = document.getElementById('btnOpenMapPickerDoc');
    const mapPickerDocModal = document.getElementById('mapPickerDocModal');
    const pickedCoordsDocText = document.getElementById('pickedCoordsDocText');
    const btnApplyPickedCoordsDoc = document.getElementById('btnApplyPickedCoordsDoc');

    let mapPickerDocInstance = null;
    let mapMarkerDoc = null;
    let currentLatDoc = -6.917464;
    let currentLngDoc = 107.619123;

    if (btnGetLocationDoc && latDocInput && lngDocInput) {
        btnGetLocationDoc.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    latDocInput.value = pos.coords.latitude.toFixed(6);
                    lngDocInput.value = pos.coords.longitude.toFixed(6);
                },
                function(err) {
                    alert('Gagal mendeteksi lokasi GPS: ' + err.message);
                }
            );
        });
    }

    if (btnOpenMapPickerDoc && mapPickerDocModal) {
        const bsMapDocModal = new bootstrap.Modal(mapPickerDocModal);

        btnOpenMapPickerDoc.addEventListener('click', function() {
            if (latDocInput.value && lngDocInput.value) {
                currentLatDoc = parseFloat(latDocInput.value);
                currentLngDoc = parseFloat(lngDocInput.value);
            }
            bsMapDocModal.show();
        });

        mapPickerDocModal.addEventListener('shown.bs.modal', function () {
            if (!mapPickerDocInstance) {
                mapPickerDocInstance = L.map('mapPickerDoc').setView([currentLatDoc, currentLngDoc], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapPickerDocInstance);

                mapMarkerDoc = L.marker([currentLatDoc, currentLngDoc], { draggable: true }).addTo(mapPickerDocInstance);

                mapMarkerDoc.on('dragend', function () {
                    const pos = mapMarkerDoc.getLatLng();
                    currentLatDoc = pos.lat;
                    currentLngDoc = pos.lng;
                    pickedCoordsDocText.textContent = `Koordinat: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                });

                mapPickerDocInstance.on('click', function (e) {
                    mapMarkerDoc.setLatLng(e.latlng);
                    currentLatDoc = e.latlng.lat;
                    currentLngDoc = e.latlng.lng;
                    pickedCoordsDocText.textContent = `Koordinat: ${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
                });
            } else {
                mapPickerDocInstance.invalidateSize();
                mapPickerDocInstance.setView([currentLatDoc, currentLngDoc], 14);
                mapMarkerDoc.setLatLng([currentLatDoc, currentLngDoc]);
            }
        });

        btnApplyPickedCoordsDoc.addEventListener('click', function() {
            latDocInput.value = currentLatDoc.toFixed(6);
            lngDocInput.value = currentLngDoc.toFixed(6);
            bsMapDocModal.hide();
        });
    }

    // ── 12B. AUTO-GPS GEOLOCATION FOR DOKUMENTASI MODAL ──
    const docModalEl = document.getElementById('uploadDokumentasiModal');
    const docGpsBadge = document.getElementById('docGpsStatusBadge');
    const docGpsText = document.getElementById('docGpsStatusText');

    function lockDocGps(lat, lng, accuracy = null) {
        if (latDocInput && lngDocInput) {
            latDocInput.value = parseFloat(lat).toFixed(6);
            lngDocInput.value = parseFloat(lng).toFixed(6);
        }
        if (docGpsBadge && docGpsText) {
            docGpsBadge.classList.remove('d-none');
            docGpsText.textContent = accuracy ? `GPS Terkunci (±${Math.round(accuracy)}m)` : 'GPS Terkunci';
        }
    }

    if (docModalEl) {
        docModalEl.addEventListener('shown.bs.modal', function() {
            if (latDocInput && !latDocInput.value) {
                if (_lastDetectedGps && (Date.now() - _lastDetectedGps.timestamp < 120000)) {
                    lockDocGps(_lastDetectedGps.latitude, _lastDetectedGps.longitude, _lastDetectedGps.accuracy);
                } else if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            _lastDetectedGps = {
                                latitude: pos.coords.latitude,
                                longitude: pos.coords.longitude,
                                accuracy: pos.coords.accuracy,
                                timestamp: Date.now()
                            };
                            lockDocGps(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
                        },
                        () => {},
                        { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
                    );
                }
            }
        });
    }

    // ── 12C. JOINT CLOSURE DYNAMIC LOGIC & MAP PICKER ──
    const capAsalSelect = document.getElementById('jc_kapasitas_asal');
    const tubeAsalInput = document.getElementById('jc_tube_asal');
    const capJumperSelect = document.getElementById('jc_kapasitas_jumper');
    const tubeJumperInput = document.getElementById('jc_tube_jumper');

    const defaultTubeMap = {
        '2': 1,
        '12': 1,
        '24': 2,
        '48': 4,
        '96': 8,
        '144': 12,
        '288': 24
    };

    capAsalSelect?.addEventListener('change', function() {
        if (tubeAsalInput && defaultTubeMap[this.value]) {
            tubeAsalInput.value = defaultTubeMap[this.value];
        }
    });

    capJumperSelect?.addEventListener('change', function() {
        if (tubeJumperInput && defaultTubeMap[this.value]) {
            tubeJumperInput.value = defaultTubeMap[this.value];
        }
    });

    // ══════════════════════════════════════════════════════════════════════
    // 12D. VISUAL INTERACTIVE FIBER CABLE PATCHER & MULTI-WIRE ENGINE
    // ══════════════════════════════════════════════════════════════════════
    const FIBER_COLORS = [
        { num: 1, name: 'Biru', hex: '#2563eb', bg: 'rgba(37, 99, 235, 0.25)', border: '#60a5fa' },
        { num: 2, name: 'Oranye', hex: '#ea580c', bg: 'rgba(234, 88, 12, 0.25)', border: '#fb923c' },
        { num: 3, name: 'Hijau', hex: '#16a34a', bg: 'rgba(22, 163, 74, 0.25)', border: '#4ade80' },
        { num: 4, name: 'Cokelat', hex: '#854d0e', bg: 'rgba(133, 77, 14, 0.25)', border: '#ca8a04' },
        { num: 5, name: 'Abu-abu', hex: '#64748b', bg: 'rgba(100, 116, 139, 0.25)', border: '#94a3b8' },
        { num: 6, name: 'Putih', hex: '#f8fafc', bg: 'rgba(248, 250, 252, 0.25)', border: '#cbd5e1' },
        { num: 7, name: 'Merah', hex: '#dc2626', bg: 'rgba(220, 38, 38, 0.25)', border: '#f87171' },
        { num: 8, name: 'Hitam', hex: '#0f172a', bg: 'rgba(30, 41, 59, 0.6)', border: '#64748b' },
        { num: 9, name: 'Kuning', hex: '#ca8a04', bg: 'rgba(202, 138, 4, 0.25)', border: '#fde047' },
        { num: 10, name: 'Ungu', hex: '#9333ea', bg: 'rgba(147, 51, 234, 0.25)', border: '#c084fc' },
        { num: 11, name: 'Pink', hex: '#db2777', bg: 'rgba(219, 39, 119, 0.25)', border: '#f472b6' },
        { num: 12, name: 'Toska', hex: '#0891b2', bg: 'rgba(8, 145, 178, 0.25)', border: '#22d3ee' }
    ];

    function getCapacityConfig(capacity) {
        const cap = parseInt(capacity) || 24;
        if (cap <= 2) return { tubes: 1, coresPerTube: 2 };
        if (cap <= 4) return { tubes: 1, coresPerTube: 4 };
        if (cap <= 6) return { tubes: 1, coresPerTube: 6 };
        if (cap <= 8) return { tubes: 1, coresPerTube: 8 };
        if (cap <= 12) return { tubes: 1, coresPerTube: 12 };
        const tubes = Math.ceil(cap / 12);
        return { tubes: tubes, coresPerTube: 12 };
    }

    function updateLaserCurve(svgPathEl, startDotEl, endDotEl, startCoreNum, endCoreNum, wireColor) {
        if (!svgPathEl) return;
        const total = 12;
        const y1 = Math.round(20 + ((startCoreNum - 1) / (total - 1)) * 160);
        const y2 = Math.round(20 + ((endCoreNum - 1) / (total - 1)) * 160);
        const d = `M 0 ${y1} C 65 ${y1}, 65 ${y2}, 130 ${y2}`;

        svgPathEl.setAttribute('d', d);
        svgPathEl.setAttribute('stroke', wireColor || '#38bdf8');
        if (startDotEl) {
            startDotEl.setAttribute('cy', y1);
            startDotEl.setAttribute('fill', wireColor || '#38bdf8');
        }
        if (endDotEl) {
            endDotEl.setAttribute('cy', y2);
            endDotEl.setAttribute('fill', wireColor || '#38bdf8');
        }
    }

    function renderFiberPortButtons(containerEl, currentTube, totalCoresOrSelected, selectedCoreOrConnected, connectedOrCallback, maybeCallback) {
        if (!containerEl) return;
        containerEl.innerHTML = '';

        let totalCores = 12;
        let selectedCoreNum = null;
        let connectedCoreNums = [];
        let onClickCallback = () => {};

        if (typeof totalCoresOrSelected === 'function') {
            onClickCallback = totalCoresOrSelected;
        } else if (typeof selectedCoreOrConnected === 'function') {
            selectedCoreNum = totalCoresOrSelected;
            onClickCallback = selectedCoreOrConnected;
        } else if (typeof connectedOrCallback === 'function') {
            totalCores = totalCoresOrSelected || 12;
            selectedCoreNum = selectedCoreOrConnected;
            onClickCallback = connectedOrCallback;
        } else {
            totalCores = totalCoresOrSelected || 12;
            selectedCoreNum = selectedCoreOrConnected;
            connectedCoreNums = Array.isArray(connectedOrCallback) ? connectedOrCallback : [];
            onClickCallback = maybeCallback || (() => {});
        }

        const limit = Math.min(12, totalCores || 12);

        for (let i = 0; i < limit; i++) {
            const c = FIBER_COLORS[i] || { num: i + 1, name: `Core ${i + 1}`, hex: '#64748b', border: '#94a3b8' };
            const isSelected = (c.num === selectedCoreNum);
            const isConnected = Array.isArray(connectedCoreNums) && connectedCoreNums.includes(c.num);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `fiber-port-btn ${isSelected ? 'selected' : ''} ${isConnected ? 'connected' : ''}`;
            btn.setAttribute('data-core', c.num);
            btn.setAttribute('data-tube', currentTube);
            btn.innerHTML = `
                <span class="fiber-dot" style="background-color: ${c.hex}; border: 1.5px solid ${c.border};"></span>
                <span class="fiber-port-label-num">C${c.num}</span>
                <span class="fiber-port-label-name text-truncate">${c.name}</span>
            `;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                onClickCallback(c.num, c);
            });
            containerEl.appendChild(btn);
        }
    }

    // ── A. MANUVER CORE MULTI-WIRE PATCHER CONTROLLER ──
    let manuverAsalCap = 24;
    let manuverTujuanCap = 24;
    let manuverAsalTube = 1;
    let manuverTujuanTube = 1;
    let manuverSelectedAsalCore = null; // Port clicked on Asal awaiting destination
    let manuverConnections = []; // Array of { id, asalTube, asalCore, tujuanTube, tujuanCore, color, asalName, tujuanName }

    const manuverAsalCapSelect = document.getElementById('manuverAsalCapacity');
    const manuverTujuanCapSelect = document.getElementById('manuverTujuanCapacity');
    const manuverAsalTubeTabs = document.getElementById('manuverAsalTubeTabs');
    const manuverTujuanTubeTabs = document.getElementById('manuverTujuanTubeTabs');
    const manuverAsalCoreList = document.getElementById('manuverAsalCoreList');
    const manuverTujuanCoreList = document.getElementById('manuverTujuanCoreList');
    const manuverSvgWiresGroup = document.getElementById('manuverSvgWiresGroup');
    const manuverLiveWireText = document.getElementById('manuverLiveWireText');
    const manuverWireCountBadge = document.getElementById('manuverWireCountBadge');
    const manuverTotalCoresText = document.getElementById('manuverTotalCoresText');
    const manuverConnectionsChips = document.getElementById('manuverConnectionsChips');
    const manuverHiddenInputsContainer = document.getElementById('manuverHiddenInputsContainer');

    function renderTubesForPatcher(tabsContainer, config, activeTube, onTubeSelect) {
        if (!tabsContainer) return;
        tabsContainer.innerHTML = '';
        for (let t = 1; t <= config.tubes; t++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `fiber-tube-tab-btn ${t === activeTube ? 'active' : ''}`;
            btn.setAttribute('data-tube', t);
            btn.textContent = `Tube ${t}`;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                tabsContainer.querySelectorAll('.fiber-tube-tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                onTubeSelect(t);
            });
            tabsContainer.appendChild(btn);
            if (t === activeTube) {
                setTimeout(() => {
                    try { btn.scrollIntoView({ block: 'nearest', inline: 'center' }); } catch(e) {}
                }, 20);
            }
        }
    }

    function syncManuverMultiWires() {
        if (!manuverSvgWiresGroup) return;
        manuverSvgWiresGroup.innerHTML = '';

        const asalConfig = getCapacityConfig(manuverAsalCap);
        const tujuanConfig = getCapacityConfig(manuverTujuanCap);
        const maxAsalCores = asalConfig.coresPerTube;
        const maxTujuanCores = tujuanConfig.coresPerTube;

        // Filter connections that are currently visible on active tubes or all active connections
        manuverConnections.forEach((conn, index) => {
            const isVisible = (conn.asalTube === manuverAsalTube && conn.tujuanTube === manuverTujuanTube);
            const isPartial = (conn.asalTube === manuverAsalTube || conn.tujuanTube === manuverTujuanTube);

            // Compute Y coordinates on 260px SVG canvas
            const y1 = maxAsalCores > 1 
                ? Math.round(20 + ((conn.asalCore - 1) / (maxAsalCores - 1)) * 220) 
                : 130;
            const y2 = maxTujuanCores > 1 
                ? Math.round(20 + ((conn.tujuanCore - 1) / (maxTujuanCores - 1)) * 220) 
                : 130;

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const d = `M 0 ${y1} C 70 ${y1}, 70 ${y2}, 140 ${y2}`;
            path.setAttribute('d', d);
            path.setAttribute('class', 'fiber-wire-path');
            path.setAttribute('stroke', conn.color || '#38bdf8');
            path.setAttribute('stroke-width', isVisible ? '2.5' : '1.4');
            path.setAttribute('stroke-opacity', isVisible ? '1' : (isPartial ? '0.45' : '0.2'));
            path.setAttribute('fill', 'none');
            path.setAttribute('data-index', index);

            const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
            title.textContent = `T${conn.asalTube} C${conn.asalCore} (${conn.asalName}) ➔ T${conn.tujuanTube} C${conn.tujuanCore} (${conn.tujuanName})`;
            path.appendChild(title);
            manuverSvgWiresGroup.appendChild(path);

            if (isVisible) {
                const dot1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot1.setAttribute('cx', '2');
                dot1.setAttribute('cy', y1);
                dot1.setAttribute('r', '3.5');
                dot1.setAttribute('fill', conn.color || '#38bdf8');
                dot1.setAttribute('stroke', '#ffffff');
                dot1.setAttribute('stroke-width', '1');
                manuverSvgWiresGroup.appendChild(dot1);

                const dot2 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot2.setAttribute('cx', '118');
                dot2.setAttribute('cy', y2);
                dot2.setAttribute('r', '3.5');
                dot2.setAttribute('fill', conn.color || '#38bdf8');
                dot2.setAttribute('stroke', '#ffffff');
                dot2.setAttribute('stroke-width', '1');
                manuverSvgWiresGroup.appendChild(dot2);
            }
        });

        // Update chips list
        if (manuverConnectionsChips) {
            manuverConnectionsChips.innerHTML = '';
            if (manuverConnections.length === 0) {
                manuverConnectionsChips.innerHTML = '<span class="text-muted small fst-italic py-1" style="font-size:0.72rem;">Belum ada core yang disambungkan. Klik port asal lalu tujuan.</span>';
            } else {
                manuverConnections.forEach((conn, idx) => {
                    const chip = document.createElement('div');
                    chip.className = 'fiber-conn-chip';
                    chip.innerHTML = `
                        <span class="fiber-dot-sm" style="background-color: ${conn.color};"></span>
                        <span>T${conn.asalTube}C${conn.asalCore} <span class="text-muted">(${conn.asalName})</span> &rarr; T${conn.tujuanTube}C${conn.tujuanCore} <span class="text-muted">(${conn.tujuanName})</span></span>
                        <button type="button" class="fiber-conn-chip-del" data-index="${idx}" title="Putuskan sambungan ini">&times;</button>
                    `;
                    chip.querySelector('.fiber-conn-chip-del').addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeManuverConnection(idx);
                    });
                    manuverConnectionsChips.appendChild(chip);
                });
            }
        }

        // Update counts & status
        const total = manuverConnections.length;
        if (manuverWireCountBadge) {
            manuverWireCountBadge.innerHTML = `<i class="bi bi-bezier2 me-1"></i> ${total} Sambungan Aktif`;
        }
        if (manuverTotalCoresText) {
            manuverTotalCoresText.textContent = `${total} Core Terhubung`;
        }

        // Synchronize hidden inputs for backend submission
        if (manuverHiddenInputsContainer) {
            manuverHiddenInputsContainer.innerHTML = '';
            if (total === 0) {
                // Default fallback
                manuverHiddenInputsContainer.innerHTML = `
                    <input type="hidden" id="core_asal" name="core_asal[]" value="Tube ${manuverAsalTube} Core 1">
                    <input type="hidden" id="core_tujuan" name="core_tujuan[]" value="Tube ${manuverTujuanTube} Core 1">
                `;
            } else {
                manuverConnections.forEach((conn, i) => {
                    const asalVal = `Tube ${conn.asalTube} Core ${conn.asalCore}`;
                    const tujuanVal = `Tube ${conn.tujuanTube} Core ${conn.tujuanCore}`;
                    manuverHiddenInputsContainer.insertAdjacentHTML('beforeend', `
                        <input type="hidden" ${i === 0 ? 'id="core_asal"' : ''} name="core_asal[]" value="${asalVal}">
                        <input type="hidden" ${i === 0 ? 'id="core_tujuan"' : ''} name="core_tujuan[]" value="${tujuanVal}">
                    `);
                });
            }
        }

        // Re-render ports to show active connected checkmarks
        renderAsalPorts();
        renderTujuanPorts();
    }

    function removeManuverConnection(index) {
        manuverConnections.splice(index, 1);
        syncManuverMultiWires();
    }

    function renderAsalPorts() {
        const asalConfig = getCapacityConfig(manuverAsalCap);
        const connectedCoresInActiveTube = manuverConnections
            .filter(c => c.asalTube === manuverAsalTube)
            .map(c => c.asalCore);

        renderFiberPortButtons(
            manuverAsalCoreList,
            manuverAsalTube,
            asalConfig.coresPerTube,
            manuverSelectedAsalCore?.tube === manuverAsalTube ? manuverSelectedAsalCore.core : null,
            connectedCoresInActiveTube,
            (coreNum, colorObj) => {
                manuverSelectedAsalCore = { tube: manuverAsalTube, core: coreNum, color: colorObj };
                if (manuverLiveWireText) {
                    manuverLiveWireText.innerHTML = `Asal: <strong style="color:${colorObj.hex};">Tube ${manuverAsalTube} Core ${coreNum} (${colorObj.name})</strong> &rarr; <span class="text-warning">Pilih Port Tujuan di sebelah kanan...</span>`;
                }
                renderAsalPorts();
            }
        );
    }

    function renderTujuanPorts() {
        const tujuanConfig = getCapacityConfig(manuverTujuanCap);
        const connectedCoresInActiveTube = manuverConnections
            .filter(c => c.tujuanTube === manuverTujuanTube)
            .map(c => c.tujuanCore);

        renderFiberPortButtons(
            manuverTujuanCoreList,
            manuverTujuanTube,
            tujuanConfig.coresPerTube,
            null,
            connectedCoresInActiveTube,
            (coreNum, colorObj) => {
                if (!manuverSelectedAsalCore) {
                    if (manuverLiveWireText) {
                        manuverLiveWireText.innerHTML = `<span class="text-warning">Silakan klik port Asal (kiri) terlebih dahulu!</span>`;
                    }
                    return;
                }

                // Check if connection for this specific asal port already exists; if so, replace it
                const existingIdx = manuverConnections.findIndex(
                    c => c.asalTube === manuverSelectedAsalCore.tube && c.asalCore === manuverSelectedAsalCore.core
                );

                const newConn = {
                    id: Date.now() + Math.random(),
                    asalTube: manuverSelectedAsalCore.tube,
                    asalCore: manuverSelectedAsalCore.core,
                    asalName: manuverSelectedAsalCore.color.name,
                    tujuanTube: manuverTujuanTube,
                    tujuanCore: coreNum,
                    tujuanName: colorObj.name,
                    color: manuverSelectedAsalCore.color.hex
                };

                if (existingIdx >= 0) {
                    manuverConnections[existingIdx] = newConn;
                } else {
                    manuverConnections.push(newConn);
                }

                if (manuverLiveWireText) {
                    manuverLiveWireText.innerHTML = `Tersambung: <strong style="color:${newConn.color};">T${newConn.asalTube} C${newConn.asalCore}</strong> &rarr; <strong style="color:${colorObj.hex};">T${newConn.tujuanTube} C${newConn.tujuanCore}</strong>`;
                }

                // Auto advance to next core for quick patching
                const asalConfig = getCapacityConfig(manuverAsalCap);
                if (manuverSelectedAsalCore.core < asalConfig.coresPerTube) {
                    const nextCoreNum = manuverSelectedAsalCore.core + 1;
                    const nextColor = FIBER_COLORS[nextCoreNum - 1] || FIBER_COLORS[0];
                    manuverSelectedAsalCore = { tube: manuverAsalTube, core: nextCoreNum, color: nextColor };
                } else {
                    manuverSelectedAsalCore = null;
                }

                syncManuverMultiWires();
            }
        );
    }

    function initManuverPatcher() {
        if (!manuverAsalCoreList || !manuverTujuanCoreList) return;

        // Capacity Change Listeners
        manuverAsalCapSelect?.addEventListener('change', function() {
            manuverAsalCap = parseInt(this.value) || 24;
            manuverAsalTube = 1;
            manuverSelectedAsalCore = null;
            const config = getCapacityConfig(manuverAsalCap);
            renderTubesForPatcher(manuverAsalTubeTabs, config, manuverAsalTube, (t) => {
                manuverAsalTube = t;
                renderAsalPorts();
                syncManuverMultiWires();
            });
            renderAsalPorts();
            syncManuverMultiWires();
        });

        manuverTujuanCapSelect?.addEventListener('change', function() {
            manuverTujuanCap = parseInt(this.value) || 24;
            manuverTujuanTube = 1;
            const config = getCapacityConfig(manuverTujuanCap);
            renderTubesForPatcher(manuverTujuanTubeTabs, config, manuverTujuanTube, (t) => {
                manuverTujuanTube = t;
                renderTujuanPorts();
                syncManuverMultiWires();
            });
            renderTujuanPorts();
            syncManuverMultiWires();
        });

        // Initialize default tube tabs
        const asalConfig = getCapacityConfig(manuverAsalCap);
        renderTubesForPatcher(manuverAsalTubeTabs, asalConfig, manuverAsalTube, (t) => {
            manuverAsalTube = t;
            renderAsalPorts();
            syncManuverMultiWires();
        });

        const tujuanConfig = getCapacityConfig(manuverTujuanCap);
        renderTubesForPatcher(manuverTujuanTubeTabs, tujuanConfig, manuverTujuanTube, (t) => {
            manuverTujuanTube = t;
            renderTujuanPorts();
            syncManuverMultiWires();
        });

        // Presets: Sambung Lurus 1:1 on active tube
        document.getElementById('btnManuverStraightPreset')?.addEventListener('click', function() {
            const count = Math.min(asalConfig.coresPerTube, tujuanConfig.coresPerTube);
            for (let i = 1; i <= count; i++) {
                const color = FIBER_COLORS[i - 1] || FIBER_COLORS[0];
                const existingIdx = manuverConnections.findIndex(c => c.asalTube === manuverAsalTube && c.asalCore === i);
                const item = {
                    id: Date.now() + i,
                    asalTube: manuverAsalTube,
                    asalCore: i,
                    asalName: color.name,
                    tujuanTube: manuverTujuanTube,
                    tujuanCore: i,
                    tujuanName: color.name,
                    color: color.hex
                };
                if (existingIdx >= 0) {
                    manuverConnections[existingIdx] = item;
                } else {
                    manuverConnections.push(item);
                }
            }
            manuverSelectedAsalCore = null;
            syncManuverMultiWires();
        });

        // Presets: Swap Tube 1 -> Tube 2
        document.getElementById('btnManuverSwapPreset')?.addEventListener('click', function() {
            manuverAsalTube = 1;
            manuverTujuanTube = Math.min(2, tujuanConfig.tubes);
            const count = Math.min(asalConfig.coresPerTube, tujuanConfig.coresPerTube);
            for (let i = 1; i <= count; i++) {
                const color = FIBER_COLORS[i - 1] || FIBER_COLORS[0];
                const existingIdx = manuverConnections.findIndex(c => c.asalTube === 1 && c.asalCore === i);
                const item = {
                    id: Date.now() + i,
                    asalTube: 1,
                    asalCore: i,
                    asalName: color.name,
                    tujuanTube: manuverTujuanTube,
                    tujuanCore: i,
                    tujuanName: color.name,
                    color: color.hex
                };
                if (existingIdx >= 0) {
                    manuverConnections[existingIdx] = item;
                } else {
                    manuverConnections.push(item);
                }
            }
            renderTubesForPatcher(manuverAsalTubeTabs, asalConfig, 1, (t) => { manuverAsalTube = t; renderAsalPorts(); syncManuverMultiWires(); });
            renderTubesForPatcher(manuverTujuanTubeTabs, tujuanConfig, manuverTujuanTube, (t) => { manuverTujuanTube = t; renderTujuanPorts(); syncManuverMultiWires(); });
            manuverSelectedAsalCore = null;
            syncManuverMultiWires();
        });

        // Presets: Clear All
        document.getElementById('btnManuverClearPreset')?.addEventListener('click', function() {
            manuverConnections = [];
            manuverSelectedAsalCore = null;
            if (manuverLiveWireText) {
                manuverLiveWireText.innerHTML = `Klik port Asal &rarr; klik port Tujuan untuk menambah sambungan`;
            }
            syncManuverMultiWires();
        });

        // Initial default connection (T1 C1 -> T1 C1) so form starts with a ready pair
        manuverConnections.push({
            id: Date.now(),
            asalTube: 1,
            asalCore: 1,
            asalName: 'Biru',
            tujuanTube: 1,
            tujuanCore: 1,
            tujuanName: 'Biru',
            color: FIBER_COLORS[0].hex
        });

        syncManuverMultiWires();
    }

    initManuverPatcher();

    // ── B. JOINT CLOSURE SPLICING TRAY CONTROLLER (MULTI-CORE / MULTI-PORT) ──
    let jcAsalCap = 24;
    let jcJumperCap = 24;
    let jcAsalTube = 1;
    let jcJumperTube = 1;
    let jcSelectedAsalCore = null; // Port clicked on Asal awaiting jumper destination
    let jcConnections = []; // Array of { id, asalTube, asalCore, asalName, jumperTube, jumperCore, jumperName, color, status, loss }

    const jcAsalCapSelect = document.getElementById('jcAsalCapacity');
    const jcJumperCapSelect = document.getElementById('jcJumperCapacity');
    const jc_kapasitas_asal = document.getElementById('jc_kapasitas_asal');
    const jc_kapasitas_jumper = document.getElementById('jc_kapasitas_jumper');
    const jc_tube_asal = document.getElementById('jc_tube_asal');
    const jc_tube_jumper = document.getElementById('jc_tube_jumper');

    const jcAsalTubeTabs = document.getElementById('jcAsalTubeTabs');
    const jcJumperTubeTabs = document.getElementById('jcJumperTubeTabs');
    const jcAsalCoreList = document.getElementById('jcAsalCoreList');
    const jcJumperCoreList = document.getElementById('jcJumperCoreList');
    const jcSvgWiresGroup = document.getElementById('jcSvgWiresGroup');
    const jcLiveWireText = document.getElementById('jcLiveWireText');
    const jcWireCountBadge = document.getElementById('jcWireCountBadge');
    const jcTotalCoresText = document.getElementById('jcTotalCoresText');
    const jcConnectionsChips = document.getElementById('jcConnectionsChips');
    const jcCoreRowsContainer = document.getElementById('jcCoreRowsContainer');
    const btnAddCoreRow = document.getElementById('btnAddCoreRow');

    function syncJcMultiWires() {
        if (jcSvgWiresGroup) {
            jcSvgWiresGroup.innerHTML = '';
            const asalConfig = getCapacityConfig(jcAsalCap);
            const jumperConfig = getCapacityConfig(jcJumperCap);
            const maxAsalCores = asalConfig.coresPerTube;
            const maxJumperCores = jumperConfig.coresPerTube;

            jcConnections.forEach((conn, index) => {
                const isVisible = (conn.asalTube === jcAsalTube && conn.jumperTube === jcJumperTube);
                const isPartial = (conn.asalTube === jcAsalTube || conn.jumperTube === jcJumperTube);

                const y1 = maxAsalCores > 1 
                    ? Math.round(20 + ((conn.asalCore - 1) / (maxAsalCores - 1)) * 220) 
                    : 130;
                const y2 = maxJumperCores > 1 
                    ? Math.round(20 + ((conn.jumperCore - 1) / (maxJumperCores - 1)) * 220) 
                    : 130;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                const d = `M 0 ${y1} C 70 ${y1}, 70 ${y2}, 140 ${y2}`;
                path.setAttribute('d', d);
                path.setAttribute('class', 'fiber-wire-path');
                path.setAttribute('stroke', conn.color || '#10b981');
                path.setAttribute('stroke-width', isVisible ? '2.5' : '1.4');
                path.setAttribute('stroke-opacity', isVisible ? '1' : (isPartial ? '0.45' : '0.2'));
                path.setAttribute('fill', 'none');
                path.setAttribute('data-index', index);

                const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
                title.textContent = `T${conn.asalTube} C${conn.asalCore} (${conn.asalName}) ➔ T${conn.jumperTube} C${conn.jumperCore} (${conn.jumperName})`;
                path.appendChild(title);
                jcSvgWiresGroup.appendChild(path);

                if (isVisible) {
                    const dot1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    dot1.setAttribute('cx', '2');
                    dot1.setAttribute('cy', y1);
                    dot1.setAttribute('r', '3.5');
                    dot1.setAttribute('fill', conn.color || '#10b981');
                    dot1.setAttribute('stroke', '#ffffff');
                    dot1.setAttribute('stroke-width', '1');
                    jcSvgWiresGroup.appendChild(dot1);

                    const dot2 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    dot2.setAttribute('cx', '118');
                    dot2.setAttribute('cy', y2);
                    dot2.setAttribute('r', '3.5');
                    dot2.setAttribute('fill', conn.color || '#10b981');
                    dot2.setAttribute('stroke', '#ffffff');
                    dot2.setAttribute('stroke-width', '1');
                    jcSvgWiresGroup.appendChild(dot2);
                }
            });
        }

        // Update chips list
        if (jcConnectionsChips) {
            jcConnectionsChips.innerHTML = '';
            if (jcConnections.length === 0) {
                jcConnectionsChips.innerHTML = '<span class="text-muted small fst-italic py-1" style="font-size:0.72rem;">Belum ada core yang disambungkan. Tap port Asal lalu Jumper.</span>';
            } else {
                jcConnections.forEach((conn, idx) => {
                    const chip = document.createElement('div');
                    chip.className = 'fiber-conn-chip';
                    chip.innerHTML = `
                        <span class="fiber-dot-sm" style="background-color: ${conn.color};"></span>
                        <span>T${conn.asalTube}C${conn.asalCore} <span class="text-muted">(${conn.asalName})</span> &rarr; T${conn.jumperTube}C${conn.jumperCore} <span class="text-muted">(${conn.jumperName})</span></span>
                        <button type="button" class="fiber-conn-chip-del" data-index="${idx}" title="Putuskan sambungan ini">&times;</button>
                    `;
                    chip.querySelector('.fiber-conn-chip-del').addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeJcConnection(idx);
                    });
                    jcConnectionsChips.appendChild(chip);
                });
            }
        }

        // Update counts & status
        const total = jcConnections.length;
        if (jcWireCountBadge) {
            jcWireCountBadge.innerHTML = `<i class="bi bi-bezier2 me-1"></i> ${total} Sambungan Aktif`;
        }
        if (jcTotalCoresText) {
            jcTotalCoresText.textContent = `${total} Core Terhubung`;
        }

        // Synchronize table input rows
        syncJcTableRows();

        // Re-render ports
        renderJcAsalPorts();
        renderJcJumperPorts();
    }

    function syncJcTableRows() {
        if (!jcCoreRowsContainer) return;
        jcCoreRowsContainer.innerHTML = '';

        if (jcConnections.length === 0) {
            // Default template row
            const newRow = document.createElement('div');
            newRow.className = 'jc-core-input-row p-2 bg-white rounded-3 border shadow-xs';
            newRow.innerHTML = `
                <div class="row g-1.5 align-items-center">
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Asal</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" placeholder="Tube 1" value="Tube 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Asal</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" placeholder="Core 1" value="Core 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Jumper</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" placeholder="Tube 1" value="Tube 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Jumper</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" placeholder="Core 1" value="Core 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Status</label>
                        <select class="form-select form-select-sm" name="core_status[]">
                            <option value="TERHUBUNG" selected>TERHUBUNG</option>
                            <option value="SPARE">SPARE (Sisa)</option>
                            <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                            <option value="MANUVER">MANUVER</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Loss (dB)</label>
                        <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" placeholder="0.02" value="0.02">
                    </div>
                    <div class="col-2 col-md-1 text-end pt-md-3">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 p-1 btn-remove-core-row" title="Hapus baris ini">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            `;
            jcCoreRowsContainer.appendChild(newRow);
        } else {
            jcConnections.forEach((conn, idx) => {
                const tAsal = `Tube ${conn.asalTube}`;
                const cAsal = `Core ${conn.asalCore} (${conn.asalName})`;
                const tJumper = `Tube ${conn.jumperTube}`;
                const cJumper = `Core ${conn.jumperCore} (${conn.jumperName})`;
                const status = conn.status || 'TERHUBUNG';
                const loss = conn.loss !== undefined ? conn.loss : '0.02';

                const newRow = document.createElement('div');
                newRow.className = 'jc-core-input-row p-2 bg-white rounded-3 border shadow-xs';
                newRow.innerHTML = `
                    <div class="row g-1.5 align-items-center">
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Asal</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" value="${tAsal}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Asal</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" value="${cAsal}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" value="${tJumper}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" value="${cJumper}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Status</label>
                            <select class="form-select form-select-sm" name="core_status[]">
                                <option value="TERHUBUNG" ${status === 'TERHUBUNG' ? 'selected' : ''}>TERHUBUNG</option>
                                <option value="SPARE" ${status === 'SPARE' ? 'selected' : ''}>SPARE (Sisa)</option>
                                <option value="LOSS_PUTUS" ${status === 'LOSS_PUTUS' ? 'selected' : ''}>LOSS / PUTUS</option>
                                <option value="MANUVER" ${status === 'MANUVER' ? 'selected' : ''}>MANUVER</option>
                            </select>
                        </div>
                        <div class="col-4 col-md-1">
                            <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Loss (dB)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" value="${loss}">
                        </div>
                        <div class="col-2 col-md-1 text-end pt-md-3">
                            <button type="button" class="btn btn-sm btn-outline-danger w-100 p-1 btn-remove-core-row" data-index="${idx}" title="Hapus baris ini">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                `;
                jcCoreRowsContainer.appendChild(newRow);
            });
        }
    }

    function removeJcConnection(index) {
        jcConnections.splice(index, 1);
        syncJcMultiWires();
    }

    function toggleStraightConnection(coreNum, colorObj) {
        const existingIdx = jcConnections.findIndex(
            c => c.asalTube === jcAsalTube && c.asalCore === coreNum && c.jumperTube === jcJumperTube && c.jumperCore === coreNum
        );

        if (existingIdx >= 0) {
            jcConnections.splice(existingIdx, 1);
            if (jcLiveWireText) {
                jcLiveWireText.innerHTML = `Sambungan Lurus diputuskan: <strong style="color:${colorObj.hex};">Tube ${jcAsalTube} Core ${coreNum} (${colorObj.name})</strong>`;
            }
        } else {
            // Add straight 1:1 connection
            const newConn = {
                id: Date.now() + Math.random(),
                asalTube: jcAsalTube,
                asalCore: coreNum,
                asalName: colorObj.name,
                jumperTube: jcJumperTube,
                jumperCore: coreNum,
                jumperName: colorObj.name,
                color: colorObj.hex,
                status: 'TERHUBUNG',
                loss: 0.02
            };
            jcConnections.push(newConn);
            if (jcLiveWireText) {
                jcLiveWireText.innerHTML = `Tersambung Lurus (1:1): <strong style="color:${colorObj.hex};">Tube ${jcAsalTube} Core ${coreNum} (${colorObj.name})</strong> &rarr; <strong style="color:${colorObj.hex};">Tube ${jcJumperTube} Core ${coreNum} (${colorObj.name})</strong>`;
            }
        }
        syncJcMultiWires();
    }

    function renderJcAsalPorts() {
        if (!jcAsalCoreList) return;
        const asalConfig = getCapacityConfig(jcAsalCap);
        const connectedCoresInActiveTube = jcConnections
            .filter(c => c.asalTube === jcAsalTube && c.jumperTube === jcJumperTube && c.asalCore === c.jumperCore)
            .map(c => c.asalCore);

        renderFiberPortButtons(
            jcAsalCoreList,
            jcAsalTube,
            asalConfig.coresPerTube,
            null,
            connectedCoresInActiveTube,
            (coreNum, colorObj) => {
                toggleStraightConnection(coreNum, colorObj);
            }
        );
    }

    function renderJcJumperPorts() {
        if (!jcJumperCoreList) return;
        const jumperConfig = getCapacityConfig(jcJumperCap);
        const connectedCoresInActiveTube = jcConnections
            .filter(c => c.asalTube === jcAsalTube && c.jumperTube === jcJumperTube && c.asalCore === c.jumperCore)
            .map(c => c.jumperCore);

        renderFiberPortButtons(
            jcJumperCoreList,
            jcJumperTube,
            jumperConfig.coresPerTube,
            null,
            connectedCoresInActiveTube,
            (coreNum, colorObj) => {
                toggleStraightConnection(coreNum, colorObj);
            }
        );
    }

    function initJcPatcher() {
        if (!jcAsalCoreList || !jcJumperCoreList) return;

        // Capacity Change Listeners
        function updateAsalCapacity(val) {
            jcAsalCap = parseInt(val) || 24;
            if (jcAsalCapSelect && jcAsalCapSelect.value != jcAsalCap) jcAsalCapSelect.value = jcAsalCap;
            if (jc_kapasitas_asal && jc_kapasitas_asal.value != jcAsalCap) jc_kapasitas_asal.value = jcAsalCap;
            
            jcAsalTube = 1;
            jcSelectedAsalCore = null;
            const config = getCapacityConfig(jcAsalCap);
            if (jc_tube_asal) jc_tube_asal.value = config.tubes;

            renderTubesForPatcher(jcAsalTubeTabs, config, jcAsalTube, (t) => {
                jcAsalTube = t;
                renderJcAsalPorts();
                syncJcMultiWires();
            });
            renderJcAsalPorts();
            syncJcMultiWires();
        }

        function updateJumperCapacity(val) {
            jcJumperCap = parseInt(val) || 24;
            if (jcJumperCapSelect && jcJumperCapSelect.value != jcJumperCap) jcJumperCapSelect.value = jcJumperCap;
            if (jc_kapasitas_jumper && jc_kapasitas_jumper.value != jcJumperCap) jc_kapasitas_jumper.value = jcJumperCap;
            
            jcJumperTube = 1;
            const config = getCapacityConfig(jcJumperCap);
            if (jc_tube_jumper) jc_tube_jumper.value = config.tubes;

            renderTubesForPatcher(jcJumperTubeTabs, config, jcJumperTube, (t) => {
                jcJumperTube = t;
                renderJcJumperPorts();
                syncJcMultiWires();
            });
            renderJcJumperPorts();
            syncJcMultiWires();
        }

        jcAsalCapSelect?.addEventListener('change', function() { updateAsalCapacity(this.value); });
        jc_kapasitas_asal?.addEventListener('change', function() { updateAsalCapacity(this.value); });

        jcJumperCapSelect?.addEventListener('change', function() { updateJumperCapacity(this.value); });
        jc_kapasitas_jumper?.addEventListener('change', function() { updateJumperCapacity(this.value); });

        // Initialize default tube tabs
        const asalConfig = getCapacityConfig(jcAsalCap);
        renderTubesForPatcher(jcAsalTubeTabs, asalConfig, jcAsalTube, (t) => {
            jcAsalTube = t;
            renderJcAsalPorts();
            syncJcMultiWires();
        });

        const jumperConfig = getCapacityConfig(jcJumperCap);
        renderTubesForPatcher(jcJumperTubeTabs, jumperConfig, jcJumperTube, (t) => {
            jcJumperTube = t;
            renderJcJumperPorts();
            syncJcMultiWires();
        });

        // Presets: Sambung Lurus 1:1 on active tube
        const applyStraightPreset = function() {
            const count = Math.min(asalConfig.coresPerTube, jumperConfig.coresPerTube);
            for (let i = 1; i <= count; i++) {
                const color = FIBER_COLORS[i - 1] || FIBER_COLORS[0];
                const existingIdx = jcConnections.findIndex(c => c.asalTube === jcAsalTube && c.asalCore === i);
                const item = {
                    id: Date.now() + i,
                    asalTube: jcAsalTube,
                    asalCore: i,
                    asalName: color.name,
                    jumperTube: jcJumperTube,
                    jumperCore: i,
                    jumperName: color.name,
                    color: color.hex,
                    status: 'TERHUBUNG',
                    loss: 0.02
                };
                if (existingIdx >= 0) {
                    jcConnections[existingIdx] = item;
                } else {
                    jcConnections.push(item);
                }
            }
            jcSelectedAsalCore = null;
            if (jcLiveWireText) {
                jcLiveWireText.innerHTML = `Auto-Splice 1:1 (Tube ${jcAsalTube} &rarr; Tube ${jcJumperTube}) berhasil disambungkan!`;
            }
            syncJcMultiWires();
        };

        document.getElementById('btnJcStraightPreset')?.addEventListener('click', applyStraightPreset);
        document.getElementById('btnJcAutoSpliceAll')?.addEventListener('click', applyStraightPreset);

        // Presets: Swap Tube 1 -> Tube 2
        document.getElementById('btnJcSwapPreset')?.addEventListener('click', function() {
            jcAsalTube = 1;
            jcJumperTube = Math.min(2, jumperConfig.tubes);
            const count = Math.min(asalConfig.coresPerTube, jumperConfig.coresPerTube);
            for (let i = 1; i <= count; i++) {
                const color = FIBER_COLORS[i - 1] || FIBER_COLORS[0];
                const existingIdx = jcConnections.findIndex(c => c.asalTube === 1 && c.asalCore === i);
                const item = {
                    id: Date.now() + i,
                    asalTube: 1,
                    asalCore: i,
                    asalName: color.name,
                    jumperTube: jcJumperTube,
                    jumperCore: i,
                    jumperName: color.name,
                    color: color.hex,
                    status: 'TERHUBUNG',
                    loss: 0.02
                };
                if (existingIdx >= 0) {
                    jcConnections[existingIdx] = item;
                } else {
                    jcConnections.push(item);
                }
            }
            renderTubesForPatcher(jcAsalTubeTabs, asalConfig, 1, (t) => { jcAsalTube = t; renderJcAsalPorts(); syncJcMultiWires(); });
            renderTubesForPatcher(jcJumperTubeTabs, jumperConfig, jcJumperTube, (t) => { jcJumperTube = t; renderJcJumperPorts(); syncJcMultiWires(); });
            jcSelectedAsalCore = null;
            if (jcLiveWireText) {
                jcLiveWireText.innerHTML = `Swap Tube 1 &rarr; Tube ${jcJumperTube} berhasil diterapkan!`;
            }
            syncJcMultiWires();
        });

        // Presets: Clear All
        const clearAllSplices = function() {
            jcConnections = [];
            jcSelectedAsalCore = null;
            if (jcLiveWireText) {
                jcLiveWireText.innerHTML = `Klik port Asal &rarr; klik port Jumper untuk menambah sambungan`;
            }
            syncJcMultiWires();
        };

        document.getElementById('btnJcClearPreset')?.addEventListener('click', clearAllSplices);
        document.getElementById('btnJcClearAllSplice')?.addEventListener('click', clearAllSplices);

        // Manual Row Add / Remove Listeners
        if (btnAddCoreRow) {
            btnAddCoreRow.addEventListener('click', function() {
                const nextNum = jcConnections.length + 1;
                const cIndex = ((nextNum - 1) % 12) + 1;
                const color = FIBER_COLORS[cIndex - 1] || FIBER_COLORS[0];
                const tNum = Math.ceil(nextNum / 12);

                jcConnections.push({
                    id: Date.now(),
                    asalTube: tNum,
                    asalCore: cIndex,
                    asalName: color.name,
                    jumperTube: tNum,
                    jumperCore: cIndex,
                    jumperName: color.name,
                    color: color.hex,
                    status: 'TERHUBUNG',
                    loss: 0.02
                });
                syncJcMultiWires();
            });
        }

        if (jcCoreRowsContainer) {
            jcCoreRowsContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.btn-remove-core-row');
                if (removeBtn) {
                    const idx = removeBtn.getAttribute('data-index');
                    if (idx !== null && idx !== undefined && jcConnections[idx]) {
                        removeJcConnection(parseInt(idx));
                    } else {
                        const row = removeBtn.closest('.jc-core-input-row');
                        if (row) {
                            if (jcCoreRowsContainer.querySelectorAll('.jc-core-input-row').length > 1) {
                                row.remove();
                            } else {
                                row.querySelectorAll('input').forEach(i => i.value = '');
                            }
                        }
                    }
                }
            });
        }

        // Initial default connection (T1 C1 -> T1 C1) so form starts with a ready pair
        jcConnections.push({
            id: Date.now(),
            asalTube: 1,
            asalCore: 1,
            asalName: 'Biru',
            jumperTube: 1,
            jumperCore: 1,
            jumperName: 'Biru',
            color: FIBER_COLORS[0].hex,
            status: 'TERHUBUNG',
            loss: 0.02
        });

        syncJcMultiWires();
    }

    initJcPatcher();

    // ── C. SINGLE CORE SPLICER CONTROLLER ──
    let singleAsalCap = 24;
    let singleJumperCap = 24;
    let singleAsalTube = 1;
    let singleAsalCore = 1;
    let singleJumperTube = 1;
    let singleJumperCore = 1;

    const singleAsalCapSelect = document.getElementById('singleAsalCapacity');
    const singleJumperCapSelect = document.getElementById('singleJumperCapacity');
    const singleAsalCoreList = document.getElementById('singleAsalCoreList');
    const singleJumperCoreList = document.getElementById('singleJumperCoreList');
    const singleAsalTubeTabs = document.getElementById('singleAsalTubeTabs');
    const singleJumperTubeTabs = document.getElementById('singleJumperTubeTabs');
    const single_tube_asal = document.getElementById('single_tube_asal');
    const single_core_asal = document.getElementById('single_core_asal');
    const single_tube_jumper = document.getElementById('single_tube_jumper');
    const single_core_jumper = document.getElementById('single_core_jumper');
    const singleSvgWirePath = document.getElementById('singleSvgWirePath');

    function updateSingleLaserWire() {
        if (!singleSvgWirePath) return;
        const asalConfig = getCapacityConfig(singleAsalCap);
        const jumperConfig = getCapacityConfig(singleJumperCap);
        const maxAsal = asalConfig.coresPerTube || 12;
        const maxJumper = jumperConfig.coresPerTube || 12;

        const y1 = maxAsal > 1 ? Math.round(15 + ((singleAsalCore - 1) / (maxAsal - 1)) * 190) : 110;
        const y2 = maxJumper > 1 ? Math.round(15 + ((singleJumperCore - 1) / (maxJumper - 1)) * 190) : 110;

        const color = FIBER_COLORS[singleAsalCore - 1] ? FIBER_COLORS[singleAsalCore - 1].hex : '#38bdf8';
        singleSvgWirePath.setAttribute('d', `M 0 ${y1} C 65 ${y1}, 65 ${y2}, 130 ${y2}`);
        singleSvgWirePath.setAttribute('stroke', color);
    }

    function renderSingleAsalPorts() {
        if (!singleAsalCoreList) return;
        const asalConfig = getCapacityConfig(singleAsalCap);
        renderFiberPortButtons(
            singleAsalCoreList,
            singleAsalTube,
            asalConfig.coresPerTube,
            singleAsalCore,
            null,
            (coreNum, colorObj) => {
                singleAsalCore = coreNum;
                // Di jointing lurus 1:1, memilih core pada asal otomatis menyelaraskan core jumper
                singleJumperCore = coreNum;
                syncSingleCorePatcher();
            }
        );
    }

    function renderSingleJumperPorts() {
        if (!singleJumperCoreList) return;
        const jumperConfig = getCapacityConfig(singleJumperCap);
        renderFiberPortButtons(
            singleJumperCoreList,
            singleJumperTube,
            jumperConfig.coresPerTube,
            singleJumperCore,
            null,
            (coreNum, colorObj) => {
                singleJumperCore = coreNum;
                syncSingleCorePatcher();
            }
        );
    }

    function syncSingleCorePatcher() {
        const asalColor = FIBER_COLORS.find(c => c.num === singleAsalCore) || FIBER_COLORS[0];
        const jumperColor = FIBER_COLORS.find(c => c.num === singleJumperCore) || FIBER_COLORS[0];

        if (single_tube_asal) single_tube_asal.value = `Tube ${singleAsalTube}`;
        if (single_core_asal) single_core_asal.value = `Core ${singleAsalCore} (${asalColor.name})`;
        if (single_tube_jumper) single_tube_jumper.value = `Tube ${singleJumperTube}`;
        if (single_core_jumper) single_core_jumper.value = `Core ${singleJumperCore} (${jumperColor.name})`;

        const singleAsalActiveBadge = document.getElementById('singleAsalActiveBadge');
        const singleJumperActiveBadge = document.getElementById('singleJumperActiveBadge');
        if (singleAsalActiveBadge) {
            singleAsalActiveBadge.textContent = `T${singleAsalTube} C${singleAsalCore} (${asalColor.name})`;
            singleAsalActiveBadge.style.backgroundColor = `${asalColor.hex}25`;
            singleAsalActiveBadge.style.color = asalColor.border || '#38bdf8';
        }
        if (singleJumperActiveBadge) {
            singleJumperActiveBadge.textContent = `T${singleJumperTube} C${singleJumperCore} (${jumperColor.name})`;
            singleJumperActiveBadge.style.backgroundColor = `${jumperColor.hex}25`;
            singleJumperActiveBadge.style.color = jumperColor.border || '#38bdf8';
        }

        renderSingleAsalPorts();
        renderSingleJumperPorts();
        updateSingleLaserWire();
    }

    function updateSingleAsalCapacity(val) {
        singleAsalCap = parseInt(val) || 24;
        if (singleAsalCapSelect && singleAsalCapSelect.value != singleAsalCap) singleAsalCapSelect.value = singleAsalCap;
        singleAsalTube = 1;
        const config = getCapacityConfig(singleAsalCap);
        if (singleAsalCore > config.coresPerTube) singleAsalCore = 1;

        renderTubesForPatcher(singleAsalTubeTabs, config, singleAsalTube, (t) => {
            singleAsalTube = t;
            syncSingleCorePatcher();
        });
        syncSingleCorePatcher();
    }

    function updateSingleJumperCapacity(val) {
        singleJumperCap = parseInt(val) || 24;
        if (singleJumperCapSelect && singleJumperCapSelect.value != singleJumperCap) singleJumperCapSelect.value = singleJumperCap;
        singleJumperTube = 1;
        const config = getCapacityConfig(singleJumperCap);
        if (singleJumperCore > config.coresPerTube) singleJumperCore = 1;

        renderTubesForPatcher(singleJumperTubeTabs, config, singleJumperTube, (t) => {
            singleJumperTube = t;
            syncSingleCorePatcher();
        });
        syncSingleCorePatcher();
    }

    function initSingleCorePatcher() {
        if (!singleAsalCoreList || !singleJumperCoreList) return;

        singleAsalCapSelect?.addEventListener('change', function() { updateSingleAsalCapacity(this.value); });
        singleJumperCapSelect?.addEventListener('change', function() { updateSingleJumperCapacity(this.value); });

        updateSingleAsalCapacity(singleAsalCap);
        updateSingleJumperCapacity(singleJumperCap);
    }

    initSingleCorePatcher();

    // Modal Add Single Core Trigger
    const formAddSingleCore = document.getElementById('formAddSingleCore');
    const modalCoreJcTitle = document.getElementById('modalCoreJcTitle');
    document.querySelectorAll('.open-add-core-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const jcId = this.getAttribute('data-jc-id');
            const jcName = this.getAttribute('data-jc-name');
            const capAsal = this.getAttribute('data-jc-cap-asal') || 24;
            const capJumper = this.getAttribute('data-jc-cap-jumper') || 24;

            if (formAddSingleCore) {
                formAddSingleCore.action = `/tiket/joint-closure/${jcId}/add-core`;
            }
            if (modalCoreJcTitle) {
                modalCoreJcTitle.textContent = jcName || 'JC';
            }

            singleAsalCap = parseInt(capAsal) || 24;
            singleJumperCap = parseInt(capJumper) || 24;
            if (singleAsalCapSelect) singleAsalCapSelect.value = singleAsalCap;
            if (singleJumperCapSelect) singleJumperCapSelect.value = singleJumperCap;
            updateSingleAsalCapacity(singleAsalCap);
            updateSingleJumperCapacity(singleJumperCap);
        });
    });

    const tambahJointClosureModal = document.getElementById('tambahJointClosureModal');
    if (tambahJointClosureModal) {
        tambahJointClosureModal.addEventListener('shown.bs.modal', function() {
            if (jc_kapasitas_asal) jcAsalCapSelect.value = jc_kapasitas_asal.value;
            if (jc_kapasitas_jumper) jcJumperCapSelect.value = jc_kapasitas_jumper.value;
        });
    }

    // Map Picker & GPS for Joint Closure Modal
    const btnGetLocationJc = document.getElementById('btnGetLocationJc');
    const latJcInput = document.getElementById('latitude_jc');
    const lngJcInput = document.getElementById('longitude_jc');
    const btnOpenMapPickerJc = document.getElementById('btnOpenMapPickerJc');
    const mapPickerJcModal = document.getElementById('mapPickerJcModal');
    const pickedCoordsJcText = document.getElementById('pickedCoordsJcText');
    const btnApplyPickedCoordsJc = document.getElementById('btnApplyPickedCoordsJc');

    let mapPickerJcInstance = null;
    let mapMarkerJc = null;
    let currentLatJc = -6.917464;
    let currentLngJc = 107.619123;

    if (btnGetLocationJc && latJcInput && lngJcInput) {
        btnGetLocationJc.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    latJcInput.value = pos.coords.latitude.toFixed(6);
                    lngJcInput.value = pos.coords.longitude.toFixed(6);
                },
                function(err) {
                    alert('Gagal mendeteksi lokasi GPS: ' + err.message);
                },
                { enableHighAccuracy: true }
            );
        });
    }

    if (btnOpenMapPickerJc && mapPickerJcModal) {
        const bsMapJcModal = new bootstrap.Modal(mapPickerJcModal);

        btnOpenMapPickerJc.addEventListener('click', function() {
            if (latJcInput.value && lngJcInput.value) {
                currentLatJc = parseFloat(latJcInput.value);
                currentLngJc = parseFloat(lngJcInput.value);
            }
            bsMapJcModal.show();
        });

        mapPickerJcModal.addEventListener('shown.bs.modal', function () {
            if (!mapPickerJcInstance) {
                mapPickerJcInstance = L.map('mapPickerJc').setView([currentLatJc, currentLngJc], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapPickerJcInstance);

                mapMarkerJc = L.marker([currentLatJc, currentLngJc], { draggable: true }).addTo(mapPickerJcInstance);

                mapMarkerJc.on('dragend', function () {
                    const pos = mapMarkerJc.getLatLng();
                    currentLatJc = pos.lat;
                    currentLngJc = pos.lng;
                    pickedCoordsJcText.textContent = `Koordinat: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                });

                mapPickerJcInstance.on('click', function (e) {
                    mapMarkerJc.setLatLng(e.latlng);
                    currentLatJc = e.latlng.lat;
                    currentLngJc = e.latlng.lng;
                    pickedCoordsJcText.textContent = `Koordinat: ${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
                });
            } else {
                mapPickerJcInstance.invalidateSize();
                mapPickerJcInstance.setView([currentLatJc, currentLngJc], 14);
                mapMarkerJc.setLatLng([currentLatJc, currentLngJc]);
            }
        });

        btnApplyPickedCoordsJc?.addEventListener('click', function() {
            latJcInput.value = currentLatJc.toFixed(6);
            lngJcInput.value = currentLngJc.toFixed(6);
            bsMapJcModal.hide();
        });
    }

    @if($tiket->isBackbone())
    // ── 12.5. SWITCH & TOGGLE METODE PENANGANAN (JOINTING / MANUVER / KEDUANYA) ──
    window.activeCurrentMode = @json($activePenanganan ?? 'JOINTING_LURUS');

    window.toggleKeduaPenanganan = function() {
        const current = window.activeCurrentMode || 'JOINTING_LURUS';
        const nextMode = (current === 'KEDUA' || current === 'KOMBINASI') ? 'JOINTING_LURUS' : 'KEDUA';
        window.switchPenangananMode(nextMode, true);
    };

    window.togglePenangananCard = function(clickedCard) {
        if (window.activeCurrentMode === 'KEDUA' || window.activeCurrentMode === 'KOMBINASI') {
            if (clickedCard === 'JOINTING_LURUS') {
                window.switchPenangananMode('MANUVER_CORE', true);
            } else {
                window.switchPenangananMode('JOINTING_LURUS', true);
            }
        } else if (window.activeCurrentMode === 'JOINTING_LURUS') {
            if (clickedCard === 'MANUVER_CORE') {
                window.switchPenangananMode('KEDUA', true);
            } else {
                window.switchPenangananMode('JOINTING_LURUS', true);
            }
        } else if (window.activeCurrentMode === 'MANUVER_CORE') {
            if (clickedCard === 'JOINTING_LURUS') {
                window.switchPenangananMode('KEDUA', true);
            } else {
                window.switchPenangananMode('MANUVER_CORE', true);
            }
        } else {
            window.switchPenangananMode(clickedCard, true);
        }
    };

    window.switchPenangananMode = function(mode, saveToServer = true) {
        window.activeCurrentMode = mode;
        const cardJointing = document.getElementById('cardModeJointing');
        const cardManuver = document.getElementById('cardModeManuver');
        const subJointing = document.getElementById('penangananSubViewJointing');
        const subManuver = document.getElementById('penangananSubViewManuver');
        const activeLabel = document.getElementById('penangananActiveLabel');
        const btnToggleKedua = document.getElementById('btnToggleKedua');

        if (!cardJointing || !cardManuver || !subJointing || !subManuver) return;

        const jointingCheck = cardJointing.querySelector('.penanganan-radio-check i');
        const manuverCheck = cardManuver.querySelector('.penanganan-radio-check i');

        if (mode === 'KEDUA' || mode === 'KOMBINASI' || mode === 'SEMUA') {
            cardJointing.classList.add('is-selected', 'active');
            cardManuver.classList.add('is-selected', 'active');
            if (jointingCheck) jointingCheck.className = 'bi bi-check-circle-fill text-indigo fs-5';
            if (manuverCheck) manuverCheck.className = 'bi bi-check-circle-fill text-purple fs-5';

            subJointing.classList.remove('d-none');
            subManuver.classList.remove('d-none');
            subManuver.classList.add('mt-4', 'pt-3', 'border-top');

            if (activeLabel) activeLabel.textContent = 'Jointing Lurus & Manuver Core (Keduanya)';
            if (btnToggleKedua) {
                btnToggleKedua.className = 'btn btn-xs rounded-pill px-2.5 py-1 fw-semibold btn-primary text-white shadow-xs';
                btnToggleKedua.innerHTML = '<i class="bi bi-check2-all me-1"></i> Keduanya Aktif';
            }
        } else if (mode === 'JOINTING_LURUS') {
            cardJointing.classList.add('is-selected', 'active');
            cardManuver.classList.remove('is-selected', 'active');
            if (jointingCheck) jointingCheck.className = 'bi bi-check-circle-fill text-indigo fs-5';
            if (manuverCheck) manuverCheck.className = 'bi bi-circle text-muted fs-5';

            subJointing.classList.remove('d-none');
            subManuver.classList.add('d-none');
            subManuver.classList.remove('mt-4', 'pt-3', 'border-top');

            if (activeLabel) activeLabel.textContent = 'Jointing Lurus (Kabel & JC)';
            if (btnToggleKedua) {
                btnToggleKedua.className = 'btn btn-xs rounded-pill px-2.5 py-1 fw-semibold btn-outline-primary';
                btnToggleKedua.innerHTML = '<i class="bi bi-layers-fill me-1"></i> Pilih Keduanya';
            }
        } else if (mode === 'MANUVER_CORE') {
            cardManuver.classList.add('is-selected', 'active');
            cardJointing.classList.remove('is-selected', 'active');
            if (jointingCheck) jointingCheck.className = 'bi bi-circle text-muted fs-5';
            if (manuverCheck) manuverCheck.className = 'bi bi-check-circle-fill text-purple fs-5';

            subManuver.classList.remove('d-none');
            subManuver.classList.remove('mt-4', 'pt-3', 'border-top');
            subJointing.classList.add('d-none');

            if (activeLabel) activeLabel.textContent = 'Manuver Core (Swapping Core)';
            if (btnToggleKedua) {
                btnToggleKedua.className = 'btn btn-xs rounded-pill px-2.5 py-1 fw-semibold btn-outline-primary';
                btnToggleKedua.innerHTML = '<i class="bi bi-layers-fill me-1"></i> Pilih Keduanya';
            }
        }

        if (saveToServer) {
            const updateUrl = @json(route('tiket.tipe-penanganan.update', $tiket->id));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ tipe_penanganan: mode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && typeof showToast === 'function') {
                    showToast('Sukses', 'Metode penanganan berhasil disimpan: ' + (data.label || mode), 'success');
                }
            })
            .catch(err => {
                console.error('Gagal update tipe penanganan:', err);
            });
        }
    }; {{-- end switchPenangananMode --}}
    @endif {{-- isBackbone: section 12.5 penanganan mode --}}

    // ── 13. URL PARAMETER & HASH ACTIVE TAB SWITCHER ──
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const hashParam = window.location.hash;

    if (tabParam) {
        if (tabParam === 'jointclosure') {
            const targetTabBtn = document.getElementById('penanganan-tab');
            if (targetTabBtn) new bootstrap.Tab(targetTabBtn).show();
            window.switchPenangananMode('JOINTING_LURUS', false);
        } else if (tabParam === 'manuver') {
            const targetTabBtn = document.getElementById('penanganan-tab');
            if (targetTabBtn) new bootstrap.Tab(targetTabBtn).show();
            window.switchPenangananMode('MANUVER_CORE', false);
        } else {
            const targetTabBtn = document.getElementById(tabParam + '-tab');
            if (targetTabBtn) {
                const bsTab = new bootstrap.Tab(targetTabBtn);
                bsTab.show();
            }
        }
    } else if (hashParam) {
        const cleanHash = hashParam.replace('#tab-', '').replace('#', '');
        if (cleanHash === 'jointclosure') {
            const targetTabBtn = document.getElementById('penanganan-tab');
            if (targetTabBtn) new bootstrap.Tab(targetTabBtn).show();
            window.switchPenangananMode('JOINTING_LURUS', false);
        } else if (cleanHash === 'manuver') {
            const targetTabBtn = document.getElementById('penanganan-tab');
            if (targetTabBtn) new bootstrap.Tab(targetTabBtn).show();
            window.switchPenangananMode('MANUVER_CORE', false);
        } else {
            const targetTabBtn = document.getElementById(cleanHash + '-tab');
            if (targetTabBtn) {
                const bsTab = new bootstrap.Tab(targetTabBtn);
                bsTab.show();
            }
        }
    }

    // ── 14. AUTO-SCROLL TO NEWLY SENT KRONOLOGIS CHAT BUBBLE ──
    const newKronoId = @json(session('new_krono_id'));
    const hasKronoSuccess = @json((bool) (session('success') && str_contains(session('success'), 'kronologis')));

    function scrollChatToBottom(instant = true) {
        const stream = document.getElementById('timelineList');
        if (!stream) return;
        if (instant) {
            stream.style.setProperty('scroll-behavior', 'auto', 'important');
            stream.scrollTop = stream.scrollHeight;
        } else {
            stream.scrollTo({
                top: stream.scrollHeight + 99999,
                behavior: 'smooth'
            });
            setTimeout(() => {
                checkStreamScroll(stream);
            }, 350);
        }
    }
    window.scrollChatToBottom = scrollChatToBottom;

    function alignChatToViewport(behavior = 'instant') {
        if (window.location.hash && window.location.hash !== '#kronologis-pane' && !window.location.hash.startsWith('#kronologis')) {
            return;
        }
        const kronoPane = document.getElementById('kronologis-pane');
        if (!kronoPane || !kronoPane.classList.contains('active')) return;

        const waContainer = document.querySelector('.wa-chat-container');
        if (!waContainer) return;

        const topbar = document.querySelector('.app-topbar');
        const topbarHeight = topbar ? topbar.offsetHeight : 58;

        const rect = waContainer.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const targetY = Math.max(0, rect.top + scrollTop - topbarHeight - 6);

        if (targetY > 0) {
            window.scrollTo({
                top: targetY,
                behavior: behavior
            });
        }
    }
    window.alignChatToViewport = alignChatToViewport;

    let userHasScrolledTimeline = false;

    function scrollToTargetKrono() {
        let targetEl = null;

        if (newKronoId) {
            targetEl = document.getElementById('krono-item-' + newKronoId);
        }

        if (!targetEl && window.location.hash && window.location.hash.startsWith('#krono-item-')) {
            try {
                targetEl = document.querySelector(window.location.hash);
            } catch (e) {}
        }

        const stream = document.getElementById('timelineList');
        if (!stream) return;

        const performInternalScroll = () => {
            if (targetEl) {
                const streamRect = stream.getBoundingClientRect();
                const targetRect = targetEl.getBoundingClientRect();
                const relativeTop = targetRect.top - streamRect.top + stream.scrollTop;
                stream.scrollTop = Math.max(0, relativeTop - (stream.clientHeight / 2) + (targetEl.clientHeight / 2));
            } else {
                scrollChatToBottom(true);
            }
        };

        // Jalankan scroll internal container secara instan tanpa delay
        performInternalScroll();
        requestAnimationFrame(performInternalScroll);

        // Pantau jika ada gambar atau video di dalam stream yang sedang dimuat
        const streamMedia = stream.querySelectorAll('img, video');
        streamMedia.forEach(media => {
            if (!media.complete) {
                media.addEventListener('load', () => { if (!targetEl && !userHasScrolledTimeline) scrollChatToBottom(true); }, { once: true });
                media.addEventListener('loadedmetadata', () => { if (!targetEl && !userHasScrolledTimeline) scrollChatToBottom(true); }, { once: true });
            }
        });
        window.addEventListener('load', () => { if (!targetEl && !userHasScrolledTimeline) scrollChatToBottom(true); }, { once: true });

        // Gunakan ResizeObserver agar saat rendering elemen selesai, chat stream selalu berada di posisi paling baru
        if (window.ResizeObserver) {
            let resizePasses = 0;
            const ro = new ResizeObserver(() => {
                if (!targetEl) {
                    if (!userHasScrolledTimeline || resizePasses < 4) {
                        scrollChatToBottom(true);
                        resizePasses++;
                    } else {
                        const distanceFromBottom = stream.scrollHeight - stream.scrollTop - stream.clientHeight;
                        if (distanceFromBottom < 180) {
                            scrollChatToBottom(true);
                        }
                    }
                }
            });
            ro.observe(stream);
        }
    }

    // Jalankan agar chat stream selalu siap di paling bawah secara instan
    scrollToTargetKrono();

    // Kunci posisi scroll halaman agar pas di kotak chat dan tidak melompat naik ke atas pada refresh
    if (!newKronoId && (!window.location.hash || window.location.hash === '#kronologis-pane' || window.location.hash === '')) {
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        alignChatToViewport('instant');
        requestAnimationFrame(() => alignChatToViewport('instant'));
        setTimeout(() => alignChatToViewport('instant'), 100);
        setTimeout(() => alignChatToViewport('instant'), 300);
        window.addEventListener('load', () => alignChatToViewport('instant'), { once: true });
    }

    // Listener saat tab Kronologis dibuka kembali dari tab lain (Bootstrap Tab)
    const kronologisTabBtn = document.getElementById('kronologis-tab');
    if (kronologisTabBtn) {
        // 'show.bs.tab' jalan seketika saat tombol tab diklik (sebelum transisi tab dimulai)
        kronologisTabBtn.addEventListener('show.bs.tab', function () {
            userHasScrolledTimeline = false;
            scrollChatToBottom(true);
            alignChatToViewport('instant');
        });
        // 'shown.bs.tab' jalan setelah transisi tab selesai
        kronologisTabBtn.addEventListener('shown.bs.tab', function () {
            userHasScrolledTimeline = false;
            scrollChatToBottom(true);
            alignChatToViewport('smooth');
        });
    }

    // Indikator loading saat submit form kronologis
    const formAddKronologis = document.getElementById('formAddKronologis');
    if (formAddKronologis) {
        formAddKronologis.addEventListener('submit', function() {
            const submitBtn = document.getElementById('btnSubmitKronologis');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...';
            }
        });
    }

    // ── 14B. MESSAGE ACTIONS: SALIN, BALAS, EDIT, HAPUS (3-DOTS MENU) ──
    let activeReplyData = null;
    const waReplyPreviewBar = document.getElementById('waReplyPreviewBar');
    const waReplySenderText = document.getElementById('waReplySenderText');
    const waReplySnippetText = document.getElementById('waReplySnippetText');
    const btnCancelWaReply = document.getElementById('btnCancelWaReply');

    function showCopyToast(msg = 'Pesan berhasil disalin!') {
        let toast = document.getElementById('waCopyToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'waCopyToast';
            toast.className = 'wa-copy-toast';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<i class="bi bi-clipboard-check-fill text-success fs-6"></i> <span>${msg}</span>`;
        toast.classList.add('show');
        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 2200);
    }

    function cancelReply() {
        activeReplyData = null;
        if (waReplyPreviewBar) waReplyPreviewBar.classList.add('d-none');
    }

    btnCancelWaReply?.addEventListener('click', cancelReply);

    function copyFallback(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showCopyToast('Pesan berhasil disalin!');
        } catch (err) {
            alert('Gagal menyalin pesan.');
        }
        document.body.removeChild(textarea);
    }

    // ── COPY WA BROADCAST TICKET INFO ──
    const btnCopyWaBroadcast = document.getElementById('btnCopyWaBroadcast');
    if (btnCopyWaBroadcast) {
        btnCopyWaBroadcast.addEventListener('click', function(e) {
            e.preventDefault();

            const noTiket = "{{ $tiket->no_tiket }}";
            const statusLink = "{{ $tiket->status_link_impact }}";
            const segment = "{{ $tiket->backbone_segment }}";
            const statusTiket = "{{ $tiket->status }}";
            const tglOpen = "{{ $tiket->tanggal_open ? $tiket->tanggal_open->translatedFormat('l, d F Y H:i') . ' WIB' : '-' }}";
            const targetSla = "{{ $tiket->sla_target_minutes ? round($tiket->sla_target_minutes / 60, 1) . ' Jam (' . $tiket->sla_target_minutes . ' menit)' : '-' }}";
            const deskripsi = {!! json_encode($tiket->deskripsi ?: '-') !!};
            const tiketUrl = "{{ route('tiket.show', $tiket->id) }}";

            const waText = 
`*NOTIFIKASI PENUGASAN TIKET GANGGUAN*
*PT MEDIA SOLUSI NETWORK*
─────────────────────────────
*No. Tiket* : ${noTiket}
*Segment* : ${segment}
*Dampak Link* : ${statusLink}
*Status Tiket* : ${statusTiket}
*Waktu Open* : ${tglOpen}
*Target SLA* : ${targetSla}
*Deskripsi* : ${deskripsi}
─────────────────────────────
*Tautan Detail & Update Tiket:*
${tiketUrl}

_Catatan: Mohon tim teknis terkait segera melakukan penanganan dan memperbarui laporan kronologis pekerjaan pada tautan di atas._`;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(waText).then(() => {
                    showCopyToast('Format notifikasi WhatsApp berhasil disalin!');
                }).catch(() => {
                    copyFallback(waText);
                });
            } else {
                copyFallback(waText);
            }
        });
    }

    // Delegated click handler for 3-dots dropdown options & load older messages
    document.addEventListener('click', function(e) {
        // 0. LOAD OLDER MESSAGES
        const loadOlderBtn = e.target.closest('#btnLoadOlderKrono');
        if (loadOlderBtn) {
            e.preventDefault();
            loadOlderMessages();
            return;
        }

        // 1. SALIN
        const copyBtn = e.target.closest('.btn-action-copy');
        if (copyBtn) {
            e.preventDefault();
            const text = copyBtn.getAttribute('data-text') || '';
            const cleanText = text.replace(/^>?\s*\[Membalas\s+([^\]]+)\]:\s*[^\n]+\n+/i, '');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(cleanText).then(() => {
                    showCopyToast('Pesan berhasil disalin!');
                }).catch(() => {
                    copyFallback(cleanText);
                });
            } else {
                copyFallback(cleanText);
            }
            return;
        }

        // 2. BALAS / REPLY
        const replyBtn = e.target.closest('.btn-action-reply');
        if (replyBtn) {
            e.preventDefault();
            const id = replyBtn.getAttribute('data-id');
            const sender = replyBtn.getAttribute('data-sender') || 'User';
            const rawText = replyBtn.getAttribute('data-text') || '';
            const cleanSnippet = rawText.replace(/^>?\s*\[Membalas\s+([^\]]+)\]:\s*[^\n]+\n+/i, '').replace(/\n/g, ' ').trim();

            activeReplyData = {
                id: id,
                sender: sender,
                text: cleanSnippet
            };

            if (waReplySenderText) waReplySenderText.textContent = 'Membalas ' + sender;
            if (waReplySnippetText) waReplySnippetText.textContent = cleanSnippet || 'Pesan lampiran/foto';
            if (waReplyPreviewBar) waReplyPreviewBar.classList.remove('d-none');

            const chatInput = document.getElementById('waChatTextInput');
            if (chatInput) {
                chatInput.focus();
                chatInput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            return;
        }

        // 3. EDIT
        const editBtn = e.target.closest('.btn-action-edit');
        if (editBtn) {
            e.preventDefault();
            const id = editBtn.getAttribute('data-id');
            const rawText = editBtn.getAttribute('data-text') || '';
            
            const editModalEl = document.getElementById('editKronoMsgModal');
            const editIdInput = document.getElementById('editKronoId');
            const editTextInput = document.getElementById('editKronoTextInput');

            if (editModalEl && editIdInput && editTextInput) {
                editIdInput.value = id;
                editTextInput.value = rawText;
                const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
                modalInstance.show();
            }
            return;
        }

        // 4. HAPUS
        const deleteBtn = e.target.closest('.btn-action-delete');
        if (deleteBtn) {
            e.preventDefault();
            const id = deleteBtn.getAttribute('data-id');
            const deleteModalEl = document.getElementById('deleteKronoMsgModal');
            const deleteIdInput = document.getElementById('deleteKronoId');

            if (deleteModalEl && deleteIdInput) {
                deleteIdInput.value = id;
                const modalInstance = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
                modalInstance.show();
            }
            return;
        }

        // 5. INFO PESAN (READ RECEIPTS / SEEN BY)
        const infoBtn = e.target.closest('.btn-action-msg-info');
        if (infoBtn) {
            e.preventDefault();
            const senderUserId = parseInt(infoBtn.getAttribute('data-user-id') || '0', 10);
            const sender = infoBtn.getAttribute('data-sender') || 'Pengirim';
            const senderRole = infoBtn.getAttribute('data-sender-role') || '-';
            const time = infoBtn.getAttribute('data-time') || '-';
            const rawText = infoBtn.getAttribute('data-text') || '';
            const photoUrl = infoBtn.getAttribute('data-photo') || '';
            const msgTimestamp = parseInt(infoBtn.getAttribute('data-timestamp') || '0', 10);

            const senderEl = document.getElementById('msgInfoSender');
            const timeEl = document.getElementById('msgInfoTime');
            const contentEl = document.getElementById('msgInfoContent');
            const photoContainer = document.getElementById('msgInfoPhotoContainer');
            const photoThumb = document.getElementById('msgInfoPhotoThumb');
            const readListEl = document.getElementById('msgInfoReadList');
            const readCountBadge = document.getElementById('msgInfoReadCountBadge');
            const deliveredTimeEl = document.getElementById('msgInfoDeliveredTime');

            if (senderEl) {
                senderEl.innerHTML = `${sender} <span class="badge bg-secondary-subtle text-secondary ms-1 fw-normal" style="font-size:0.7rem;">${senderRole}</span>`;
            }
            if (timeEl) timeEl.textContent = time;
            if (deliveredTimeEl) deliveredTimeEl.textContent = time + ' (Tersimpan)';

            if (contentEl) {
                if (rawText.trim()) {
                    contentEl.innerHTML = formatMessageWithMentions(rawText);
                    contentEl.classList.remove('d-none');
                } else if (photoUrl) {
                    contentEl.innerHTML = '<span class="fst-italic text-muted"><i class="bi bi-image me-1"></i> [Foto Lampiran]</span>';
                    contentEl.classList.remove('d-none');
                } else {
                    contentEl.classList.add('d-none');
                }
            }

            if (photoContainer && photoThumb) {
                if (photoUrl) {
                    photoThumb.src = photoUrl;
                    photoContainer.classList.remove('d-none');
                } else {
                    photoContainer.classList.add('d-none');
                }
            }

            // Render list pembaca (Hanya anggota tim lain, bukan diri sendiri)
            if (readListEl && readCountBadge) {
                readListEl.innerHTML = '';
                const viewsObj = window.TICKET_USER_VIEWS || {};
                const viewers = Object.values(viewsObj).filter(v => {
                    if (!v || !v.user_id) return false;
                    const vId = parseInt(v.user_id, 10);
                    // Filter: Jangan cantumkan akun yang sedang login dan jangan cantumkan si pembuat pesan
                    if (vId === currentUserId) return false;
                    if (senderUserId && vId === senderUserId) return false;

                    const viewedAt = parseInt(v.viewed_at || '0', 10);
                    return isTiketClosed || (viewedAt >= msgTimestamp);
                });

                readCountBadge.textContent = `${viewers.length} Anggota`;

                if (viewers.length === 0) {
                    readListEl.innerHTML = `
                        <div class="text-center py-3 text-muted" style="font-size:0.8rem;">
                            <i class="bi bi-clock-history d-block fs-4 text-secondary mb-1 opacity-50"></i>
                            Belum ada anggota tim lain yang membuka tiket ini setelah pesan terkirim.
                        </div>
                    `;
                } else {
                    viewers.forEach(v => {
                        const vName = v.name || 'User #' + v.user_id;
                        const vRole = (v.role || 'teknis').toUpperCase();
                        const vInitials = vName.substring(0, 2).toUpperCase();
                        const vColor = getSenderColor(vName);

                        let readTimeFormatted = 'Telah membaca tiket';
                        if (v.viewed_at) {
                            const dateObj = new Date(v.viewed_at * 1000);
                            const hours = String(dateObj.getHours()).padStart(2, '0');
                            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                            readTimeFormatted = `Dibaca pukul ${hours}:${minutes} WIB`;
                        }

                        const itemHtml = `
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border border-light-subtle">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-2xs" style="width:34px; height:34px; background-color:${vColor}; font-size:0.75rem;">
                                        ${v.avatar ? `<img src="${v.avatar}" class="w-100 h-100 rounded-circle" style="object-fit:cover;">` : vInitials}
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark d-flex align-items-center gap-1.5" style="font-size:0.82rem;">
                                            <span>${vName}</span>
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.65rem;">${vRole}</span>
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">${readTimeFormatted}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <i class="bi bi-check2-all text-info fs-5" title="Dibaca"></i>
                                </div>
                            </div>
                        `;
                        readListEl.insertAdjacentHTML('beforeend', itemHtml);
                    });
                }
            }

            const modalEl = document.getElementById('msgInfoModal');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
            return;
        }
    });

    // ── 5B. FORWARD PHOTO TO MANDATORY DOCUMENTATION HELPER ──
    window.forwardPhotoToDoc = function(photoUrl, lat, lng, timeStr, kategoriDefault) {
        const modalEl = document.getElementById('uploadDokumentasiModal');
        if (!modalEl) return;

        const photoInput = document.getElementById('photosInput');
        const sourcePhotoUrlInput = document.getElementById('docSourcePhotoUrl');
        const previewBox = document.getElementById('docSourcePhotoPreviewBox');
        const previewImg = document.getElementById('docSourcePhotoImg');
        const multiUploadWrapper = document.getElementById('docMultiUploadWrapper');
        const modalTitle = document.getElementById('uploadDokModalTitle');
        const latInput = document.getElementById('latitude_doc');
        const lngInput = document.getElementById('longitude_doc');
        const kategoriSelect = document.getElementById('doc_kategori');
        const timestampInput = document.getElementById('doc_timestamp');

        if (sourcePhotoUrlInput) sourcePhotoUrlInput.value = photoUrl;
        if (previewImg) previewImg.src = photoUrl;
        if (previewBox) previewBox.classList.remove('d-none');
        if (multiUploadWrapper) multiUploadWrapper.classList.add('d-none');
        if (photoInput) photoInput.removeAttribute('required');

        if (modalTitle) {
            modalTitle.innerHTML = '<i class="bi bi-folder-plus text-success me-2"></i>Simpan Foto Chat ke Dokumentasi Wajib';
        }

        if (lat && lng && latInput && lngInput) {
            latInput.value = parseFloat(lat).toFixed(6);
            lngInput.value = parseFloat(lng).toFixed(6);
        }

        if (kategoriDefault && kategoriSelect) {
            for (let i = 0; i < kategoriSelect.options.length; i++) {
                if (kategoriSelect.options[i].value === kategoriDefault) {
                    kategoriSelect.selectedIndex = i;
                    break;
                }
            }
        }

        if (timeStr && timestampInput) {
            timestampInput.value = timeStr;
        }

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    };

    // Reset Dokumentasi Modal state when modal is closed or cancelled
    function resetDocModalState() {
        const photoInput = document.getElementById('photosInput');
        const sourcePhotoUrlInput = document.getElementById('docSourcePhotoUrl');
        const previewBox = document.getElementById('docSourcePhotoPreviewBox');
        const multiUploadWrapper = document.getElementById('docMultiUploadWrapper');
        const modalTitle = document.getElementById('uploadDokModalTitle');

        if (sourcePhotoUrlInput) sourcePhotoUrlInput.value = '';
        if (previewBox) previewBox.classList.add('d-none');
        if (multiUploadWrapper) multiUploadWrapper.classList.remove('d-none');
        if (photoInput) photoInput.setAttribute('required', 'required');
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="bi bi-camera-fill text-info me-2"></i>Upload Foto Dokumentasi Lapangan';
        }
    }

    document.getElementById('btnCancelForwardDoc')?.addEventListener('click', function(e) {
        e.preventDefault();
        resetDocModalState();
    });

    document.getElementById('uploadDokumentasiModal')?.addEventListener('hidden.bs.modal', function() {
        resetDocModalState();
    });

    // Form Edit Message Modal Submit Handler
    const formEditKronoMsg = document.getElementById('formEditKronoMsg');
    const btnSaveEditKrono = document.getElementById('btnSaveEditKrono');
    formEditKronoMsg?.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editKronoId')?.value;
        const textVal = document.getElementById('editKronoTextInput')?.value.trim();
        if (!id || !textVal) return;

        if (btnSaveEditKrono) {
            btnSaveEditKrono.disabled = true;
            btnSaveEditKrono.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        }

        fetch(`${destroyUrlBase}/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ informasi: textVal })
        })
        .then(res => res.json())
        .then(res => {
            if (btnSaveEditKrono) {
                btnSaveEditKrono.disabled = false;
                btnSaveEditKrono.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Perubahan';
            }

            if (res.success) {
                const editModalEl = document.getElementById('editKronoMsgModal');
                if (editModalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(editModalEl);
                    modalInstance?.hide();
                }

                // Update text di DOM bubble
                const rowEl = document.getElementById('krono-item-' + id);
                if (rowEl) {
                    const textEl = rowEl.querySelector('.wa-msg-text');
                    if (textEl) {
                        textEl.innerHTML = formatMessageWithMentions(textVal);
                    }
                    rowEl.querySelectorAll('.btn-action-copy, .btn-action-reply, .btn-action-edit').forEach(b => {
                        b.setAttribute('data-text', textVal);
                    });
                    rowEl.classList.add('wa-bubble-new-highlight');
                    setTimeout(() => rowEl.classList.remove('wa-bubble-new-highlight'), 3000);
                }

                showCopyToast('Pesan berhasil diperbarui!');
            } else {
                alert('Gagal mengedit pesan: ' + (res.message || 'Terjadi kesalahan.'));
            }
        })
        .catch(err => {
            if (btnSaveEditKrono) {
                btnSaveEditKrono.disabled = false;
                btnSaveEditKrono.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Perubahan';
            }
            alert('Gagal mengedit pesan: ' + err.message);
        });
    });

    // Confirm Delete Message Button Click Handler
    const btnConfirmDeleteKrono = document.getElementById('btnConfirmDeleteKrono');
    btnConfirmDeleteKrono?.addEventListener('click', function() {
        const id = document.getElementById('deleteKronoId')?.value;
        if (!id) return;

        btnConfirmDeleteKrono.disabled = true;
        btnConfirmDeleteKrono.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...';

        fetch(`${destroyUrlBase}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            btnConfirmDeleteKrono.disabled = false;
            btnConfirmDeleteKrono.innerHTML = '<i class="bi bi-trash3 me-1"></i> Hapus';

            if (res.success) {
                const deleteModalEl = document.getElementById('deleteKronoMsgModal');
                if (deleteModalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(deleteModalEl);
                    modalInstance?.hide();
                }

                const rowEl = document.getElementById('krono-item-' + id);
                if (rowEl) {
                    rowEl.style.transition = 'all 0.3s ease';
                    rowEl.style.opacity = '0';
                    rowEl.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        rowEl.remove();
                        const remaining = document.querySelectorAll('.wa-msg-row');
                        if (remaining.length === 0) {
                            renderTimelineFromData([]);
                        }
                    }, 300);
                }
                showCopyToast('Pesan berhasil dihapus.');
            } else {
                alert('Gagal menghapus pesan: ' + (res.message || 'Akses ditolak.'));
            }
        })
        .catch(err => {
            btnConfirmDeleteKrono.disabled = false;
            btnConfirmDeleteKrono.innerHTML = '<i class="bi bi-trash3 me-1"></i> Hapus';
            alert('Gagal menghapus pesan: ' + err.message);
        });
    });

    // ── 15. WHATSAPP DIRECT INLINE CHAT INPUT & ATTACHMENTS ──
    const waDirectChatForm = document.getElementById('waDirectChatForm');
    if (waDirectChatForm) {
        const waChatTextInput = document.getElementById('waChatTextInput');
        const waChatFotoInput = document.getElementById('waChatFotoInput');
        const waChatVideoRecordInput = document.getElementById('waChatVideoRecordInput');
        const waChatLat = document.getElementById('waChatLatitude');
        const waChatLng = document.getElementById('waChatLongitude');
        const btnWaUploadFoto = document.getElementById('btnWaUploadFoto');
        const btnWaRecordVideo = document.getElementById('btnWaRecordVideo');
        const btnWaCameraWatermark = document.getElementById('btnWaCameraWatermark');
        const btnWaCameraPolos = document.getElementById('btnWaCameraPolos');
        const btnWaShareLocation = document.getElementById('btnWaShareLocation');
        const waAttachmentPreviewBar = document.getElementById('waAttachmentPreviewBar');
        const waPhotoPreviewChip = document.getElementById('waPhotoPreviewChip');
        const waPhotoFileName = document.getElementById('waPhotoFileName');
        const waPhotoSizeBadge = document.getElementById('waPhotoSizeBadge');
        const waPhotoThumb = document.getElementById('waPhotoThumb');
        const waPhotoDefaultIcon = document.getElementById('waPhotoDefaultIcon');
        const btnRemoveWaPhoto = document.getElementById('btnRemoveWaPhoto');
        const waVideoPreviewChip = document.getElementById('waVideoPreviewChip');
        const waVideoFileName = document.getElementById('waVideoFileName');
        const waVideoSizeBadge = document.getElementById('waVideoSizeBadge');
        const waVideoThumb = document.getElementById('waVideoThumb');
        const waVideoDefaultIcon = document.getElementById('waVideoDefaultIcon');
        const btnRemoveWaVideo = document.getElementById('btnRemoveWaVideo');
        const waLocationChip = document.getElementById('waLocationChip');
        const waLocationCoordsText = document.getElementById('waLocationCoordsText');
        const btnRemoveWaLocation = document.getElementById('btnRemoveWaLocation');
        const btnWaSendMsg = document.getElementById('btnWaSendMsg');

        const waMentionDropdown = document.getElementById('waMentionDropdown');
        const waMentionList = document.getElementById('waMentionList');

        let currentWaCompressedPhoto = null;
        let waCompressionPromise = null;
        let currentWaCompressedVideo = null;
        let waVideoCompressionPromise = null;
        let currentWaVideoThumbUrl = null;
        let currentPhotoMode = 'watermark'; // 'watermark' | 'plain' | 'gallery'

        let activeMentionIndex = 0;
        let filteredMentionUsers = [];
        let mentionStartIndex = -1;

        // Auto-expand textarea on typing
        waChatTextInput?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Quick reply chips handler (Rekomendasi Chat Cepat)
        document.querySelectorAll('.wa-quick-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                const text = this.getAttribute('data-text');
                if (text && waChatTextInput) {
                    waChatTextInput.value = text;
                    waChatTextInput.style.height = 'auto';
                    waChatTextInput.style.height = Math.min(waChatTextInput.scrollHeight, 100) + 'px';
                    waChatTextInput.focus();
                    waChatTextInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        });

        function showMentionDropdown(query, startIndex) {
            if (!waMentionDropdown || !waMentionList) return;

            const q = query.toLowerCase().trim();
            filteredMentionUsers = MENTIONABLE_USERS.filter(u => {
                const nameMatch = (u.name || '').toLowerCase().includes(q);
                const roleMatch = (u.role || '').toLowerCase().includes(q);
                return nameMatch || roleMatch;
            });

            if (filteredMentionUsers.length === 0) {
                hideMentionDropdown();
                return;
            }

            mentionStartIndex = startIndex;
            activeMentionIndex = 0;

            waMentionList.innerHTML = filteredMentionUsers.map((u, idx) => `
                <div class="wa-mention-item ${idx === 0 ? 'active' : ''}" data-index="${idx}">
                    ${u.avatar_url 
                        ? `<img src="${u.avatar_url}" alt="${u.name}" class="wa-mention-avatar">`
                        : `<div class="wa-mention-avatar-initial">${u.initial || 'U'}</div>`
                    }
                    <div class="wa-mention-info flex-grow-1 overflow-hidden">
                        <div class="wa-mention-name text-truncate">${u.name}</div>
                        <div class="wa-mention-role text-truncate">${u.role || '-'}</div>
                    </div>
                </div>
            `).join('');

            waMentionDropdown.classList.remove('d-none');
        }

        function hideMentionDropdown() {
            if (waMentionDropdown) {
                waMentionDropdown.classList.add('d-none');
            }
            filteredMentionUsers = [];
            mentionStartIndex = -1;
            activeMentionIndex = 0;
        }

        function updateMentionActiveItem() {
            if (!waMentionList) return;
            const items = waMentionList.querySelectorAll('.wa-mention-item');
            items.forEach((el, idx) => {
                if (idx === activeMentionIndex) {
                    el.classList.add('active');
                    el.scrollIntoView({ block: 'nearest' });
                } else {
                    el.classList.remove('active');
                }
            });
        }

        function insertMention(user) {
            if (!waChatTextInput || mentionStartIndex === -1 || !user) return;

            const val = waChatTextInput.value;
            const cursorPos = waChatTextInput.selectionStart;
            
            const before = val.substring(0, mentionStartIndex);
            const after = val.substring(cursorPos);
            const mentionText = `@${user.name} `;

            waChatTextInput.value = before + mentionText + after;
            const newCursorPos = before.length + mentionText.length;
            waChatTextInput.setSelectionRange(newCursorPos, newCursorPos);
            
            // Trigger auto-expand & input
            waChatTextInput.dispatchEvent(new Event('input'));
            hideMentionDropdown();
            waChatTextInput.focus();
        }

        function checkMentionTrigger() {
            if (!waChatTextInput) return;
            const val = waChatTextInput.value;
            const cursorPos = waChatTextInput.selectionStart;

            const textBeforeCursor = val.substring(0, cursorPos);
            const lastAtIndex = textBeforeCursor.lastIndexOf('@');

            if (lastAtIndex !== -1) {
                const charBeforeAt = lastAtIndex > 0 ? textBeforeCursor[lastAtIndex - 1] : ' ';
                const textBetween = textBeforeCursor.substring(lastAtIndex + 1);

                if ((/\s/.test(charBeforeAt) || lastAtIndex === 0) && !textBetween.includes('\n') && textBetween.length <= 30) {
                    showMentionDropdown(textBetween, lastAtIndex);
                    return;
                }
            }

            hideMentionDropdown();
        }

        // Input & Click triggers for @mentions
        waChatTextInput?.addEventListener('input', function() {
            checkMentionTrigger();
        });

        waChatTextInput?.addEventListener('click', function() {
            checkMentionTrigger();
        });

        // Click on mention list item
        waMentionList?.addEventListener('mousedown', function(e) {
            e.preventDefault(); // Prevent blur on textarea
            const item = e.target.closest('.wa-mention-item');
            if (item) {
                const idx = parseInt(item.getAttribute('data-index'), 10);
                if (!isNaN(idx) && filteredMentionUsers[idx]) {
                    insertMention(filteredMentionUsers[idx]);
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (waMentionDropdown && !waMentionDropdown.contains(e.target) && e.target !== waChatTextInput) {
                hideMentionDropdown();
            }
        });

        // Keydown handler (Arrow keys, Enter, Tab, Escape, Submit)
        waChatTextInput?.addEventListener('keydown', function(e) {
            // Jika dropdown mention sedang aktif
            if (waMentionDropdown && !waMentionDropdown.classList.contains('d-none') && filteredMentionUsers.length > 0) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeMentionIndex = (activeMentionIndex + 1) % filteredMentionUsers.length;
                    updateMentionActiveItem();
                    return;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeMentionIndex = (activeMentionIndex - 1 + filteredMentionUsers.length) % filteredMentionUsers.length;
                    updateMentionActiveItem();
                    return;
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    if (filteredMentionUsers[activeMentionIndex]) {
                        insertMention(filteredMentionUsers[activeMentionIndex]);
                    }
                    return;
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    hideMentionDropdown();
                    return;
                }
            }

            // Enter key to submit (Shift+Enter for newline)
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0 || (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0) || (waChatVideoRecordInput && waChatVideoRecordInput.files && waChatVideoRecordInput.files.length > 0) || currentWaCompressedPhoto || currentWaCompressedVideo) {
                    waDirectChatForm.requestSubmit();
                }
            }
        });

        // 1. Upload Media (Foto / Video dari Galeri)
        btnWaUploadFoto?.addEventListener('click', function(e) {
            e.preventDefault();
            currentPhotoMode = 'gallery';
            if (waChatFotoInput) {
                waChatFotoInput.removeAttribute('capture');
                waChatFotoInput.setAttribute('accept', 'image/*,video/*');
                waChatFotoInput.value = '';
                waChatFotoInput.click();
            }
        });

        // 1.B Rekam Video Lapangan Langsung dari Kamera HP
        btnWaRecordVideo?.addEventListener('click', function(e) {
            e.preventDefault();
            if (waChatVideoRecordInput) {
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                if (isMobile) {
                    waChatVideoRecordInput.setAttribute('capture', 'environment');
                } else {
                    waChatVideoRecordInput.removeAttribute('capture');
                }
                waChatVideoRecordInput.value = '';
                waChatVideoRecordInput.click();
            }
        });

        // 2. Kamera GPS (Dengan Logo MSN, Timestamp & Alamat)
        btnWaCameraWatermark?.addEventListener('click', function(e) {
            e.preventDefault();
            currentPhotoMode = 'watermark';
            if (waChatFotoInput) {
                waChatFotoInput.setAttribute('accept', 'image/*');
                waChatFotoInput.setAttribute('capture', 'environment');
                waChatFotoInput.value = '';
                waChatFotoInput.click();
            }
        });

        // 3. Kamera Polos (Tanpa Watermark)
        btnWaCameraPolos?.addEventListener('click', function(e) {
            e.preventDefault();
            currentPhotoMode = 'plain';
            if (waChatFotoInput) {
                waChatFotoInput.setAttribute('accept', 'image/*');
                waChatFotoInput.setAttribute('capture', 'environment');
                waChatFotoInput.value = '';
                waChatFotoInput.click();
            }
        });

        function clearPhotoAttachment() {
            currentWaCompressedPhoto = null;
            waCompressionPromise = null;
            if (waChatFotoInput) waChatFotoInput.value = '';
            if (waPhotoThumb) {
                waPhotoThumb.src = '#';
                waPhotoThumb.classList.add('d-none');
            }
            if (waPhotoDefaultIcon) waPhotoDefaultIcon.classList.remove('d-none');
            if (waPhotoPreviewChip) {
                waPhotoPreviewChip.classList.add('d-none');
                waPhotoPreviewChip.classList.remove('d-flex');
            }
        }

        function clearVideoAttachment() {
            currentWaCompressedVideo = null;
            waVideoCompressionPromise = null;
            currentWaVideoThumbUrl = null;
            if (waChatVideoRecordInput) waChatVideoRecordInput.value = '';
            if (waVideoThumb) {
                waVideoThumb.src = '#';
                waVideoThumb.classList.add('d-none');
            }
            if (waVideoDefaultIcon) waVideoDefaultIcon.classList.remove('d-none');
            if (waVideoPreviewChip) {
                waVideoPreviewChip.classList.add('d-none');
                waVideoPreviewChip.classList.remove('d-flex');
            }
        }

        // Handler terpadu untuk file foto atau video yang dipilih/direkam
        function handleMediaSelected(file, isDirectRecord = false) {
            if (!file) return;

            const isVideo = file.type.startsWith('video/') || /\.(mp4|webm|mov|m4v|3gp|avi)$/i.test(file.name);

            if (isVideo) {
                // Bersihkan foto jika sebelumnya ada (1 media per pesan)
                clearPhotoAttachment();

                if (waVideoFileName) waVideoFileName.textContent = file.name || (isDirectRecord ? 'rekaman_lapangan.mp4' : 'video.mp4');
                if (waVideoSizeBadge) {
                    waVideoSizeBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill py-0.5 px-1.5';
                    waVideoSizeBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span><span id="waVideoStatusText">Mengompres agar ringan...</span>';
                }
                if (waVideoPreviewChip) {
                    waVideoPreviewChip.classList.remove('d-none');
                    waVideoPreviewChip.classList.add('d-flex');
                }
                if (waAttachmentPreviewBar) waAttachmentPreviewBar.classList.remove('d-none');

                // Ekstrak thumbnail frame pertama secara cepat
                getVideoThumbnail(file).then(thumb => {
                    if (thumb && waVideoThumb) {
                        waVideoThumb.src = thumb;
                        waVideoThumb.classList.remove('d-none');
                        if (waVideoDefaultIcon) waVideoDefaultIcon.classList.add('d-none');
                        currentWaVideoThumbUrl = thumb;
                    }
                });

                // Jalankan kompresi video ringan (~1Mbps, 720p/480p)
                waVideoCompressionPromise = compressVideoFile(file, {
                    maxDimension: 854,
                    bitrate: 1000000,
                    fps: 24,
                    maxDuration: 60,
                    onProgress: (pct) => {
                        const statusTxt = document.getElementById('waVideoStatusText');
                        if (statusTxt) statusTxt.textContent = `Ringankan: ${pct}%`;
                    }
                }).then(compressedVideo => {
                    currentWaCompressedVideo = compressedVideo;
                    const origSize = formatFileSize(file.size);
                    const compSize = formatFileSize(compressedVideo.size);
                    if (waVideoSizeBadge) {
                        waVideoSizeBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5';
                        waVideoSizeBadge.innerHTML = `<i class="bi bi-lightning-charge-fill me-0.5 text-warning"></i><span>Ringan &bull; ${compSize}</span>`;
                        waVideoSizeBadge.title = `Video berhasil diringankan dari ${origSize} menjadi ${compSize} agar cepat terkirim di jaringan lapangan.`;
                    }
                    return compressedVideo;
                }).catch(err => {
                    console.warn('Kompresi video gagal, menggunakan file asli:', err);
                    currentWaCompressedVideo = file;
                    if (waVideoSizeBadge) {
                        waVideoSizeBadge.className = 'badge bg-light text-dark border rounded-pill py-0.5 px-2';
                        waVideoSizeBadge.textContent = formatFileSize(file.size);
                    }
                    return file;
                });
            } else {
                // File adalah gambar / foto
                clearVideoAttachment();

                if (waPhotoFileName) waPhotoFileName.textContent = file.name;
                if (waPhotoSizeBadge) {
                    waPhotoSizeBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill py-0.5 px-1.5';
                    waPhotoSizeBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span>...';
                }
                if (waPhotoPreviewChip) {
                    waPhotoPreviewChip.classList.remove('d-none');
                    waPhotoPreviewChip.classList.add('d-flex');
                }
                if (waAttachmentPreviewBar) waAttachmentPreviewBar.classList.remove('d-none');

                if (waPhotoThumb && file.type.startsWith('image/')) {
                    try {
                        waPhotoThumb.src = URL.createObjectURL(file);
                        waPhotoThumb.classList.remove('d-none');
                        if (waPhotoDefaultIcon) waPhotoDefaultIcon.classList.add('d-none');
                    } catch(e) {}
                }

                const isWatermark = (currentPhotoMode === 'watermark');

                waCompressionPromise = compressImageFile(file, {
                    maxWidth: 1600,
                    maxHeight: 1600,
                    quality: 0.82,
                    withWatermark: isWatermark,
                    onLocationDetected: (coords) => {
                        if (waChatLat && !waChatLat.value) waChatLat.value = coords.latitude.toFixed(7);
                        if (waChatLng && !waChatLng.value) waChatLng.value = coords.longitude.toFixed(7);
                    }
                }).then(compressedFile => {
                    currentWaCompressedPhoto = compressedFile;
                    const origSize = formatFileSize(file.size);
                    const compSize = formatFileSize(compressedFile.size);

                    if (waPhotoSizeBadge) {
                        if (isWatermark) {
                            waPhotoSizeBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5';
                            waPhotoSizeBadge.innerHTML = `<i class="bi bi-shield-check me-1"></i>GPS Stamp &bull; ${compSize}`;
                            waPhotoSizeBadge.title = `Foto telah diberi GPS Timestamp & Logo MSN. Ukuran asli ${origSize} dikompresi menjadi ${compSize}`;
                        } else {
                            waPhotoSizeBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-0.5 px-1.5';
                            waPhotoSizeBadge.innerHTML = `<i class="bi bi-camera me-1"></i>Foto Polos &bull; ${compSize}`;
                            waPhotoSizeBadge.title = `Foto kamera asli tanpa watermark. Ukuran asli ${origSize} dikompresi menjadi ${compSize}`;
                        }
                    }

                    if (waPhotoThumb) {
                        try {
                            waPhotoThumb.src = URL.createObjectURL(compressedFile);
                        } catch(e) {}
                    }

                    return compressedFile;
                }).catch(err => {
                    console.warn('Kompresi gambar gagal, menggunakan file asli:', err);
                    currentWaCompressedPhoto = file;
                    if (waPhotoSizeBadge) {
                        waPhotoSizeBadge.className = 'badge bg-light text-dark border rounded-pill py-0.5 px-2';
                        waPhotoSizeBadge.textContent = formatFileSize(file.size);
                    }
                    return file;
                });
            }
        }

        // Event saat file foto/video dipilih dari galeri/file
        waChatFotoInput?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleMediaSelected(this.files[0], false);
            } else {
                clearPhotoAttachment();
                checkPreviewBarEmpty();
            }
        });

        // Event saat video direkam langsung dari kamera HP
        waChatVideoRecordInput?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleMediaSelected(this.files[0], true);
            } else {
                clearVideoAttachment();
                checkPreviewBarEmpty();
            }
        });

        // Hapus attachment foto
        btnRemoveWaPhoto?.addEventListener('click', function() {
            clearPhotoAttachment();
            checkPreviewBarEmpty();
        });

        // Hapus attachment video
        btnRemoveWaVideo?.addEventListener('click', function() {
            clearVideoAttachment();
            checkPreviewBarEmpty();
        });

        // Share Lokasi GPS
        btnWaShareLocation?.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur lokasi GPS.');
                return;
            }
            btnWaShareLocation.disabled = true;
            const iconDiv = btnWaShareLocation.querySelector('.wa-attach-icon');
            const originalHtml = iconDiv ? iconDiv.innerHTML : '';
            if (iconDiv) iconDiv.innerHTML = '<span class="spinner-border spinner-border-sm" style="width: 0.8rem; height: 0.8rem;"></span>';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    if (waChatLat) waChatLat.value = lat;
                    if (waChatLng) waChatLng.value = lng;
                    if (waLocationCoordsText) waLocationCoordsText.textContent = `GPS: ${lat}, ${lng}`;
                    if (waLocationChip) {
                        waLocationChip.classList.remove('d-none');
                        waLocationChip.classList.add('d-flex');
                    }
                    if (waAttachmentPreviewBar) waAttachmentPreviewBar.classList.remove('d-none');
                    btnWaShareLocation.disabled = false;
                    if (iconDiv) iconDiv.innerHTML = originalHtml;
                },
                function(err) {
                    alert('Gagal mengambil lokasi GPS: ' + err.message);
                    btnWaShareLocation.disabled = false;
                    if (iconDiv) iconDiv.innerHTML = originalHtml;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        // Hapus attachment lokasi
        btnRemoveWaLocation?.addEventListener('click', function() {
            if (waChatLat) waChatLat.value = '';
            if (waChatLng) waChatLng.value = '';
            if (waLocationChip) {
                waLocationChip.classList.add('d-none');
                waLocationChip.classList.remove('d-flex');
            }
            checkPreviewBarEmpty();
        });

        function checkPreviewBarEmpty() {
            const photoHidden = !waPhotoPreviewChip || waPhotoPreviewChip.classList.contains('d-none');
            const videoHidden = !waVideoPreviewChip || waVideoPreviewChip.classList.contains('d-none');
            const locHidden = !waLocationChip || waLocationChip.classList.contains('d-none');
            if (photoHidden && videoHidden && locHidden) {
                if (waAttachmentPreviewBar) waAttachmentPreviewBar.classList.add('d-none');
            }
        }

        // ── 15.5. VOICE NOTE TO TEXT (SPEECH-TO-TEXT LANGSUNG DI DALAM INPUT CHAT) ──
        const btnWaVoiceNote = document.getElementById('btnWaVoiceNote');
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        let recognition = null;
        let isRecordingVoice = false;
        let originalPlaceholder = waChatTextInput ? waChatTextInput.getAttribute('placeholder') || 'Ketik update koordinasi ...' : '';
        let voiceToastEl = null;

        function showVoiceListeningToast() {
            if (!voiceToastEl) {
                voiceToastEl = document.createElement('div');
                voiceToastEl.className = 'wa-voice-listening-toast';
                voiceToastEl.innerHTML = '<span class="wa-voice-wave-dot"></span> <span>Mendengarkan suara... Silakan bicara</span>';
                const pill = document.querySelector('.wa-floating-input-pill');
                if (pill) {
                    pill.style.position = 'relative';
                    pill.appendChild(voiceToastEl);
                }
            }
            voiceToastEl.classList.remove('d-none');
        }

        function hideVoiceListeningToast() {
            if (voiceToastEl) {
                voiceToastEl.classList.add('d-none');
            }
        }

        if (SpeechRecognition) {
            try {
                recognition = new SpeechRecognition();
                recognition.lang = 'id-ID'; // Bahasa Indonesia
                recognition.continuous = true;
                recognition.interimResults = true;
                recognition.maxAlternatives = 1;

                let speechStartText = '';

                recognition.onstart = function() {
                    isRecordingVoice = true;
                    if (btnWaVoiceNote) {
                        btnWaVoiceNote.classList.add('recording');
                        btnWaVoiceNote.title = 'Sedang mendengarkan... Klik untuk berhenti';
                        btnWaVoiceNote.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
                    }
                    if (waChatTextInput) {
                        speechStartText = waChatTextInput.value;
                        waChatTextInput.setAttribute('placeholder', '🔴 Mendengarkan suara... Bicara sekarang');
                        waChatTextInput.focus();
                    }
                    showVoiceListeningToast();
                };

                recognition.onresult = function(event) {
                    let interimTranscript = '';
                    let finalTranscript = '';

                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        const transcript = event.results[i][0].transcript;
                        if (event.results[i].isFinal) {
                            finalTranscript += transcript;
                        } else {
                            interimTranscript += transcript;
                        }
                    }

                    if (waChatTextInput) {
                        const currentSpoken = (finalTranscript + interimTranscript).trim();
                        if (currentSpoken.length > 0) {
                            const separator = (speechStartText.trim().length > 0 && !speechStartText.endsWith(' ')) ? ' ' : '';
                            waChatTextInput.value = speechStartText + (speechStartText ? separator : '') + currentSpoken;
                            // Auto expand textarea
                            waChatTextInput.style.height = 'auto';
                            waChatTextInput.style.height = Math.min(waChatTextInput.scrollHeight, 100) + 'px';
                            waChatTextInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                };

                recognition.onerror = function(event) {
                    console.warn('Speech recognition error:', event.error);
                    if (event.error === 'not-allowed') {
                        alert('Izin mikrofon belum diberikan. Silakan izinkan akses mikrofon pada browser Anda untuk menggunakan fitur speech-to-text.');
                    }
                    stopVoiceRecognition();
                };

                recognition.onend = function() {
                    stopVoiceRecognition();
                };
            } catch (err) {
                console.warn('SpeechRecognition initialization error:', err);
            }
        }

        function startVoiceRecognition() {
            if (!recognition) {
                alert('Browser Anda belum mendukung Web Speech Recognition. Disarankan menggunakan Google Chrome atau browser berbasis Chromium pada HP/Laptop.');
                return;
            }
            try {
                recognition.start();
            } catch (err) {
                console.warn('SpeechRecognition start retry:', err);
                try {
                    recognition.stop();
                    setTimeout(() => {
                        try { recognition.start(); } catch(e) {}
                    }, 200);
                } catch(e) {}
            }
        }

        function stopVoiceRecognition() {
            isRecordingVoice = false;
            if (btnWaVoiceNote) {
                btnWaVoiceNote.classList.remove('recording');
                btnWaVoiceNote.title = 'Ketik dengan Suara (Voice Note to Text)';
                btnWaVoiceNote.innerHTML = '<i class="bi bi-mic-fill"></i>';
            }
            if (waChatTextInput) {
                waChatTextInput.setAttribute('placeholder', originalPlaceholder);
            }
            hideVoiceListeningToast();
            if (recognition) {
                try { recognition.stop(); } catch (e) {}
            }
        }

        btnWaVoiceNote?.addEventListener('click', function(e) {
            e.preventDefault();
            if (isRecordingVoice) {
                stopVoiceRecognition();
            } else {
                startVoiceRecognition();
            }
        });

        // Submitting Chat Form via AJAX (WhatsApp-Native Optimistic UI: Clock -> Sent (Grey 2-ticks) -> Read (Blue 2-ticks))
        waDirectChatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            stopVoiceRecognition();
            const textVal = waChatTextInput ? waChatTextInput.value.trim() : '';
            const hasPhoto = (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0 && !waChatFotoInput.files[0].type.startsWith('video/')) || currentWaCompressedPhoto;
            const hasVideo = currentWaCompressedVideo || (waChatVideoRecordInput && waChatVideoRecordInput.files && waChatVideoRecordInput.files.length > 0) || (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files[0] && (waChatFotoInput.files[0].type.startsWith('video/') || /\.(mp4|webm|mov|m4v|3gp|avi)$/i.test(waChatFotoInput.files[0].name)));
            const hasLoc = waChatLat && waChatLat.value !== '';

            if (!textVal && !hasPhoto && !hasVideo && !hasLoc) return;

            // Simpan data form & previews sebelum input di-reset
            const photoThumbSrc = (waPhotoThumb && !waPhotoThumb.classList.contains('d-none')) ? waPhotoThumb.src : null;
            const videoPreviewSrc = currentWaCompressedVideo ? URL.createObjectURL(currentWaCompressedVideo) : (currentWaVideoThumbUrl || null);
            const latVal = waChatLat ? waChatLat.value : '';
            const lngVal = waChatLng ? waChatLng.value : '';
            const activeReply = activeReplyData ? { ...activeReplyData } : null;

            // Tunggu jika proses kompresi media di background sedang berlangsung
            if (waCompressionPromise) {
                try { await waCompressionPromise; } catch(e) {}
            }
            if (waVideoCompressionPromise) {
                try { await waVideoCompressionPromise; } catch(e) {}
            }

            const formData = new FormData(waDirectChatForm);
            
            // Pasang teks kutipan jika sedang membalas pesan (Reply)
            if (activeReply) {
                const snippet = (activeReply.text || '').substring(0, 80).replace(/\n/g, ' ');
                const quotedText = `[Membalas ${activeReply.sender}]: ${snippet}\n\n` + textVal;
                formData.set('informasi', quotedText);
                cancelReply();
            }

            // Pasang file video atau foto yang sudah terkompresi otomatis
            if (currentWaCompressedVideo) {
                formData.set('foto', currentWaCompressedVideo, currentWaCompressedVideo.name);
                formData.set('video', currentWaCompressedVideo, currentWaCompressedVideo.name);
            } else if (currentWaCompressedPhoto) {
                formData.set('foto', currentWaCompressedPhoto, currentWaCompressedPhoto.name);
            }

            // 1. LANGSUNG BERSIHKAN INPUT TANPA MENAMPILKAN SPINNER LOADING DI TOMBOL/INPUT
            if (waChatTextInput) {
                waChatTextInput.value = '';
                waChatTextInput.style.height = 'auto';
                if (window.innerWidth < 768) {
                    waChatTextInput.blur();
                }
            }
            clearPhotoAttachment();
            clearVideoAttachment();
            if (waChatLat) waChatLat.value = '';
            if (waChatLng) waChatLng.value = '';
            if (waLocationChip) {
                waLocationChip.classList.add('d-none');
                waLocationChip.classList.remove('d-flex');
            }
            checkPreviewBarEmpty();

            // 2. OPTIMISTIC MESSAGE INSERTION (ICON JAM / CLOCK STATUS)
            const tempId = 'temp-msg-' + Date.now();
            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';

            const wrapper = document.getElementById('timelineWrapper');
            let stream = document.getElementById('timelineList');
            const emptyEl = document.getElementById('emptyTimeline');
            if (emptyEl) emptyEl.remove();

            if (!stream && wrapper) {
                wrapper.innerHTML = '<div class="wa-chat-stream" id="timelineList"></div>';
                stream = document.getElementById('timelineList');
                attachStreamScrollListener(stream);
            }

            if (stream) {
                const optimisticHtml = `
                    <div class="wa-msg-row wa-msg-outgoing wa-msg-anim-send" id="${tempId}" data-timestamp="${Math.floor(Date.now() / 1000)}">
                        <div class="wa-bubble wa-bubble-outgoing">
                            <div class="wa-bubble-header">
                                <div class="wa-sender-info">
                                    <span class="wa-sender-name" style="color: #0f766e;">Anda</span>
                                    <span class="wa-role-pill">{{ auth()->user()->role_short ?? 'User' }}</span>
                                </div>
                            </div>
                            <div class="wa-msg-text">${formatMessageWithMentions(formData.get('informasi') || textVal)}</div>
                            ${hasVideo ? `
                            <div class="wa-media-card wa-video-card" style="opacity: 0.88;">
                                ${videoPreviewSrc ? `<video src="${videoPreviewSrc}" controls playsinline preload="metadata" class="wa-video-player" style="filter: brightness(0.92);"></video>` : ''}
                                <div class="wa-media-badge">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.72rem; height: 0.72rem;"></span>
                                    <span>Mengunggah video ringan...</span>
                                </div>
                            </div>` : (hasPhoto && photoThumbSrc && photoThumbSrc !== '#' ? `
                            <div class="wa-media-card" style="opacity: 0.85;">
                                <img src="${photoThumbSrc}" alt="Mengunggah Foto..." class="wa-media-img" style="filter: brightness(0.92);">
                                <div class="wa-media-badge">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.72rem; height: 0.72rem;"></span>
                                    <span>Mengunggah foto...</span>
                                </div>
                            </div>` : '')}
                            ${hasLoc ? `
                            <div class="wa-location-card">
                                <div class="wa-loc-icon"><i class="bi bi-geo-alt-fill text-danger"></i></div>
                                <div class="wa-loc-info">
                                    <div class="wa-loc-title">Lokasi Titik Lapangan</div>
                                    <div class="wa-loc-coords">${latVal}, ${lngVal}</div>
                                </div>
                            </div>` : ''}
                            <div class="wa-bubble-footer">
                                <span class="wa-time">${timeStr}</span>
                                <i class="bi bi-clock wa-status-icon wa-status-pending" id="status-icon-${tempId}" title="Mengirim..."></i>
                            </div>
                        </div>
                    </div>
                `;
                stream.insertAdjacentHTML('beforeend', optimisticHtml);
                stream.scrollTop = stream.scrollHeight;
            }

            // 3. KIRIM DATA KE SERVER VIA FETCH DENGAN DUKUNGAN OFFLINE-FIRST
            const sendDirectMessage = () => {
                if (!navigator.onLine && window.OfflineSync) {
                    window.OfflineSync.enqueueRequest({
                        url: waDirectChatForm.action,
                        method: 'POST',
                        formData: formData,
                        meta: {
                            label: 'Pesan Tiket #{{ $tiket->id }}',
                            tiket_id: {{ $tiket->id }},
                            temp_element_id: tempId,
                            text: textVal || (hasVideo ? 'Mengunggah Video' : (hasPhoto ? 'Mengunggah Foto' : 'Koordinat GPS Lapangan')),
                            has_photo: hasPhoto,
                            has_video: hasVideo,
                            has_location: hasLoc
                        }
                    });
                    const statusIcon = document.getElementById('status-icon-' + tempId);
                    if (statusIcon) {
                        statusIcon.className = 'bi bi-cloud-slash text-warning';
                        statusIcon.title = 'Tersimpan offline di perangkat (Akan otomatis dikirim saat ada sinyal)';
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Tersimpan Offline',
                            text: 'Pesan tersimpan di HP dan akan otomatis terkirim saat sinyal kembali.',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    }
                    return;
                }

                fetch(waDirectChatForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    const ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        // Server returned non-JSON (e.g. redirect/HTML error page)
                        return res.text().then(text => {
                            throw new Error('Server returned non-JSON response (HTTP ' + res.status + ')');
                        });
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.success && res.data) {
                        const tempEl = document.getElementById(tempId);
                        if (tempEl) {
                            tempEl.id = 'krono-item-' + res.data.id;
                            tempEl.setAttribute('data-id', res.data.id);
                            const parsedTs = res.data.timestamp ? Math.floor(new Date(res.data.timestamp).getTime() / 1000) : Math.floor(Date.now() / 1000);
                            tempEl.setAttribute('data-timestamp', parsedTs);
                            const statusIcon = document.getElementById('status-icon-' + tempId);
                            if (statusIcon) {
                                // TAHAP: CEKLIS 2 ABU-ABU (SENT / TERKIRIM - MENUNGGU DILIHAT)
                                statusIcon.id = 'status-icon-' + res.data.id;
                                statusIcon.className = 'bi bi-check2-all wa-status-icon wa-status-sent';
                                statusIcon.title = 'Terkirim (Belum dilihat)';
                            }

                            // Update media url asli jika upload video atau foto
                            if (res.data.is_video || res.data.video_url) {
                                const mediaCard = tempEl.querySelector('.wa-media-card');
                                if (mediaCard) {
                                    mediaCard.style.opacity = '1';
                                    mediaCard.className = 'wa-media-card wa-video-card';
                                    mediaCard.onclick = null;
                                    mediaCard.innerHTML = `<video src="${res.data.video_url || res.data.foto_url}" controls playsinline preload="metadata" class="wa-video-player"></video>`;
                                }
                            } else if (res.data.foto_url) {
                                const mediaCard = tempEl.querySelector('.wa-media-card');
                                if (mediaCard) {
                                    mediaCard.style.opacity = '1';
                                    mediaCard.onclick = () => zoomPhoto(res.data.foto_url, `${res.data.kategori} - ${res.data.formatted_time}`);
                                    const img = mediaCard.querySelector('.wa-media-img');
                                    if (img) {
                                        img.src = res.data.foto_url;
                                        img.style.filter = 'none';
                                    }
                                    const badge = mediaCard.querySelector('.wa-media-badge');
                                    if (badge) {
                                        badge.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i><span>Klik untuk memperbesar</span>';
                                    }
                                }
                            }

                            // Pasang action menu 3-dots
                            const header = tempEl.querySelector('.wa-bubble-header');
                            if (header && !header.querySelector('.wa-bubble-menu-wrapper')) {
                                const safeInfoAttr = rawEscape(res.data.informasi || '');
                                header.insertAdjacentHTML('beforeend', `
                                    <div class="dropdown wa-bubble-menu-wrapper">
                                        <button type="button" class="wa-msg-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan pesan">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end wa-msg-dropdown-menu shadow border-0">
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-copy" data-id="${res.data.id}" data-text="${safeInfoAttr}">
                                                    <i class="bi bi-clipboard text-primary"></i> Salin
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-msg-info"
                                                        data-id="${res.data.id}"
                                                        data-user-id="${currentUserId}"
                                                        data-sender="Anda"
                                                        data-sender-role="${res.data.user_role || '-'}"
                                                        data-time="${res.data.formatted_time || ''}"
                                                        data-text="${safeInfoAttr}"
                                                        data-photo="${res.data.foto_url || ''}"
                                                        data-timestamp="${Math.floor(Date.now()/1000)}">
                                                    <i class="bi bi-info-circle-fill text-info"></i> Info Pesan
                                                </button>
                                            </li>
                                            ${res.data.foto_url && !isTiketClosed && canChat ? `
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-forward-doc text-success"
                                                        onclick="forwardPhotoToDoc('${res.data.foto_url}', '${res.data.latitude || ''}', '${res.data.longitude || ''}', '${res.data.timestamp ? res.data.timestamp.substring(0,16) : ''}', '${res.data.kategori || ''}')">
                                                    <i class="bi bi-folder-plus text-success"></i> Simpan ke Dokumentasi
                                                </button>
                                            </li>` : ''}
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-reply" data-id="${res.data.id}" data-sender="Anda" data-text="${safeInfoAttr}">
                                                    <i class="bi bi-reply-fill text-info"></i> Balas
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-edit" data-id="${res.data.id}" data-text="${safeInfoAttr}">
                                                    <i class="bi bi-pencil-square text-warning"></i> Edit
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button type="button" class="dropdown-item wa-msg-dropdown-item text-danger btn-action-delete" data-id="${res.data.id}">
                                                    <i class="bi bi-trash3-fill"></i> Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                `);
                            }
                        } else {
                            appendSingleKronoToTimeline(res.data);
                        }

                        // Hilangkan banner peringatan 30 menit secara real-time karena update sudah dikirim
                        const intervalRow = document.getElementById('fieldIntervalStatusRow');
                        const intervalBanner = document.getElementById('fieldReportIntervalBanner');
                        if (intervalRow) {
                            intervalRow.style.transition = 'all 0.4s ease';
                            intervalRow.style.opacity = '0';
                            intervalRow.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                intervalRow.remove();
                                if (intervalBanner && !intervalBanner.querySelector('.border-top')) {
                                    intervalBanner.remove();
                                }
                            }, 400);
                        } else if (intervalBanner) {
                            intervalBanner.style.transition = 'all 0.4s ease';
                            intervalBanner.style.opacity = '0';
                            intervalBanner.style.transform = 'translateY(-10px)';
                            setTimeout(() => intervalBanner.remove(), 400);
                        }
                    } else {
                        // Server returned success:false - show error icon on optimistic message
                        const statusIcon = document.getElementById('status-icon-' + tempId);
                        if (statusIcon) {
                            statusIcon.className = 'bi bi-exclamation-circle-fill text-danger';
                            statusIcon.title = 'Gagal terkirim: ' + (res.message || 'Error');
                        }
                        console.error('Chat send failed:', res.message);
                    }
                })
                .catch(err => {
                    console.error('Chat send error:', err);
                    // Jika terputus koneksi saat mengirim, alihkan ke antrean offline lokal
                    if (window.OfflineSync) {
                        window.OfflineSync.enqueueRequest({
                            url: waDirectChatForm.action,
                            method: 'POST',
                            formData: formData,
                            meta: {
                                label: 'Pesan Tiket #{{ $tiket->id }}',
                                tiket_id: {{ $tiket->id }},
                                temp_element_id: tempId,
                                text: textVal || (hasVideo ? 'Mengunggah Video' : (hasPhoto ? 'Mengunggah Foto' : 'Koordinat GPS Lapangan')),
                                has_photo: hasPhoto,
                                has_video: hasVideo,
                                has_location: hasLoc
                            }
                        });
                        const statusIcon = document.getElementById('status-icon-' + tempId);
                        if (statusIcon) {
                            statusIcon.className = 'bi bi-cloud-slash text-warning';
                            statusIcon.title = 'Tersimpan offline di perangkat (Akan otomatis dikirim saat ada sinyal)';
                        }
                    } else {
                        const statusIcon = document.getElementById('status-icon-' + tempId);
                        if (statusIcon) {
                            statusIcon.className = 'bi bi-exclamation-circle-fill text-danger';
                            statusIcon.title = 'Gagal terkirim: ' + err.message;
                        }
                        // Jangan tampilkan alert() agar tidak menginterupsi UX
                        console.error('Gagal mengirim pesan:', err.message);
                    }
                });
            };

            sendDirectMessage();
        });
    }

    // ── 15.B OFFLINE AUTO-SYNC REAL-TIME EVENT LISTENER ──
    window.addEventListener('offline-sync:item-synced', function(e) {
        const item = e.detail?.item;
        const resData = e.detail?.result?.data;
        if (!item || !resData) return;

        const tempId = item.meta?.temp_element_id;
        if (!tempId) return;

        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            tempEl.id = 'krono-item-' + resData.id;
            tempEl.setAttribute('data-id', resData.id);
            const parsedTs = resData.timestamp ? Math.floor(new Date(resData.timestamp).getTime() / 1000) : Math.floor(Date.now() / 1000);
            tempEl.setAttribute('data-timestamp', parsedTs);
            const statusIcon = document.getElementById('status-icon-' + tempId);
            if (statusIcon) {
                statusIcon.id = 'status-icon-' + resData.id;
                statusIcon.className = 'bi bi-check2-all wa-status-icon wa-status-sent';
                statusIcon.title = 'Terkirim (Belum dilihat)';
            }

            // Update media card (video / foto) setelah sinkronisasi offline sukses
            if (resData.is_video || resData.video_url) {
                const mediaCard = tempEl.querySelector('.wa-media-card');
                if (mediaCard) {
                    mediaCard.style.opacity = '1';
                    mediaCard.className = 'wa-media-card wa-video-card';
                    mediaCard.onclick = null;
                    mediaCard.innerHTML = `<video src="${resData.video_url || resData.foto_url}" controls playsinline preload="metadata" class="wa-video-player"></video>`;
                }
            } else if (resData.foto_url) {
                const mediaCard = tempEl.querySelector('.wa-media-card');
                if (mediaCard) {
                    mediaCard.style.opacity = '1';
                    mediaCard.onclick = function() {
                        openWaImagePreview(resData.foto_url, resData.informasi || 'Dokumentasi Lapangan');
                    };
                    mediaCard.innerHTML = `<img src="${resData.foto_url}" alt="Dokumentasi" class="wa-media-img"><div class="wa-media-badge"><i class="bi bi-camera-fill me-1"></i> Foto Lapangan</div>`;
                }
            }
        }
    });

    // ── 16. SCROLL TO BOTTOM FLOATING BUTTON & TIMELINE SCROLL LISTENER ──
    const btnWaScrollBottom = document.getElementById('btnWaScrollBottom');

    function checkStreamScroll(streamEl) {
        if (!streamEl) return;
        const distanceFromBottom = streamEl.scrollHeight - streamEl.scrollTop - streamEl.clientHeight;

        if (btnWaScrollBottom) {
            if (distanceFromBottom > 80) {
                btnWaScrollBottom.classList.remove('d-none');
                btnWaScrollBottom.classList.add('d-flex');
            } else {
                btnWaScrollBottom.classList.add('d-none');
                btnWaScrollBottom.classList.remove('d-flex');
            }
        }

        // Auto trigger muat riwayat lama saat user scroll ke bagian paling atas
        if (streamEl.scrollTop <= 40 && !isLoadingOlder) {
            const btn = document.getElementById('btnLoadOlderKrono');
            if (btn && !btn.disabled) {
                loadOlderMessages();
            }
        }
    }

    function attachStreamScrollListener(streamEl) {
        if (!streamEl) return;
        if (streamEl._waScrollHandler) {
            streamEl.removeEventListener('scroll', streamEl._waScrollHandler);
        }
        streamEl._waScrollHandler = function() { checkStreamScroll(streamEl); };
        streamEl.addEventListener('scroll', streamEl._waScrollHandler);
        streamEl.addEventListener('wheel', function() { userHasScrolledTimeline = true; }, { passive: true });
        streamEl.addEventListener('touchmove', function() { userHasScrolledTimeline = true; }, { passive: true });
    }

    // Attach listener to initial stream if present
    const initStream = document.getElementById('timelineList');
    if (initStream) {
        attachStreamScrollListener(initStream);
        scrollChatToBottom(true);
        checkStreamScroll(initStream);
    }

    btnWaScrollBottom?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        userHasScrolledTimeline = false;
        scrollChatToBottom(false);
    });

    // ── 17. FULLSCREEN CHAT TOGGLE ──
    const btnWaFullscreen = document.getElementById('btnWaFullscreen');
    const icoWaFullscreen = document.getElementById('icoWaFullscreen');
    const waContainer    = document.querySelector('.wa-chat-container');
    let savedWindowScrollY = 0;
    let isWaTransitioning = false;

    function enterWaFullscreen() {
        if (!waContainer || isWaTransitioning) return;
        isWaTransitioning = true;
        // Simpan posisi scroll halaman saat ini sebelum masuk mode fixed fullscreen
        savedWindowScrollY = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;

        waContainer.classList.remove('wa-mini-returning', 'wa-fullscreen-closing');
        waContainer.classList.add('wa-fullscreen');
        document.body.classList.add('wa-chat-fullscreen-active');
        if (icoWaFullscreen) {
            icoWaFullscreen.classList.remove('bi-arrows-fullscreen');
            icoWaFullscreen.classList.add('bi-fullscreen-exit');
        }
        if (btnWaFullscreen) btnWaFullscreen.title = 'Perkecil chat';
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            isWaTransitioning = false;
            scrollChatToBottom(true);
        }, 320);

        // Scroll stream ke bawah setelah animasi berjalan
        requestAnimationFrame(() => {
            scrollChatToBottom(true);
        });
    }

    function exitWaFullscreen() {
        if (!waContainer || isWaTransitioning) return;
        isWaTransitioning = true;

        waContainer.classList.add('wa-fullscreen-closing');
        if (icoWaFullscreen) {
            icoWaFullscreen.classList.remove('bi-fullscreen-exit');
            icoWaFullscreen.classList.add('bi-arrows-fullscreen');
        }
        if (btnWaFullscreen) btnWaFullscreen.title = 'Perbesar chat';

        setTimeout(() => {
            waContainer.classList.remove('wa-fullscreen', 'wa-fullscreen-closing');
            document.body.classList.remove('wa-chat-fullscreen-active');
            document.body.style.overflow = '';

            // Animasi transisi saat kembali ke ukuran mini (card)
            waContainer.classList.add('wa-mini-returning');
            setTimeout(() => {
                waContainer.classList.remove('wa-mini-returning');
                isWaTransitioning = false;
            }, 300);

            // Pertahankan posisi scroll halaman tepat di elemen chat, tidak melompat ke paling atas halaman
            if (savedWindowScrollY > 0) {
                window.scrollTo({
                    top: savedWindowScrollY,
                    behavior: 'instant'
                });
            } else {
                waContainer.scrollIntoView({ behavior: 'instant', block: 'nearest' });
            }

            const stream = document.getElementById('timelineList');
            if (stream) stream.scrollTop = stream.scrollHeight;
        }, 190);
    }

    btnWaFullscreen?.addEventListener('click', function() {
        if (waContainer && waContainer.classList.contains('wa-fullscreen')) {
            exitWaFullscreen();
        } else {
            enterWaFullscreen();
        }
    });

    // Tekan Escape untuk keluar dari fullscreen
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && waContainer && waContainer.classList.contains('wa-fullscreen')) {
            exitWaFullscreen();
        }
    });
});

// ── FAILSAFE: Pastikan waDirectChatForm tidak pernah submit secara tradisional ──
// Script ini berjalan terpisah dari DOMContentLoaded utama sebagai perlindungan ganda.
// Memanggil preventDefault() dua kali aman dan tidak bermasalah.
(function() {
    function guardWaChatForm() {
        const form = document.getElementById('waDirectChatForm');
        if (!form || form._failsafeGuarded) return;
        form._failsafeGuarded = true;
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Selalu cegah submit tradisional
        }, true); // capture phase — dijalankan sebelum listener bubble phase
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', guardWaChatForm);
    } else {
        guardWaChatForm();
    }
})();
</script>
@endpush
