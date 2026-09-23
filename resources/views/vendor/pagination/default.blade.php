@if ($paginator->hasPages())
    <nav class="d-flex align-items-center justify-content-between flex-wrap gap-2" role="navigation" aria-label="Navigasi Halaman">
        <!-- ── MOBILE VIEW (< 576px) ── -->
        <div class="d-flex d-sm-none align-items-center justify-content-between w-100 gap-2">
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

            {{-- Current Page Badge --}}
            <span class="badge bg-white text-navy border px-2.5 py-1.5 font-monospace shadow-xs" style="font-size: 0.76rem; border-radius: 8px;">
                <span class="text-primary fw-bold">{{ $paginator->currentPage() }}</span> / <span class="text-muted">{{ $paginator->lastPage() }}</span>
            </span>

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

        <!-- ── TABLET & DESKTOP VIEW (>= 576px) ── -->
        <div class="d-none d-sm-flex align-items-center justify-content-end w-100">
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true" title="Halaman Sebelumnya">
                            <i class="bi bi-chevron-left" style="font-size: 0.72rem;"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Halaman Sebelumnya">
                            <i class="bi bi-chevron-left" style="font-size: 0.72rem;"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Halaman Berikutnya">
                            <i class="bi bi-chevron-right" style="font-size: 0.72rem;"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true" title="Halaman Berikutnya">
                            <i class="bi bi-chevron-right" style="font-size: 0.72rem;"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
