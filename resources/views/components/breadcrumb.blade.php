@props(['items' => []])

<nav aria-label="breadcrumb" class="cjp-breadcrumb-nav mb-2.5">
    <ol class="cjp-breadcrumb-list">
        <li class="cjp-breadcrumb-item">
            <a href="{{ route('dashboard') }}" class="cjp-breadcrumb-link">
                <span class="cjp-breadcrumb-icon">
                    <i class="bi bi-house-door-fill"></i>
                </span>
                <span>Dashboard</span>
            </a>
        </li>
        @foreach($items as $label => $url)
            <li class="cjp-breadcrumb-sep-item" aria-hidden="true">
                <i class="bi bi-chevron-right cjp-breadcrumb-chevron"></i>
            </li>
            @if($loop->last || is_null($url))
                <li class="cjp-breadcrumb-item active" aria-current="page">
                    <span class="cjp-breadcrumb-current">{{ $label }}</span>
                </li>
            @else
                <li class="cjp-breadcrumb-item">
                    <a href="{{ $url }}" class="cjp-breadcrumb-link">{{ $label }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
