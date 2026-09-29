<style>
    /* ═══════════════════════════════════════════════════════════════════
       WHATSAPP CHAT-STYLE TIMELINE KRONOLOGIS
       ═══════════════════════════════════════════════════════════════════ */
    /* ── FULLSCREEN & MINI ANIMATIONS ── */
    @keyframes waFullscreenIn {
        0% {
            opacity: 0;
            transform: scale(0.92) translateY(18px);
            border-radius: 20px;
        }
        60% {
            opacity: 1;
            transform: scale(1.008) translateY(-2px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
            border-radius: 0;
        }
    }

    @keyframes waFullscreenOut {
        0% {
            opacity: 1;
            transform: scale(1) translateY(0);
            border-radius: 0;
        }
        100% {
            opacity: 0;
            transform: scale(0.92) translateY(18px);
            border-radius: 18px;
        }
    }

    @keyframes waMiniIn {
        0% {
            opacity: 0.5;
            transform: scale(0.96) translateY(-6px);
        }
        70% {
            transform: scale(1.01) translateY(1px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .wa-chat-container {
        background-color: #efeae2;
        background-image: url("{{ asset('assets/chat-bg-whatsapp.png') }}?v={{ @filemtime(public_path('assets/chat-bg-whatsapp.png')) ?: time() }}");
        background-size: 380px auto;
        background-repeat: repeat;
        background-attachment: local;
        border-radius: var(--neu-radius, 18px);
        border: 1px solid #dcdfd8;
        overflow: hidden;
        box-shadow: 0 4px 24px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        transition: border-radius 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }

    .wa-chat-container.wa-mini-returning {
        animation: waMiniIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* ── FULLSCREEN MODE (TRUE 100% FULLSCREEN FIX WITH ANIMATION) ── */
    .wa-chat-container.wa-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 9999999 !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        background-color: #efeae2 !important;
        background-image: url("{{ asset('assets/chat-bg-whatsapp.png') }}?v={{ @filemtime(public_path('assets/chat-bg-whatsapp.png')) ?: time() }}") !important;
        background-size: 380px auto !important;
        background-repeat: repeat !important;
        background-attachment: local !important;
        margin: 0 !important;
        animation: waFullscreenIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        will-change: transform, opacity;
    }

    .wa-chat-container.wa-fullscreen.wa-fullscreen-closing {
        animation: waFullscreenOut 0.2s cubic-bezier(0.4, 0, 1, 1) forwards !important;
        pointer-events: none;
    }

    .wa-chat-container.wa-fullscreen .wa-chat-header {
        position: relative !important;
        top: 0 !important;
        z-index: 10 !important;
        background: linear-gradient(135deg, #07152b 0%, #0d2552 50%, #163688 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 4px 18px rgba(7, 21, 43, 0.25) !important;
        flex-shrink: 0 !important;
        padding-top: max(0.65rem, env(safe-area-inset-top, 0px)) !important;
    }

    .wa-chat-container.wa-fullscreen #timelineWrapper {
        flex: 1 1 0 !important;
        min-height: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    .wa-chat-container.wa-fullscreen .wa-chat-stream {
        flex: 1 1 0 !important;
        min-height: 0 !important;
        max-height: none !important;
        height: auto !important;
        overflow-y: auto !important;
    }

    .wa-chat-container.wa-fullscreen .wa-quick-replies-wrapper {
        padding: 0.45rem 1.25rem 0.25rem 1.25rem !important;
        max-width: 980px;
        margin: 0 auto !important;
        width: 100%;
    }

    .wa-chat-container.wa-fullscreen .wa-chat-input-bar {
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        padding: 0.55rem 1.25rem max(1.35rem, calc(1.1rem + env(safe-area-inset-bottom, 0px))) 1.25rem !important;
        max-width: 980px;
        margin: 0 auto !important;
        width: 100%;
    }

    .wa-chat-container.wa-fullscreen .wa-floating-input-pill {
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.12), 0 2px 6px rgba(15, 23, 42, 0.08) !important;
    }

    .wa-chat-container.wa-fullscreen .wa-send-btn {
        box-shadow: 0 4px 14px rgba(44, 127, 255, 0.35) !important;
    }

    .wa-chat-container.wa-fullscreen .wa-scroll-bottom-btn {
        bottom: 125px !important;
        right: max(20px, calc((100vw - 980px) / 2 + 20px)) !important;
    }

    .wa-chat-container.wa-fullscreen:not(:has(.wa-quick-replies-wrapper)) .wa-scroll-bottom-btn {
        bottom: 85px !important;
    }

    @media (max-width: 768px) {
        .wa-chat-container.wa-fullscreen .wa-chat-input-bar {
            padding: 0.45rem 0.75rem max(1.2rem, calc(0.95rem + env(safe-area-inset-bottom, 0px))) 0.75rem !important;
        }
        .wa-chat-container.wa-fullscreen .wa-quick-replies-wrapper {
            padding: 0.3rem 0.75rem 0.2rem 0.75rem !important;
        }
    }

    /* Sembunyikan semua elemen lain saat fullscreen aktif */
    body.wa-chat-fullscreen-active .app-topbar,
    body.wa-chat-fullscreen-active header,
    body.wa-chat-fullscreen-active .mobile-bottom-nav,
    body.wa-chat-fullscreen-active .app-sidebar,
    body.wa-chat-fullscreen-active .app-footer,
    body.wa-chat-fullscreen-active footer,
    body.wa-chat-fullscreen-active .card-header,
    body.wa-chat-fullscreen-active #tiketTab,
    body.wa-chat-fullscreen-active .nav-tabs-mobile,
    body.wa-chat-fullscreen-active .sidebar-overlay,
    body.wa-chat-fullscreen-active #pwaInstallBanner,
    body.wa-chat-fullscreen-active #webPushPromptBanner,
    body.wa-chat-fullscreen-active .toast-container {
        display: none !important;
    }

    /* Reset body & wrapper saat fullscreen agar tidak terpotong oleh zoom / transform */
    body.wa-chat-fullscreen-active {
        zoom: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
        width: 100% !important;
        height: 100% !important;
    }

    body.wa-chat-fullscreen-active .app-wrapper,
    body.wa-chat-fullscreen-active .app-main,
    body.wa-chat-fullscreen-active .app-content {
        transform: none !important;
        filter: none !important;
        perspective: none !important;
        contain: none !important;
        zoom: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Tombol fullscreen */
    .wa-fullscreen-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        position: relative;
        z-index: 2;
    }

    .wa-fullscreen-btn:hover {
        background: rgba(44, 127, 255, 0.4);
        color: #ffffff;
        border-color: rgba(44, 127, 255, 0.7);
        box-shadow: 0 0 14px rgba(44, 127, 255, 0.5);
        transform: scale(1.08);
    }

    .wa-fullscreen-btn:active {
        transform: scale(0.93);
    }


    .wa-chat-header {
        background: linear-gradient(135deg, #07152b 0%, #0d2552 50%, #163688 100%);
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 18px rgba(7, 21, 43, 0.18);
        position: relative;
        z-index: 6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        overflow: hidden;
    }

    .wa-chat-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 16px 16px;
        pointer-events: none;
        opacity: 0.55;
    }

    .wa-header-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(44, 127, 255, 0.45), 0 0 0 2px rgba(255, 255, 255, 0.15);
        position: relative;
        z-index: 2;
    }

    .wa-header-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: -0.2px;
        position: relative;
        z-index: 2;
    }

    .wa-header-meta {
        font-size: 0.73rem;
        color: rgba(219, 234, 254, 0.85);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        z-index: 2;
    }

    .wa-header-meta strong {
        color: #38bdf8;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .wa-pulse-dot {
        width: 6.5px;
        height: 6.5px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        margin-right: 4px;
        box-shadow: 0 0 8px #22c55e;
        animation: waPulse 1.8s infinite;
    }

    @keyframes waPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.8); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* Matikan smooth scroll bawaan browser agar chat stream instan berada di posisi bawah */
    html, body {
        scroll-behavior: auto !important;
    }

    #timelineWrapper {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    .card:has(#tiketTabContent) {
        margin-bottom: 0.35rem !important;
        padding-bottom: 0 !important;
    }

    .card:has(#tiketTabContent) .card-body {
        padding: 0.35rem 0.5rem 0.25rem 0.5rem !important;
    }

    .app-content:has(#tiketTabContent) {
        padding-bottom: 0.35rem !important;
    }

    .tab-pane:not(#kronologis-pane) {
        padding: 0.85rem 0.65rem;
    }

    .wa-chat-stream {
        padding: 0.85rem 0.85rem 0.65rem 0.85rem !important;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        height: clamp(380px, calc(100dvh - 300px), calc(100vh - 210px));
        min-height: 360px;
        max-height: calc(100dvh - 200px);
        overflow-y: auto;
        overflow-x: hidden;
        overflow-anchor: auto !important;
        overscroll-behavior-y: contain;
    }

    /* Kunci scroll anchoring HANYA pada anchor bawah agar rendering awal langsung berada di chat terbaru tanpa delay/lompatan */
    .wa-msg-row,
    .wa-message-row,
    .wa-date-divider,
    .wa-media-card,
    .wa-location-card,
    .wa-quote-box {
        overflow-anchor: none !important;
    }

    #waStreamBottomAnchor {
        overflow-anchor: auto !important;
        height: 1px;
        width: 100%;
        flex-shrink: 0;
        pointer-events: none;
    }

    .wa-chat-stream::-webkit-scrollbar {
        width: 6px;
    }
    .wa-chat-stream::-webkit-scrollbar-track {
        background: transparent;
    }
    .wa-chat-stream::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 6px;
    }

    /* Date Divider Pill */
    .wa-date-divider {
        display: flex;
        justify-content: center;
        margin: 0.85rem 0 0.5rem 0;
        position: relative;
        z-index: 2;
    }

    .wa-date-chip {
        background: #ffffff;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.28rem 0.9rem;
        border-radius: 999px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
        border: 1px solid #dcdfd8;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.15px;
        user-select: none;
    }

    /* ─── CHAT MESSAGE SEND & RECEIVE KEYFRAME ANIMATIONS ─── */
    @keyframes waMsgSendIn {
        0% {
            opacity: 0;
            transform: scale(0.9) translateY(16px) translateX(12px);
        }
        65% {
            opacity: 1;
            transform: scale(1.015) translateY(-2px) translateX(0);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0) translateX(0);
        }
    }

    @keyframes waMsgReceiveIn {
        0% {
            opacity: 0;
            transform: scale(0.9) translateY(16px) translateX(-12px);
        }
        65% {
            opacity: 1;
            transform: scale(1.015) translateY(-2px) translateX(0);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0) translateX(0);
        }
    }

    .wa-msg-anim-send {
        animation: waMsgSendIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        transform-origin: bottom right;
        will-change: transform, opacity;
    }

    .wa-msg-anim-receive {
        animation: waMsgReceiveIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        transform-origin: bottom left;
        will-change: transform, opacity;
    }

    /* Message Row & Bubble */
    .wa-msg-row,
    .wa-message-row {
        display: flex;
        gap: 0.45rem;
        align-items: flex-end;
        width: 100%;
        scroll-margin-bottom: 110px;
        scroll-margin-top: 90px;
    }

    .wa-msg-incoming,
    .wa-row-incoming {
        justify-content: flex-start !important;
    }

    .wa-msg-outgoing,
    .wa-row-outgoing {
        justify-content: flex-end !important;
    }

    .wa-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        font-weight: 700;
        color: #ffffff;
        flex-shrink: 0;
        margin-bottom: 2px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        letter-spacing: 0.5px;
        overflow: hidden;
    }

    .wa-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    .wa-avatar-me {
        background: linear-gradient(135deg, #2C7FFF, #1b39da) !important;
    }

    .wa-bubble {
        max-width: 82%;
        min-width: 180px;
        padding: 0.55rem 0.75rem 0.35rem;
        position: relative;
        word-wrap: break-word;
    }

    .wa-bubble-incoming {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px 16px 16px 4px;
        box-shadow: 0 1.5px 4px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
    }

    .wa-bubble-outgoing {
        background: #e0edfe;
        border: 1px solid rgba(37, 99, 235, 0.12);
        border-radius: 16px 16px 4px 16px;
        box-shadow: 0 1.5px 4px rgba(37, 99, 235, 0.06), 0 1px 2px rgba(37, 99, 235, 0.03);
        margin-left: auto;
    }

    .wa-bubble-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
    }

    .wa-sender-info {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 1;
        min-width: 0;
    }

    .wa-sender-name {
        font-size: 0.76rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .wa-role-pill {
        font-size: 0.62rem;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 999px;
        line-height: 1.4;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.2px;
        white-space: nowrap;
    }

    .wa-bubble-outgoing .wa-role-pill {
        background: rgba(44, 127, 255, 0.15);
        color: #1b39da;
        border: 1px solid rgba(27, 57, 218, 0.25);
    }

    .wa-bubble-incoming .wa-role-pill {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .wa-bubble-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }

    .wa-bubble-menu-wrapper {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
    }

    .wa-msg-menu-btn {
        background: transparent !important;
        border: none !important;
        color: #334155 !important;
        width: auto;
        min-width: 20px;
        height: auto;
        padding: 2px 4px;
        border-radius: 4px;
        cursor: pointer;
        line-height: 1;
        transition: all 0.18s ease;
        opacity: 1 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: none !important;
        -webkit-tap-highlight-color: transparent;
    }

    .wa-msg-menu-btn i {
        font-size: 0.85rem;
        display: inline-block;
        line-height: 1;
    }

    .wa-bubble-outgoing .wa-msg-menu-btn {
        background: transparent !important;
        border: none !important;
        color: #1e40af !important;
    }

    .wa-bubble-incoming .wa-msg-menu-btn {
        background: transparent !important;
        border: none !important;
        color: #475569 !important;
    }

    .wa-msg-menu-btn:hover,
    .wa-msg-menu-btn:focus,
    .wa-msg-menu-btn[aria-expanded="true"] {
        opacity: 1 !important;
        color: #0f172a !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transform: scale(1.08);
    }

    .wa-bubble-outgoing .wa-msg-menu-btn:hover,
    .wa-bubble-outgoing .wa-msg-menu-btn:focus,
    .wa-bubble-outgoing .wa-msg-menu-btn[aria-expanded="true"] {
        background: transparent !important;
        border: none !important;
        color: #172554 !important;
        box-shadow: none !important;
    }

    .wa-msg-dropdown-menu {
        min-width: 165px;
        border-radius: 12px !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        padding: 0.35rem !important;
        font-size: 0.82rem;
        z-index: 1050 !important;
    }

    .wa-msg-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.45rem 0.65rem;
        border-radius: 8px;
        color: #334155;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .wa-msg-dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .wa-msg-dropdown-item.text-danger:hover {
        background: #fee2e2;
        color: #dc2626 !important;
    }

    /* Reply Quoted Box (inside chat bubble & above chat input) */
    .wa-reply-preview-bar {
        background: #f8fafc;
        border-left: 4px solid #2C7FFF;
        border-radius: 6px;
        padding: 0.35rem 0.65rem;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        animation: fadeInDown 0.2s ease;
    }

    .wa-reply-preview-content {
        flex-grow: 1;
        overflow: hidden;
    }

    .wa-reply-preview-sender {
        font-size: 0.72rem;
        font-weight: 700;
        color: #2C7FFF;
        line-height: 1.2;
    }

    .wa-reply-preview-text {
        font-size: 0.73rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .wa-quote-box {
        background: rgba(0, 0, 0, 0.04);
        border-left: 3.5px solid #0284c7;
        border-radius: 6px;
        padding: 0.3rem 0.55rem;
        margin-bottom: 0.4rem;
        font-size: 0.75rem;
    }

    .wa-bubble-outgoing .wa-quote-box {
        background: rgba(44, 127, 255, 0.08);
        border-left-color: #1b39da;
    }

    .wa-quote-sender {
        font-weight: 700;
        font-size: 0.72rem;
        color: #0284c7;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .wa-bubble-outgoing .wa-quote-sender {
        color: #1b39da;
    }

    .wa-quote-text {
        color: #475569;
        font-style: normal;
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .wa-copy-toast {
        position: fixed;
        bottom: 85px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(8px);
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .wa-copy-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* WhatsApp-style Kategori Tags */
    .wa-kategori-tag {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: 3.5px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        line-height: 1.3;
    }
    .tag-izin     { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .tag-otdr     { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .tag-tracing  { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }
    .tag-material { background: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; }
    .tag-jointing { background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; }
    .tag-link_up, .tag-linkup { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .tag-selesai  { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .tag-lain     { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .wa-kategori-sublabel {
        font-size: 0.68rem;
        color: #64748b;
        margin-bottom: 0.35rem;
        font-weight: 500;
    }

    .wa-del-btn {
        background: none;
        border: none;
        color: #94a3b8;
        padding: 0 2px;
        font-size: 0.78rem;
        cursor: pointer;
        line-height: 1;
        transition: color 0.15s ease;
    }
    .wa-del-btn:hover {
        color: #ef4444;
    }

    /* Message Body Text */
    .wa-msg-text {
        font-size: 0.835rem;
        color: #111b21;
        line-height: 1.35;
        word-break: break-word;
    }

    /* Photo Attachment Card */
    .wa-media-card {
        margin-top: 0.45rem;
        border-radius: 10px;
        overflow: hidden;
        max-width: 280px;
        width: 100%;
        height: 158px;
        cursor: pointer;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #f1f5f9;
        aspect-ratio: 16 / 9;
        contain: size layout;
    }
    .wa-media-img {
        width: 100%;
        height: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        display: block;
        transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .wa-media-card:hover .wa-media-img {
        transform: scale(1.03);
        opacity: 0.92;
    }
    .wa-media-badge {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 0.67rem;
        font-weight: 500;
        padding: 2px 7px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        pointer-events: none;
    }

    /* Video Attachment Card */
    .wa-media-card.wa-video-card {
        cursor: default;
        background: #0f172a;
        max-width: 320px;
        min-height: 140px;
        border: 1px solid rgba(15, 23, 42, 0.2);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
    }
    .wa-video-player {
        width: 100%;
        max-height: 240px;
        min-height: 140px;
        border-radius: 9px;
        display: block;
        background: #000000;
        outline: none;
    }

    /* Shared Location Card */
    .wa-location-card {
        margin-top: 0.45rem;
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        padding: 0.45rem 0.65rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        backdrop-filter: blur(4px);
    }
    .wa-loc-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fee2e2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .wa-loc-info {
        flex-grow: 1;
        overflow: hidden;
    }
    .wa-loc-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    .wa-loc-coords {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.68rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-loc-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0284c7;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .wa-loc-btn:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    /* Bubble Footer */
    .wa-bubble-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.25rem;
        margin-top: 0.2rem;
    }
    .wa-time {
        font-size: 0.65rem;
        color: #667781;
    }
    .wa-status-icon {
        line-height: 1;
        display: inline-flex;
        align-items: center;
        transition: color 0.3s ease, transform 0.2s ease;
    }
    .wa-status-pending {
        font-size: 0.72rem !important;
        color: #8696a0 !important;
        animation: waClockPulse 1.4s infinite ease-in-out;
    }
    .wa-status-sent {
        font-size: 0.85rem !important;
        color: #8696a0 !important; /* Ceklis 2 abu */
    }
    .wa-status-read,
    .wa-double-check {
        font-size: 0.85rem !important;
        color: #53bdeb !important; /* Ceklis 2 biru */
    }
    @keyframes waClockPulse {
        0%, 100% { opacity: 0.6; transform: scale(0.95); }
        50% { opacity: 1; transform: scale(1.05); }
    }

    /* ═══════════════════════════════════════════════════════════════════
       WHATSAPP FLOATING INPUT BAR (NATIVE MOBILE & DESKTOP STYLE)
       ═══════════════════════════════════════════════════════════════════ */
    .wa-chat-input-bar {
        background: transparent !important;
        padding: 0.35rem 0.75rem calc(0.4rem + env(safe-area-inset-bottom, 0px)) 0.75rem !important;
        border-top: none !important;
        display: flex;
        align-items: flex-end;
        gap: 0.55rem;
        position: relative;
        z-index: 15;
    }

    /* Floating White Capsule Pill (Enclosing Paperclip + Textarea + Attachments) */
    .wa-floating-input-pill {
        flex: 1 1 auto;
        min-width: 0;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 1px 4px rgba(11, 20, 26, 0.12), 0 2px 8px rgba(11, 20, 26, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: flex-end;
        padding: 3px 10px 3px 6px;
        min-height: 44px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .wa-floating-input-pill:focus-within {
        box-shadow: 0 2px 8px rgba(11, 20, 26, 0.16), 0 0 0 2px rgba(44, 127, 255, 0.18);
        border-color: rgba(44, 127, 255, 0.35);
    }

    .wa-attach-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: transparent;
        border: none;
        color: #54656f;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        margin-bottom: 2px;
        transition: all 0.18s ease;
    }

    .wa-attach-btn:hover {
        background: rgba(0, 0, 0, 0.06);
        color: #2C7FFF;
        transform: rotate(-12deg);
    }

    .wa-attach-menu {
        border-radius: 16px !important;
        padding: 0.4rem 0 !important;
        min-width: 240px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        margin-bottom: 8px !important;
    }

    .wa-attach-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .wa-input-wrapper {
        flex-grow: 1;
        min-width: 0;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 2px 4px 2px 4px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 38px;
    }

    .wa-input-wrapper:focus-within {
        border: none !important;
        box-shadow: none !important;
    }

    .wa-chat-textarea {
        border: none;
        outline: none;
        background: transparent;
        width: 100%;
        resize: none;
        font-size: 0.88rem;
        color: #0f172a;
        max-height: 110px;
        line-height: 1.35;
        padding: 5px 2px 3px 2px;
    }

    .wa-chat-textarea::placeholder {
        color: #8696a0;
        font-size: 0.85rem;
    }

    .wa-attach-preview-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.25rem;
        padding-top: 0.25rem;
        border-top: 1px dashed #e2e8f0;
    }

    /* Standalone Circular Floating Send Button (FAB) */
    .wa-send-btn {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(44, 127, 255, 0.4), 0 1px 3px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        margin-bottom: 0;
    }

    .wa-send-btn:hover {
        background: linear-gradient(135deg, #1b39da 0%, #122894 100%);
        transform: scale(1.06);
        box-shadow: 0 4px 14px rgba(44, 127, 255, 0.5);
    }

    .wa-send-btn:active {
        transform: scale(0.94);
    }

    /* WhatsApp-style Voice Note to Text (Speech Recognition) */
    .wa-voice-btn {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 50%;
        background: transparent;
        border: none;
        color: #54656f;
        font-size: 1.18rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        margin-bottom: 2px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        -webkit-tap-highlight-color: transparent;
        position: relative;
    }

    .wa-voice-btn:hover {
        background: rgba(0, 0, 0, 0.06);
        color: #2C7FFF;
        transform: scale(1.08);
    }

    .wa-voice-btn.recording {
        background: #ef4444 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: waVoicePulse 1.3s infinite cubic-bezier(0.66, 0, 0, 1);
        transform: scale(1.08);
    }

    @keyframes waVoicePulse {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    .wa-voice-listening-toast {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 20;
        white-space: nowrap;
        pointer-events: none;
        animation: fadeInDown 0.2s ease;
    }

    .wa-voice-wave-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #ef4444;
        animation: pulseDot 1s infinite;
    }

    @media (max-width: 768px) {
        .wa-chat-input-bar {
            padding: 0.45rem 0.6rem calc(0.45rem + env(safe-area-inset-bottom, 0px)) 0.6rem !important;
            gap: 0.45rem !important;
        }
        .wa-floating-input-pill {
            padding: 2px 8px 2px 4px;
            min-height: 42px;
        }
        .wa-send-btn {
            width: 42px;
            height: 42px;
            min-width: 42px;
            font-size: 1rem;
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       GOJEK-STYLE QUICK REPLY CHIPS (REKOMENDASI CHAT CEPAT)
       ═══════════════════════════════════════════════════════════════════ */
    .wa-quick-replies-wrapper {
        padding: 0.35rem 0.85rem 0.2rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
        position: relative;
        z-index: 12;
    }
    .wa-quick-replies-wrapper::-webkit-scrollbar {
        display: none;
    }

    .wa-quick-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.42rem;
        padding: 0.35rem 0.82rem;
        background: #ffffff;
        border: 1px solid rgba(203, 213, 225, 0.9);
        border-radius: 50rem;
        font-size: 0.76rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        text-decoration: none;
        line-height: 1.2;
    }

    .wa-quick-chip:hover {
        background: #f8fafc;
        border-color: #2C7FFF;
        color: #1b39da;
        transform: translateY(-1.5px);
        box-shadow: 0 4px 10px rgba(44, 127, 255, 0.16);
    }

    .wa-quick-chip:active {
        transform: scale(0.96);
        background: #f1f5f9;
    }

    .wa-quick-chip-icon {
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .wa-quick-replies-wrapper {
            padding: 0.25rem 0.6rem 0.15rem 0.6rem;
            gap: 0.35rem;
        }
        .wa-quick-chip {
            padding: 0.28rem 0.65rem;
            font-size: 0.72rem;
        }
    }

    /* Floating Scroll to Bottom Button (WhatsApp Style) */
    .wa-chat-container {
        position: relative;
    }

    .wa-scroll-bottom-btn {
        position: absolute;
        bottom: 116px;
        right: 20px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(203, 213, 225, 0.8);
        color: #1b39da;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18);
        cursor: pointer;
        z-index: 20;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .wa-chat-container:not(:has(.wa-quick-replies-wrapper)) .wa-scroll-bottom-btn {
        bottom: 74px;
    }

    .wa-chat-container:has(.wa-closed-notice) .wa-scroll-bottom-btn {
        bottom: 60px;
    }

    .wa-scroll-bottom-btn:hover {
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(44, 127, 255, 0.45);
    }

    .wa-scroll-bottom-btn:active {
        transform: scale(0.92);
    }

    /* WhatsApp Closed State Notice */
    .wa-closed-notice {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        text-align: center;
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    /* ── Penanganan Core & JC Mode Selector ── */
    /* ── Penanganan Core & JC Mode Selector ── */
    .penanganan-mode-card {
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 14px;
        cursor: pointer;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .penanganan-mode-card .penanganan-mode-inner {
        background: #f8fafc;
        border: 2px solid #e2e8f0 !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 14px;
        padding: 0.85rem 1rem;
    }
    .penanganan-mode-card:hover .penanganan-mode-inner {
        border-color: #cbd5e1 !important;
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .penanganan-mode-card:active .penanganan-mode-inner {
        transform: scale(0.98);
    }
    .penanganan-mode-card.is-selected .penanganan-mode-inner {
        background: #ffffff;
        border-color: #6366f1 !important;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.12);
    }
    .penanganan-mode-card#cardModeManuver.is-selected .penanganan-mode-inner {
        background: #ffffff;
        border-color: #a855f7 !important;
        box-shadow: 0 4px 16px rgba(168, 85, 247, 0.12);
    }
    .penanganan-mode-icon-box {
        width: 40px;
        height: 40px;
        font-size: 1.15rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .penanganan-mode-card.is-selected .penanganan-mode-icon-box {
        transform: scale(1.05);
    }
    .bg-indigo-subtle {
        background-color: rgba(99, 102, 241, 0.12) !important;
    }
    .text-indigo {
        color: #4f46e5 !important;
    }
    .bg-purple-subtle {
        background-color: rgba(168, 85, 247, 0.12) !important;
    }
    .text-purple {
        color: #9333ea !important;
    }

    @media (max-width: 767.98px) {
        .penanganan-mode-card .penanganan-mode-inner {
            padding: 0.75rem 0.85rem !important;
            border-radius: 12px;
        }
        .penanganan-mode-icon-box {
            width: 36px !important;
            height: 36px !important;
            font-size: 1rem !important;
        }
        .penanganan-title-text {
            font-size: 0.88rem !important;
        }
        .penanganan-desc-text {
            font-size: 0.74rem !important;
            line-height: 1.35 !important;
        }
    }

    /* Empty state */
    .wa-empty-icon i {
        font-size: 2.8rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 767.98px) {
        .card:has(#tiketTabContent) {
            margin-bottom: 0.35rem !important;
            padding-bottom: 0 !important;
        }
        .card:has(#tiketTabContent) .card-body {
            padding-bottom: 0.2rem !important;
        }
        .wa-chat-container {
            border-radius: 14px;
            margin-bottom: 0 !important;
        }
        .app-content {
            padding-bottom: 0.35rem !important;
        }
        body {
            padding-bottom: calc(var(--bottom-nav-height, 86px) + 6px) !important;
        }
        .wa-chat-stream {
            height: clamp(260px, calc(100dvh - 355px), 600px) !important;
            min-height: 240px !important;
            max-height: calc(100dvh - 330px) !important;
            padding: 0.75rem 0.65rem 0.65rem 0.65rem !important;
            gap: 0.6rem;
            scroll-behavior: auto !important;
            overflow-anchor: auto !important;
            overscroll-behavior-y: contain;
        }
        .wa-scroll-bottom-btn {
            bottom: 66px;
            right: 14px;
        }
    }

    @media (max-width: 575.98px) {
        .wa-chat-header {
            padding: 0.65rem 0.85rem;
            gap: 0.5rem;
        }
        .wa-header-avatar {
            width: 34px;
            height: 34px;
            font-size: 0.95rem;
        }
        .wa-header-title {
            font-size: 0.82rem;
        }
        .wa-header-meta {
            font-size: 0.68rem;
        }
        .wa-header-add-btn {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            padding: 0 !important;
            border-radius: 50% !important;
        }
        .wa-bubble {
            max-width: 90%;
            min-width: 160px;
            padding: 0.5rem 0.65rem 0.3rem;
        }
        .wa-avatar {
            width: 26px;
            height: 26px;
            font-size: 0.62rem;
        }
        .wa-media-card {
            max-width: 100%;
        }
    }

    /* ─── NEW KRONOLOGIS CHAT BUBBLE HIGHLIGHT PULSE ─── */
    @keyframes waBubblePulseIncoming {
        0% {
            background-color: #ebf3ff !important;
            box-shadow: 0 0 0 3px rgba(44, 127, 255, 0.3), 0 4px 12px rgba(15, 23, 42, 0.08) !important;
        }
        50% {
            background-color: #f4f8ff !important;
            box-shadow: 0 0 0 1.5px rgba(44, 127, 255, 0.15), 0 2px 6px rgba(15, 23, 42, 0.05) !important;
        }
        100% {
            background-color: #ffffff !important;
            box-shadow: 0 1.5px 4px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        }
    }

    @keyframes waBubblePulseOutgoing {
        0% {
            background-color: #d2e6fe !important;
            box-shadow: 0 0 0 3px rgba(44, 127, 255, 0.3), 0 4px 12px rgba(37, 99, 235, 0.1) !important;
        }
        50% {
            background-color: #dbeafe !important;
            box-shadow: 0 0 0 1.5px rgba(44, 127, 255, 0.15), 0 2px 6px rgba(37, 99, 235, 0.06) !important;
        }
        100% {
            background-color: #e0edfe !important;
            box-shadow: 0 1.5px 4px rgba(37, 99, 235, 0.06), 0 1px 2px rgba(37, 99, 235, 0.03) !important;
        }
    }

    .wa-bubble-new-highlight .wa-bubble-incoming,
    .wa-bubble-new-highlight.wa-bubble-incoming {
        animation: waBubblePulseIncoming 2s ease forwards !important;
    }

    .wa-bubble-new-highlight .wa-bubble-outgoing,
    .wa-bubble-new-highlight.wa-bubble-outgoing {
        animation: waBubblePulseOutgoing 2s ease forwards !important;
    }

    /* ── MODALS & PHOTO LIGHTBOX (ON TOP OF FULLSCREEN CHAT Z-INDEX 99999) ── */
    .modal {
        z-index: 100005 !important;
    }
    .modal-backdrop {
        z-index: 100000 !important;
    }
    .photo-lightbox-modal {
        z-index: 100010 !important;
    }
    .photo-lightbox-modal .modal-dialog {
        max-width: 95vw;
        margin: 1rem auto;
        z-index: 100015 !important;
    }
    .photo-lightbox-content {
        background: rgba(11, 20, 36, 0.96) !important;
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 20px !important;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75);
    }
    .photo-lightbox-header {
        background: rgba(15, 23, 42, 0.85);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.75rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .btn-lightbox-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        transition: all 0.2s;
        text-decoration: none !important;
    }
    .btn-lightbox-action:hover {
        background: rgba(44, 127, 255, 0.4);
        border-color: rgba(44, 127, 255, 0.7);
        transform: scale(1.05);
    }
    .btn-lightbox-close {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.22);
        border: 1px solid rgba(239, 68, 68, 0.4);
        color: #fca5a5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-lightbox-close:hover {
        background: rgba(239, 68, 68, 0.5);
        color: #ffffff;
        transform: scale(1.05);
    }
    .photo-lightbox-body {
        padding: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #020617;
        min-height: 250px;
    }
    .photo-lightbox-img {
        max-height: 80vh;
        max-width: 100%;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.6);
    }

    /* ═══════════════════════════════════════════════════════════════════
       WHATSAPP-STYLE @MENTION USER AUTOCOMPLETE DROPDOWN
       ═══════════════════════════════════════════════════════════════════ */
    .wa-mention-dropdown {
        position: absolute;
        bottom: 100%;
        left: 0;
        width: min(320px, calc(100vw - 40px));
        max-height: 240px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(203, 213, 225, 0.9);
        border-radius: 14px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.2), 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 8px;
        z-index: 1050;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: waMentionSlideUp 0.16s ease-out;
    }

    @keyframes waMentionSlideUp {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .wa-mention-header {
        padding: 0.45rem 0.85rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .wa-mention-list {
        overflow-y: auto;
        max-height: 195px;
        padding: 0.25rem 0;
    }

    .wa-mention-item {
        padding: 0.45rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        cursor: pointer;
        transition: background 0.12s ease;
        user-select: none;
    }

    .wa-mention-item:hover,
    .wa-mention-item.active {
        background: #e0f2fe;
    }

    .wa-mention-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.08);
    }

    .wa-mention-avatar-initial {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .wa-mention-name {
        font-size: 0.84rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.25;
    }

    .wa-mention-role {
        font-size: 0.68rem;
        color: #64748b;
    }

    .wa-mention-tag-highlight {
        color: #0284c7;
        font-weight: 700;
        background: rgba(2, 132, 199, 0.12);
        padding: 1px 5px;
        border-radius: 4px;
        display: inline-block;
        margin: 0 1px;
    }

    /* ═══════════════════════════════════════════════════════════════════
       ELEGANT TICKET DETAIL HERO & METRIC TILES
       ═══════════════════════════════════════════════════════════════════ */
    .tiket-metric-tile {
        background: var(--neu-surface, #e6ecf4);
        border: 1px solid var(--neu-border, rgba(255,255,255,0.8));
        border-radius: var(--neu-radius-sm, 12px);
        box-shadow: var(--neu-flat-xs, 2px 2px 6px #c2ccd9, -2px -2px 6px #ffffff);
        padding: 0.75rem 0.85rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .tiket-metric-tile:hover {
        transform: translateY(-1px);
        box-shadow: var(--neu-flat-sm, 4px 4px 10px #c2ccd9, -4px -4px 10px #ffffff);
    }

    .tiket-metric-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        flex-shrink: 0;
        box-shadow: var(--neu-flat-xs, 2px 2px 4px #c2ccd9, -2px -2px 4px #ffffff);
    }

    .bg-teal-subtle { background-color: #ccfbf1 !important; color: #0f766e !important; }
    .bg-primary-subtle { background-color: #e0f2fe !important; color: #0284c7 !important; }
    .bg-success-subtle { background-color: #dcfce7 !important; color: #16a34a !important; }
    .bg-danger-subtle { background-color: #fee2e2 !important; color: #dc2626 !important; }

    .tiket-metric-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tiket-metric-value {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }

    .tiket-metric-unit {
        font-size: 0.7rem;
        font-weight: 500;
        color: #64748b;
    }

    .tiket-metric-sub {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 2px;
    }

    .gap-1\.5 { gap: 0.375rem !important; }
    .gap-2\.5 { gap: 0.625rem !important; }
    .mb-1\.5 { margin-bottom: 0.375rem !important; }
    .mb-2\.5 { margin-bottom: 0.625rem !important; }

    .tiket-desc-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #0d9488;
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    .tiket-desc-text {
        font-size: 0.81rem;
        color: #334155;
        line-height: 1.55;
        white-space: pre-line;
    }

    /* ── INFO STRIP (deskripsi + foto bukti side by side) ── */
    .tiket-info-strip {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    @media (min-width: 992px) {
        .tiket-info-strip {
            flex-direction: row;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .tiket-info-strip-desc {
            flex: 1 1 0;
            min-width: 0;
        }
        .tiket-info-strip-foto {
            flex: 0 0 auto;
            min-width: 220px;
            max-width: 50%;
        }
    }

    .tiket-info-strip-desc,
    .tiket-info-strip-foto {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .tiket-info-strip-desc {
        border-left: 4px solid #0d9488;
        padding: 0.7rem 0.9rem;
    }
    .tiket-info-strip-foto {
        border-left: 4px solid #ef4444;
        background: #fff8f8;
        border-color: #fecaca;
    }

    .tiket-info-strip-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.45rem;
    }
    .tiket-info-strip-label i {
        color: #0d9488;
        font-size: 0.82rem;
    }
    .tiket-info-strip-label--red i {
        color: #dc2626;
    }
    .tiket-info-strip-foto .tiket-info-strip-label {
        padding: 0.55rem 0.75rem 0;
        margin-bottom: 0;
        border-bottom: 1px solid #fecaca;
        padding-bottom: 0.45rem;
    }

    .tiket-foto-bukti-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        padding: 0.6rem 0.75rem;
        align-items: flex-start;
    }

    /* ── THUMBNAIL ── */
    .tiket-foto-thumb {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        background: #0f172a;
        flex-shrink: 0;
        border: 2px solid #fecaca;
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .tiket-foto-thumb:hover {
        transform: translateY(-3px) scale(1.05);
        border-color: #ef4444;
        box-shadow: 0 6px 16px rgba(239,68,68,0.25);
    }
    .tiket-foto-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    .tiket-foto-thumb-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(transparent 50%, rgba(15,23,42,0.8) 100%);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 5px;
        color: #fff;
        font-size: 0.65rem;
        opacity: 0;
        transition: opacity 0.18s ease;
    }
    .tiket-foto-thumb:hover .tiket-foto-thumb-overlay {
        opacity: 1;
    }


    .tiket-meta-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        padding-top: 0.75rem;
        margin-top: 0.25rem;
        border-top: 1px solid #f1f5f9;
        font-size: 0.78rem;
    }


    @media (max-width: 575.98px) {
        .tiket-metric-tile {
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
        }
        .tiket-metric-value {
            font-size: 0.82rem;
        }
    }

    #mapPicker, #mapPickerTitik {
        height: 350px;
        width: 100%;
        border-radius: 8px;
    }
    #titikPerbaikanMap {
        height: 280px;
        width: 100%;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    /* ═══════════════════════════════════════════════════════════════════
       HERO HEADER ACTIONS BAR & BUTTONS (CLEAN & MODERN)
       ═══════════════════════════════════════════════════════════════════ */
    .tiket-hero-actions {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .btn-tiket-hero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        height: 36px;
        padding: 0 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 50rem;
        transition: all 0.18s ease;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid transparent;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
        cursor: pointer;
        line-height: 1;
    }

    /* 1. Default / Back / Dropdown: Frosted Glass */
    .btn-tiket-hero-ghost {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #f8fafc !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .btn-tiket-hero-ghost:hover, .btn-tiket-hero-ghost:focus {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.45) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35) !important;
    }

    /* 2. WhatsApp: Modern WhatsApp Emerald Gradient */
    .btn-tiket-hero-wa {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    .btn-tiket-hero-wa:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4) !important;
    }

    /* 3. Edit: Amber/Warning Accent */
    .btn-tiket-hero-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    .btn-tiket-hero-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4) !important;
    }

    /* 4. Closing Tiket: Solid Teal / Blue-Green Accent */
    .btn-tiket-hero-success {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .btn-tiket-hero-success:hover {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4) !important;
    }

    .btn-tiket-hero-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .btn-tiket-hero-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4) !important;
    }

    .btn-tiket-hero-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .btn-tiket-hero-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4) !important;
    }

    .btn-tiket-hero-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .btn-tiket-hero-purple:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4) !important;
    }

    /* ── UNIFIED HERO ACTION DROPDOWN MENU ── */
    .tiket-hero-dropdown-menu {
        min-width: 275px !important;
        padding: 0.5rem !important;
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.16), 0 4px 14px rgba(15, 23, 42, 0.08) !important;
        background: #ffffff !important;
        animation: heroDropdownFadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes heroDropdownFadeIn {
        from { opacity: 0; transform: translateY(6px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .tiket-hero-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.45rem 0.65rem;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.15s ease;
        text-decoration: none;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }
    .tiket-hero-dropdown-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .tiket-hero-dropdown-item.disabled,
    .tiket-hero-dropdown-item:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: transparent !important;
    }
    .tiket-dropdown-icon-box {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.92rem;
        transition: transform 0.15s ease;
    }
    .tiket-hero-dropdown-item:hover .tiket-dropdown-icon-box {
        transform: scale(1.08);
    }
    .tiket-dropdown-header-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #94a3b8;
        padding: 0.4rem 0.65rem 0.25rem 0.65rem;
    }

    /* ═══════════════════════════════════════════════════════════════════
       SLA TIMELINE STEPPER (POINT 5)
       ═══════════════════════════════════════════════════════════════════ */
    .sla-stepper-wrapper {
        width: 100%;
        overflow-x: hidden;
    }
    .sla-stepper-track {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        width: 100%;
        min-width: 0;
        position: relative;
    }
    .sla-step-item {
        flex: 1 1 0;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        padding: 0 4px;
    }
    .sla-step-node-container {
        display: flex;
        align-items: center;
        width: 100%;
        position: relative;
        justify-content: center;
    }
    .sla-step-node {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 700;
        z-index: 2;
        background: #f1f5f9;
        color: #64748b;
        border: 2px solid #cbd5e1;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }
    .sla-step-line {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #e2e8f0;
        transform: translateY(-50%);
        z-index: 1;
    }
    .sla-step-line.line-completed {
        background: #10b981;
    }
    .sla-step-line.line-active {
        background: linear-gradient(90deg, #10b981, #2563eb);
    }
    .sla-step-item.step-completed .sla-step-node {
        background: #10b981;
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    }
    .sla-step-item.step-active .sla-step-node {
        background: #2563eb;
        color: #ffffff;
        border-color: #1d4ed8;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
    }
    .sla-step-item.step-paused .sla-step-node {
        background: #f59e0b;
        color: #ffffff;
        border-color: #d97706;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
    }
    .sla-step-content {
        width: 100%;
        min-width: 0;
    }
    .sla-step-title {
        font-size: 0.78rem;
        line-height: 1.25;
        word-break: break-word;
    }
    .sla-step-timestamp {
        font-size: 0.68rem;
        line-height: 1.2;
    }
    .sla-step-badge .badge {
        font-size: 0.65rem;
    }

    @media (max-width: 767.98px) {
        .sla-step-item {
            padding: 0 2px;
        }
        .sla-step-node {
            width: 26px;
            height: 26px;
            font-size: 0.72rem;
            border-width: 1.5px;
        }
        .sla-step-line {
            height: 2px;
        }
        .sla-step-title {
            font-size: 0.66rem !important;
            line-height: 1.15;
            letter-spacing: -0.2px;
        }
        .sla-step-timestamp {
            font-size: 0.58rem !important;
            line-height: 1.1;
        }
        .sla-step-badge {
            margin-top: 2px !important;
        }
        .sla-step-badge .badge {
            font-size: 0.56rem !important;
            padding: 0.12rem 0.35rem !important;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    @media (max-width: 480px) {
        .sla-step-node {
            width: 22px;
            height: 22px;
            font-size: 0.6rem;
        }
        .sla-step-title {
            font-size: 0.6rem !important;
            line-height: 1.1;
        }
        .sla-step-timestamp {
            font-size: 0.52rem !important;
        }
        .sla-step-badge .badge {
            font-size: 0.5rem !important;
            padding: 0.1rem 0.25rem !important;
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       MANDATORY CLOSING CHECKLIST & READINESS CARDS
       ═══════════════════════════════════════════════════════════════════ */
    .closing-readiness-card {
        background: #ffffff;
        border-radius: var(--neu-radius, 16px);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .closing-readiness-header {
        padding: 0.85rem 1.15rem;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .closing-progress-bar-track {
        width: 100%;
        height: 4px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .closing-progress-bar-fill {
        height: 100%;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .closing-item-tile {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        padding: 0.75rem 0.85rem;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        height: 100%;
    }
    .closing-item-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .closing-item-tile.is-complete {
        background: linear-gradient(145deg, #f0fdf4 0%, #ecfdf5 100%);
        border-color: #a7f3d0;
    }
    .closing-item-tile.is-complete:hover {
        border-color: #34d399;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
    }
    .closing-item-tile.is-pending {
        background: #ffffff;
        border-color: #f1f5f9;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .closing-item-tile.is-pending:hover {
        background: #f8fafc;
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }
    .closing-tile-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
        transition: transform 0.2s ease;
    }
    .closing-item-tile:hover .closing-tile-icon-box {
        transform: scale(1.08);
    }
    .closing-item-tile.is-complete .closing-tile-icon-box {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    .closing-item-tile.is-pending .closing-tile-icon-box {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px dashed #cbd5e1;
    }
    .closing-tile-arrow {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: #94a3b8;
        background: rgba(241, 245, 249, 0.6);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .closing-item-tile:hover .closing-tile-arrow {
        color: #2563eb;
        background: #eff6ff;
        transform: translateX(2px);
    }
    .closing-item-tile.is-complete .closing-tile-arrow {
        color: #059669;
        background: #d1fae5;
    }

    .closing-ready-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #d1fae5 100%);
        border: 1.5px solid #86efac;
        border-radius: 14px;
        padding: 0.85rem 1.15rem;
        box-shadow: 0 4px 16px -2px rgba(16, 185, 129, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: all 0.25s ease;
    }
    .closing-ready-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        position: relative;
    }
    .closing-ready-icon::after {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 14px;
        border: 1.5px solid rgba(16, 185, 129, 0.4);
        animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulseRing {
        0%, 100% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 0.15; transform: scale(1.08); }
    }
    .btn-closing-action {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 0.55rem 1.25rem !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.1px;
        box-shadow: 0 3px 12px rgba(5, 150, 105, 0.32) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .btn-closing-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.45) !important;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
    }
    .btn-closing-action:active {
        transform: translateY(0);
    }

    @media (max-width: 575.98px) {
        .closing-readiness-header {
            padding: 0.65rem 0.85rem;
        }
        .closing-item-tile {
            padding: 0.55rem 0.65rem;
            border-radius: 10px;
        }
        .closing-tile-icon-box {
            width: 28px;
            height: 28px;
            font-size: 0.82rem;
            border-radius: 8px;
        }
        .closing-tile-title {
            font-size: 0.76rem !important;
        }
        .closing-tile-desc {
            font-size: 0.66rem !important;
        }
        .closing-ready-banner {
            padding: 0.75rem 0.85rem;
        }
        .btn-closing-action {
            width: 100%;
            justify-content: center;
            margin-top: 0.5rem;
        }
        .verifikasi-banner {
            padding: 0.85rem 1rem !important;
        }
        .verifikasi-actions {
            width: 100%;
            flex-direction: column;
        }
        .verifikasi-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       PENDING VERIFIKASI / NOC CALLOUT BANNER
       ═══════════════════════════════════════════════════════════════════ */
    .verifikasi-banner {
        background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 55%, #f8fafc 100%);
        border: 1.5px solid #93c5fd;
        border-radius: 16px;
        padding: 0.95rem 1.25rem;
        box-shadow: 0 4px 20px -3px rgba(37, 99, 235, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        margin-bottom: 1.15rem;
        transition: all 0.25s ease;
    }
    .verifikasi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        position: relative;
    }
    .verifikasi-icon-box::after {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 15px;
        border: 1.5px solid rgba(59, 130, 246, 0.4);
        animation: pulseRingBlue 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulseRingBlue {
        0%, 100% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 0.15; transform: scale(1.08); }
    }
    .btn-verifikasi-reject {
        background: #ffffff !important;
        color: #dc2626 !important;
        border: 1.5px solid #fca5a5 !important;
        border-radius: 10px !important;
        padding: 0.55rem 1.15rem !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.1px;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.08) !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-verifikasi-reject:hover {
        background: #fee2e2 !important;
        border-color: #ef4444 !important;
        color: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15) !important;
    }
    .btn-verifikasi-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 0.55rem 1.3rem !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.1px;
        box-shadow: 0 3px 12px rgba(16, 185, 129, 0.35) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .btn-verifikasi-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45) !important;
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
    }
    .btn-verifikasi-approve:active, .btn-verifikasi-reject:active {
        transform: translateY(0);
    }
       FIBER OPTIC JOINT CLOSURE & CABLE SPLICING (PRO STYLES)
       ═══════════════════════════════════════════════════════════════════ */
    .icon-box-indigo {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3) !important;
    }
    .btn-indigo-pro {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.35) !important;
    }
    .btn-indigo-pro:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.45) !important;
    }
    .jc-card-pro {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .jc-card-pro:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
    }
    .jc-header-pro {
        padding: 0.85rem 1.15rem;
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .jc-title-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #0f172a;
        color: #38bdf8;
        font-family: 'JetBrains Mono', Consolas, monospace;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        border: 1px solid #1e293b;
        letter-spacing: 0.3px;
    }
    .jc-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    .jc-chip-eksisting {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .jc-chip-baru {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .jc-chip-type {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    .jc-chip-location {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .jc-chip-geo {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .jc-chip-geo:hover {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }
    .jc-spec-banner {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 0.85rem 1rem;
    }
    .jc-spec-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 0.95rem;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .jc-tube-badge-asal {
        display: inline-block;
        background: #eef2ff !important;
        color: #4338ca !important;
        border: 1px solid #c7d2fe !important;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
    }
    .jc-tube-badge-jumper {
        display: inline-block;
        background: #ecfdf5 !important;
        color: #065f46 !important;
        border: 1px solid #a7f3d0 !important;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
    }
    .jc-btn-delete {
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        transition: all 0.15s ease;
    }
    .jc-btn-delete:hover {
        background: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
    }
    .jc-core-row {
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.15s ease;
    }
    .jc-core-row:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .gps-lock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 50rem;
        font-size: 0.72rem;
        font-weight: 600;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .gps-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #10b981;
        animation: waPulse 1.8s infinite;
    }

    /* ═══════════════════════════════════════════════════════════════════
       VISUAL INTERACTIVE FIBER CABLE PATCHER & SPLICING BOARD
       ═══════════════════════════════════════════════════════════════════ */
    .fiber-patcher-box {
        background: radial-gradient(circle at 50% 0%, #1e293b 0%, #0b1329 100%);
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.35);
        color: #f8fafc;
        position: relative;
        overflow: hidden;
    }
    @media (max-width: 575.98px) {
        .fiber-patcher-box {
            padding: 0.75rem 0.5rem;
            border-radius: 12px;
        }
    }
    .fiber-patcher-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.65rem;
        margin-bottom: 0.65rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    .fiber-preset-actions-scroll {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        overflow-x: auto;
        max-width: 100%;
        padding-bottom: 2px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .fiber-preset-actions-scroll::-webkit-scrollbar {
        display: none;
    }
    .fiber-preset-btn {
        background: rgba(30, 41, 59, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #e2e8f0;
        font-size: 0.73rem;
        font-weight: 600;
        padding: 0.3rem 0.65rem;
        border-radius: 50rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        cursor: pointer;
        text-decoration: none;
        flex-shrink: 0;
    }
    .fiber-preset-btn:hover {
        background: rgba(59, 130, 246, 0.25);
        border-color: #60a5fa;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .fiber-preset-btn-warning:hover {
        background: rgba(245, 158, 11, 0.25);
        border-color: #fbbf24;
        color: #ffffff;
    }
    .fiber-preset-btn-danger:hover {
        background: rgba(239, 68, 68, 0.25);
        border-color: #f87171;
        color: #ffffff;
    }
    .fiber-cap-select {
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #38bdf8;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 0.2rem 0.4rem;
        border-radius: 6px;
        outline: none;
        max-width: 110px;
    }
    .fiber-cap-select option {
        background: #0f172a;
        color: #f8fafc;
    }
    .fiber-patcher-grid {
        display: grid;
        grid-template-columns: 1fr 120px 1fr;
        gap: 0.75rem;
        align-items: stretch;
        position: relative;
    }
    @media (max-width: 991.98px) {
        .fiber-patcher-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }
        .fiber-canvas-center {
            display: none !important;
        }
    }
    .fiber-panel-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 0.75rem 0.65rem;
        backdrop-filter: blur(8px);
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }
    @media (max-width: 575.98px) {
        .fiber-panel-card {
            padding: 0.5rem 0.4rem;
            border-radius: 10px;
        }
    }
    .fiber-panel-title {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.3rem;
    }
    .fiber-tube-tabs {
        display: flex;
        gap: 0.35rem;
        margin-bottom: 0.6rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 5px;
        max-width: 100%;
        width: 100%;
        scrollbar-width: thin;
        scrollbar-color: rgba(56, 189, 248, 0.4) rgba(15, 23, 42, 0.6);
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    .fiber-tube-tabs::-webkit-scrollbar {
        height: 5px;
        display: block;
    }
    .fiber-tube-tabs::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 4px;
    }
    .fiber-tube-tabs::-webkit-scrollbar-thumb {
        background: rgba(56, 189, 248, 0.45);
        border-radius: 4px;
    }
    .fiber-tube-tabs::-webkit-scrollbar-thumb:hover {
        background: rgba(56, 189, 248, 0.85);
    }
    .fiber-tube-tab-btn {
        padding: 0.2rem 0.55rem;
        font-size: 0.7rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(15, 23, 42, 0.6);
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .fiber-tube-tab-btn:hover {
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.3);
    }
    .fiber-tube-tab-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #60a5fa;
        box-shadow: 0 0 10px rgba(37, 99, 235, 0.6);
    }
    .fiber-core-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.35rem;
        max-height: 270px;
        overflow-y: auto;
        padding-right: 2px;
        scrollbar-width: thin;
    }
    @media (max-width: 991.98px) {
        .fiber-core-list {
            grid-template-columns: 1fr;
            gap: 0.3rem;
            max-height: 250px;
        }
    }
    .fiber-port-btn {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.5rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(15, 23, 42, 0.9);
        color: #f1f5f9;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: left;
        user-select: none;
        position: relative;
        min-height: 32px;
    }
    @media (max-width: 575.98px) {
        .fiber-port-btn {
            padding: 0.28rem 0.35rem;
            font-size: 0.7rem;
            gap: 0.3rem;
            min-height: 30px;
        }
    }
    .fiber-port-btn:hover {
        background: rgba(51, 65, 85, 0.95);
        border-color: rgba(255, 255, 255, 0.35);
        transform: translateY(-1px);
    }
    .fiber-port-btn.selected {
        background: rgba(37, 99, 235, 0.45) !important;
        border-color: #60a5fa !important;
        color: #ffffff !important;
        box-shadow: 0 0 10px rgba(96, 165, 250, 0.7);
    }
    .fiber-port-btn.connected {
        border-color: #10b981 !important;
        background: rgba(16, 185, 129, 0.22) !important;
    }
    .fiber-port-btn.connected::after {
        content: '✓';
        position: absolute;
        right: 4px;
        font-size: 0.65rem;
        font-weight: 800;
        color: #34d399;
    }
    .fiber-port-label-num {
        font-weight: 700;
        color: #ffffff;
        font-family: monospace;
        font-size: 0.74rem;
        letter-spacing: -0.2px;
    }
    .fiber-port-label-name {
        color: #cbd5e1;
        font-size: 0.68rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fiber-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        box-shadow: 0 0 5px rgba(0,0,0,0.6);
    }
    .fiber-dot-sm {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .fiber-canvas-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 140px;
        max-height: 220px;
        padding: 0.25rem;
    }
    @media (max-width: 991.98px) {
        .fiber-canvas-center {
            display: none !important;
        }
    }
    .fiber-svg-wire {
        width: 100%;
        height: 100%;
        max-height: 180px;
        overflow: visible;
    }
    .fiber-wire-path {
        stroke-dasharray: 4, 4;
        animation: fiberLaserFlow 1.2s linear infinite;
        filter: drop-shadow(0 0 2px currentColor);
        cursor: pointer;
        transition: stroke-width 0.2s ease;
    }
    .fiber-wire-path:hover {
        stroke-width: 4 !important;
        filter: drop-shadow(0 0 6px currentColor) !important;
    }
    @keyframes fiberLaserFlow {
        from { stroke-dashoffset: 24; }
        to { stroke-dashoffset: 0; }
    }
    .fiber-conn-status-badge {
        padding: 0.4rem 0.85rem;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(59, 130, 246, 0.4);
        border-radius: 50rem;
        font-size: 0.78rem;
        font-family: monospace;
        color: #93c5fd;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        max-width: 100%;
        overflow-x: auto;
    }
    .fiber-pulse-laser {
        animation: laserPulseAnim 1.5s infinite ease-in-out;
    }
    .btn-fiber-connect,
    #btnJcConnectSelected {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: 1px solid #34d399 !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45) !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.95rem !important;
        border-radius: 50rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.35rem !important;
        letter-spacing: 0.3px !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25) !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-fiber-connect:hover,
    #btnJcConnectSelected:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border-color: #6ee7b7 !important;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.65) !important;
        transform: translateY(-1px) scale(1.04) !important;
    }
    .btn-fiber-connect:active,
    #btnJcConnectSelected:active {
        transform: translateY(0) scale(0.98) !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4) !important;
    }
    @keyframes laserPulseAnim {
        0%, 100% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.08); opacity: 1; filter: drop-shadow(0 0 10px #38bdf8); }
    }
    .fiber-chips-tray {
        background: rgba(11, 19, 38, 0.95);
        border: 1px solid rgba(56, 189, 248, 0.25);
        border-radius: 12px;
        padding: 0.7rem 0.9rem;
        margin-top: 0.75rem;
        box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.4);
    }
    .fiber-connections-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        max-height: 120px;
        overflow-y: auto;
    }
    .fiber-conn-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.26rem 0.7rem;
        border-radius: 50rem;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(56, 189, 248, 0.35);
        color: #f8fafc;
        font-size: 0.74rem;
        font-family: monospace;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        transition: all 0.15s ease;
    }
    .fiber-conn-chip .text-muted,
    .fiber-conn-chip small {
        color: #94a3b8 !important;
        font-weight: normal;
    }
    .fiber-conn-chip:hover {
        border-color: rgba(56, 189, 248, 0.7);
        background: rgba(51, 65, 85, 0.95);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    .fiber-conn-chip-del {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #f87171;
        cursor: pointer;
        font-size: 0.8rem;
        line-height: 1;
        margin-left: 3px;
        transition: all 0.15s ease;
    }
    .fiber-conn-chip-del:hover {
        background: #ef4444;
        border-color: #dc2626;
        color: #ffffff;
        transform: scale(1.1);
    }

    /* ═══════════════════════════════════════════════════════════════════
       SCROLLABLE MODALS WITH FORMS FIX (UNIVERSAL)
       ═══════════════════════════════════════════════════════════════════ */
    .modal-dialog-scrollable {
        height: calc(100% - var(--bs-modal-margin) * 2) !important;
        max-height: calc(100dvh - var(--bs-modal-margin) * 2) !important;
    }
    .modal-dialog-scrollable .modal-content {
        max-height: 100% !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }
    .modal-dialog-scrollable .modal-content > form,
    .modal-dialog-scrollable form {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        height: 100% !important;
        max-height: 100% !important;
        overflow: hidden !important;
    }
    .modal-dialog-scrollable .modal-header {
        flex-shrink: 0 !important;
    }
    .modal-dialog-scrollable .modal-body {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        min-height: 0 !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .modal-dialog-scrollable .modal-footer {
        flex-shrink: 0 !important;
        background-color: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        z-index: 10 !important;
    }

    /* ═══════════════════════════════════════════════════════════════════
       COMPACT & CLEAN MODAL STYLING FOR JC & MANUVER CORE (MOBILE & DESKTOP)
       ═══════════════════════════════════════════════════════════════════ */
    #tambahJointClosureModal .modal-content,
    #addManuverModal .modal-content {
        border-radius: 16px !important;
        overflow: hidden;
    }

    #assignTeknisiModal .modal-header,
    #tambahJointClosureModal .modal-header,
    #addManuverModal .modal-header {
        padding: 0.85rem 1.25rem !important;
        background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%) !important;
        color: #ffffff !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
    }

    #assignTeknisiModal .modal-title,
    #tambahJointClosureModal .modal-title,
    #addManuverModal .modal-title {
        color: #ffffff !important;
        font-size: 0.92rem !important;
        font-weight: 700;
        letter-spacing: -0.1px;
    }

    #tambahJointClosureModal .modal-body,
    #addManuverModal .modal-body {
        padding: 0.85rem 1rem !important;
        background-color: #f8fafc;
    }

    .section-card-manuver {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
    }

    #tambahJointClosureModal .section-card-jc {
        padding: 0.6rem 0.65rem !important;
        border-radius: 10px !important;
        margin-bottom: 0.6rem !important;
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    #tambahJointClosureModal .section-title-jc {
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        margin-bottom: 0.45rem !important;
        letter-spacing: 0.2px;
    }

    #tambahJointClosureModal .form-label,
    #addManuverModal .form-label {
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        margin-bottom: 0.25rem !important;
    }

    #tambahJointClosureModal .form-control,
    #tambahJointClosureModal .form-select,
    #addManuverModal .form-control,
    #addManuverModal .form-select {
        font-size: 0.8rem !important;
        padding: 0.35rem 0.65rem !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    #addManuverModal .form-control:focus,
    #addManuverModal .form-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
        background-color: #ffffff !important;
        box-shadow: none !important;
        color: #0f172a !important;
    }

    #tambahJointClosureModal .form-control:focus,
    #tambahJointClosureModal .form-select:focus,
    #addManuverModal .form-control:focus,
    #addManuverModal .form-select:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }

    #addManuverModal .btn-group .btn {
        height: 32px !important;
        padding: 0.2rem 0.45rem !important;
        font-size: 0.74rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
    }

    #tambahJointClosureModal .input-group-text,
    #addManuverModal .input-group-text {
        font-size: 0.72rem !important;
        padding: 0.25rem 0.5rem !important;
        height: 32px !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        color: #475569 !important;
    }

    #tambahJointClosureModal .input-group .btn,
    #addManuverModal .input-group .btn {
        height: 32px !important;
        padding: 0.25rem 0.6rem !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    #tambahJointClosureModal .form-switch,
    #addManuverModal .form-switch {
        padding: 0.45rem 0.6rem !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }

    #tambahJointClosureModal .form-switch .form-check-input,
    #addManuverModal .form-switch .form-check-input {
        width: 1.85rem !important;
        height: 1.05rem !important;
        margin-top: 0.1rem !important;
    }

    /* Splicing matrix compact rows */
    #tambahJointClosureModal .jc-core-input-row {
        padding: 0.4rem 0.5rem !important;
        border-radius: 7px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        margin-bottom: 0.3rem !important;
    }

    #tambahJointClosureModal .jc-core-input-row .form-label {
        font-size: 0.65rem !important;
        color: #64748b !important;
        margin-bottom: 1px !important;
        font-weight: 600 !important;
    }

    #tambahJointClosureModal .jc-core-input-row .form-control,
    #tambahJointClosureModal .jc-core-input-row .form-select {
        font-size: 0.74rem !important;
        padding: 0.2rem 0.4rem !important;
        height: 28px !important;
        min-height: 28px !important;
        border-radius: 5px !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
    }

    #tambahJointClosureModal .jc-core-input-row .form-control:focus,
    #tambahJointClosureModal .jc-core-input-row .form-select:focus {
        background-color: #ffffff !important;
        border-color: #2563eb !important;
    }

    #tambahJointClosureModal .btn-remove-core-row {
        height: 28px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 5px !important;
        font-size: 0.78rem !important;
    }

    @media (max-width: 575.98px) {
        #tambahJointClosureModal .modal-header,
        #addManuverModal .modal-header {
            padding: 0.5rem 0.75rem !important;
        }
        #tambahJointClosureModal .modal-body,
        #addManuverModal .modal-body {
            padding: 0.45rem !important;
        }
        #tambahJointClosureModal .section-card-jc {
            padding: 0.5rem 0.5rem !important;
            margin-bottom: 0.45rem !important;
            border-radius: 8px !important;
        }
        #tambahJointClosureModal .section-title-jc {
            font-size: 0.72rem !important;
            margin-bottom: 0.35rem !important;
        }
        #tambahJointClosureModal .modal-footer,
        #addManuverModal .modal-footer {
            padding: 0.45rem 0.75rem !important;
        }
    }
</style>
