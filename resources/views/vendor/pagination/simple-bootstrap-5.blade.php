@if ($paginator->hasPages())
    <nav class="d-flex align-items-center justify-content-between flex-wrap gap-2" role="navigation" aria-label="Navigasi Halaman">
        <div class="d-flex align-items-center justify-content-between w-100 gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-nav-pill disabled" aria-disabled="true">
                    <i class="bi bi-chevron-left" style="font-size: 0.72rem;"></i>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-nav-pill" rel="prev">
                    <i class="bi bi-chevron-left text-primary" style="font-size: 0.72rem;"></i>
                    <span>Sebelumnya</span>
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-nav-pill" rel="next">
                    <span>Berikutnya</span>
                    <i class="bi bi-chevron-right text-primary" style="font-size: 0.72rem;"></i>
                </a>
            @else
                <span class="page-nav-pill disabled" aria-disabled="true">
                    <span>Berikutnya</span>
                    <i class="bi bi-chevron-right" style="font-size: 0.72rem;"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
