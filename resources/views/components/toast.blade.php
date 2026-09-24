<div class="cjp-toast-container position-fixed top-0 end-0 p-3" style="z-index: 1099;">
    {{-- ── SUCCESS TOAST ── --}}
    @if(session('success') || session('status'))
        <div class="cjp-toast toast-success shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-cjp-toast>
            <div class="toast-content">
                <div class="toast-icon-wrapper">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="toast-text-group">
                    <div class="toast-title">Berhasil</div>
                    <div class="toast-message">{{ session('success') ?? session('status') }}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Tutup" onclick="closeCjpToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    @endif

    {{-- ── ERROR / DANGER TOAST ── --}}
    @if(session('error') || session('danger'))
        <div class="cjp-toast toast-danger shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-cjp-toast>
            <div class="toast-content">
                <div class="toast-icon-wrapper">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="toast-text-group">
                    <div class="toast-title">Terjadi Kesalahan</div>
                    <div class="toast-message">{{ session('error') ?? session('danger') }}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Tutup" onclick="closeCjpToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    @endif

    {{-- ── WARNING TOAST ── --}}
    @if(session('warning'))
        <div class="cjp-toast toast-warning shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-cjp-toast>
            <div class="toast-content">
                <div class="toast-icon-wrapper">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="toast-text-group">
                    <div class="toast-title">Peringatan</div>
                    <div class="toast-message">{{ session('warning') }}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Tutup" onclick="closeCjpToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    @endif

    {{-- ── INFO TOAST ── --}}
    @if(session('info'))
        <div class="cjp-toast toast-info shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-cjp-toast>
            <div class="toast-content">
                <div class="toast-icon-wrapper">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="toast-text-group">
                    <div class="toast-title">Informasi</div>
                    <div class="toast-message">{{ session('info') }}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Tutup" onclick="closeCjpToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    @endif

    {{-- ── VALIDATION ERRORS LIST ── --}}
    @if(isset($errors) && $errors->any())
        <div class="cjp-toast toast-danger shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-cjp-toast>
            <div class="toast-content">
                <div class="toast-icon-wrapper">
                    <i class="bi bi-shield-fill-x"></i>
                </div>
                <div class="toast-text-group">
                    <div class="toast-title">Validasi Formulir Gagal</div>
                    <ul class="mb-0 ps-3 toast-message" style="font-size: 0.78rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Tutup" onclick="closeCjpToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        </div>
    @endif
</div>

<script>
    function closeCjpToast(btn) {
        const toast = btn.closest('.cjp-toast');
        if (toast) {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 360);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('[data-cjp-toast]');
        toasts.forEach(toast => {
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 360);
                }
            }, 5000);
        });
    });
</script>
