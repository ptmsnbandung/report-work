@extends('layouts.app')

@section('title', 'Detail Tiket ' . $tiket->no_tiket)

@push('styles')
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

    .wa-chat-container.wa-fullscreen .wa-chat-input-bar {
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        padding-bottom: max(0.65rem, env(safe-area-inset-bottom, 0px)) !important;
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

    .wa-chat-stream {
        padding: 1rem 0.85rem 1rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        min-height: 450px;
        max-height: 650px;
        overflow-y: auto;
        scroll-behavior: auto !important;
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
        margin: 0.65rem 0;
        position: sticky;
        top: 6px;
        z-index: 4;
    }

    .wa-date-chip {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: #475569;
        font-size: 0.71rem;
        font-weight: 600;
        padding: 0.28rem 0.85rem;
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(203, 213, 225, 0.85);
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.15px;
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
        font-size: 1.25rem;
        font-weight: 900;
        -webkit-text-stroke: 0.5px currentColor;
        display: inline-block;
        line-height: 1;
    }

    .wa-bubble-outgoing .wa-msg-menu-btn {
        background: transparent !important;
        border: none !important;
        color: #1e3a8a !important;
    }

    .wa-bubble-incoming .wa-msg-menu-btn {
        background: transparent !important;
        border: none !important;
        color: #334155 !important;
    }

    .wa-msg-menu-btn:hover,
    .wa-msg-menu-btn:focus,
    .wa-msg-menu-btn[aria-expanded="true"] {
        opacity: 1 !important;
        color: #000000 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transform: scale(1.18);
    }

    .wa-bubble-outgoing .wa-msg-menu-btn:hover,
    .wa-bubble-outgoing .wa-msg-menu-btn:focus,
    .wa-bubble-outgoing .wa-msg-menu-btn[aria-expanded="true"] {
        background: transparent !important;
        border: none !important;
        color: #1d4ed8 !important;
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
        cursor: pointer;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #f1f5f9;
        min-height: 120px;
    }
    .wa-media-img {
        width: 100%;
        max-height: 180px;
        min-height: 120px;
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
        padding: 0.55rem 0.85rem calc(0.55rem + env(safe-area-inset-bottom, 0px)) 0.85rem !important;
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
        bottom: 74px;
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
            margin-bottom: 0.5rem !important;
            padding-bottom: 0 !important;
        }
        .card:has(#tiketTabContent) .card-body {
            padding-bottom: 0.25rem !important;
        }
        .wa-chat-container {
            border-radius: 14px;
            margin-bottom: 0 !important;
        }
        .app-content {
            padding-bottom: 0.5rem !important;
        }
        body {
            padding-bottom: calc(var(--bottom-nav-height, 86px) + 6px) !important;
        }
        .wa-chat-stream {
            min-height: clamp(420px, calc(100dvh - 210px), 750px);
            max-height: clamp(420px, calc(100dvh - 210px), 750px);
            padding: 0.75rem 0.65rem 0.85rem 0.65rem !important;
            gap: 0.6rem;
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
    @keyframes waBubblePulse {
        0% {
            background-color: rgba(44, 127, 255, 0.12);
        }
        50% {
            background-color: rgba(44, 127, 255, 0.06);
        }
        100% {
            background-color: transparent;
        }
    }

    .wa-bubble-new-highlight .wa-bubble {
        animation: waBubblePulse 1.8s ease !important;
        outline: none !important;
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
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .sla-stepper-track {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        min-width: 580px;
        position: relative;
    }
    .sla-step-item {
        flex: 1;
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
    }
    /* ═══════════════════════════════════════════════════════════════════
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
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        margin-top: 0.85rem;
    }
    .fiber-connections-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        max-height: 110px;
        overflow-y: auto;
    }
    .fiber-conn-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.6rem;
        border-radius: 50rem;
        background: rgba(30, 41, 59, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #f1f5f9;
        font-size: 0.72rem;
        font-family: monospace;
        transition: all 0.15s ease;
    }
    .fiber-conn-chip:hover {
        border-color: rgba(255, 255, 255, 0.35);
        background: rgba(51, 65, 85, 0.9);
    }
    .fiber-conn-chip-del {
        background: transparent;
        border: none;
        color: #f87171;
        cursor: pointer;
        padding: 0 2px;
        margin-left: 2px;
        font-size: 0.85rem;
        line-height: 1;
        transition: color 0.15s ease;
    }
    .fiber-conn-chip-del:hover {
        color: #ef4444;
        transform: scale(1.2);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- ── BREADCRUMB (Desktop only to save vertical space on mobile) ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Daftar Tiket Gangguan' => route('tiket.index'),
            $tiket->no_tiket => null
        ]" />
    </div>

    <!-- ── ACTIVE STOP CLOCK ALERT BANNER ── -->
    @if($tiket->is_stop_clock && $tiket->activeStopClock)
    <div class="alert alert-warning border-2 border-warning shadow-sm rounded-xl p-3 p-md-3.5 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                <i class="bi bi-pause-circle-fill fs-3 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark font-monospace">SLA PAUSED</span>
                    <span>Tiket Sedang Dalam Status Stop Clock</span>
                </h6>
                <div class="small text-muted">
                    <strong>Alasan:</strong> <span class="badge bg-secondary bg-opacity-25 text-dark">{{ $tiket->activeStopClock->reason_label }}</span> &bull;
                    <strong>Keterangan:</strong> {{ $tiket->activeStopClock->notes ?: '-' }} &bull;
                    <strong>Sejak:</strong> {{ $tiket->activeStopClock->stopped_at->format('d/m/Y H:i') }} ({{ $tiket->activeStopClock->stopped_at->diffForHumans() }})
                </div>
            </div>
        </div>
        @if(auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
        <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="flex-shrink-0 m-0" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
            @csrf
            <button type="submit" class="btn btn-success btn-sm px-3 py-2 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-play-circle-fill"></i>
                <span>Resume Clock (Lanjutkan SLA)</span>
            </button>
        </form>
        @endif
    </div>
    @endif

    <!-- ── PENDING VERIFIKASI CALLOUT BANNER ── -->
    @if($tiket->status === 'PENDING_VERIFIKASI')
    <div class="alert alert-primary border border-primary border-opacity-50 shadow-sm rounded-xl p-3 p-md-3.5 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background-color: #f0f7ff;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                <i class="bi bi-hourglass-split fs-3 text-primary"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-navy d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white">MENUNGGU VERIFIKASI NOC</span>
                    <span>Pekerjaan Selesai oleh Teknisi</span>
                </h6>
                <div class="small text-muted">
                    Teknisi <strong>{{ $tiket->resolver?->name ?? 'Teknisi' }}</strong> telah menyelesaikan pekerjaan lapangan pada {{ $tiket->resolved_at ? $tiket->resolved_at->format('d/m/Y H:i') : '-' }}.
                    @if($tiket->closing_notes_teknisi)
                    <div class="mt-1 p-2 bg-white rounded border border-primary-subtle text-dark">
                        <strong>Catatan Teknisi:</strong> <em>{{ $tiket->closing_notes_teknisi }}</em>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @if(auth()->user()->hasRole(['admin', 'helpdesk']))
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button type="button" class="btn btn-danger btn-sm px-3 py-2 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#rejectClosingAwalModal">
                <i class="bi bi-x-circle me-1"></i> Reject (Kembalikan)
            </button>
            <button type="button" class="btn btn-success btn-sm px-3 py-2 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#closeTiketModal">
                <i class="bi bi-check2-circle me-1"></i> Verifikasi & Close Tiket
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- ── TOP HEADER HERO BANNER (BRAND BLUE FULL-WIDTH) ── -->
    <div class="card border-0 shadow-lg rounded-xl mb-3 text-white overflow-visible" style="background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 30px rgba(7, 21, 43, 0.35); position: relative; z-index: 1; overflow: visible !important;">
        <div class="card-body p-3.5 p-md-4 overflow-visible" style="overflow: visible !important;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="min-w-0 w-100 w-md-auto">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                        <span class="d-inline-flex align-items-center gap-2 text-white font-monospace px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); font-size:0.78rem; font-weight:700; letter-spacing:0.5px;">
                            <i class="bi bi-ticket-perforated-fill text-info" style="font-size: 0.95rem;"></i>
                            <span>{{ $tiket->no_tiket }}</span>
                        </span>
                        @if($tiket->status === 'OPEN')
                            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold" id="headerStatusBadge">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> OPEN
                            </span>
                        @elseif($tiket->status === 'PROSES')
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold" id="headerStatusBadge">
                                <i class="bi bi-arrow-repeat me-1"></i> PROSES
                            </span>
                        @elseif($tiket->status === 'PENDING_VERIFIKASI')
                            <span class="badge rounded-pill px-3 py-1 ms-1 fw-bold" id="headerStatusBadge" style="background-color: rgba(59, 130, 246, 0.3) !important; color: #93c5fd !important; border: 1px solid rgba(59, 130, 246, 0.6) !important;">
                                <i class="bi bi-hourglass-split me-1"></i> MENUNGGU VERIFIKASI NOC
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold" id="headerStatusBadge">
                                <i class="bi bi-check-circle-fill me-1"></i> CLOSE
                            </span>
                        @endif

                        @if($tiket->is_stop_clock)
                            <span class="badge bg-danger text-white border border-white border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold">
                                <i class="bi bi-pause-fill me-1"></i> STOP CLOCK
                            </span>
                        @endif

                        @if($tiket->sla_status === 'TEPAT')
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold">
                                <i class="bi bi-shield-check me-1"></i> TEPAT SLA
                            </span>
                        @elseif($tiket->sla_status === 'LEBIH')
                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> MELEBIHI SLA
                            </span>
                        @endif
                    </div>
                    <h1 class="h4 fw-bold text-white mb-0 lh-sm" style="letter-spacing: -0.2px;">{{ $tiket->status_link_impact }}</h1>
                </div>

                <!-- Action Buttons -->
                <div class="tiket-hero-actions w-100 w-md-auto justify-content-start justify-content-md-end flex-wrap">
                    <a href="{{ route('tiket.index') }}" class="btn-tiket-hero btn-tiket-hero-ghost">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali</span>
                    </a>

                    <!-- Single Unified Action Dropdown -->
                    <div class="dropdown position-relative d-inline-block" style="z-index: 10;">
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-fill"></i>
                            <span>Menu Aksi</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-1 tiket-hero-dropdown-menu">
                            
                            <!-- 1. Workflow Actions (Closing / Verifikasi) -->
                            @if(auth()->user()->hasRole(['teknis', 'teknisi']) && $tiket->status === 'PROSES')
                            <li>
                                <button type="button" class="tiket-hero-dropdown-item text-primary"
                                        data-bs-toggle="modal" data-bs-target="#closingAwalModal">
                                    <div class="tiket-dropdown-icon-box bg-primary-subtle text-primary">
                                        <i class="bi bi-check2-all"></i>
                                    </div>
                                    <span>Closing Awal (Selesai)</span>
                                </button>
                            </li>
                            @endif

                            @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                                @if($tiket->status === 'PENDING_VERIFIKASI')
                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item text-success"
                                            data-bs-toggle="modal" data-bs-target="#closeTiketModal">
                                        <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <span>Verifikasi & Close Tiket</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item text-danger"
                                            data-bs-toggle="modal" data-bs-target="#rejectClosingAwalModal">
                                        <div class="tiket-dropdown-icon-box bg-danger-subtle text-danger">
                                            <i class="bi bi-x-circle"></i>
                                        </div>
                                        <span>Reject Closing Awal</span>
                                    </button>
                                </li>
                                @else
                                <li>
                                    <div class="tiket-hero-dropdown-item disabled">
                                        <div class="tiket-dropdown-icon-box bg-secondary-subtle text-secondary">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                        <span class="text-muted">Menunggu Closing Awal</span>
                                    </div>
                                </li>
                                @endif
                            @endif

                            <!-- 2. SLA & Shift Actions -->
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis', 'teknisi']))
                                @if(!$tiket->is_stop_clock)
                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#startStopClockModal">
                                        <div class="tiket-dropdown-icon-box bg-warning-subtle text-warning">
                                            <i class="bi bi-pause-circle-fill"></i>
                                        </div>
                                        <span>Stop Clock (Jeda SLA)</span>
                                    </button>
                                </li>
                                @else
                                <li>
                                    <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="m-0 w-100" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
                                        @csrf
                                        <button type="submit" class="tiket-hero-dropdown-item text-success">
                                            <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                                <i class="bi bi-play-circle-fill"></i>
                                            </div>
                                            <span>Resume Clock (Lanjut SLA)</span>
                                        </button>
                                    </form>
                                </li>
                                @endif

                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#handoverShiftModal">
                                        <div class="tiket-dropdown-icon-box" style="background-color: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </div>
                                        <span>Oper Shift (Handover)</span>
                                    </button>
                                </li>
                            @endif

                            <!-- 3. Edit & Share Actions -->
                            @if(auth()->user()->hasRole(['admin', 'helpdesk']))
                                @if($tiket->status === 'OPEN')
                                <li>
                                    <a href="{{ route('tiket.edit', $tiket->id) }}" class="tiket-hero-dropdown-item">
                                        <div class="tiket-dropdown-icon-box bg-warning-subtle text-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </div>
                                        <span>Edit Data Tiket</span>
                                    </a>
                                </li>
                                @endif

                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item" id="btnCopyWaBroadcast">
                                        <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                            <i class="bi bi-whatsapp"></i>
                                        </div>
                                        <span>Salin Info WhatsApp</span>
                                    </button>
                                </li>
                            @endif

                            <!-- 4. Export Section -->
                            <li><hr class="dropdown-divider my-1"></li>
                            <li class="tiket-dropdown-header-label">Export Dokumen</li>
                            <li>
                                <a class="tiket-hero-dropdown-item" href="{{ route('reports.export.tiket.pdf', $tiket->id) }}">
                                    <div class="tiket-dropdown-icon-box bg-danger-subtle text-danger">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <span>Export Berita Acara PDF</span>
                                </a>
                            </li>
                            <li>
                                <a class="tiket-hero-dropdown-item" href="{{ route('reports.export.excel', ['search' => $tiket->no_tiket]) }}">
                                    <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </div>
                                    <span>Export Data Excel</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── SUMMARY INFO CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-3.5">
            <!-- 4 Metric Micro-Tiles Grid -->
            <div class="row g-2 g-md-2.5 mb-2.5">
                <!-- 1. Segment Backbone -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-teal-subtle text-teal">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <span class="tiket-metric-label">Segment Backbone</span>
                        </div>
                        <div class="tiket-metric-value text-truncate" title="{{ $tiket->backbone_segment }}">
                            {{ $tiket->backbone_segment }}
                        </div>
                    </div>
                </div>

                <!-- 2. Waktu Open -->
                <div class="col-6 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-primary-subtle text-primary">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <span class="tiket-metric-label">Waktu Open</span>
                        </div>
                        <div class="tiket-metric-value">
                            {{ $tiket->tanggal_open->format('d M, H:i') }} <span class="tiket-metric-unit">WIB</span>
                        </div>
                        <div class="tiket-metric-sub">
                            {{ $tiket->tanggal_open->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <!-- 3. Waktu Close -->
                <div class="col-6 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon {{ $tiket->tanggal_close ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                                <i class="bi {{ $tiket->tanggal_close ? 'bi-check2-circle' : 'bi-dash-circle' }}"></i>
                            </div>
                            <span class="tiket-metric-label">Waktu Close</span>
                        </div>
                        @if($tiket->tanggal_close)
                            <div class="tiket-metric-value text-success">
                                {{ $tiket->tanggal_close->format('d M, H:i') }} <span class="tiket-metric-unit text-success">WIB</span>
                            </div>
                            <div class="tiket-metric-sub text-success">
                                {{ $tiket->tanggal_close->diffForHumans($tiket->tanggal_open, true) }} durasi
                            </div>
                        @else
                            <div class="tiket-metric-value text-muted" style="font-size:0.8rem; font-style:italic; font-weight:500;">
                                Belum ditutup
                            </div>
                            <div class="tiket-metric-sub text-muted">
                                Tiket aktif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 4. Target SLA & MTTR -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-danger-subtle text-danger">
                                <i class="bi bi-stopwatch-fill"></i>
                            </div>
                            <span class="tiket-metric-label">Target SLA & MTTR</span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <div class="tiket-metric-value">
                                Target: <span class="text-muted fw-normal">{{ $tiket->formatted_sla_target }}</span>
                            </div>
                            <div class="tiket-metric-sub fw-bold {{ $tiket->status === 'CLOSE' ? ($tiket->sla_status === 'LEBIH' ? 'text-danger' : 'text-success') : 'text-muted' }}">
                                @if($tiket->status === 'CLOSE')
                                    MTTR: {{ $tiket->formatted_mttr }}
                                @else
                                    MTTR: Berjalan...
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($tiket->deskripsi)
            <!-- Catatan Awal & Deskripsi -->
            <div class="tiket-desc-card mb-3">
                <div class="d-flex align-items-center gap-2 text-navy fw-bold small mb-1.5">
                    <i class="bi bi-chat-left-text text-teal"></i>
                    <span>Deskripsi Gangguan & Catatan Awal:</span>
                </div>
                <div class="tiket-desc-text">{{ $tiket->deskripsi }}</div>
            </div>
            @endif

            <!-- Meta Footer Strip -->
            <div class="tiket-meta-footer">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-person-fill text-primary"></i>
                    <span class="text-muted">Dibuat:</span>
                    <strong class="text-navy">{{ $tiket->creator?->name ?? 'Sistem' }}</strong>
                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded" style="font-size:0.65rem;">{{ $tiket->creator?->role_short ?? '-' }}</span>
                </div>
                @if($tiket->closed_by)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-person-check-fill text-success"></i>
                    <span class="text-muted">Ditutup:</span>
                    <strong class="text-navy">{{ $tiket->closer?->name ?? 'Sistem' }}</strong>
                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded" style="font-size:0.65rem;">{{ $tiket->closer?->role_short ?? '-' }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── VISUAL SLA TIMELINE STEPPER (POINT 5) ── -->
    @php
        $slaStages = $tiket->sla_timeline_stages;
    @endphp
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-3.5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-1.5 text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-bezier2"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-navy small">Garis Alur SLA &amp; Siklus Hidup Tiket</span>
                        <span class="text-muted small ms-1 d-none d-sm-inline">(Open &rarr; Respon &rarr; Stop Clock &rarr; Closing Lapangan &rarr; Closing NOC)</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $tiket->status === 'CLOSE' ? ($tiket->sla_status === 'LEBIH' ? 'bg-danger text-white' : 'bg-success text-white') : 'bg-primary text-white' }} px-2.5 py-1 rounded-pill small fw-bold">
                        @if($tiket->status === 'CLOSE')
                            {{ $tiket->sla_status === 'LEBIH' ? 'SLA OVERDUE (' . $tiket->formatted_mttr . ')' : 'SLA ACHIEVED (' . $tiket->formatted_mttr . ')' }}
                        @else
                            Target SLA: {{ $tiket->formatted_sla_target }} (Aktif)
                        @endif
                    </span>
                </div>
            </div>

            <!-- Horizontal Stepper Progress -->
            <div class="sla-stepper-wrapper py-2">
                <div class="sla-stepper-track">
                    @foreach($slaStages as $index => $stage)
                        @php
                            $isCompleted = (bool) ($stage['is_completed'] ?? false);
                            $isCurrent = (bool) ($stage['is_current'] ?? false);
                            
                            $nodeColorClass = 'step-pending';
                            if ($isCompleted) {
                                $nodeColorClass = 'step-completed';
                            } elseif ($isCurrent) {
                                $nodeColorClass = ($stage['key'] === 'STOP_CLOCK' ? 'step-paused' : 'step-active');
                            }
                        @endphp
                        <div class="sla-step-item {{ $nodeColorClass }}">
                            <div class="sla-step-node-container">
                                <div class="sla-step-node">
                                    @if($isCompleted)
                                        <i class="bi bi-check-lg"></i>
                                    @elseif($isCurrent)
                                        @if($stage['key'] === 'STOP_CLOCK')
                                            <i class="bi bi-pause-fill"></i>
                                        @else
                                            <span class="spinner-grow spinner-grow-sm text-white" style="width: 10px; height: 10px;" role="status"></span>
                                        @endif
                                    @else
                                        <span class="step-num">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <div class="sla-step-line {{ $isCompleted ? 'line-completed' : ($isCurrent ? 'line-active' : '') }}"></div>
                                @endif
                            </div>
                            <div class="sla-step-content mt-2">
                                <div class="sla-step-title fw-bold text-navy small">{{ $stage['title'] }}</div>
                                <div class="sla-step-timestamp text-muted small" style="font-size: 0.7rem;">
                                    {{ $stage['timestamp'] ?: '-' }}
                                </div>
                                <div class="sla-step-badge mt-1">
                                    @if(!empty($stage['badge']))
                                        <span class="badge {{ $isCompleted ? 'bg-success bg-opacity-15 text-success' : ($isCurrent ? 'bg-primary bg-opacity-15 text-primary' : 'bg-light text-muted') }} rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                            {{ $stage['badge'] }}
                                        </span>
                                    @elseif($isCompleted)
                                        <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                            Selesai
                                        </span>
                                    @elseif($isCurrent)
                                        <span class="badge bg-primary bg-opacity-15 text-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                            Sedang Proses
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ── 30-MINUTE FIELD REPORT INTERVAL & STOP CLOCK REMINDER WIDGET (POINT 6 & 9) ── -->
    @if($tiket->status !== 'CLOSE')
        @php
            $fieldStatus = $tiket->field_update_status;
            $minsSinceLast = $tiket->minutes_since_last_update;
            $avgInterval = $tiket->average_report_interval_minutes;
            $lastKronologis = $tiket->last_kronologis;
            $showIntervalAlert = in_array($fieldStatus, ['OVERDUE', 'WARNING']);
            $showStopClockAlert = $tiket->is_stop_clock && $tiket->activeStopClock;
        @endphp

        @if($showIntervalAlert || $showStopClockAlert)
        <div id="fieldReportIntervalBanner" class="card border-0 mb-3 overflow-hidden shadow-sm" style="{{ $fieldStatus === 'OVERDUE' ? 'background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 50%, #fee2e2 100%); border: 1px solid #fca5a5 !important; border-left: 5px solid #ef4444 !important; border-radius: 14px; box-shadow: 0 4px 20px -4px rgba(239, 68, 68, 0.12);' : 'background: linear-gradient(135deg, #fffdf0 0%, #fefce8 50%, #fef3c7 100%); border: 1px solid #fde047 !important; border-left: 5px solid #f59e0b !important; border-radius: 14px; box-shadow: 0 4px 20px -4px rgba(245, 158, 11, 0.12);' }}">
            <div class="card-body p-3 p-md-3.5">
                @if($showIntervalAlert)
                <div class="row align-items-center g-3" id="fieldIntervalStatusRow">
                    <!-- Left: Interval Monitor Status -->
                    <div class="col-12 col-lg-7">
                        <div class="d-flex align-items-start gap-3">
                            @if($fieldStatus === 'OVERDUE')
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35); border-radius: 12px;">
                                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                            <span class="spinner-grow spinner-grow-sm" style="width: 0.45rem; height: 0.45rem;" role="status"></span>
                                            OVERDUE > 30 MENIT
                                        </span>
                                        <span class="fw-bold text-danger-emphasis fs-6" style="letter-spacing: -0.2px;">Wajib Kirim Update Kondisi Lapangan!</span>
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.8rem; line-height: 1.45;">
                                        Laporan kronologis terakhir diupdate <strong class="text-danger fw-bold">{{ $tiket->minutes_since_last_update_formatted ? $tiket->minutes_since_last_update_formatted . ' yang lalu' : 'belum pernah ada laporan' }}</strong>.
                                        SOP mewajibkan teknisi memberikan info perkembangan di lapangan minimal setiap <strong>30 Menit</strong>.
                                    </div>
                                </div>
                            @elseif($fieldStatus === 'WARNING')
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); border-radius: 12px;">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="badge rounded-pill bg-warning text-dark px-2.5 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                            <i class="bi bi-clock-history"></i>
                                            PERINGATAN 20-30 MENIT
                                        </span>
                                        <span class="fw-bold text-dark fs-6" style="letter-spacing: -0.2px;">Persiapkan Update Laporan Lapangan</span>
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.8rem; line-height: 1.45;">
                                        Laporan terakhir <strong class="text-dark fw-bold">{{ $tiket->minutes_since_last_update_formatted }} yang lalu</strong>. Segera input update progress sebelum batas 30 menit terlewati.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Quick Action & Stats -->
                    <div class="col-12 col-lg-5">
                        <div class="d-flex flex-row align-items-center justify-content-lg-end gap-2 flex-wrap flex-sm-nowrap">
                            <!-- Metric 1: Update Terakhir -->
                            <div class="p-2 px-3 rounded-3 text-center flex-fill" style="background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(203, 213, 225, 0.8); box-shadow: 0 2px 6px rgba(0,0,0,0.03); min-width: 105px;">
                                <div class="text-muted d-flex align-items-center justify-content-center gap-1" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px;">
                                    <i class="bi bi-clock-history text-secondary"></i> Update Terakhir
                                </div>
                                <div class="fw-bold text-navy font-monospace mt-0.5" style="font-size: 0.92rem;">
                                    {{ $lastKronologis ? ($lastKronologis->timestamp ? $lastKronologis->timestamp->format('H:i') : $lastKronologis->created_at->format('H:i')) . ' WIB' : '-' }}
                                </div>
                            </div>

                            <!-- Metric 2: Rata-Rata Interval (Format Jam & Menit) -->
                            <div class="p-2 px-3 rounded-3 text-center flex-fill" style="background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(203, 213, 225, 0.8); box-shadow: 0 2px 6px rgba(0,0,0,0.03); min-width: 120px;">
                                <div class="text-muted d-flex align-items-center justify-content-center gap-1" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px;">
                                    <i class="bi bi-stopwatch text-secondary"></i> Rata-rata Interval
                                </div>
                                <div class="fw-bold text-navy mt-0.5" style="font-size: 0.88rem; white-space: nowrap;">
                                    {{ $tiket->average_report_interval_formatted }}
                                </div>
                            </div>

                            <!-- Action CTA Button -->
                            @if(auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button type="button" class="btn btn-sm px-3.5 py-2.5 fw-bold rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 flex-fill text-white flex-shrink-0" style="{{ $fieldStatus === 'OVERDUE' ? 'background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35); border: none;' : 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); border: none;' }}" onclick="const kTab = document.getElementById('kronologis-tab'); if(kTab) kTab.click(); const waInp = document.getElementById('waChatTextInput') || document.getElementById('informasi'); if(waInp) { waInp.focus(); waInp.scrollIntoView({behavior: 'smooth', block: 'center'}); }">
                                    <i class="bi bi-chat-left-dots-fill"></i>
                                    <span>Kirim Update</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Point 9: Helpdesk Stop Clock Reminder & Checklist -->
                @if($tiket->is_stop_clock && $tiket->activeStopClock)
                    <div class="mt-3 pt-2.5 border-top border-warning border-opacity-30">
                        <div class="d-flex align-items-start gap-2.5 p-2.5 rounded-3" style="background: #fffbeb; border: 1px dashed #f59e0b;">
                            <i class="bi bi-bell-fill text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <strong class="text-dark small"><i class="bi bi-exclamation-circle me-1"></i>Reminder Helpdesk (Stop Clock Aktif):</strong>
                                    <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">Follow-up Diperlukan</span>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                    Pastikan Helpdesk secara berkala mengontak pihak eksternal (PLN / Vendor / Perizinan) terkait kendala <em>"{{ $tiket->activeStopClock->reason_label }}"</em>.
                                    Segera lakukan <strong>Resume Clock</strong> begitu hambatan terselesaikan agar perhitungan MTTR tetap akurat.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endif

    <!-- ── MANDATORY CLOSING CHECKLIST CARD (ANTI CLOSING SEMBARANGAN) ── -->
    @php
        $prereqs = $tiket->checkClosingPrerequisites();
        $completedCount = 0;
        if ($prereqs['items']['resume_filled']) $completedCount++;
        if ($prereqs['items']['photo_uploaded']) $completedCount++;
        if ($prereqs['items']['titik_perbaikan']) $completedCount++;
        if ($prereqs['items']['tipe_penanganan']) $completedCount++;
        $progressPercent = $completedCount * 25;
    @endphp
    @if($tiket->status !== 'CLOSE')
    <div class="closing-readiness-card">
        <!-- Header with Progress -->
        <div class="closing-readiness-header">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #ffffff; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);">
                    <i class="bi bi-shield-lock-fill" style="font-size: 0.95rem;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="fw-bold text-navy" style="font-size: 0.92rem; letter-spacing: -0.2px;">Kesiapan Closing Tiket</span>
                        <span class="badge rounded-pill fw-bold" style="background: {{ $progressPercent === 100 ? '#ecfdf5' : '#eff6ff' }}; color: {{ $progressPercent === 100 ? '#059669' : '#1d4ed8' }}; border: 1px solid {{ $progressPercent === 100 ? '#a7f3d0' : '#bfdbfe' }}; font-size: 0.68rem;">
                            {{ $completedCount }}/4 Terpenuhi ({{ $progressPercent }}%)
                        </span>
                    </div>
                    <div class="text-muted small d-none d-sm-block" style="font-size: 0.72rem; margin-top: 1px;">
                        4 syarat mandatori yang wajib dilengkapi teknisi sebelum closing awal dapat diajukan
                    </div>
                </div>
            </div>

            <div>
                @if($prereqs['ready'])
                    <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-30 px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Siap Closing Awal</span>
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-40 px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        <span>Belum Lengkap ({{ count($prereqs['missing_items']) }} item lagi)</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Slim Visual Progress Bar -->
        <div class="closing-progress-bar-track">
            <div class="closing-progress-bar-fill" style="width: {{ $progressPercent }}%; background: {{ $progressPercent === 100 ? 'linear-gradient(90deg, #10b981, #059669)' : ($progressPercent >= 50 ? 'linear-gradient(90deg, #3b82f6, #10b981)' : 'linear-gradient(90deg, #f59e0b, #3b82f6)') }};"></div>
        </div>

        <!-- 4 Grid Tiles -->
        <div class="p-3 bg-light bg-opacity-40">
            <div class="row g-2.5">
                <!-- 1. Resume -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['resume_filled'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('resume-tab'); if(tab) { tab.click(); document.getElementById('resume-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengisi / melihat Resume Pekerjaan">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['resume_filled'] ? 'bi-check-lg' : 'bi-file-earmark-text' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">1. Resume Kerja</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['resume_filled'] ? '#059669' : '#64748b' }};">
                                    {{ $prereqs['items']['resume_filled'] ? 'Problem & Action diisi' : 'Belum diisi lengkap' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Dokumentasi -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['photo_uploaded'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('dokumentasi-tab'); if(tab) { tab.click(); document.getElementById('dokumentasi-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengupload Foto Dokumentasi / OTDR">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['photo_uploaded'] ? 'bi-check-lg' : 'bi-camera' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">2. Foto / OTDR</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['photo_uploaded'] ? '#059669' : '#64748b' }};">
                                    {{ $tiket->dokumentasis->count() > 0 ? $tiket->dokumentasis->count() . ' foto terupload' : 'Wajib minimal 1 foto' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 3. Titik Perbaikan -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['titik_perbaikan'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('material-tab'); if(tab) { tab.click(); document.getElementById('material-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk menambahkan Titik Koordinat / Joint Closure">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['titik_perbaikan'] ? 'bi-check-lg' : 'bi-geo-alt' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">3. Koordinat / JC</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['titik_perbaikan'] ? '#059669' : '#64748b' }};">
                                    {{ $tiket->titikPerbaikans->count() > 0 ? $tiket->titikPerbaikans->count() . ' titik tercatat' : 'Wajib minimal 1 titik' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 4. Tipe Penanganan -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['tipe_penanganan'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('penanganan-tab'); if(tab) { tab.click(); document.getElementById('penanganan-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk memilih Tipe Penanganan (Jointing Lurus / Manuver Core)">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['tipe_penanganan'] ? 'bi-check-lg' : 'bi-tools' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">4. Penanganan</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['tipe_penanganan'] ? '#059669' : '#64748b' }};">
                                    {{ $tiket->tipe_penanganan ? str_replace('_', ' ', $tiket->tipe_penanganan) : 'Wajib dipilih' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ── TABBED NAVIGATION (MOBILE SCROLLABLE) ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-1 pb-0 mb-md-4 pb-md-0">
        <div class="card-header bg-white p-2 border-bottom">
            <ul class="nav nav-tabs-mobile" id="tiketTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-1.5"
                            id="kronologis-tab" data-bs-toggle="tab" data-bs-target="#kronologis-pane" type="button" role="tab">
                        <i class="bi bi-clock-history text-teal"></i>
                        <span>Kronologis</span>
                        <span class="badge bg-light text-navy border" id="kronologisCountBadge">{{ $tiket->kronologis->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume-pane" type="button" role="tab">
                        <i class="bi bi-file-earmark-check text-primary"></i>
                        <span>Resume</span>
                        @if($tiket->resume)
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;">Ada</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="penanganan-tab" data-bs-toggle="tab" data-bs-target="#penanganan-pane" type="button" role="tab">
                        <i class="bi bi-bezier2" style="color: #6366f1 !important;"></i>
                        <span>Penanganan Core &amp; JC</span>
                        <span class="badge bg-light text-navy border" id="penangananCountBadge">{{ $tiket->jointClosures->count() + $tiket->manuverCores->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="material-tab" data-bs-toggle="tab" data-bs-target="#material-pane" type="button" role="tab">
                        <i class="bi bi-box-seam text-warning"></i>
                        <span>Material &amp; Titik</span>
                        <span class="badge bg-light text-navy border">{{ $tiket->materials->count() + $tiket->titikPerbaikans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="dokumentasi-tab" data-bs-toggle="tab" data-bs-target="#dokumentasi-pane" type="button" role="tab">
                        <i class="bi bi-camera-fill text-info"></i>
                        <span>Dokumentasi</span>
                        <span class="badge bg-light text-navy border" id="dokumentasiCountBadge">{{ $tiket->dokumentasis->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="stopclock-tab" data-bs-toggle="tab" data-bs-target="#stopclock-pane" type="button" role="tab">
                        <i class="bi bi-pause-circle text-warning"></i>
                        <span>Stop Clock &amp; Shift</span>
                        @if($tiket->stopClocks->count() > 0 || $tiket->handoverShifts->count() > 0)
                            <span class="badge bg-light text-navy border">{{ $tiket->stopClocks->count() + $tiket->handoverShifts->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-1 pb-1 p-sm-2.5 p-md-4">
            <div class="tab-content" id="tiketTabContent">

                <!-- ════ TAB 1: KRONOLOGIS (FASE 3 - WHATSAPP CHAT FEED) ════ -->
                <div class="tab-pane fade show active" id="kronologis-pane" role="tabpanel">
                    <div class="wa-chat-container shadow-xs">
                        <!-- Chat Header -->
                        <div class="wa-chat-header">
                            <div class="d-flex align-items-center gap-2 gap-sm-2.5 overflow-hidden flex-grow-1">
                                <div class="wa-header-avatar">
                                    <i class="bi bi-chat-dots-fill text-white"></i>
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="wa-header-title d-flex align-items-center gap-1.5">
                                        <span class="text-truncate"><span class="d-none d-sm-inline">Koordinasi </span>Update Lapangan</span>
                                        <span class="badge rounded-pill d-none d-sm-inline-flex align-items-center" style="font-size:0.65rem; font-weight:700; background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.4); padding: 3px 8px;">
                                            <span class="wa-pulse-dot"></span> Live Sync (5s)
                                        </span>
                                    </div>
                                    <div class="wa-header-meta text-truncate">
                                        <span class="d-inline d-sm-none fw-semibold" style="color: #4ade80;"><span class="wa-pulse-dot"></span>Live &bull; </span>
                                        <span class="d-none d-sm-inline">Catatan teknis tiket </span><strong>{{ $tiket->no_tiket }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Fullscreen -->
                            <button id="btnWaFullscreen" class="wa-fullscreen-btn" title="Perbesar chat">
                                <i class="bi bi-arrows-fullscreen" id="icoWaFullscreen"></i>
                            </button>

                        </div>

                        <!-- Chat Messages Stream Area -->
                        <div id="timelineWrapper">
                            @if($tiket->kronologis->count() > 0)
                                <div class="wa-chat-stream" id="timelineList">
                                    @php 
                                        $lastDate = null; 
                                        $currentUserId = auth()->id();
                                        $nameColors = ['#075e54', '#128c7e', '#0284c7', '#7c3aed', '#d97706', '#059669', '#2563eb'];
                                        $totalKronoCount = $totalKronologis ?? $tiket->kronologis()->count();
                                        $renderedCount = $tiket->kronologis->count();
                                        $hasOlderKrono = $totalKronoCount > $renderedCount;
                                        $oldestRenderedId = $tiket->kronologis->first()?->id ?? 0;
                                        
                                        // Baca timestamp kehadiran/view pengguna lain dari cache
                                        $viewsKey = "tiket_{$tiket->id}_user_views";
                                        $ticketViews = \Illuminate\Support\Facades\Cache::get($viewsKey, []);
                                        $otherViewTimes = collect($ticketViews)->where('user_id', '!=', $currentUserId)->pluck('viewed_at');
                                        $maxOtherViewTime = $otherViewTimes->max() ?? 0;
                                        
                                        $lastOtherKronoTime = $tiket->kronologis->where('user_id', '!=', $currentUserId)->max('timestamp');
                                        $lastOtherKronoTimestamp = $lastOtherKronoTime ? $lastOtherKronoTime->timestamp : 0;
                                        $maxReadTimestampByOthers = max((int)$maxOtherViewTime, (int)$lastOtherKronoTimestamp);
                                        $isTiketClosedOrVerified = in_array($tiket->status, ['CLOSE', 'MENUNGGU_VERIFIKASI', 'RESOLVED']);
                                    @endphp

                                    @if($hasOlderKrono)
                                        <div id="loadOlderKronoWrapper" class="text-center py-2.5 mb-2">
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3.5 shadow-xs fw-semibold text-secondary" id="btnLoadOlderKrono" data-oldest-id="{{ $oldestRenderedId }}">
                                                <i class="bi bi-clock-history me-1.5 text-primary"></i> Muat Pesan Sebelumnya (<span id="olderKronoCount">{{ $totalKronoCount - $renderedCount }}</span> lagi)
                                            </button>
                                        </div>
                                    @endif
                                    @foreach($tiket->kronologis as $krono)
                                        @php 
                                            $currentDate = $krono->timestamp->format('Y-m-d'); 
                                            $isMe = ($krono->user_id === $currentUserId);
                                            $colorIndex = abs(crc32($krono->user?->name ?? 'User')) % count($nameColors);
                                            $senderColor = $nameColors[$colorIndex];
                                            $initials = strtoupper(substr($krono->user?->name ?? 'U', 0, 2));
                                            $userAvatar = $krono->user?->avatar_url;
                                            $kronoTimeUnix = $krono->timestamp->timestamp;
                                            $isReadByOthers = $isTiketClosedOrVerified || ($maxReadTimestampByOthers > 0 && $kronoTimeUnix <= $maxReadTimestampByOthers);
                                        @endphp

                                        @if($currentDate !== $lastDate)
                                            <div class="wa-date-divider">
                                                <span class="wa-date-chip">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ $krono->timestamp->translatedFormat('l, d F Y') }}
                                                </span>
                                            </div>
                                            @php $lastDate = $currentDate; @endphp
                                        @endif

                                        <div class="wa-msg-row {{ $isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming' }}" id="krono-item-{{ $krono->id }}" data-id="{{ $krono->id }}" data-timestamp="{{ $krono->timestamp->timestamp }}">
                                            @if(!$isMe)
                                            <div class="wa-avatar" style="background-color: {{ $userAvatar ? 'transparent' : $senderColor }};" title="{{ $krono->user?->name }}">
                                                @if($userAvatar)
                                                    <img src="{{ $userAvatar }}" alt="{{ $krono->user?->name }}" class="wa-avatar-img">
                                                @else
                                                    {{ $initials }}
                                                @endif
                                            </div>
                                            @endif

                                            <div class="wa-bubble {{ $isMe ? 'wa-bubble-outgoing' : 'wa-bubble-incoming' }}">
                                                <!-- Bubble Header: Sender, Role & 3-Dots Action Menu -->
                                                <div class="wa-bubble-header">
                                                    <div class="wa-sender-info">
                                                        <span class="wa-sender-name" style="color: {{ $isMe ? '#0f766e' : $senderColor }};">
                                                            {{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}
                                                        </span>
                                                        <span class="wa-role-pill">{{ $krono->user?->role_short ?? '-' }}</span>
                                                    </div>

                                                    <!-- 3-Dots Message Action Dropdown -->
                                                    <div class="dropdown wa-bubble-menu-wrapper">
                                                        <button type="button" class="wa-msg-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan pesan">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end wa-msg-dropdown-menu shadow border-0">
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-copy" data-id="{{ $krono->id }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-clipboard text-primary"></i> Salin
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-msg-info"
                                                                        data-id="{{ $krono->id }}"
                                                                        data-user-id="{{ $krono->user_id }}"
                                                                        data-sender="{{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}"
                                                                        data-sender-role="{{ $krono->user?->role_short ?? '-' }}"
                                                                        data-time="{{ $krono->timestamp->format('d/m/Y H:i') }} WIB"
                                                                        data-text="{{ e($krono->informasi) }}"
                                                                        data-photo="{{ $krono->foto_url ? asset($krono->foto_url) : '' }}"
                                                                        data-timestamp="{{ $krono->timestamp->timestamp }}">
                                                                    <i class="bi bi-info-circle-fill text-info"></i> Info Pesan
                                                                </button>
                                                            </li>
                                                            @if($krono->foto_url && $tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-forward-doc text-success"
                                                                        onclick="forwardPhotoToDoc('{{ asset($krono->foto_url) }}', '{{ $krono->latitude ?? '' }}', '{{ $krono->longitude ?? '' }}', '{{ $krono->timestamp->format('Y-m-d\TH:i') }}', '{{ $krono->kategori }}')">
                                                                    <i class="bi bi-folder-plus text-success"></i> Simpan ke Dokumentasi
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-reply" data-id="{{ $krono->id }}" data-sender="{{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-reply-fill text-info"></i> Balas
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @php
                                                                $sentMoment = $krono->created_at ?? $krono->timestamp;
                                                                $diffMinutes = $sentMoment ? $sentMoment->diffInMinutes(now()) : 999;
                                                                $canEditMessage = ($tiket->status !== 'CLOSE') && ($isMe || auth()->user()->hasRole('admin')) && ($diffMinutes <= 5);
                                                            @endphp
                                                            @if($canEditMessage)
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-edit" data-id="{{ $krono->id }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-pencil-square text-warning"></i> Edit
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @if(auth()->user()->hasRole('admin'))
                                                            <li><hr class="dropdown-divider my-1"></li>
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item text-danger btn-action-delete" data-id="{{ $krono->id }}">
                                                                    <i class="bi bi-trash3-fill"></i> Hapus
                                                                </button>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Message Body with Quoted Reply & Mention Highlights -->
                                                @php
                                                    $rawInfo = $krono->informasi ?? '';
                                                    $quoteSender = null;
                                                    $quoteText = null;
                                                    if (preg_match('/^>?\s*\[Membalas\s+([^\]]+)\]:\s*([^\n]+)\n+/i', $rawInfo, $quoteMatches)) {
                                                        $quoteSender = $quoteMatches[1];
                                                        $quoteText = $quoteMatches[2];
                                                        $rawInfo = substr($rawInfo, strlen($quoteMatches[0]));
                                                    }
                                                    $cleanedInfo = preg_replace('/(\r?\n\s*){2,}/', "\n", trim($rawInfo));
                                                    // Otomatis ubah angka menit menjadi format jam dan menit yang rapi (misal: 751 menit -> 12 jam 31 menit)
                                                    $cleanedInfo = preg_replace_callback('/(Durasi Jeda|Total Jeda SLA Tiket|Total Stop Clock):\s*(\d+)\s*menit/i', function($m) {
                                                        $mins = (int) $m[2];
                                                        return $m[1] . ': ' . \App\Models\Tiket::formatDuration($mins);
                                                    }, $cleanedInfo);
                                                @endphp
                                                @if($quoteSender)
                                                <div class="wa-quote-box">
                                                    <div class="wa-quote-sender"><i class="bi bi-reply-fill me-1"></i>{{ $quoteSender }}</div>
                                                    <div class="wa-quote-text">{{ $quoteText }}</div>
                                                </div>
                                                @endif
                                                <div class="wa-msg-text">{!! preg_replace('/(@[a-zA-Z0-9_\.\-]+(?:\s+[a-zA-Z0-9_\.\-]+)?)/u', '<span class="wa-mention-tag-highlight">$1</span>', nl2br(e($cleanedInfo))) !!}</div>

                                                <!-- Attached Photo (WhatsApp Media Card) -->
                                                @if($krono->foto_url)
                                                <div class="wa-media-card" onclick="zoomPhoto('{{ asset($krono->foto_url) }}', '{{ $krono->kategori }} - {{ $krono->timestamp->format('d/m/Y H:i') }} WIB')">
                                                    <img src="{{ asset($krono->foto_url) }}" alt="Foto Kronologis" class="wa-media-img" loading="lazy">
                                                    <div class="wa-media-badge">
                                                        <i class="bi bi-arrows-fullscreen"></i>
                                                        <span>Klik untuk memperbesar</span>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Shared Location Card -->
                                                @if($krono->has_coordinates)
                                                <div class="wa-location-card">
                                                    <div class="wa-loc-icon">
                                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                                    </div>
                                                    <div class="wa-loc-info">
                                                        <div class="wa-loc-title">Lokasi Titik Lapangan</div>
                                                        <div class="wa-loc-coords">{{ $krono->latitude }}, {{ $krono->longitude }}</div>
                                                    </div>
                                                    <a href="{{ $krono->google_maps_url }}" target="_blank" class="wa-loc-btn" title="Buka di Google Maps">
                                                        <span>Peta</span>
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                </div>
                                                @endif

                                                <!-- Bubble Footer: Time & Double Checkmark (Abu jika belum ada tanggapan orang lain, Biru jika sudah) -->
                                                <div class="wa-bubble-footer">
                                                    <span class="wa-time">{{ $krono->timestamp->format('H:i') }} WIB</span>
                                                    @if($isMe)
                                                        <i class="bi bi-check2-all wa-status-icon {{ $isReadByOthers ? 'wa-status-read' : 'wa-status-sent' }}" title="{{ $isReadByOthers ? 'Dilihat oleh tim' : 'Terkirim (Belum dilihat)' }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
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
                                </div>
                            @endif
                        </div>
                        <script>
                            (function() {
                                var s = document.getElementById('timelineList');
                                if (s) s.scrollTop = s.scrollHeight;
                            })();
                        </script>

                        <!-- Floating Scroll-to-Bottom Button (WhatsApp Style) -->
                        <button type="button" id="btnWaScrollBottom" class="wa-scroll-bottom-btn d-none" title="Ke Pesan Terbaru">
                            <i class="bi bi-chevron-double-down"></i>
                        </button>

                        <!-- Gojek-Style Quick Reply Chips (Rekomendasi Chat Cepat) -->
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <div class="wa-quick-replies-wrapper" id="waQuickRepliesWrapper">
                            <button type="button" class="wa-quick-chip" data-text="Sedang menuju ke lokasi titik gangguan">
                                <span class="wa-quick-chip-icon text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                                <span>Menuju lokasi</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang investigasi di lapangan & pengukuran OTDR">
                                <span class="wa-quick-chip-icon text-info"><i class="bi bi-search"></i></span>
                                <span>Investigasi & OTDR</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ditemukan kabel fiber optik putus / bending">
                                <span class="wa-quick-chip-icon text-danger"><i class="bi bi-scissors"></i></span>
                                <span>Kabel putus / bending</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang proses splicing / penyambungan core kabel">
                                <span class="wa-quick-chip-icon text-warning"><i class="bi bi-lightning-charge-fill"></i></span>
                                <span>Proses splicing core</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang ukur nilai redaman / power level optik">
                                <span class="wa-quick-chip-icon" style="color: #6366f1;"><i class="bi bi-speedometer2"></i></span>
                                <span>Ukur redaman optik</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Redaman sudah normal & link sudah UP kembali">
                                <span class="wa-quick-chip-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
                                <span>Redaman normal & Link UP</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ada kendala di lapangan, mohon bantuan koordinasi / manuver core">
                                <span class="wa-quick-chip-icon text-warning"><i class="bi bi-shuffle"></i></span>
                                <span>Kendala / butuh manuver</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Melampirkan foto dokumentasi hasil perbaikan di lapangan">
                                <span class="wa-quick-chip-icon text-secondary"><i class="bi bi-camera-fill"></i></span>
                                <span>Dokumentasi perbaikan</span>
                            </button>
                        </div>

                        <!-- WhatsApp Direct Inline Chat Input Bar (WhatsApp-Native Pro) -->
                        <form action="{{ route('tiket.kronologis.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="waDirectChatForm" class="wa-chat-input-bar">
                            @csrf
                            <input type="hidden" name="kategori" value="LAIN">
                            <input type="hidden" name="latitude" id="waChatLatitude" value="">
                            <input type="hidden" name="longitude" id="waChatLongitude" value="">
                            <input type="file" name="foto" id="waChatFotoInput" accept="image/*" class="d-none">

                            <!-- Floating White Capsule Pill (Enclosing Paperclip + Textarea + Attachments) -->
                            <div class="wa-floating-input-pill">
                                <!-- Paperclip Attachment Dropdown Menu -->
                                <div class="dropup position-relative d-flex align-items-center">
                                    <button type="button" class="wa-attach-btn" id="waAttachDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Lampirkan foto / lokasi">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start wa-attach-menu shadow-lg border-0" aria-labelledby="waAttachDropdownBtn">
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaUploadFoto">
                                                <div class="wa-attach-icon bg-primary-subtle text-primary">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small text-dark">Upload Foto</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Pilih foto dari galeri HP / perangkat</div>
                                                </div>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaCameraWatermark">
                                                <div class="wa-attach-icon bg-success-subtle text-success">
                                                    <i class="bi bi-camera-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small text-dark">Kamera GPS (Watermark)</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Dengan Logo MSN, Timestamp & Alamat</div>
                                                </div>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaCameraPolos">
                                                <div class="wa-attach-icon bg-info-subtle text-info">
                                                    <i class="bi bi-camera"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small text-dark">Kamera Polos (Tanpa Watermark)</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Potret langsung foto kamera original</div>
                                                </div>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaShareLocation">
                                                <div class="wa-attach-icon bg-danger-subtle text-danger">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small text-dark">Share Lokasi Saya</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Sematkan koordinat GPS lapangan</div>
                                                </div>
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Text Input Area & Attachment Chips -->
                                <div class="wa-input-wrapper position-relative">
                                    <!-- WhatsApp @Mention User Autocomplete Popup -->
                                    <div id="waMentionDropdown" class="wa-mention-dropdown d-none">
                                        <div class="wa-mention-header">
                                            <i class="bi bi-at text-primary"></i> Tag Anggota Tim
                                        </div>
                                        <div class="wa-mention-list" id="waMentionList"></div>
                                    </div>

                                    <!-- WhatsApp Reply Bar Preview (shows when replying to a message) -->
                                    <div id="waReplyPreviewBar" class="wa-reply-preview-bar d-none">
                                        <div class="wa-reply-preview-content">
                                            <div class="wa-reply-preview-sender" id="waReplySenderText">Membalas User</div>
                                            <div class="wa-reply-preview-text" id="waReplySnippetText">Isi pesan yang dibalas...</div>
                                        </div>
                                        <button type="button" class="btn-close" style="font-size: 0.6rem;" id="btnCancelWaReply" title="Batalkan Balasan"></button>
                                    </div>

                                    <textarea name="informasi" id="waChatTextInput" class="wa-chat-textarea" rows="1" placeholder="Ketik update koordinasi ..." required></textarea>
                                    <!-- Attachment Previews Bar (shows when photo or location is attached) -->
                                    <div id="waAttachmentPreviewBar" class="wa-attach-preview-bar d-none">
                                        <div id="waPhotoPreviewChip" class="d-none align-items-center gap-1.5 badge bg-white text-dark border shadow-xs me-1 py-1 px-2 rounded-pill" style="max-width: 100%;">
                                            <img id="waPhotoThumb" src="#" class="rounded-circle border d-none" style="width: 20px; height: 20px; object-fit: cover;" alt="Foto">
                                            <i class="bi bi-image text-primary" id="waPhotoDefaultIcon"></i>
                                            <span id="waPhotoFileName" class="text-truncate fw-semibold" style="max-width: 85px;">foto.jpg</span>
                                            <span id="waPhotoSizeBadge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-check2 me-0.5"></i> <span id="waPhotoSizeText">Ready</span>
                                            </span>
                                            <button type="button" class="btn-close ms-1" style="font-size: 0.55rem;" id="btnRemoveWaPhoto" title="Hapus Foto"></button>
                                        </div>
                                        <div id="waLocationChip" class="d-none align-items-center gap-1.5 badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2 rounded-pill">
                                            <i class="bi bi-geo-alt-fill"></i>
                                            <span id="waLocationCoordsText">GPS Attached</span>
                                            <button type="button" class="btn-close ms-1" style="font-size: 0.55rem;" id="btnRemoveWaLocation" title="Hapus Lokasi"></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Voice Note to Text (Speech Recognition) Microphone Button -->
                                <button type="button" class="wa-voice-btn" id="btnWaVoiceNote" title="Ketik dengan Suara (Voice Note to Text)">
                                    <i class="bi bi-mic-fill"></i>
                                </button>
                            </div>

                            <!-- Floating Submit Button -->
                            <button type="submit" class="wa-send-btn" id="btnWaSendMsg" title="Kirim Update Kronologis">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                        @endif

                        @if($tiket->status === 'CLOSE')
                        <div class="wa-closed-notice">
                            <i class="bi bi-lock-fill text-muted"></i>
                            <span>Tiket ini berstatus <strong>CLOSE</strong>. Riwayat percakapan koordinasi diarsipkan.</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ════ TAB 2: RESUME PEKERJAAN (FASE 4) ════ -->
                <div class="tab-pane fade" id="resume-pane" role="tabpanel">
                    <!-- Header Banner -->
                    <div class="tab-header-banner">
                        <div class="tab-header-left">
                            <div class="tab-header-icon-box icon-box-blue">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div>
                                <div class="tab-header-title">Resume Akhir Pekerjaan</div>
                                <div class="tab-header-subtitle">Rangkuman temuan masalah, tindakan perbaikan, dan tim pelaksana</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button class="btn-tab-action-pro btn-primary-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#editResumeModal">
                            <i class="bi bi-pencil-square"></i>
                            <span>{{ $tiket->resume ? 'Edit Resume' : 'Input Resume Baru' }}</span>
                        </button>
                        @endif
                    </div>

                    @if($tiket->resume)
                        <div class="row g-3">
                            <!-- Team OM -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro resume-card-team">
                                    <div class="resume-card-label text-info">
                                        <i class="bi bi-people-fill"></i> Tim Teknis Pelaksana
                                    </div>
                                    <div class="resume-card-text">
                                        @if(is_array($tiket->resume->team_om))
                                            <div class="d-flex flex-wrap gap-1.5 mt-1">
                                                @foreach($tiket->resume->team_om as $namaTeknis)
                                                    <span class="badge bg-light text-navy border px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-person-fill text-primary" style="font-size: 0.75rem;"></i>
                                                        <span class="fw-semibold">{{ $namaTeknis }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="fw-semibold text-navy">{{ $tiket->resume->team_om ?: '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Problem -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro resume-card-problem">
                                    <div class="resume-card-label text-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Problem / Temuan Masalah
                                    </div>
                                    <div class="resume-card-text fw-bold text-navy">
                                        {{ $tiket->resume->problem_temuan ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="col-12">
                                <div class="resume-card-pro resume-card-action">
                                    <div class="resume-card-label text-success">
                                        <i class="bi bi-wrench-adjustable-circle-fill"></i> Action / Tindakan Perbaikan
                                    </div>
                                    <div class="resume-card-text" style="white-space: pre-line; line-height: 1.65;">{{ $tiket->resume->action ?: '-' }}</div>
                                </div>
                            </div>

                            <!-- Tipe Penanganan & Jointing Details (Point 4) -->
                            <!-- Tipe Penanganan & Jointing Summary -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #0d9488; background: #f0fdfa;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="resume-card-label text-teal mb-0">
                                            <i class="bi bi-bezier2"></i> Tipe Penanganan Fisik / Core
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm text-teal p-0 text-decoration-none fw-bold" style="font-size: 0.72rem;" onclick="document.getElementById('penanganan-tab')?.click(); document.getElementById('penanganan-pane')?.scrollIntoView({behavior:'smooth'});">
                                            Buka Menu &rarr;
                                        </button>
                                    </div>
                                    <div class="resume-card-text">
                                        @if(in_array(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan), ['KEDUA', 'KOMBINASI', 'SEMUA']))
                                            <span class="badge bg-indigo text-white px-2.5 py-1 rounded-pill" style="background: #6366f1 !important;">
                                                <i class="bi bi-layers-fill me-1"></i> Jointing Lurus &amp; Manuver Core (Keduanya)
                                            </span>
                                        @elseif(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'JOINTING_LURUS')
                                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-diagram-3-fill me-1"></i> Jointing Lurus (Kabel &amp; JC)
                                            </span>
                                        @elseif(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'MANUVER_CORE')
                                            <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-shuffle me-1"></i> Manuver Core (Swapping Core)
                                            </span>
                                        @else
                                            <span class="text-muted small">Belum ditentukan (Pilih di tab Penanganan Core &amp; JC)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #6366f1; background: #eef2ff;">
                                    <div class="resume-card-label" style="color: #4f46e5;">
                                        <i class="bi bi-box-seam-fill"></i> Data Terinput di Menu Penanganan
                                    </div>
                                    <div class="resume-card-text">
                                        <div class="small text-navy">
                                            <strong>Joint Closure:</strong> {{ $tiket->jointClosures->count() }} Titik &bull;
                                            <strong>Manuver:</strong> {{ $tiket->manuverCores->count() }} Mapping Core
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($tiket->resume->catatan_tambahan)
                            <div class="col-12">
                                <div class="resume-card-pro resume-card-notes">
                                    <div class="resume-card-label text-purple">
                                        <i class="bi bi-journal-text"></i> Catatan Tambahan
                                    </div>
                                    <div class="resume-card-text text-muted" style="white-space: pre-line;">{{ $tiket->resume->catatan_tambahan }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <!-- Modern Empty State -->
                        <div class="tab-empty-card">
                            <div class="tab-empty-icon-circle icon-box-blue">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="tab-empty-title">Resume Pekerjaan Belum Diinput</div>
                            <div class="tab-empty-desc">
                                Rangkuman penanganan teknis, akar penyebab gangguan, dan tindakan pemulihan di lapangan belum dicatat untuk tiket ini.
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-primary-pro" data-bs-toggle="modal" data-bs-target="#editResumeModal">
                                <i class="bi bi-plus-circle"></i>
                                <span>Input Resume Sekarang</span>
                            </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- ════ TAB: PENANGANAN CORE & JOINT CLOSURE (JC) ════ -->
                <div class="tab-pane fade" id="penanganan-pane" role="tabpanel">
                    @php
                        $rawPenanganan = $tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan;
                        $hasJc = $tiket->jointClosures->count() > 0;
                        $hasManuver = $tiket->manuverCores->count() > 0;

                        if (in_array($rawPenanganan, ['KEDUA', 'KOMBINASI', 'SEMUA']) || ($hasJc && $hasManuver)) {
                            $activePenanganan = 'KEDUA';
                        } elseif ($rawPenanganan === 'MANUVER_CORE' || (!$hasJc && $hasManuver)) {
                            $activePenanganan = 'MANUVER_CORE';
                        } elseif ($rawPenanganan === 'JOINTING_LURUS' || ($hasJc && !$hasManuver)) {
                            $activePenanganan = 'JOINTING_LURUS';
                        } else {
                            $activePenanganan = 'JOINTING_LURUS';
                        }

                        $isJointingActive = in_array($activePenanganan, ['JOINTING_LURUS', 'KEDUA', 'KOMBINASI', 'SEMUA']);
                        $isManuverActive = in_array($activePenanganan, ['MANUVER_CORE', 'KEDUA', 'KOMBINASI', 'SEMUA']);
                    @endphp

                    <!-- Tipe Penanganan Switcher Banner -->
                    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 bg-white overflow-hidden">
                        <div class="card-body p-2.5 p-sm-3 p-md-4">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2.5 mb-3 pb-2 border-bottom">
                                <div>
                                    <div class="fw-bold text-navy fs-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-bezier2" style="color: #6366f1;"></i>
                                        <span>Pilih Metode Penanganan Fisik / Core</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.8rem;">
                                        Tentukan metode perbaikan kabel. Anda dapat memilih salah satu atau <strong>mengaktifkan keduanya</strong> sekaligus.
                                    </div>
                                </div>
                                <div id="penangananStatusIndicator" class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 flex-wrap w-100 w-md-auto">
                                    <span class="badge bg-light text-navy border px-2.5 py-1.5 rounded-pill small">
                                        Pilihan: <strong id="penangananActiveLabel" class="text-primary">
                                            @if($activePenanganan === 'KEDUA')
                                                Jointing &amp; Manuver (Keduanya)
                                            @elseif($activePenanganan === 'MANUVER_CORE')
                                                Manuver Core
                                            @else
                                                Jointing Lurus
                                            @endif
                                        </strong>
                                    </span>
                                    <button type="button" 
                                            class="btn btn-xs rounded-pill px-2.5 py-1 fw-semibold {{ $activePenanganan === 'KEDUA' ? 'btn-primary text-white shadow-xs' : 'btn-outline-primary' }}" 
                                            id="btnToggleKedua"
                                            onclick="toggleKeduaPenanganan()"
                                            title="Pilih dan aktifkan kedua metode sekaligus">
                                        @if($activePenanganan === 'KEDUA')
                                            <i class="bi bi-check2-all me-1"></i> Keduanya Aktif
                                        @else
                                            <i class="bi bi-layers-fill me-1"></i> Pilih Keduanya
                                        @endif
                                    </button>
                                </div>
                            </div>

                            <div class="row g-2 g-md-3" id="penangananModeSelector">
                                <!-- Option 1: Jointing Lurus -->
                                <div class="col-12 col-md-6">
                                    <div class="penanganan-mode-card {{ $isJointingActive ? 'active is-selected' : '' }}"
                                         id="cardModeJointing"
                                         onclick="togglePenangananCard('JOINTING_LURUS')">
                                        <div class="d-flex align-items-start gap-2.5 penanganan-mode-inner h-100">
                                            <div class="penanganan-mode-icon-box bg-indigo-subtle text-indigo rounded-circle d-flex align-items-center justify-content-center mt-0.5">
                                                <i class="bi bi-diagram-3-fill"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                    <div class="fw-bold text-navy text-truncate penanganan-title-text">1. Jointing Lurus</div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                                        <span class="badge bg-indigo-subtle text-indigo font-monospace px-1.5 py-0.5 rounded-pill" style="font-size: 0.68rem;">{{ $tiket->jointClosures->count() }} Data JC</span>
                                                        <div class="penanganan-radio-check d-flex align-items-center">
                                                            <i class="bi {{ $isJointingActive ? 'bi-check-circle-fill text-indigo fs-5' : 'bi-circle text-muted fs-5' }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-muted penanganan-desc-text">
                                                    Penyambungan kabel lurus eksisting &amp; jumper, mapping tube-core per tray, serta penambahan closure baru.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Manuver Core -->
                                <div class="col-12 col-md-6">
                                    <div class="penanganan-mode-card {{ $isManuverActive ? 'active is-selected' : '' }}"
                                         id="cardModeManuver"
                                         onclick="togglePenangananCard('MANUVER_CORE')">
                                        <div class="d-flex align-items-start gap-2.5 penanganan-mode-inner h-100">
                                            <div class="penanganan-mode-icon-box bg-purple-subtle text-purple rounded-circle d-flex align-items-center justify-content-center mt-0.5">
                                                <i class="bi bi-shuffle"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                    <div class="fw-bold text-navy text-truncate penanganan-title-text">2. Manuver Core</div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                                        <span class="badge bg-purple-subtle text-purple font-monospace px-1.5 py-0.5 rounded-pill" style="font-size: 0.68rem;">{{ $tiket->manuverCores->count() }} Record</span>
                                                        <div class="penanganan-radio-check d-flex align-items-center">
                                                            <i class="bi {{ $isManuverActive ? 'bi-check-circle-fill text-purple fs-5' : 'bi-circle text-muted fs-5' }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-muted penanganan-desc-text">
                                                    Pengalihan alokasi core serat optik (swapping core / bypass jalur putus) sebelum dan sesudah perbaikan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── SUB-VIEW: JOINTING LURUS (KABEL & JC) ── -->
                    <div id="penangananSubViewJointing" class="{{ $isJointingActive ? '' : 'd-none' }}">
                        <div class="tab-header-banner">
                            <div class="tab-header-left">
                                <div class="tab-header-icon-box icon-box-indigo">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                                <div>
                                    <div class="tab-header-title">Pencatatan Kabel &amp; Sambungan Joint Closure (JC)</div>
                                    <div class="tab-header-subtitle">Kapasitas kabel eksisting &amp; jumper, mapping tube-core, status core sisa, dan aset baru closure</div>
                                </div>
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-indigo-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#tambahJointClosureModal">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Tambah Joint Closure</span>
                            </button>
                            @endif
                        </div>

                        @if($tiket->jointClosures->count() > 0)
                            <div class="d-flex flex-column gap-4">
                                @foreach($tiket->jointClosures as $jc)
                                <div class="jc-card-pro">
                                    <!-- JC Card Header -->
                                    <div class="jc-header-pro">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="jc-title-badge">
                                                <i class="bi bi-box-seam text-cyan"></i>
                                                <span>{{ $jc->nama_closure }}</span>
                                            </div>

                                            @if($jc->is_aset_baru)
                                                <span class="jc-meta-chip jc-chip-baru">
                                                    <i class="bi bi-stars"></i> ASET BARU
                                                </span>
                                            @else
                                                <span class="jc-meta-chip jc-chip-eksisting">
                                                    <i class="bi bi-layers"></i> EKSISTING
                                                </span>
                                            @endif

                                            <span class="jc-meta-chip jc-chip-type">
                                                <i class="bi bi-tag-fill text-indigo"></i> {{ $jc->jenis_closure }}
                                            </span>

                                            <span class="jc-meta-chip jc-chip-location">
                                                <i class="bi bi-geo-fill text-orange"></i> {{ str_replace('_', ' ', $jc->lokasi_fisik) }}
                                            </span>

                                            @if($jc->latitude && $jc->longitude)
                                            <a href="{{ $jc->google_maps_url }}" target="_blank" class="jc-meta-chip jc-chip-geo" title="Buka di Google Maps">
                                                <i class="bi bi-geo-alt-fill text-primary"></i> {{ round($jc->latitude, 5) }}, {{ round($jc->longitude, 5) }}
                                                <i class="bi bi-box-arrow-up-right ms-0.5" style="font-size: 0.6rem;"></i>
                                            </a>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                <i class="bi bi-person text-secondary me-1"></i>{{ $jc->creator?->name ?? 'Sistem' }} &bull; {{ $jc->created_at->format('d/m H:i') }}
                                            </span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.joint-closure.destroy', $jc->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Joint Closure {{ $jc->nama_closure }} beserta semua sambungan core di dalamnya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="jc-btn-delete" title="Hapus Joint Closure">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- JC Card Body -->
                                    <div class="card-body p-3 p-md-4">
                                        <!-- Cable Capacity Specs Row -->
                                        <div class="jc-spec-banner mb-3">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-5">
                                                    <div class="jc-spec-box">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                                                                <i class="bi bi-arrow-down-right-circle text-indigo me-1"></i>Kabel Eksisting / Asal
                                                            </span>
                                                            <span class="jc-tube-badge-asal">
                                                                {{ $jc->jumlah_tube_asal }} Tube
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-baseline gap-2">
                                                            <span class="fw-bold text-navy fs-4">{{ $jc->kapasitas_kabel_asal }}</span>
                                                            <span class="text-muted small fw-semibold">Core</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-2 text-center py-1">
                                                    <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded-circle shadow-xs" style="width: 38px; height: 38px;">
                                                        <i class="bi bi-arrow-left-right text-indigo fs-5"></i>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.68rem; font-weight: 600;">DISAMBUNG</div>
                                                </div>

                                                <div class="col-12 col-md-5">
                                                    <div class="jc-spec-box">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                                                                <i class="bi bi-arrow-up-left-circle text-success me-1"></i>Kabel Jumper / Distribusi
                                                            </span>
                                                            <span class="jc-tube-badge-jumper">
                                                                {{ $jc->jumlah_tube_jumper }} Tube
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-baseline gap-2">
                                                            <span class="fw-bold text-navy fs-4">{{ $jc->kapasitas_kabel_jumper }}</span>
                                                            <span class="text-muted small fw-semibold">Core</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($jc->keterangan)
                                            <div class="mt-2 pt-2 border-top text-muted small" style="font-size: 0.78rem;">
                                                <i class="bi bi-info-circle text-primary me-1"></i><strong>Catatan:</strong> {{ $jc->keterangan }}
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Summary Core Status & Action Bar -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                @php
                                                    $cSummary = $jc->core_summary;
                                                @endphp
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-check2-circle me-1"></i>{{ $cSummary['terhubung'] }} Terhubung
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-dash-circle me-1"></i>{{ $cSummary['spare'] }} Core Sisa / Spare
                                                </span>
                                                @if($cSummary['loss'] > 0)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-x-circle me-1"></i>{{ $cSummary['loss'] }} Loss / Putus
                                                </span>
                                                @endif
                                                @if($cSummary['manuver'] > 0)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-shuffle me-1"></i>{{ $cSummary['manuver'] }} Manuver
                                                </span>
                                                @endif
                                                <span class="badge bg-light text-navy border px-2.5 py-1 rounded-pill small fw-semibold">
                                                    Total: {{ $cSummary['total'] }} Splice
                                                </span>
                                            </div>

                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold open-add-core-btn"
                                                    data-jc-id="{{ $jc->id }}"
                                                    data-jc-name="{{ $jc->nama_closure }}"
                                                    data-jc-cap-asal="{{ $jc->kapasitas_kabel_asal ?? 24 }}"
                                                    data-jc-cap-jumper="{{ $jc->kapasitas_kabel_jumper ?? 24 }}"
                                                    data-jc-tube-asal="{{ $jc->jumlah_tube_asal ?? 2 }}"
                                                    data-jc-tube-jumper="{{ $jc->jumlah_tube_jumper ?? 2 }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addJointClosureCoreModal">
                                                <i class="bi bi-plus-lg me-1"></i> Tambah Sambungan Core
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Core Splicing Matrix Records -->
                                        @if($jc->cores->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle border mb-0 rounded-3 overflow-hidden">
                                                    <thead class="table-light">
                                                        <tr style="font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.4px; color: #475569;">
                                                            <th class="ps-3 py-2">Kabel Asal (Tube - Core)</th>
                                                            <th style="width: 30px;"></th>
                                                            <th class="py-2">Kabel Jumper (Tube - Core)</th>
                                                            <th class="py-2">Status</th>
                                                            <th class="py-2">Loss (dB)</th>
                                                            <th class="py-2">Keterangan</th>
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <th class="text-end pe-3 py-2">Aksi</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 0.82rem;">
                                                        @foreach($jc->cores as $c)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="badge bg-indigo-subtle text-indigo font-monospace px-2 py-0.5 me-1" style="font-size: 0.72rem;">
                                                                    {{ $c->tube_asal }}
                                                                </span>
                                                                <span class="font-monospace fw-bold text-navy">
                                                                    {{ $c->core_asal }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center text-muted px-1" style="width: 30px;">
                                                                <i class="bi bi-arrow-right text-indigo"></i>
                                                            </td>
                                                            <td>
                                                                @if($c->tube_jumper || $c->core_jumper)
                                                                    <span class="badge bg-success-subtle text-success font-monospace px-2 py-0.5 me-1" style="font-size: 0.72rem;">
                                                                        {{ $c->tube_jumper ?: '-' }}
                                                                    </span>
                                                                    <span class="font-monospace fw-bold text-navy">
                                                                        {{ $c->core_jumper ?: '-' }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted fst-italic small">Tanpa Sambungan (Dikosongkan)</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $c->status_badge_class }} px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                                    {{ str_replace('_', ' ', $c->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="font-monospace">
                                                                @if($c->loss_db !== null)
                                                                    <span class="badge bg-light text-navy border font-monospace px-2 py-0.5">
                                                                        {{ number_format($c->loss_db, 2) }} dB
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-muted small">
                                                                {{ $c->keterangan ?: '-' }}
                                                            </td>
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <td class="text-end pe-3">
                                                                <form action="{{ route('tiket.joint-closure.delete-core', $c->id) }}" method="POST"
                                                                      onsubmit="return confirm('Hapus baris sambungan ini?');" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger p-0 px-1.5 py-0.5 rounded" title="Hapus baris core">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            @endif
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3 bg-light rounded-3 border border-dashed">
                                                <p class="text-muted small mb-2">Belum ada baris sambungan core yang dicatat untuk closure ini.</p>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 open-add-core-btn"
                                                        data-jc-id="{{ $jc->id }}"
                                                        data-jc-name="{{ $jc->nama_closure }}"
                                                        data-jc-cap-asal="{{ $jc->kapasitas_kabel_asal ?? 24 }}"
                                                        data-jc-cap-jumper="{{ $jc->kapasitas_kabel_jumper ?? 24 }}"
                                                        data-jc-tube-asal="{{ $jc->jumlah_tube_asal ?? 2 }}"
                                                        data-jc-tube-jumper="{{ $jc->jumlah_tube_jumper ?? 2 }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addJointClosureCoreModal">
                                                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris Core Pertama
                                                </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tab-empty-card">
                                <div class="tab-empty-icon-circle icon-box-indigo">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <div class="tab-empty-title">Belum Ada Data Kabel &amp; Joint Closure</div>
                                <div class="tab-empty-desc">
                                    Catat spesifikasi kabel eksisting, kabel jumper, sambungan tube-core, core spare/sisa, dan tandai penambahan aset baru Joint Closure (JC).
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-indigo-pro" data-bs-toggle="modal" data-bs-target="#tambahJointClosureModal">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Tambah Joint Closure Sekarang</span>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- ── SUB-VIEW: MANUVER CORE ── -->
                    <div id="penangananSubViewManuver" class="{{ $isManuverActive ? '' : 'd-none' }} {{ ($isJointingActive && $isManuverActive) ? 'mt-4 pt-3 border-top' : '' }}">
                        <div class="tab-header-banner">
                            <div class="tab-header-left">
                                <div class="tab-header-icon-box icon-box-violet">
                                    <i class="bi bi-shuffle"></i>
                                </div>
                                <div>
                                    <div class="tab-header-title">Catatan Manuver Core Fiber Optik</div>
                                    <div class="tab-header-subtitle">Mapping alokasi core sebelum dan sesudah perbaikan</div>
                                </div>
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-violet-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addManuverModal">
                                <i class="bi bi-plus-circle"></i>
                                <span>Tambah Manuver Core</span>
                            </button>
                            @endif
                        </div>

                        @if($tiket->manuverCores->count() > 0)
                            @php
                                $groupedManuver = $tiket->manuverCores->groupBy('titik');
                            @endphp

                            <div class="row g-4">
                                @foreach($groupedManuver as $titikName => $cores)
                                    @php
                                        $sebelumList = $cores->where('tipe', 'SEBELUM');
                                        $sesudahList = $cores->where('tipe', 'SESUDAH');
                                    @endphp
                                    <div class="col-12 col-xl-6">
                                        <div class="manuver-closure-card-pro h-100">
                                            <div class="manuver-closure-header-pro">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-teal text-navy font-monospace fw-bold px-2 py-1">{{ $titikName }}</span>
                                                    <span class="small fw-semibold">Titik Joint Closure / Patching</span>
                                                </div>
                                                <span class="badge bg-white text-navy font-monospace">{{ $cores->count() }} Mapping Core</span>
                                            </div>

                                            <div class="card-body p-3">
                                                <div class="row g-3">
                                                    <!-- Kolom SEBELUM -->
                                                    <div class="col-12 col-md-6 border-end-md">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="badge bg-secondary text-white"><i class="bi bi-clock-history me-1"></i> SEBELUM (Eksisting)</span>
                                                            <span class="text-muted small">{{ $sebelumList->count() }} item</span>
                                                        </div>

                                                        @if($sebelumList->count() > 0)
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($sebelumList as $mPrev)
                                                                <div class="fiber-route-card-pro">
                                                                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                                                        <span class="text-muted">{{ $mPrev->core_asal }}</span>
                                                                        <i class="bi bi-arrow-right text-secondary mx-1"></i>
                                                                        <span class="fw-bold text-navy">{{ $mPrev->core_tujuan }}</span>
                                                                    </div>
                                                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                                    <form action="{{ route('tiket.manuver-core.destroy', $mPrev->id) }}" method="POST"
                                                                          onsubmit="return confirm('Hapus record manuver ini?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus">
                                                                            <i class="bi bi-trash3"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-light rounded text-center text-muted small">
                                                                Belum ada data alokasi sebelum perbaikan.
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Kolom SESUDAH -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="badge bg-success text-white"><i class="bi bi-check2-circle me-1"></i> SESUDAH (Hasil Perbaikan)</span>
                                                            <span class="text-muted small">{{ $sesudahList->count() }} item</span>
                                                        </div>

                                                        @if($sesudahList->count() > 0)
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($sesudahList as $mNext)
                                                                <div class="fiber-route-card-pro fiber-route-card-success">
                                                                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                                                        <span class="text-muted">{{ $mNext->core_asal }}</span>
                                                                        <i class="bi bi-arrow-right text-success mx-1"></i>
                                                                        <span class="fw-bold text-success">{{ $mNext->core_tujuan }}</span>
                                                                    </div>
                                                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                                    <form action="{{ route('tiket.manuver-core.destroy', $mNext->id) }}" method="POST"
                                                                          onsubmit="return confirm('Hapus record manuver ini?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus">
                                                                            <i class="bi bi-trash3"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-light rounded text-center text-muted small">
                                                                Belum ada data alokasi sesudah perbaikan.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tab-empty-card">
                                <div class="tab-empty-icon-circle icon-box-violet">
                                    <i class="bi bi-shuffle"></i>
                                </div>
                                <div class="tab-empty-title">Belum Ada Data Manuver Core</div>
                                <div class="tab-empty-desc">
                                    Catat alokasi perpindahan core kabel optik (contoh: <code>Tube 2 Core 1 &rarr; Tube 2 Core 1</code>) pada titik perbaikan.
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-violet-pro" data-bs-toggle="modal" data-bs-target="#addManuverModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Manuver Sekarang</span>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ════ TAB 3: MATERIAL & TITIK PERBAIKAN (FASE 4) ════ -->
                <div class="tab-pane fade" id="material-pane" role="tabpanel">
                    <div class="row g-4">
                        <!-- Materials Column -->
                        <div class="col-12 col-lg-6">
                            <div class="tab-header-banner">
                                <div class="tab-header-left">
                                    <div class="tab-header-icon-box icon-box-amber">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </div>
                                    <div>
                                        <div class="tab-header-title">Material Digunakan</div>
                                        <div class="tab-header-subtitle">Pemakaian kabel, splice, & aksesoris</div>
                                    </div>
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-amber-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Material</span>
                                </button>
                                @endif
                            </div>

                            @if($tiket->materials->count() > 0)
                                <!-- Mobile card view -->
                                <div class="d-flex flex-column gap-2 d-md-none">
                                    @foreach($tiket->materials as $mat)
                                    <div class="material-card-mobile">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1">
                                            <i class="bi bi-box text-warning" style="font-size: 0.85rem;"></i>
                                            <div class="fw-semibold text-navy text-truncate small">{{ $mat->nama_material }}</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <span class="material-qty-pill">{{ $mat->jumlah }} {{ $mat->satuan }}</span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.material.destroy', [$tiket->id, $mat->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus material ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Desktop table view -->
                                <div class="table-responsive border rounded-3 bg-white d-none d-md-block shadow-xs">
                                    <table class="table custom-table table-sm align-middle mb-0">
                                        <thead>
                                             <tr>
                                                <th>Nama Material</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Satuan</th>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <th class="text-end" style="width: 40px;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tiket->materials as $mat)
                                            <tr>
                                                <td class="fw-semibold text-navy">{{ $mat->nama_material }}</td>
                                                <td class="text-end font-monospace fw-bold text-navy">{{ $mat->jumlah }}</td>
                                                <td><span class="badge bg-light text-navy border">{{ $mat->satuan }}</span></td>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <td class="text-end">
                                                    <form action="{{ route('tiket.material.destroy', [$tiket->id, $mat->id]) }}" method="POST"
                                                          onsubmit="return confirm('Hapus material ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="tab-empty-card">
                                    <div class="tab-empty-icon-circle icon-box-amber">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="tab-empty-title">Belum Ada Material Dicatat</div>
                                    <div class="tab-empty-desc">
                                        Catat pemakaian kabel fiber optik, protection sleeve, clamp, atau joint closure untuk tiket ini.
                                    </div>
                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                    <button class="btn-tab-action-pro btn-amber-pro" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Tambah Material</span>
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Titik Perbaikan Column -->
                        <div class="col-12 col-lg-6">
                            <div class="tab-header-banner">
                                <div class="tab-header-left">
                                    <div class="tab-header-icon-box icon-box-rose">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div>
                                        <div class="tab-header-title">Titik Tagging Perbaikan</div>
                                        <div class="tab-header-subtitle">Koordinat GPS closure & jointing</div>
                                    </div>
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-rose-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addTitikModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Titik</span>
                                </button>
                                @endif
                            </div>

                            @if($tiket->titikPerbaikans->count() > 0)
                                <!-- Leaflet Map View of Points -->
                                <div id="titikPerbaikanMap" class="mb-3 border rounded-3 shadow-xs overflow-hidden"></div>

                                <!-- Mobile card view -->
                                <div class="d-flex flex-column gap-2 d-md-none">
                                    @foreach($tiket->titikPerbaikans as $tp)
                                    <div class="titik-tag-card-mobile">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="titik-name-badge">{{ $tp->nama_titik }}</span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.titik-perbaikan.destroy', [$tiket->id, $tp->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus titik perbaikan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                        <div class="mb-1">
                                            <a href="{{ $tp->google_maps_url }}" target="_blank" class="small font-monospace text-decoration-none text-navy d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt-fill text-danger"></i>
                                                <span>{{ $tp->latitude }}, {{ $tp->longitude }}</span>
                                                <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.65rem;"></i>
                                            </a>
                                        </div>
                                        @if($tp->keterangan)
                                        <div class="small text-muted fst-italic">{{ $tp->keterangan }}</div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Desktop table view -->
                                <div class="table-responsive border rounded-3 bg-white d-none d-md-block shadow-xs">
                                    <table class="table custom-table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Titik</th>
                                                <th>Koordinat</th>
                                                <th>Keterangan</th>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <th class="text-end" style="width: 40px;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tiket->titikPerbaikans as $tp)
                                            <tr>
                                                <td><span class="titik-name-badge">{{ $tp->nama_titik }}</span></td>
                                                <td>
                                                    <a href="{{ $tp->google_maps_url }}" target="_blank" class="small font-monospace text-decoration-none text-navy">
                                                        {{ $tp->latitude }}, {{ $tp->longitude }} <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.7rem;"></i>
                                                    </a>
                                                </td>
                                                <td class="small text-muted">{{ $tp->keterangan ?: '-' }}</td>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <td class="text-end">
                                                    <form action="{{ route('tiket.titik-perbaikan.destroy', [$tiket->id, $tp->id]) }}" method="POST"
                                                          onsubmit="return confirm('Hapus titik perbaikan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="tab-empty-card">
                                    <div class="tab-empty-icon-circle icon-box-rose">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="tab-empty-title">Belum Ada Titik Tagging</div>
                                    <div class="tab-empty-desc">
                                        Tandai titik lokasi tiang, penutupan closure (JC1, JC2), atau lokasi putus kabel pada peta GPS.
                                    </div>
                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                    <button class="btn-tab-action-pro btn-rose-pro" data-bs-toggle="modal" data-bs-target="#addTitikModal">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Tambah Titik Perbaikan</span>
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ════ TAB 4: DOKUMENTASI FOTO (FASE 5) ════ -->
                <div class="tab-pane fade" id="dokumentasi-pane" role="tabpanel">
                    <div class="tab-header-banner">
                        <div class="tab-header-left">
                            <div class="tab-header-icon-box icon-box-cyan">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                            <div>
                                <div class="tab-header-title">Dokumentasi Foto Lapangan</div>
                                <div class="tab-header-subtitle">Foto jointing, closure, kondisi tiang, dan hasil ukur OTDR</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button class="btn-tab-action-pro btn-cyan-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#uploadDokumentasiModal">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <span>Upload Foto Dokumentasi</span>
                        </button>
                        @endif
                    </div>

                    @if($tiket->dokumentasis->count() > 0)
                        <!-- Category Filter Chips -->
                        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center" id="docFilterChips">
                            <span class="small text-muted fw-bold me-1"><i class="bi bi-funnel-fill text-teal"></i> Filter:</span>
                            <button type="button" class="doc-filter-pill-pro active" data-filter="all">
                                Semua ({{ $tiket->dokumentasis->count() }})
                            </button>
                            @php
                                $categories = $tiket->dokumentasis->groupBy('kategori');
                            @endphp
                            @foreach($categories as $catName => $items)
                            <button type="button" class="doc-filter-pill-pro" data-filter="{{ Str::slug($catName) }}">
                                {{ $catName }} ({{ $items->count() }})
                            </button>
                            @endforeach
                        </div>

                        <!-- Gallery Grid -->
                        <div class="row g-3" id="docGalleryGrid">
                            @foreach($tiket->dokumentasis as $dok)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 doc-gallery-item" data-category="{{ Str::slug($dok->kategori) }}">
                                <div class="doc-card-pro h-100 position-relative">
                                    <!-- Photo Thumbnail -->
                                    <div class="doc-img-wrapper position-relative" style="height: 180px; background-color: #0f172a; overflow: hidden; cursor: pointer;"
                                         onclick="zoomPhoto('{{ asset('storage/' . $dok->file_path) }}', '{{ $dok->kategori }} &bull; {{ $dok->formatted_timestamp }}')">
                                        <img src="{{ asset('storage/' . $dok->file_path) }}"
                                             class="w-100 h-100"
                                             style="object-fit: cover; transition: transform 0.3s ease;"
                                             alt="{{ $dok->kategori }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset($dok->file_path) }}';">
                                        
                                        <!-- Category Badge (Top Left) -->
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge {{ $dok->kategori_badge_class }} shadow-sm">
                                                {{ $dok->kategori }}
                                            </span>
                                        </div>

                                        <!-- Timestamp Overlay (Bottom Left) -->
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                                            <span class="text-white font-monospace" style="font-size: 0.72rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $dok->formatted_timestamp }}
                                            </span>
                                        </div>

                                        <!-- Hover Zoom Overlay Icon -->
                                        <div class="doc-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                             style="background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s ease;">
                                            <span class="badge bg-white text-dark shadow-sm px-2.5 py-1.5 rounded-pill"><i class="bi bi-zoom-in me-1"></i> Lihat Foto</span>
                                        </div>
                                    </div>

                                    <!-- Card Info & Actions Footer -->
                                    <div class="card-body p-2 bg-white d-flex justify-content-between align-items-center border-top">
                                        <div>
                                            @if($dok->latitude && $dok->longitude)
                                            <a href="{{ $dok->google_maps_url }}" target="_blank" class="small text-navy text-decoration-none font-monospace" title="Buka di Google Maps">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ round($dok->latitude, 5) }}, {{ round($dok->longitude, 5) }}
                                                <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.65rem;"></i>
                                            </a>
                                            @else
                                            <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-geo text-muted me-1"></i>Tanpa GPS</span>
                                            @endif
                                        </div>

                                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                        <form action="{{ route('tiket.dokumentasi.destroy', $dok->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto dokumentasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus foto">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="tab-empty-card">
                            <div class="tab-empty-icon-circle icon-box-cyan">
                                <i class="bi bi-camera"></i>
                            </div>
                            <div class="tab-empty-title">Belum Ada Foto Dokumentasi</div>
                            <div class="tab-empty-desc">
                                Unggah foto hasil sambungan (jointing), penutupan closure, tiang jalur kabel, atau hasil pengukuran OTDR sebagai dokumentasi fisik.
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-cyan-pro" data-bs-toggle="modal" data-bs-target="#uploadDokumentasiModal">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Upload Foto Sekarang</span>
                            </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- ════ TAB 6: STOP CLOCK & SHIFT HANDOVER ════ -->
                <div class="tab-pane fade" id="stopclock-pane" role="tabpanel">
                    <div class="tab-header-banner">
                        <div class="tab-header-left">
                            <div class="tab-header-icon-box" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
                                <i class="bi bi-stopwatch"></i>
                            </div>
                            <div>
                                <div class="tab-header-title">Stop Clock SLA & Serah Terima Shift</div>
                                <div class="tab-header-subtitle">Log riwayat jeda penghitungan SLA dan handover antar shift</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if(!$tiket->is_stop_clock)
                            <button class="btn-tab-action-pro btn-warning-pro" data-bs-toggle="modal" data-bs-target="#startStopClockModal">
                                <i class="bi bi-pause-circle"></i>
                                <span>Stop Clock</span>
                            </button>
                            @else
                            <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
                                @csrf
                                <button type="submit" class="btn-tab-action-pro btn-success-pro" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; border: none;">
                                    <i class="bi bi-play-circle-fill"></i>
                                    <span>Resume Clock</span>
                                </button>
                            </form>
                            @endif
                            <button class="btn-tab-action-pro btn-violet-pro" data-bs-toggle="modal" data-bs-target="#handoverShiftModal">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Oper Shift</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- 1. RIWAYAT STOP CLOCK -->
                    <div class="card border-0 shadow-sm rounded-xl mb-4 bg-white">
                        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pause-circle-fill text-warning"></i>
                                <span class="fw-bold text-navy small">Log Stop Clock (Pengurangan Durasi SLA)</span>
                            </div>
                            <span class="badge bg-light text-navy border font-monospace">{{ $tiket->total_stop_clock_minutes }} Menit Total Jeda</span>
                        </div>
                        <div class="card-body p-0">
                            @if($tiket->stopClocks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.83rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-2.5">Alasan Stop Clock</th>
                                            <th class="py-2.5">Mulai Stop</th>
                                            <th class="py-2.5">Resume / Selesai</th>
                                            <th class="py-2.5">Durasi Jeda</th>
                                            <th class="py-2.5">Petugas</th>
                                            <th class="pe-3 py-2.5">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiket->stopClocks as $sc)
                                        <tr>
                                            <td class="ps-3 fw-bold text-navy">
                                                <span class="badge bg-secondary bg-opacity-15 text-dark">{{ $sc->reason_label }}</span>
                                            </td>
                                            <td class="font-monospace text-muted">{{ $sc->stopped_at->format('d/m/Y H:i') }}</td>
                                            <td class="font-monospace">
                                                @if($sc->resumed_at)
                                                    <span class="text-success">{{ $sc->resumed_at->format('d/m/Y H:i') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark animate-pulse">Sedang Berlangsung</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold font-monospace text-navy">
                                                {{ $sc->duration_minutes ? $sc->duration_minutes . ' Menit' : ($sc->is_active ? round(now()->diffInMinutes($sc->stopped_at)) . ' Mnt (Aktif)' : '-') }}
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-navy">{{ $sc->user?->name ?? 'Sistem' }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ $sc->user?->role_short ?? '-' }}</div>
                                            </td>
                                            <td class="pe-3 text-muted small">{{ $sc->notes ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-stopwatch text-muted d-block fs-3 mb-1"></i>
                                Belum ada riwayat Stop Clock pada tiket ini.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. RIWAYAT OPER SHIFT (HANDOVER) -->
                    <div class="card border-0 shadow-sm rounded-xl bg-white">
                        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-arrow-left-right text-purple"></i>
                                <span class="fw-bold text-navy small">Log Serah Terima Pekerjaan (Oper Shift)</span>
                            </div>
                            <span class="badge bg-light text-navy border font-monospace">{{ $tiket->handoverShifts->count() }} Handover</span>
                        </div>
                        <div class="card-body p-0">
                            @if($tiket->handoverShifts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.83rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-2.5">Waktu Handover</th>
                                            <th class="py-2.5">Dari Shift &rarr; Menuju</th>
                                            <th class="py-2.5">Status Lapangan</th>
                                            <th class="py-2.5">Kendala Pending</th>
                                            <th class="py-2.5">Alokasi Tim Lanjutan</th>
                                            <th class="pe-3 py-2.5">Petugas Handover</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiket->handoverShifts as $ho)
                                        <tr>
                                            <td class="ps-3 font-monospace text-muted">{{ $ho->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-25 text-dark">{{ $ho->shift_sebelum ?: 'Shift Sebelumnya' }}</span>
                                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                <span class="badge bg-primary bg-opacity-25 text-primary fw-bold">{{ $ho->shift_tujuan }}</span>
                                            </td>
                                            <td class="small text-navy">{{ $ho->status_lapangan }}</td>
                                            <td class="small text-danger">{{ $ho->kendala_pending ?: '-' }}</td>
                                            <td class="small text-muted">{{ $ho->alokasi_team ?: '-' }}</td>
                                            <td class="pe-3">
                                                <div class="small fw-semibold text-navy">{{ $ho->user?->name ?? 'Sistem' }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ $ho->user?->role_short ?? '-' }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-arrow-left-right text-muted d-block fs-3 mb-1"></i>
                                Belum ada riwayat handover shift pada tiket ini.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- ── MODAL INPUT RESUME PEKERJAAN (FASE 4) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
<div class="modal fade" id="editResumeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.resume.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-file-earmark-text-fill text-teal me-2"></i>{{ $tiket->resume ? 'Edit' : 'Input' }} Resume Pekerjaan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Team OM -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">
                            Team OM / Daftar Nama Teknis Lapangan <span class="text-danger">*</span>
                        </label>
                        <div id="teamOmContainer">
                            @php
                                $teamOmList = $tiket->resume && is_array($tiket->resume->team_om) ? $tiket->resume->team_om : [auth()->user()->name];
                            @endphp
                            @foreach($teamOmList as $index => $nama)
                            <div class="input-group input-group-sm mb-2 team-om-row">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="team_om[]" class="form-control" value="{{ $nama }}" placeholder="Nama Teknisi" required>
                                <button type="button" class="btn btn-outline-danger btn-remove-team" {{ count($teamOmList) > 1 ? '' : 'disabled' }}>
                                    <i class="bi bi-dash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="btnAddTeamMember">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Anggota Team
                        </button>
                    </div>

                    <!-- Problem / Temuan Masalah -->
                    <div class="mb-3">
                        <label for="problem_temuan" class="form-label small fw-bold text-navy">
                            Problem / Temuan Masalah di Lapangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="problem_temuan"
                                  name="problem_temuan"
                                  rows="2"
                                  placeholder="Contoh: Kabel Tertarik Truk di Crossingan Jalan span KM 12+400"
                                  required>{{ old('problem_temuan', $tiket->resume?->problem_temuan) }}</textarea>
                    </div>

                    <!-- Action / Tindakan Perbaikan -->
                    <div class="mb-3">
                        <label for="action" class="form-label small fw-bold text-navy">
                            Action / Tindakan Perbaikan yang Dilakukan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="action"
                                  name="action"
                                  rows="3"
                                  placeholder="Contoh: Jumper Kabel 150 meter, Pemasangan New JC 2 Titik (JC1 dan JC2)..."
                                  required>{{ old('action', $tiket->resume?->action) }}</textarea>
                    </div>

                    <!-- Info Tipe Penanganan (Dikelola di tab Penanganan Core & JC) -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <label class="form-label small fw-bold text-navy mb-0">
                                    <i class="bi bi-bezier2 text-primary me-1"></i> Tipe Penanganan Fisik / Core:
                                </label>
                                <span class="badge {{ ($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'MANUVER_CORE' ? 'bg-primary text-white' : 'bg-success text-white' }} ms-1 px-2.5 py-1 rounded-pill">
                                    {{ $tiket->tipe_penanganan_label }}
                                </span>
                            </div>
                            <small class="text-muted" style="font-size:0.72rem;">
                                Dikelola di menu <strong>Penanganan Core &amp; JC</strong>
                            </small>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="mb-0">
                        <label for="catatan_tambahan" class="form-label small fw-semibold text-navy">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control form-control-sm"
                                  id="catatan_tambahan"
                                  name="catatan_tambahan"
                                  rows="2"
                                  placeholder="Catatan tambahan hasil pengujian redaman / informasi khusus...">{{ old('catatan_tambahan', $tiket->resume?->catatan_tambahan) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Simpan Resume
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH MATERIAL (FASE 4) ── -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.material.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-box-seam-fill text-warning me-2"></i>Tambah Material Digunakan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_material" class="form-label small fw-bold text-navy">
                            Nama Material <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm mb-1" id="nama_material" name="nama_material" placeholder="Contoh: JC 24C / Kabel 24C" required list="presetMaterials">
                        <datalist id="presetMaterials">
                            <option value="JC 24C">
                            <option value="Kabel ADSS 24C">
                            <option value="Kabel Duct 24C">
                            <option value="Protection Sleeve 60mm">
                            <option value="Closure Dome 24C">
                            <option value="Pigtail SC-UPC">
                            <option value="Adapter SC-UPC">
                            <option value="Suspension Clamp">
                            <option value="Dead End Clamp">
                            <option value="Stainless Steel Band">
                        </datalist>
                        <div class="form-text small">Pilih dari rekomendasi atau ketik nama material baru.</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label for="jumlah" class="form-label small fw-bold text-navy">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-sm" id="jumlah" name="jumlah" min="1" value="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="satuan" class="form-label small fw-bold text-navy">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="satuan" name="satuan" required>
                                <option value="pcs" selected>pcs</option>
                                <option value="meter">meter</option>
                                <option value="unit">unit</option>
                                <option value="set">set</option>
                                <option value="roll">roll</option>
                                <option value="tube">tube</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4">
                        <i class="bi bi-plus-circle me-1"></i> Tambahkan Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH JOINT CLOSURE (JC) ── -->
<div class="modal fade" id="tambahJointClosureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.joint-closure.store', $tiket->id) }}" method="POST" id="formTambahJointClosure">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-diagram-3-fill text-teal me-2"></i>Tambah Data Joint Closure (JC) &amp; Sambungan Kabel
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Section 1: Identitas & Lokasi Closure -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <h6 class="fw-bold text-navy mb-3 small text-uppercase letter-spacing-1">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> 1. Identitas &amp; Lokasi Fisik Closure
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label for="jc_nama" class="form-label small fw-bold text-navy">
                                    Nama / Kode Closure <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="jc_nama" name="nama_closure" placeholder="Contoh: JC-01, JC Tiang Span 14" required>
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="jc_jenis" class="form-label small fw-bold text-navy">
                                    Jenis Closure <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" id="jc_jenis" name="jenis_closure" required>
                                    <option value="DOME" selected>DOME (Closure Kubah)</option>
                                    <option value="INLINE">INLINE (Closure Lurus)</option>
                                    <option value="BOX_FAT">BOX FAT / FDT</option>
                                    <option value="OTB">OTB (Optical Termination Box)</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="jc_lokasi" class="form-label small fw-bold text-navy">
                                    Penempatan Fisik <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" id="jc_lokasi" name="lokasi_fisik" required>
                                    <option value="POLE" selected>POLE / Tiang Udara</option>
                                    <option value="MANHOLE">MANHOLE (Bawah Tanah)</option>
                                    <option value="HANDHOLE">HANDHOLE</option>
                                    <option value="PEDESTAL">PEDESTAL</option>
                                    <option value="INDOOR_RACK">INDOOR / Rak OTB</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label small fw-semibold text-navy d-flex justify-content-between mb-1">
                                    <span><i class="bi bi-crosshair text-danger me-1"></i> Koordinat GPS Lokasi Closure</span>
                                    <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationJc">
                                        <i class="bi bi-crosshair"></i> Ambil GPS Saya
                                    </button>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="any" class="form-control" id="latitude_jc" name="latitude" placeholder="Latitude (-6.xxx)">
                                    <input type="number" step="any" class="form-control" id="longitude_jc" name="longitude" placeholder="Longitude (107.xxx)">
                                    <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerJc" title="Pilih di Peta">
                                        <i class="bi bi-map"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-5 d-flex align-items-center">
                                <div class="form-check form-switch p-3 bg-white rounded-3 border w-100">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="jc_is_aset_baru" name="is_aset_baru" value="1">
                                    <label class="form-check-label small fw-bold text-navy cursor-pointer" for="jc_is_aset_baru">
                                        <i class="bi bi-stars text-warning me-1"></i> Tandai Penambahan Aset Baru (New Cut)
                                    </label>
                                    <div class="text-muted" style="font-size: 0.7rem;">Centang jika ada pemasangan closure fisik baru yang sebelumnya tidak ada.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Spesifikasi Kabel & Kapasitas Tube -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <h6 class="fw-bold text-navy mb-3 small text-uppercase letter-spacing-1">
                            <i class="bi bi-bezier2 text-primary me-1"></i> 2. Spesifikasi Kabel &amp; Kapasitas (Kabel Eksisting vs Jumper)
                        </h6>
                        <div class="row g-3">
                            <!-- Kabel Asal -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="d-flex align-items-center gap-1.5 mb-2">
                                        <span class="badge bg-primary text-white rounded-pill px-2">KABEL ASAL / EKSISTING</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label for="jc_kapasitas_asal" class="form-label small fw-bold text-navy">
                                                Kapasitas Kabel <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm" id="jc_kapasitas_asal" name="kapasitas_kabel_asal" required>
                                                <option value="2">2 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core</option>
                                                <option value="48">48 Core</option>
                                                <option value="96">96 Core</option>
                                                <option value="144">144 Core</option>
                                                <option value="288">288 Core</option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="jc_tube_asal" class="form-label small fw-bold text-navy">
                                                Jumlah Tube <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control form-control-sm" id="jc_tube_asal" name="jumlah_tube_asal" value="2" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kabel Jumper -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="d-flex align-items-center gap-1.5 mb-2">
                                        <span class="badge bg-teal text-white rounded-pill px-2">KABEL JUMPER / DISTRIBUSI</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label for="jc_kapasitas_jumper" class="form-label small fw-bold text-navy">
                                                Kapasitas Kabel <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm" id="jc_kapasitas_jumper" name="kapasitas_kabel_jumper" required>
                                                <option value="2">2 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core</option>
                                                <option value="48">48 Core</option>
                                                <option value="96">96 Core</option>
                                                <option value="144">144 Core</option>
                                                <option value="288">288 Core</option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="jc_tube_jumper" class="form-label small fw-bold text-navy">
                                                Jumlah Tube <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control form-control-sm" id="jc_tube_jumper" name="jumlah_tube_jumper" value="2" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Visual Interactive Splicing Tray & Dynamic Core Splicing Matrix -->
                    <div class="p-3 bg-light rounded-3 border">
                        <!-- Visual Interactive Splicing Tray Component -->
                        <div class="fiber-patcher-box mb-3">
                            <div class="fiber-patcher-header">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        <i class="bi bi-bezier2 me-1"></i> Visual Fiber Patcher (Jointing Lurus)
                                    </span>
                                </div>
                                <div class="fiber-preset-actions-scroll">
                                    <button type="button" class="fiber-preset-btn" id="btnJcStraightPreset" title="Sambungkan 1:1 untuk tube yang sedang aktif">
                                        <i class="bi bi-arrows-expand me-1 text-info"></i> Sambung (1:1)
                                    </button>
                                    <button type="button" class="fiber-preset-btn fiber-preset-btn-warning" id="btnJcSwapPreset" title="Swap sambungan Tube 1 ke Tube 2">
                                        <i class="bi bi-shuffle me-1 text-warning"></i> Swap T1 &rarr; T2
                                    </button>
                                    <button type="button" class="fiber-preset-btn fiber-preset-btn-danger" id="btnJcClearPreset" title="Hapus semua sambungan kabel">
                                        <i class="bi bi-trash3 me-1 text-danger"></i> Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Connection Real-time Indicator -->
                            <div class="text-center mb-2">
                                <div class="fiber-conn-status-badge" id="jcLiveWireBadge">
                                    <span class="fiber-pulse-laser text-info"><i class="bi bi-lightning-charge-fill"></i></span>
                                    <span id="jcLiveWireText">Klik port core (C1–C12) untuk langsung menyambung 1-ke-1 secara otomatis</span>
                                </div>
                            </div>

                            <div class="fiber-patcher-grid">
                                <!-- Left: Kabel Asal / Eksisting -->
                                <div class="fiber-panel-card">
                                    <div class="fiber-panel-title text-info">
                                        <span><i class="bi bi-arrow-right-circle me-1"></i> Kabel Asal (Input)</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <select class="fiber-cap-select" id="jcAsalCapacity">
                                                <option value="2">2 Core</option>
                                                <option value="4">4 Core</option>
                                                <option value="6">6 Core</option>
                                                <option value="8">8 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core (2T)</option>
                                                <option value="48">48 Core (4T)</option>
                                                <option value="96">96 Core (8T)</option>
                                                <option value="144">144 Core (12T)</option>
                                                <option value="288">288 Core (24T)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Dynamic Tube Tabs -->
                                    <div class="fiber-tube-tabs" id="jcAsalTubeTabs"></div>
                                    <!-- Dynamic Fiber Core Ports List -->
                                    <div class="fiber-core-list" id="jcAsalCoreList"></div>
                                </div>

                                <!-- Center: Interactive Multi-Wire SVG Fiber Laser Canvas (Desktop) -->
                                <div class="fiber-canvas-center d-none d-lg-flex">
                                    <svg class="fiber-svg-wire" id="jcSvgCanvas" viewBox="0 0 120 260" preserveAspectRatio="none">
                                        <g id="jcSvgWiresGroup">
                                            <!-- Dynamic SVG Paths rendered via JavaScript -->
                                        </g>
                                    </svg>
                                    <div class="text-center mt-1">
                                        <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace" style="font-size:0.68rem;" id="jcWireCountBadge">
                                            <i class="bi bi-arrow-left-right me-1"></i> 0 Sambungan
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Kabel Jumper / Distribusi -->
                                <div class="fiber-panel-card">
                                    <div class="fiber-panel-title text-teal">
                                        <span><i class="bi bi-arrow-left-circle me-1"></i> Kabel Jumper (Output)</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <select class="fiber-cap-select" id="jcJumperCapacity">
                                                <option value="2">2 Core</option>
                                                <option value="4">4 Core</option>
                                                <option value="6">6 Core</option>
                                                <option value="8">8 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core (2T)</option>
                                                <option value="48">48 Core (4T)</option>
                                                <option value="96">96 Core (8T)</option>
                                                <option value="144">144 Core (12T)</option>
                                                <option value="288">288 Core (24T)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Dynamic Tube Tabs -->
                                    <div class="fiber-tube-tabs" id="jcJumperTubeTabs"></div>
                                    <!-- Dynamic Fiber Core Ports List -->
                                    <div class="fiber-core-list" id="jcJumperCoreList"></div>
                                </div>
                            </div>

                            <!-- Connected Chips Tray -->
                            <div class="fiber-chips-tray">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold text-white" style="font-size:0.75rem;">
                                        <i class="bi bi-link-45deg me-1 text-info"></i> Daftar Sambungan Aktif:
                                    </span>
                                    <span class="small text-muted" id="jcTotalCoresText" style="font-size:0.7rem;">0 Core Terhubung</span>
                                </div>
                                <div class="fiber-connections-chips" id="jcConnectionsChips">
                                    <span class="text-muted small fst-italic py-1" style="font-size:0.72rem;">Belum ada core yang disambungkan. Tap port Asal lalu Jumper.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Synchronized Splicing Table -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-navy"><i class="bi bi-table me-1"></i> Daftar Core yang Disambung / Matrix:</span>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5" id="btnAddCoreRow">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Manual
                            </button>
                        </div>

                        <div id="jcCoreRowsContainer" class="d-flex flex-column gap-2">
                            <!-- Template Row 1 (Default) -->
                            <div class="jc-core-input-row p-2.5 bg-white rounded-3 border shadow-xs">
                                <div class="row g-2 align-items-center">
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Asal</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" placeholder="Tube 1" value="Tube 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Asal</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" placeholder="Core 1" value="Core 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Jumper</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" placeholder="Tube 1" value="Tube 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Jumper</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" placeholder="Core 1" value="Core 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Status</label>
                                        <select class="form-select form-select-sm" name="core_status[]">
                                            <option value="TERHUBUNG" selected>TERHUBUNG</option>
                                            <option value="SPARE">SPARE (Sisa)</option>
                                            <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                                            <option value="MANUVER">MANUVER</option>
                                        </select>
                                    </div>
                                    <div class="col-5 col-md-1">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Loss (dB)</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" placeholder="0.02" value="0.02">
                                    </div>
                                    <div class="col-1 text-end pt-3">
                                        <button type="button" class="btn btn-link text-danger p-0 btn-remove-core-row" title="Hapus baris ini">
                                            <i class="bi bi-x-circle fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-indigo-pro btn-sm px-4 fw-semibold shadow-xs">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Data Joint Closure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH SINGLE CORE JOINT CLOSURE ── -->
<div class="modal fade" id="addJointClosureCoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="" method="POST" id="formAddSingleCore">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-diagram-2-fill text-teal me-2"></i>Tambah Sambungan Core - <span id="modalCoreJcTitle">JC</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Visual Port Selector for Single Core -->
                    <div class="fiber-patcher-box mb-3">
                        <div class="fiber-patcher-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-teal text-white px-2 py-0.5 rounded-pill font-monospace" style="font-size:0.75rem;">
                                    <i class="bi bi-bezier2 me-1"></i> Single Core Splicer (Jointing Lurus)
                                </span>
                                <span class="small text-light" style="font-size:0.75rem;">Klik nomor port core (C1–C12) untuk memilih titik sambungan</span>
                            </div>
                        </div>
                        <div class="fiber-patcher-grid">
                            <!-- Left: Asal -->
                            <div class="fiber-panel-card">
                                <div class="fiber-panel-title text-info">
                                    <span><i class="bi bi-arrow-right-circle me-1"></i> Kabel Asal (Input)</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <select class="fiber-cap-select" id="singleAsalCapacity">
                                            <option value="2">2 Core</option>
                                            <option value="4">4 Core</option>
                                            <option value="6">6 Core</option>
                                            <option value="8">8 Core</option>
                                            <option value="12">12 Core</option>
                                            <option value="24" selected>24 Core (2T)</option>
                                            <option value="48">48 Core (4T)</option>
                                            <option value="96">96 Core (8T)</option>
                                            <option value="144">144 Core (12T)</option>
                                            <option value="288">288 Core (24T)</option>
                                        </select>
                                        <span class="badge bg-info bg-opacity-25 text-info font-monospace" id="singleAsalActiveBadge">T1 C1 (Biru)</span>
                                    </div>
                                </div>
                                <div class="fiber-tube-tabs" id="singleAsalTubeTabs"></div>
                                <div class="fiber-core-list" id="singleAsalCoreList"></div>
                            </div>
                            <!-- Center: Laser Wire -->
                            <div class="fiber-canvas-center">
                                <svg class="fiber-svg-wire" id="singleSvgCanvas" viewBox="0 0 130 220" preserveAspectRatio="none">
                                    <path class="fiber-wire-path" id="singleSvgWirePath" d="M 0 20 C 65 20, 65 20, 130 20" stroke="#2563eb" stroke-width="3" fill="none" />
                                </svg>
                                <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace mt-1" style="font-size:0.68rem;" id="singleWireCountBadge">
                                    1:1 Straight
                                </div>
                            </div>
                            <!-- Right: Jumper -->
                            <div class="fiber-panel-card">
                                <div class="fiber-panel-title text-teal">
                                    <span><i class="bi bi-arrow-left-circle me-1"></i> Kabel Jumper (Output)</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <select class="fiber-cap-select" id="singleJumperCapacity">
                                            <option value="2">2 Core</option>
                                            <option value="4">4 Core</option>
                                            <option value="6">6 Core</option>
                                            <option value="8">8 Core</option>
                                            <option value="12">12 Core</option>
                                            <option value="24" selected>24 Core (2T)</option>
                                            <option value="48">48 Core (4T)</option>
                                            <option value="96">96 Core (8T)</option>
                                            <option value="144">144 Core (12T)</option>
                                            <option value="288">288 Core (24T)</option>
                                        </select>
                                        <span class="badge bg-teal bg-opacity-25 text-teal font-monospace" id="singleJumperActiveBadge">T1 C1 (Biru)</span>
                                    </div>
                                </div>
                                <div class="fiber-tube-tabs" id="singleJumperTubeTabs"></div>
                                <div class="fiber-core-list" id="singleJumperCoreList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-navy">Tube Asal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_tube_asal" name="tube_asal" placeholder="Contoh: Tube 1" value="Tube 1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-navy">Core Asal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_core_asal" name="core_asal" placeholder="Contoh: Core 1" value="Core 1" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-navy">Tube Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_tube_jumper" name="tube_jumper" placeholder="Contoh: Tube 1 (opsional jika spare)" value="Tube 1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-navy">Core Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_core_jumper" name="core_jumper" placeholder="Contoh: Core 1 (opsional jika spare)" value="Core 1">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold text-navy">Status Sambungan <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="status" required>
                                <option value="TERHUBUNG" selected>TERHUBUNG (Spliced)</option>
                                <option value="SPARE">SPARE (Core Sisa / Tidak disambung)</option>
                                <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                                <option value="MANUVER">MANUVER</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-semibold text-navy">Loss Sambungan (dB)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db" placeholder="0.02" value="0.02">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-navy">Keterangan Tambahan (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" name="keterangan" placeholder="Contoh: Sambung ke arah Node C">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-plus-circle me-1"></i> Simpan Sambungan Core
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR JOINT CLOSURE ── -->
<div class="modal fade" id="mapPickerJcModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Lokasi Joint Closure di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerJc" style="height: 350px; width: 100%; border-radius: 8px;"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsJcText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsJc">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH TITIK PERBAIKAN (FASE 4) ── -->
<div class="modal fade" id="addTitikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.titik-perbaikan.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Tambah Titik Tagging Perbaikan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_titik" class="form-label small fw-bold text-navy">
                            Nama Titik Tagging <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="nama_titik" name="nama_titik" placeholder="Contoh: JC1 / JC2 / Tiang Span 14" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy d-flex justify-content-between">
                            <span>Koordinat GPS <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationTitik">
                                <i class="bi bi-crosshair"></i> GPS Saya
                            </button>
                        </label>
                        <div class="input-group input-group-sm mb-1">
                            <input type="number" step="any" class="form-control" id="latitude_titik" name="latitude" placeholder="Latitude (-6.xxx)" required>
                            <input type="number" step="any" class="form-control" id="longitude_titik" name="longitude" placeholder="Longitude (106.xxx)" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerTitik" title="Pilih di Peta">
                                <i class="bi bi-map"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="keterangan" class="form-label small fw-semibold text-navy">Keterangan Titik (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" id="keterangan" name="keterangan" placeholder="Contoh: Closure di tiang pinggir jalan raya...">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-pin-map me-1"></i> Simpan Titik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR TITIK PERBAIKAN ── -->
<div class="modal fade" id="mapPickerTitikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Perbaikan di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerTitik"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsTitikText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsTitik">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL UPLOAD DOKUMENTASI FOTO (FASE 5) ── -->
<div class="modal fade" id="uploadDokumentasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.dokumentasi.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="formUploadDokumentasi">
                @csrf
                <input type="hidden" name="source_photo_url" id="docSourcePhotoUrl" value="">
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="uploadDokModalTitle">
                        <i class="bi bi-camera-fill text-info me-2"></i>Upload Foto Dokumentasi Lapangan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Forwarded Photo Preview Box (Shows when saving from chat) -->
                        <div id="docSourcePhotoPreviewBox" class="col-12 d-none">
                            <div class="card border-primary border-opacity-50 bg-primary-subtle bg-opacity-10 p-3 rounded-3 shadow-xs">
                                <div class="d-flex align-items-center gap-3">
                                    <img id="docSourcePhotoImg" src="#" alt="Foto Chat" class="rounded-3 border shadow-xs" style="width: 72px; height: 72px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="badge bg-primary text-white mb-1"><i class="bi bi-chat-left-text me-1"></i> Foto dari Chat Lapangan</div>
                                        <div class="small fw-bold text-navy">Foto ini akan disimpan ke Dokumentasi Wajib Pekerjaan</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Silakan pilih kategori dokumentasi dan verifikasi koordinat/catatan di bawah ini.</div>
                                    </div>
                                    <button type="button" class="btn-close" style="font-size: 0.65rem;" id="btnCancelForwardDoc" title="Batal simpan dari chat"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori Dokumentasi -->
                        <div class="col-12 col-md-6">
                            <label for="doc_kategori" class="form-label small fw-bold text-navy">
                                Kategori Dokumentasi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="doc_kategori" name="kategori" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="Hasil Jointing">🔧 Hasil Jointing / Splicing</option>
                                <option value="Closure Terpasang">📦 Closure Terpasang</option>
                                <option value="Kondisi Lokasi">📍 Kondisi Lokasi & Kerusakan</option>
                                <option value="Hasil OTDR">📊 Hasil Pengukuran OTDR</option>
                                <option value="Dokumentasi Lapangan">📸 Dokumentasi Lapangan Umum</option>
                                <option value="Lain-lain">💬 Lain-lain</option>
                            </select>
                            <!-- Quick Category Selector Chips -->
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Hasil Jointing">Hasil Jointing</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Closure Terpasang">Closure Terpasang</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Kondisi Lokasi">Kondisi Lokasi</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Hasil OTDR">Hasil OTDR</span>
                            </div>
                        </div>

                        <!-- Waktu Dokumentasi -->
                        <div class="col-12 col-md-6">
                            <label for="doc_timestamp" class="form-label small fw-bold text-navy">
                                Waktu Foto / Dokumentasi (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   class="form-control form-control-sm"
                                   id="doc_timestamp"
                                   name="timestamp"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                        </div>

                        <!-- Multi-File Image Upload Zone (Hidden if saving from chat) -->
                        <div class="col-12" id="docMultiUploadWrapper">
                            <label class="form-label small fw-bold text-navy">
                                Pilih File Foto (Bisa Multiple) <span class="text-danger">*</span>
                            </label>
                            <div class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" id="dropzoneDoc">
                                <i class="bi bi-cloud-arrow-up display-5 text-teal opacity-75 d-block mb-1"></i>
                                <span class="small fw-semibold text-navy d-block mb-1">Pilih satu atau beberapa file foto</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Maksimal 5 MB per foto (Format: JPG, PNG, WEBP).</span>
                                <input type="file"
                                       class="form-control form-control-sm mt-2"
                                       id="photosInput"
                                       name="photos[]"
                                       accept="image/jpeg,image/png,image/webp"
                                       multiple
                                       required>
                            </div>
                            <!-- Instant Thumbnails Preview Container -->
                            <div id="docThumbnailsContainer" class="d-flex flex-wrap gap-2 mt-3 d-none"></div>
                        </div>

                        <!-- Koordinat GPS Opsional -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-navy d-flex justify-content-between align-items-center mb-1">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat GPS Lokasi (Otomatis / EXIF)</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="gps-lock-badge d-none" id="docGpsStatusBadge">
                                        <span class="gps-pulse-dot"></span> <span id="docGpsStatusText">GPS Terkunci</span>
                                    </span>
                                    <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationDoc">
                                        <i class="bi bi-crosshair"></i> Ambil GPS
                                    </button>
                                </div>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="any" class="form-control" id="latitude_doc" name="latitude" placeholder="Latitude (-6.xxx)">
                                <input type="number" step="any" class="form-control" id="longitude_doc" name="longitude" placeholder="Longitude (106.xxx)">
                                <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerDoc" title="Pilih di Peta">
                                    <i class="bi bi-map"></i>
                                </button>
                            </div>
                            <div class="form-text small">Koordinat otomatis diambil dari GPS perangkat / metadata EXIF foto saat diupload.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-xs" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; color: #ffffff !important;">
                        <i class="bi bi-cloud-arrow-up-fill me-1 text-white"></i> Simpan Dokumentasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════ WHATSAPP-STYLE MESSAGE INFO MODAL (READ BY / SEEN BY) ════ -->
<div class="modal fade" id="msgInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white py-2.5 px-3.5">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:34px; height:34px; background:rgba(56,189,248,0.2); color:#38bdf8;">
                        <i class="bi bi-info-circle-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" style="font-size:0.95rem;">Info Pesan</h6>
                        <span class="text-white-50" style="font-size:0.72rem;">Detail pembacaan & pengiriman pesan</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3.5 bg-light">
                <!-- Message Preview Snippet Card -->
                <div class="card border-0 shadow-xs rounded-3 mb-3 bg-white p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="fw-bold small text-navy" id="msgInfoSender">Pengirim</span>
                        <span class="text-muted small" style="font-size:0.74rem;" id="msgInfoTime">Waktu</span>
                    </div>
                    <div class="small text-secondary text-break p-2 rounded bg-light border" id="msgInfoContent" style="max-height:110px; overflow-y:auto; font-size:0.82rem;">
                        Isi pesan...
                    </div>
                    <div id="msgInfoPhotoContainer" class="d-none mt-2 text-center">
                        <img id="msgInfoPhotoThumb" src="#" alt="Foto Lampiran" class="rounded border shadow-xs" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                    </div>
                </div>

                <!-- Read Status Section (Dibaca Oleh) -->
                <div class="card border-0 shadow-xs rounded-3 bg-white p-3 mb-2.5">
                    <div class="d-flex align-items-center gap-1.5 mb-2 pb-1.5 border-bottom">
                        <i class="bi bi-check2-all text-info fw-bold fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.86rem;">Dibaca Oleh</h6>
                        <span class="badge bg-info-subtle text-info ms-auto" id="msgInfoReadCountBadge">0 Anggota</span>
                    </div>
                    <div id="msgInfoReadList" class="d-flex flex-column gap-2" style="max-height: 190px; overflow-y: auto;">
                        <!-- Rendered via JS -->
                    </div>
                </div>

                <!-- Delivered Status Section (Terkirim) -->
                <div class="card border-0 shadow-xs rounded-3 bg-white p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2 text-secondary fw-bold fs-5"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-dark" style="font-size:0.84rem;">Terkirim ke Server</div>
                            <div class="text-muted small" style="font-size:0.72rem;" id="msgInfoDeliveredTime">Terkirim</div>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.7rem;">Tersimpan</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3.5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR DOKUMENTASI ── -->
<div class="modal fade" id="mapPickerDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Lokasi Dokumentasi di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerDoc"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsDocText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsDoc">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH MANUVER CORE (FASE 5) ── -->
<div class="modal fade" id="addManuverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <form action="{{ route('tiket.manuver-core.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white px-3 px-md-4 py-2.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-teal bg-opacity-25 p-1.5 text-teal" style="width: 32px; height: 32px;">
                            <i class="bi bi-shuffle fs-6 text-info"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0" style="font-size: 0.95rem;">
                                Tambah Record Manuver Core
                            </h6>
                            <div class="text-white-50" style="font-size: 0.72rem;">Splicing & Bypassing Fiber Optik</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4" style="max-height: calc(100dvh - 125px); overflow-y: auto; -webkit-overflow-scrolling: touch;">
                    <div class="row g-2.5 g-md-3">
                        <!-- Titik Lokasi & Jenis Lokasi -->
                        <div class="col-12 col-md-7">
                            <label for="titik_manuver" class="form-label small fw-bold text-navy mb-1">
                                Titik / Lokasi Manuver <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-sm text-uppercase font-monospace"
                                   id="titik_manuver"
                                   name="titik"
                                   placeholder="Contoh: JC-01, OTB-POP-BDG, FAT-04"
                                   required>
                            <!-- Quick suggestions from existing Titik Perbaikan & Joint Closures -->
                            @if($tiket->titikPerbaikans->count() > 0 || $tiket->jointClosures->count() > 0)
                            <div class="d-flex flex-wrap gap-1 mt-1.5 align-items-center">
                                <span class="small text-muted me-1" style="font-size: 0.7rem;">Pilih preset:</span>
                                @foreach($tiket->titikPerbaikans as $tp)
                                <button type="button" class="btn btn-xs btn-outline-primary titik-preset-btn py-0 px-2" style="font-size: 0.7rem;" data-titik="{{ $tp->nama_titik }}">
                                    {{ $tp->nama_titik }}
                                </button>
                                @endforeach
                                @foreach($tiket->jointClosures as $jc)
                                <button type="button" class="btn btn-xs btn-outline-indigo titik-preset-btn py-0 px-2" style="font-size: 0.7rem;" data-titik="{{ $jc->nama_closure }}">
                                    {{ $jc->nama_closure }}
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="col-12 col-md-5">
                            <label for="lokasi_tipe_manuver" class="form-label small fw-bold text-navy mb-1">
                                Tipe Lokasi Aset <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="lokasi_tipe_manuver" name="lokasi_tipe" required>
                                <option value="CLOSURE_LAPANGAN" selected>CLOSURE LAPANGAN (JC)</option>
                                <option value="POP">POP (Point of Presence)</option>
                                <option value="OTB">OTB (Optical Termination Box)</option>
                                <option value="FAT_FDT">FAT / FDT / ODC</option>
                            </select>
                        </div>

                        <!-- Tipe Manuver (Sebelum / Sesudah) -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-navy d-block mb-1">
                                Status Alokasi <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipe" id="tipeSebelum" value="SEBELUM" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary btn-sm py-1.5" for="tipeSebelum" style="font-size: 0.78rem;">
                                    <i class="bi bi-clock-history me-1"></i> SEBELUM (Awal)
                                </label>

                                <input type="radio" class="btn-check" name="tipe" id="tipeSesudah" value="SESUDAH" autocomplete="off">
                                <label class="btn btn-outline-success btn-sm py-1.5" for="tipeSesudah" style="font-size: 0.78rem;">
                                    <i class="bi bi-check2-circle me-1"></i> SESUDAH (Hasil)
                                </label>
                            </div>
                        </div>

                        <!-- Durasi Status Manuver -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-navy d-block mb-1">
                                Sifat Manuver <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="status_manuver" id="manuverTemp" value="TEMPORARY" autocomplete="off" checked>
                                <label class="btn btn-outline-warning btn-sm py-1.5" for="manuverTemp" style="font-size: 0.78rem;">
                                    <i class="bi bi-hourglass-split me-1"></i> SEMENTARA (Darurat)
                                </label>

                                <input type="radio" class="btn-check" name="status_manuver" id="manuverPerm" value="PERMANENT" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm py-1.5" for="manuverPerm" style="font-size: 0.78rem;">
                                    <i class="bi bi-pin-angle-fill me-1"></i> PERMANEN
                                </label>
                            </div>
                        </div>

                        <!-- VISUAL INTERACTIVE CABLE PATCHER FOR MANUVER CORE -->
                        <div class="col-12">
                            <div class="fiber-patcher-box">
                                <div class="fiber-patcher-header">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                            <i class="bi bi-bezier2 me-1"></i> Visual Fiber Patcher
                                        </span>
                                    </div>
                                    <div class="fiber-preset-actions-scroll">
                                        <button type="button" class="fiber-preset-btn" id="btnManuverStraightPreset" title="Sambungkan 1:1 untuk tube yang sedang aktif">
                                            <i class="bi bi-arrows-expand me-1 text-info"></i> Sambung (1:1)
                                        </button>
                                        <button type="button" class="fiber-preset-btn fiber-preset-btn-warning" id="btnManuverSwapPreset" title="Swap sambungan Tube 1 ke Tube 2">
                                            <i class="bi bi-shuffle me-1 text-warning"></i> Swap T1 &rarr; T2
                                        </button>
                                        <button type="button" class="fiber-preset-btn fiber-preset-btn-danger" id="btnManuverClearPreset" title="Hapus semua sambungan kabel">
                                            <i class="bi bi-trash3 me-1 text-danger"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <!-- Connection Real-time Indicator -->
                                <div class="text-center mb-2">
                                    <div class="fiber-conn-status-badge" id="manuverLiveWireBadge">
                                        <span class="fiber-pulse-laser text-info"><i class="bi bi-lightning-charge-fill"></i></span>
                                        <span id="manuverLiveWireText">Tap Port Asal (Kiri) &rarr; Tap Port Tujuan (Kanan)</span>
                                    </div>
                                </div>

                                <div class="fiber-patcher-grid">
                                    <!-- Panel Kiri: Port Asal / Input Cable -->
                                    <div class="fiber-panel-card">
                                        <div class="fiber-panel-title text-info">
                                            <span><i class="bi bi-box-arrow-in-right me-1"></i> ASAL (INPUT)</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="fiber-cap-select" id="manuverAsalCapacity">
                                                    <option value="2">2 Core</option>
                                                    <option value="4">4 Core</option>
                                                    <option value="6">6 Core</option>
                                                    <option value="8">8 Core</option>
                                                    <option value="12">12 Core</option>
                                                    <option value="24" selected>24 Core (2T)</option>
                                                    <option value="48">48 Core (4T)</option>
                                                    <option value="96">96 Core (8T)</option>
                                                    <option value="144">144 Core (12T)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Dynamic Tube Tabs -->
                                        <div class="fiber-tube-tabs" id="manuverAsalTubeTabs"></div>
                                        <!-- Dynamic Fiber Core Ports List -->
                                        <div class="fiber-core-list" id="manuverAsalCoreList"></div>
                                    </div>

                                    <!-- Center: Interactive Multi-Wire SVG Fiber Laser Canvas (Desktop) -->
                                    <div class="fiber-canvas-center d-none d-lg-flex">
                                        <svg class="fiber-svg-wire" id="manuverSvgCanvas" viewBox="0 0 120 260" preserveAspectRatio="none">
                                            <g id="manuverSvgWiresGroup">
                                                <!-- Dynamic SVG Paths rendered via JavaScript -->
                                            </g>
                                        </svg>
                                        <div class="text-center mt-1">
                                            <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace" style="font-size:0.68rem;" id="manuverWireCountBadge">
                                                <i class="bi bi-arrow-left-right me-1"></i> 0 Garis
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Panel Kanan: Port Tujuan / Output Cable -->
                                    <div class="fiber-panel-card">
                                        <div class="fiber-panel-title text-success">
                                            <span><i class="bi bi-box-arrow-right me-1"></i> TUJUAN (OUTPUT)</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="fiber-cap-select" id="manuverTujuanCapacity">
                                                    <option value="2">2 Core</option>
                                                    <option value="4">4 Core</option>
                                                    <option value="6">6 Core</option>
                                                    <option value="8">8 Core</option>
                                                    <option value="12">12 Core</option>
                                                    <option value="24" selected>24 Core (2T)</option>
                                                    <option value="48">48 Core (4T)</option>
                                                    <option value="96">96 Core (8T)</option>
                                                    <option value="144">144 Core (12T)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Dynamic Tube Tabs -->
                                        <div class="fiber-tube-tabs" id="manuverTujuanTubeTabs"></div>
                                        <!-- Dynamic Fiber Core Ports List -->
                                        <div class="fiber-core-list" id="manuverTujuanCoreList"></div>
                                    </div>
                                </div>

                                <!-- Connected Lines Tray (Chips) -->
                                <div class="fiber-chips-tray" id="manuverChipsTray">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span class="small fw-bold text-light" style="font-size:0.75rem;">
                                            <i class="bi bi-diagram-3 me-1 text-info"></i> Sambungan Terpasang:
                                        </span>
                                        <span class="small text-muted font-monospace" style="font-size:0.68rem;" id="manuverTotalCoresText">0 Core</span>
                                    </div>
                                    <div class="fiber-connections-chips" id="manuverConnectionsChips">
                                        <span class="text-muted small fst-italic py-1" style="font-size:0.72rem;">Belum ada core yang disambungkan. Tap port Asal lalu Tujuan.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Synchronized Inputs for Backend Submission (Handles multiple pairs) -->
                            <div id="manuverHiddenInputsContainer">
                                <input type="hidden" id="core_asal" name="core_asal[]" value="Tube 1 Core 1">
                                <input type="hidden" id="core_tujuan" name="core_tujuan[]" value="Tube 1 Core 1">
                            </div>
                        </div>

                        <!-- Core Dialihkan & Titik Kembali -->
                        <div class="col-12 col-sm-6">
                            <label for="core_dialihkan" class="form-label small fw-semibold text-navy mb-1">Core Yang Dialihkan (Opsional)</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="core_dialihkan" name="core_dialihkan" placeholder="Contoh: Core 4 dialihkan ke Core 12">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="titik_kembali" class="form-label small fw-semibold text-navy mb-1">Titik Normalisasi / Kembali (Opsional)</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="titik_kembali" name="titik_kembali" placeholder="Contoh: OTB POP Bandung Rack 2">
                        </div>

                        <!-- Status Core Aset -->
                        <div class="col-12 col-sm-6">
                            <label for="status_core_aset" class="form-label small fw-bold text-navy mb-1">Status Core Aset</label>
                            <select class="form-select form-select-sm" id="status_core_aset" name="status_core_aset">
                                <option value="OCCUPIED_MANUVER" selected>OCCUPIED MANUVER (Terpakai Jalur Baru)</option>
                                <option value="BROKEN_LOSS">BROKEN / LOSS (Core Rusak)</option>
                                <option value="SPARE_AVAILABLE">SPARE AVAILABLE (Tersedia)</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12 col-sm-6">
                            <label for="keterangan_manuver" class="form-label small fw-semibold text-navy mb-1">Keterangan / Alasan Manuver</label>
                            <input type="text" class="form-control form-control-sm" id="keterangan_manuver" name="keterangan" placeholder="Contoh: Bypassing kabel putus span 14">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-3 px-md-4 py-2.5 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-cjp-teal btn-sm px-4 fw-semibold shadow-xs">
                        <i class="bi bi-save me-1"></i> Simpan Manuver Core
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL INPUT KRONOLOGIS (FASE 3) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
<div class="modal fade" id="addKronologisModal" tabindex="-1" aria-labelledby="addKronologisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.kronologis.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="formAddKronologis">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="addKronologisModalLabel">
                        <i class="bi bi-plus-circle-fill text-teal me-2"></i>Tambah Update Kronologis Lapangan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Kategori Kronologis -->
                        <div class="col-12 col-md-6">
                            <label for="kategori" class="form-label small fw-bold text-navy">
                                Kategori Update <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="kategori" name="kategori" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="IZIN">🔑 IZIN - Izin Masuk Ruangan / Lokasi</option>
                                <option value="OTDR">📊 OTDR - Hasil Pengukuran OTDR</option>
                                <option value="TRACING">🔍 TRACING - Tracing Jalur Kabel</option>
                                <option value="MATERIAL">📦 MATERIAL - Material Menuju / Sampai Lokasi</option>
                                <option value="JOINTING">🔧 JOINTING - Proses Jointing / Splicing</option>
                                <option value="LINK_UP">📶 LINK_UP - Link UP / Normalisasi</option>
                                <option value="SELESAI">✅ SELESAI - Pekerjaan Selesai</option>
                                <option value="LAIN">💬 LAIN - Update Lapangan Lainnya</option>
                            </select>
                        </div>

                        <!-- Waktu Update -->
                        <div class="col-12 col-md-6">
                            <label for="krono_timestamp" class="form-label small fw-bold text-navy">
                                Tanggal & Jam Update (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   class="form-control form-control-sm"
                                   id="krono_timestamp"
                                   name="timestamp"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                        </div>

                        <!-- Informasi Kronologis -->
                        <div class="col-12">
                            <label for="informasi" class="form-label small fw-bold text-navy">
                                Informasi & Catatan Pekerjaan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-sm"
                                      id="informasi"
                                      name="informasi"
                                      rows="3"
                                      placeholder="Tuliskan perkembangan pekerjaan (misal: 'Team tiba di lokasi span KM 12+400, persiapan pasang closure JC1...')"
                                      required></textarea>
                        </div>

                        <!-- Upload Foto Lapangan (Opsional) -->
                        <div class="col-12 col-md-6">
                            <label for="foto" class="form-label small fw-semibold text-navy">
                                <i class="bi bi-camera me-1"></i> Upload Foto Lapangan (Opsional)
                            </label>
                            <input type="file"
                                   class="form-control form-control-sm"
                                   id="foto"
                                   name="foto"
                                   accept="image/*">
                            <div class="form-text small">Maks 5 MB (Format: JPG, PNG, WEBP).</div>
                            <div id="fotoPreviewContainer" class="mt-2 d-none">
                                <img id="fotoPreview" src="#" alt="Preview Foto" class="img-thumbnail" style="max-height: 90px;">
                            </div>
                        </div>

                        <!-- Koordinat GPS (Opsional) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-navy d-flex justify-content-between">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat GPS (Opsional)</span>
                                <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocation">
                                    <i class="bi bi-crosshair"></i> GPS Saya
                                </button>
                            </label>
                            <div class="input-group input-group-sm mb-1">
                                <input type="number" step="any" class="form-control" id="latitude" name="latitude" placeholder="Latitude (-6.xxx)">
                                <input type="number" step="any" class="form-control" id="longitude" name="longitude" placeholder="Longitude (106.xxx)">
                                <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPicker" title="Pilih di Peta">
                                    <i class="bi bi-map"></i>
                                </button>
                            </div>
                            <div class="form-text small" id="locationStatus">Klik "GPS Saya" atau ikon peta untuk memilih titik.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-cjp-teal btn-sm px-4" id="btnSubmitKronologis">
                        <i class="bi bi-send-fill me-1"></i> Kirim Update Kronologis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER (LEAFLET.JS) ── -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Koordinat di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPicker"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoords">
                        <i class="bi bi-check2 me-1"></i> Gunakan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL EDIT PESAN KOORDINASI ── -->
<div class="modal fade" id="editKronoMsgModal" tabindex="-1" aria-labelledby="editKronoMsgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-bottom py-3 bg-light rounded-top-4">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="editKronoMsgModalLabel">
                    <i class="bi bi-pencil-square text-warning fs-5"></i> Edit Pesan Koordinasi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKronoMsg">
                <div class="modal-body py-3">
                    <input type="hidden" id="editKronoId" value="">
                    <div class="mb-2">
                        <label for="editKronoTextInput" class="form-label small fw-semibold text-muted">Isi Pesan</label>
                        <textarea id="editKronoTextInput" class="form-control rounded-3" rows="4" required placeholder="Tulis perbaikan isi pesan koordinasi..." style="font-size: 0.88rem; resize: vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveEditKrono">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL KONFIRMASI HAPUS PESAN KOORDINASI ── -->
<div class="modal fade" id="deleteKronoMsgModal" tabindex="-1" aria-labelledby="deleteKronoMsgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg border-0 text-center p-3">
            <div class="modal-body py-2">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; font-size: 1.35rem;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Hapus Pesan?</h6>
                <p class="text-muted small mb-0">Apakah Anda yakin ingin menghapus catatan koordinasi ini? Tindakan tidak dapat dibatalkan.</p>
                <input type="hidden" id="deleteKronoId" value="">
            </div>
            <div class="d-flex gap-2 justify-content-center mt-3">
                <button type="button" class="btn btn-light rounded-pill px-3 flex-grow-1" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-3 flex-grow-1" id="btnConfirmDeleteKrono">
                    <i class="bi bi-trash3 me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL ZOOM PHOTO LIGHTBOX ── -->
<div class="modal fade photo-lightbox-modal" id="photoZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content photo-lightbox-content">
            <div class="photo-lightbox-header">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-50 px-2 py-1 rounded-pill small">
                        <i class="bi bi-camera-fill me-1"></i>Foto Lapangan
                    </span>
                    <span class="text-white fw-semibold small text-truncate" id="photoZoomTitle">Foto Dokumentasi</span>
                </div>
                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                    <a href="#" id="photoZoomDownloadBtn" target="_blank" download="foto-lapangan.jpg" class="btn-lightbox-action" title="Download Foto">
                        <i class="bi bi-download"></i>
                    </a>
                    <button type="button" class="btn-lightbox-close" data-bs-dismiss="modal" title="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="photo-lightbox-body">
                <img src="#" id="photoZoomImg" class="photo-lightbox-img" alt="Zoom Foto Lapangan">
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL CLOSING AWAL TEKNISI (POINT 1 & 2) ── -->
@if(auth()->user()->hasRole(['admin', 'teknis', 'teknisi']) && $tiket->status === 'PROSES')
<div class="modal fade" id="closingAwalModal" tabindex="-1" aria-labelledby="closingAwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.closing-awal', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="closingAwalModalLabel">
                        <i class="bi bi-check2-all text-info me-2"></i>Penyelesaian Pekerjaan Lapangan (Closing Awal)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-primary py-2.5 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Setelah disubmit, status tiket akan berubah menjadi <strong>MENUNGGU VERIFIKASI (PENDING_VERIFIKASI)</strong>. HelpDesk / NOC akan melakukan pengecekan stabilitas link sebelum closing akhir.
                    </div>

                    <!-- Checklist Prasyarat Mandatori (Point 1) -->
                    <div class="card border rounded-3 p-3 mb-3 bg-light">
                        <h6 class="fw-bold text-navy small mb-2 d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-check text-primary"></i> Checklist Mandatori Penyelesaian Tiket
                        </h6>
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['resume_filled'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">1. Resume Pekerjaan</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['resume_filled'] ? 'Problem & Action terisi' : 'Belum diisi di tab Resume' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['photo_uploaded'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">2. Dokumentasi Foto Lapangan / OTDR</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['photo_uploaded'] ? $tiket->dokumentasis->count() . ' foto terupload' : 'Minimal 1 foto dokumentasi wajib diunggah' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['titik_perbaikan'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">3. Titik Koordinat Perbaikan / JC</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['titik_perbaikan'] ? $tiket->titikPerbaikans->count() . ' titik tercatat' : 'Minimal 1 titik perbaikan wajib diinput' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['tipe_penanganan'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">4. Tipe Penanganan</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['tipe_penanganan'] ? str_replace('_', ' ', $tiket->tipe_penanganan) : 'Wajib dipilih di Penanganan Core & JC (Jointing / Manuver)' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$prereqs['ready'])
                        <div class="alert alert-danger py-2 px-3 small mt-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Perhatian:</strong> Anda belum dapat mengajukan closing karena masih ada <strong>{{ count($prereqs['missing_items']) }} item prasyarat mandatori</strong> yang belum dilengkapi.
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="catatan_closing_teknisi" class="form-label small fw-bold text-navy">
                            Catatan Ringkas Hasil Lapangan untuk Helpdesk / NOC <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="catatan_closing_teknisi"
                                  name="catatan_closing_teknisi"
                                  rows="3"
                                  placeholder="Contoh: Splicing core 1-12 di closure KM 14 selesai, redaman terukur -18.2 dBm, link siap diverifikasi NOC..."
                                  required>{{ old('catatan_closing_teknisi') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" {{ $prereqs['ready'] ? '' : 'disabled' }}>
                        <i class="bi bi-send-check me-1"></i> Selesaikan & Kirim ke NOC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL REJECT CLOSING AWAL OLEH HELPDESK (POINT 2) ── -->
@if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'PENDING_VERIFIKASI')
<div class="modal fade" id="rejectClosingAwalModal" tabindex="-1" aria-labelledby="rejectClosingAwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.reject-closing-awal', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold text-white" id="rejectClosingAwalModalLabel">
                        <i class="bi bi-arrow-return-left me-2"></i>Kembalikan ke Lapangan (Reject Closing)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 small mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> Status tiket akan dikembalikan ke <strong>PROSES</strong>. Teknisi akan diminta melakukan investigasi atau perbaikan lanjutan.
                    </div>

                    <div class="mb-0">
                        <label for="alasan_reject" class="form-label small fw-bold text-navy">
                            Alasan Penolakan / Catatan untuk Teknisi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="alasan_reject"
                                  name="alasan_reject"
                                  rows="3"
                                  placeholder="Contoh: Redaman di OLT masih tinggi (-28 dBm) atau link masih flapping, mohon periksa kembali core nomor 4..."
                                  required>{{ old('alasan_reject') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-x-circle me-1"></i> Konfirmasi Kembalikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL STOP CLOCK (POINT 3) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
<div class="modal fade" id="startStopClockModal" tabindex="-1" aria-labelledby="startStopClockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.stop-clock.start', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title fw-bold" id="startStopClockModalLabel">
                        <i class="bi bi-pause-circle-fill me-2"></i>Stop Clock SLA (Jeda Perhitungan)
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Selama stop clock aktif, durasi jeda waktu tidak akan dihitung ke dalam SLA / MTTR gangguan link.
                    </div>

                    <div class="mb-3">
                        <label for="stop_clock_reason" class="form-label small fw-bold text-navy">
                            Alasan Stop Clock <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm" id="stop_clock_reason" name="reason" required>
                            <option value="">-- Pilih Alasan Stop Clock --</option>
                            <option value="MENUNGGU_AKSES_PELANGGAN">Menunggu Izin / Akses Lokasi Pelanggan</option>
                            <option value="CUACA_BURUK">Cuaca Buruk / Hujan Deras / Banjir</option>
                            <option value="MENUNGGU_MATERIAL">Menunggu Pengiriman Material / Sparing Khusus</option>
                            <option value="KENDALA_PIHAK_KETIGA">Kendala Perizinan Pihak Ketiga (PLN/Bina Marga/dll)</option>
                            <option value="PERMINTAAN_PELANGGAN">Penundaan atas Permintaan Pelanggan</option>
                            <option value="FORCE_MAJEURE">Force Majeure / Bencana Alam</option>
                            <option value="LAINNYA">Alasan Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="stop_clock_notes" class="form-label small fw-semibold text-navy">Keterangan Tambahan / Detail Kendala</label>
                        <textarea class="form-control form-control-sm"
                                  id="stop_clock_notes"
                                  name="notes"
                                  rows="2"
                                  placeholder="Contoh: Petugas keamanan gedung belum memberikan izin masuk sebelum jam 13:00..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold">
                        <i class="bi bi-pause-fill me-1"></i> Mulai Stop Clock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL OPER SHIFT / HANDOVER (POINT 8) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
<div class="modal fade" id="handoverShiftModal" tabindex="-1" aria-labelledby="handoverShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">
            <form action="{{ route('tiket.handover-shift', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white p-3.5" style="background: linear-gradient(135deg, #07152b 0%, #102d66 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(139, 92, 246, 0.25); color: #c4b5fd;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0" id="handoverShiftModalLabel">Serah Terima Pekerjaan (Oper Shift)</h6>
                            <span class="small text-white-50" style="font-size: 0.72rem;">Tiket: {{ $tiket->no_tiket }} ({{ $tiket->backbone_segment }})</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <!-- Status Ringkasan Lapangan Saat Ini -->
                    <div class="p-2.5 rounded-3 mb-3 bg-light border border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="small">
                            <span class="text-muted">Status:</span> <strong class="text-navy">{{ $tiket->status }}</strong> &bull;
                            <span class="text-muted">Update:</span> <strong>{{ $tiket->minutes_since_last_update !== null ? $tiket->minutes_since_last_update . 'm lalu' : 'Baru' }}</strong>
                        </div>
                        @if($tiket->is_stop_clock)
                            <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.68rem;">
                                <i class="bi bi-pause-circle-fill me-1"></i>SLA Paused
                            </span>
                        @endif
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="shift_sebelum" class="form-label small fw-semibold text-navy mb-1">Shift Asal (From)</label>
                            <select class="form-select form-select-sm" id="shift_sebelum" name="shift_sebelum" required>
                                <option value="Shift 1 (Pagi 07:00-15:00)" selected>Shift 1 (Pagi 07:00-15:00)</option>
                                <option value="Shift 2 (Siang 15:00-23:00)">Shift 2 (Siang 15:00-23:00)</option>
                                <option value="Shift 3 (Malam 23:00-07:00)">Shift 3 (Malam 23:00-07:00)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="shift_tujuan" class="form-label small fw-bold text-navy mb-1">
                                Shift Tujuan (To) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="shift_tujuan" name="shift_tujuan" required>
                                <option value="Shift 1 (Pagi 07:00-15:00)">Shift 1 (Pagi 07:00-15:00)</option>
                                <option value="Shift 2 (Siang 15:00-23:00)" selected>Shift 2 (Siang 15:00-23:00)</option>
                                <option value="Shift 3 (Malam 23:00-07:00)">Shift 3 (Malam 23:00-07:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_to_id" class="form-label small fw-bold text-navy mb-1">
                            Petugas Penerima Handover (PIC Lanjutan)
                        </label>
                        <select class="form-select form-select-sm" id="user_to_id" name="user_to_id">
                            <option value="">-- Pilih Petugas Penerima Shift --</option>
                            @foreach($mentionableUsers as $mu)
                                <option value="{{ $mu['id'] }}">
                                    {{ $mu['name'] }} ({{ $mu['role'] }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size: 0.7rem;">Petugas terpilih akan menerima notifikasi serah terima tiket ini secara instan di HP &amp; browser.</div>
                    </div>

                    <!-- Quick Template Chips -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-navy mb-1">Template Cepat Catatan:</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Jointing Core Berlangsung] ')">+ Jointing Core</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Menunggu OTDR Ulang] ')">+ Butuh OTDR</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Material & Splicer Lengkap] ')">+ Material OK</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Kunci Shelter Diserahterimakan] ')">+ Kunci Shelter</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status_lapangan" class="form-label small fw-bold text-navy mb-1">
                            Catatan Serah Terima / Kondisi Lapangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="status_lapangan"
                                  name="status_lapangan"
                                  rows="3"
                                  placeholder="Tuliskan perkembangan pekerjaan terakhir, sisa core/kabel yang perlu disambung, dan hal penting bagi petugas shift berikutnya..."
                                  required></textarea>
                    </div>

                    <div class="mb-0">
                        <label for="kendala_pending" class="form-label small fw-semibold text-navy mb-1">Hambatan / Catatan Tambahan (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" id="kendala_pending" name="kendala_pending" placeholder="Contoh: Genset/baterai splicer menipis, butuh recharge">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 px-3.5 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none;">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Kirim &amp; Simpan Handover</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function appendHandoverNote(text) {
        const textarea = document.getElementById('status_lapangan');
        if (textarea) {
            textarea.value = (textarea.value ? textarea.value + ' ' : '') + text;
            textarea.focus();
        }
    }
</script>
@endif

<!-- ── MODAL CLOSING TIKET / VERIFIKASI CLOSING AKHIR ── -->
@if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'PENDING_VERIFIKASI')
<div class="modal fade" id="closeTiketModal" tabindex="-1" aria-labelledby="closeTiketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.close', $tiket->id) }}" method="POST" id="formCloseTiket">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="closeTiketModalLabel">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ $tiket->status === 'PENDING_VERIFIKASI' ? 'Verifikasi & Closing Akhir' : 'Penutupan Tiket' }} [{{ $tiket->no_tiket }}]
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($tiket->status === 'PENDING_VERIFIKASI')
                    <div class="alert alert-success py-2.5 px-3 small mb-3">
                        <div class="fw-bold"><i class="bi bi-check2-all me-1"></i> Closing Awal dari Teknisi:</div>
                        <div class="text-muted mt-1">
                            Diselesaikan oleh <strong>{{ $tiket->resolver?->name ?? 'Teknisi' }}</strong> pada {{ $tiket->resolved_at ? $tiket->resolved_at->format('d/m/Y H:i') : '-' }}.
                            @if($tiket->closing_notes_teknisi)
                            <div class="mt-1 fst-italic">"{{ $tiket->closing_notes_teknisi }}"</div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Saat tiket di-close, sistem akan mengunci data dan secara otomatis menghitung durasi total gangguan (MTTR) serta memeriksa kepatuhan SLA.
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status Link Impact</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->status_link_impact }}" readonly>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Waktu Open</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" value="{{ $tiket->tanggal_open->format('d/m/Y H:i') }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Target SLA</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->formatted_sla_target }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_tanggal_close" class="form-label small fw-bold text-navy">
                            Waktu Closing Tiket (WIB) <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                               class="form-control form-control-sm"
                               id="modal_tanggal_close"
                               name="tanggal_close"
                               value="{{ now()->format('Y-m-d\TH:i') }}"
                               required>
                        <div class="form-text small">Sesuaikan jika waktu link UP berbeda dengan saat closing.</div>
                    </div>

                    <!-- Live MTTR and SLA Calculator Preview in Modal -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Total Jeda Stop Clock:</span>
                            <span class="fw-bold font-monospace text-warning">{{ $tiket->total_stop_clock_minutes }} Menit</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Estimasi MTTR (Bersih):</span>
                            <span class="fw-bold font-monospace text-navy" id="modalMttrPreview">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Status SLA:</span>
                            <span class="badge bg-secondary" id="modalSlaPreview">Menghitung...</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="modal_catatan_closing" class="form-label small fw-semibold text-navy">Catatan Closing (Opsional)</label>
                        <textarea class="form-control form-control-sm"
                                  id="modal_catatan_closing"
                                  name="catatan_closing"
                                  rows="2"
                                  placeholder="Contoh: Link backbone telah UP normal dan stabil setelah proses perbaikan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> {{ $tiket->status === 'PENDING_VERIFIKASI' ? 'Konfirmasi Verifikasi & Close' : 'Konfirmasi Closing Tiket' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
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

    function buildSingleKronoHtml(k) {
            const isMe = (k.user_id === currentUserId);
            const initials = (k.user_name || 'U').substring(0, 2).toUpperCase();
            const senderColor = getSenderColor(k.user_name);
            const userAvatar = k.user_avatar || null;
            const senderDisplayName = isMe ? 'Anda' : (k.user_name || 'User');
            const sentMoment = k.created_at ? new Date(k.created_at) : (k.timestamp ? new Date(k.timestamp) : new Date());
            const diffMinutes = (Date.now() - sentMoment.getTime()) / (1000 * 60);
            const canEdit = !isTiketClosed && (isMe || isAdmin) && (diffMinutes <= 5);
            const canReply = !isTiketClosed && canChat;
            const safeInfoAttr = rawEscape(k.informasi || '');

            const kTimestampUnix = k.timestamp ? Math.floor(new Date(k.timestamp).getTime() / 1000) : Math.floor(sentMoment.getTime() / 1000);

            return `
                <div class="wa-msg-row ${isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming'}" id="krono-item-${k.id}" data-id="${k.id}" data-timestamp="${kTimestampUnix}">
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
                                    ${isAdmin ? `
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

                        ${k.foto_url ? `
                        <div class="wa-media-card" onclick="zoomPhoto('${k.foto_url}', '${k.kategori} - ${k.formatted_time}')">
                            <img src="${k.foto_url}" alt="Foto Kronologis" class="wa-media-img" loading="lazy">
                            <div class="wa-media-badge">
                                <i class="bi bi-arrows-fullscreen"></i>
                                <span>Klik untuk memperbesar</span>
                            </div>
                        </div>` : ''}

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

        stream.insertAdjacentHTML('beforeend', buildSingleKronoHtml(k));

        // Pertahankan posisi scroll window
        window.scrollTo(0, savedWindowY);

        // Hanya auto-scroll stream jika user sedang di bawah atau pengirim adalah diri sendiri
        if (isNearBottom || isSelf) {
            stream.scrollTop = stream.scrollHeight;
            requestAnimationFrame(() => {
                stream.scrollTop = stream.scrollHeight;
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
            stream.scrollTop = stream.scrollHeight;
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
            newRow.className = 'jc-core-input-row p-2.5 bg-white rounded-3 border shadow-xs';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Asal</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" placeholder="Tube 1" value="Tube 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Asal</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" placeholder="Core 1" value="Core 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Jumper</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" placeholder="Tube 1" value="Tube 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Jumper</label>
                        <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" placeholder="Core 1" value="Core 1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Status</label>
                        <select class="form-select form-select-sm" name="core_status[]">
                            <option value="TERHUBUNG" selected>TERHUBUNG</option>
                            <option value="SPARE">SPARE (Sisa)</option>
                            <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                            <option value="MANUVER">MANUVER</option>
                        </select>
                    </div>
                    <div class="col-5 col-md-1">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Loss (dB)</label>
                        <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" placeholder="0.02" value="0.02">
                    </div>
                    <div class="col-1 text-end pt-3">
                        <button type="button" class="btn btn-link text-danger p-0 btn-remove-core-row" title="Hapus baris ini">
                            <i class="bi bi-x-circle fs-5"></i>
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
                newRow.className = 'jc-core-input-row p-2.5 bg-white rounded-3 border shadow-xs';
                newRow.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Asal</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" value="${tAsal}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Asal</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" value="${cAsal}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Tube Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" value="${tJumper}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Core Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" value="${cJumper}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Status</label>
                            <select class="form-select form-select-sm" name="core_status[]">
                                <option value="TERHUBUNG" ${status === 'TERHUBUNG' ? 'selected' : ''}>TERHUBUNG</option>
                                <option value="SPARE" ${status === 'SPARE' ? 'selected' : ''}>SPARE (Sisa)</option>
                                <option value="LOSS_PUTUS" ${status === 'LOSS_PUTUS' ? 'selected' : ''}>LOSS / PUTUS</option>
                                <option value="MANUVER" ${status === 'MANUVER' ? 'selected' : ''}>MANUVER</option>
                            </select>
                        </div>
                        <div class="col-5 col-md-1">
                            <label class="form-label small text-muted mb-1" style="font-size: 0.7rem;">Loss (dB)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" value="${loss}">
                        </div>
                        <div class="col-1 text-end pt-3">
                            <button type="button" class="btn btn-link text-danger p-0 btn-remove-core-row" data-index="${idx}" title="Hapus baris ini">
                                <i class="bi bi-x-circle fs-5"></i>
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

    // ── 12.5. SWITCH & TOGGLE METODE PENANGANAN (JOINTING / MANUVER / KEDUANYA) ──
    window.activeCurrentMode = @json($activePenanganan);

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
    };

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
                // Selalu pastikan internal scroll chat berada di paling bawah (pesan terbaru)
                stream.scrollTop = stream.scrollHeight;
            }
        };

        // Jalankan scroll internal container secara instan
        performInternalScroll();
        setTimeout(performInternalScroll, 60);
        setTimeout(performInternalScroll, 300);

        // Jika ada gambar di dalam chat stream yang masih loading, sesuaikan scroll container
        const streamImgs = stream.querySelectorAll('img');
        streamImgs.forEach(img => {
            if (!img.complete) {
                img.addEventListener('load', performInternalScroll, { once: true });
            }
        });
        window.addEventListener('load', performInternalScroll, { once: true });

        // CATATAN: Window halaman browser TIDAK di-scroll ke bawah (window.scrollTo ditiadakan).
        // Halaman browser tetap berada di atas agar informasi detail tiket langsung terlihat.
    }

    // Jalankan agar chat stream selalu siap di paling bawah
    scrollToTargetKrono();

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
        const waChatLat = document.getElementById('waChatLatitude');
        const waChatLng = document.getElementById('waChatLongitude');
        const btnWaUploadFoto = document.getElementById('btnWaUploadFoto');
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
        const waLocationChip = document.getElementById('waLocationChip');
        const waLocationCoordsText = document.getElementById('waLocationCoordsText');
        const btnRemoveWaLocation = document.getElementById('btnRemoveWaLocation');
        const btnWaSendMsg = document.getElementById('btnWaSendMsg');

        const waMentionDropdown = document.getElementById('waMentionDropdown');
        const waMentionList = document.getElementById('waMentionList');

        let currentWaCompressedPhoto = null;
        let waCompressionPromise = null;
        let currentPhotoMode = 'watermark'; // 'watermark' | 'plain' | 'gallery'

        let activeMentionIndex = 0;
        let filteredMentionUsers = [];
        let mentionStartIndex = -1;

        // Auto-expand textarea on typing
        waChatTextInput?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
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

            // Gojek-style quick reply chip click handler
            const chip = e.target.closest('.wa-quick-chip');
            if (chip && waChatTextInput) {
                e.preventDefault();
                const text = chip.getAttribute('data-text') || chip.textContent.trim();
                waChatTextInput.value = text;
                waChatTextInput.focus();
                waChatTextInput.dispatchEvent(new Event('input', { bubbles: true }));

                const pill = waChatTextInput.closest('.wa-floating-input-pill');
                if (pill) {
                    pill.style.borderColor = '#2C7FFF';
                    pill.style.boxShadow = '0 0 0 3px rgba(44, 127, 255, 0.25)';
                    setTimeout(() => {
                        pill.style.borderColor = '';
                        pill.style.boxShadow = '';
                    }, 400);
                }
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
                if (this.value.trim().length > 0 || (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0) || currentWaCompressedPhoto) {
                    waDirectChatForm.requestSubmit();
                }
            }
        });

        // 1. Upload Foto (Galeri)
        btnWaUploadFoto?.addEventListener('click', function() {
            currentPhotoMode = 'gallery';
            if (waChatFotoInput) {
                waChatFotoInput.removeAttribute('capture');
                waChatFotoInput.click();
            }
        });

        // 2. Kamera GPS (Dengan Logo MSN, Timestamp & Alamat)
        btnWaCameraWatermark?.addEventListener('click', function() {
            currentPhotoMode = 'watermark';
            if (waChatFotoInput) {
                waChatFotoInput.setAttribute('capture', 'environment');
                waChatFotoInput.click();
            }
        });

        // 3. Kamera Polos (Tanpa Watermark)
        btnWaCameraPolos?.addEventListener('click', function() {
            currentPhotoMode = 'plain';
            if (waChatFotoInput) {
                waChatFotoInput.setAttribute('capture', 'environment');
                waChatFotoInput.click();
            }
        });

        // Event saat file foto dipilih / difoto dari kamera
        waChatFotoInput?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const originalFile = this.files[0];
                if (waPhotoFileName) waPhotoFileName.textContent = originalFile.name;
                
                // Tampilkan chip dengan status sedang mengompres
                if (waPhotoSizeBadge) {
                    waPhotoSizeBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill py-0.5 px-1.5';
                    waPhotoSizeBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span>...';
                }
                if (waPhotoPreviewChip) {
                    waPhotoPreviewChip.classList.remove('d-none');
                    waPhotoPreviewChip.classList.add('d-flex');
                }
                if (waAttachmentPreviewBar) waAttachmentPreviewBar.classList.remove('d-none');

                // Preview thumbnail instan
                if (waPhotoThumb && originalFile.type.startsWith('image/')) {
                    try {
                        waPhotoThumb.src = URL.createObjectURL(originalFile);
                        waPhotoThumb.classList.remove('d-none');
                        if (waPhotoDefaultIcon) waPhotoDefaultIcon.classList.add('d-none');
                    } catch(e) {}
                }

                const isWatermark = (currentPhotoMode === 'watermark');

                // Proses kompresi & watermark GPS (jika mode watermark dipilih)
                waCompressionPromise = compressImageFile(originalFile, {
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

                    const origSize = formatFileSize(originalFile.size);
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
                    currentWaCompressedPhoto = originalFile;
                    if (waPhotoSizeBadge) {
                        waPhotoSizeBadge.className = 'badge bg-light text-dark border rounded-pill py-0.5 px-2';
                        waPhotoSizeBadge.textContent = formatFileSize(originalFile.size);
                    }
                    return originalFile;
                });
            } else {
                currentWaCompressedPhoto = null;
                waCompressionPromise = null;
                if (waPhotoPreviewChip) {
                    waPhotoPreviewChip.classList.add('d-none');
                    waPhotoPreviewChip.classList.remove('d-flex');
                }
                checkPreviewBarEmpty();
            }
        });

        // Hapus attachment foto
        btnRemoveWaPhoto?.addEventListener('click', function() {
            currentWaCompressedPhoto = null;
            waCompressionPromise = null;
            if (waChatFotoInput) waChatFotoInput.value = '';
            if (waPhotoThumb) {
                waPhotoThumb.src = '#';
                waPhotoThumb.classList.add('d-none');
            }
            if (waPhotoDefaultIcon) {
                waPhotoDefaultIcon.classList.remove('d-none');
            }
            if (waPhotoPreviewChip) {
                waPhotoPreviewChip.classList.add('d-none');
                waPhotoPreviewChip.classList.remove('d-flex');
            }
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
            if (waPhotoPreviewChip && waLocationChip && waPhotoPreviewChip.classList.contains('d-none') && waLocationChip.classList.contains('d-none')) {
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
            const hasPhoto = (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0) || currentWaCompressedPhoto;
            const hasLoc = waChatLat && waChatLat.value !== '';

            if (!textVal && !hasPhoto && !hasLoc) return;

            // Simpan data form & previews sebelum input di-reset
            const photoThumbSrc = (waPhotoThumb && !waPhotoThumb.classList.contains('d-none')) ? waPhotoThumb.src : null;
            const latVal = waChatLat ? waChatLat.value : '';
            const lngVal = waChatLng ? waChatLng.value : '';
            const activeReply = activeReplyData ? { ...activeReplyData } : null;

            // Tunggu jika proses kompresi foto di background sedang berlangsung
            if (waCompressionPromise) {
                try { await waCompressionPromise; } catch(e) {}
            }

            const formData = new FormData(waDirectChatForm);
            
            // Pasang teks kutipan jika sedang membalas pesan (Reply)
            if (activeReply) {
                const snippet = (activeReply.text || '').substring(0, 80).replace(/\n/g, ' ');
                const quotedText = `[Membalas ${activeReply.sender}]: ${snippet}\n\n` + textVal;
                formData.set('informasi', quotedText);
                cancelReply();
            }

            // Pasang file foto yang sudah terkompresi otomatis
            if (currentWaCompressedPhoto) {
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
            currentWaCompressedPhoto = null;
            waCompressionPromise = null;
            if (waChatFotoInput) waChatFotoInput.value = '';
            if (waChatLat) waChatLat.value = '';
            if (waChatLng) waChatLng.value = '';
            if (waPhotoThumb) {
                waPhotoThumb.src = '#';
                waPhotoThumb.classList.add('d-none');
            }
            if (waPhotoDefaultIcon) {
                waPhotoDefaultIcon.classList.remove('d-none');
            }
            if (waPhotoPreviewChip) {
                waPhotoPreviewChip.classList.add('d-none');
                waPhotoPreviewChip.classList.remove('d-flex');
            }
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
                    <div class="wa-msg-row wa-msg-outgoing" id="${tempId}" data-timestamp="${Math.floor(Date.now() / 1000)}" style="animation: waMsgPopIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);">
                        <div class="wa-bubble wa-bubble-outgoing">
                            <div class="wa-bubble-header">
                                <div class="wa-sender-info">
                                    <span class="wa-sender-name" style="color: #0f766e;">Anda</span>
                                    <span class="wa-role-pill">{{ auth()->user()->role_short ?? 'User' }}</span>
                                </div>
                            </div>
                            <div class="wa-msg-text">${formatMessageWithMentions(formData.get('informasi') || textVal)}</div>
                            ${hasPhoto && photoThumbSrc && photoThumbSrc !== '#' ? `
                            <div class="wa-media-card" style="opacity: 0.85;">
                                <img src="${photoThumbSrc}" alt="Mengunggah Foto..." class="wa-media-img" style="filter: brightness(0.92);">
                                <div class="wa-media-badge">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 0.72rem; height: 0.72rem;"></span>
                                    <span>Mengunggah foto...</span>
                                </div>
                            </div>` : ''}
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
                            text: textVal || (hasPhoto ? 'Mengunggah Foto' : 'Koordinat GPS Lapangan'),
                            has_photo: hasPhoto,
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
                            text: 'Pesan & foto tersimpan di HP dan akan otomatis terkirim saat sinyal kembali.',
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
                .then(res => res.json())
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

                            // Update foto url asli jika upload foto
                            if (res.data.foto_url) {
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
                        const statusIcon = document.getElementById('status-icon-' + tempId);
                        if (statusIcon) {
                            statusIcon.className = 'bi bi-exclamation-circle-fill text-danger';
                            statusIcon.title = 'Gagal terkirim: ' + (res.message || 'Error');
                        }
                        alert('Gagal mengirim pesan: ' + (res.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
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
                                text: textVal || (hasPhoto ? 'Mengunggah Foto' : 'Koordinat GPS Lapangan'),
                                has_photo: hasPhoto,
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
                        alert('Gagal mengirim pesan: ' + err.message);
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
    }

    // Attach listener to initial stream if present
    const initStream = document.getElementById('timelineList');
    if (initStream) {
        attachStreamScrollListener(initStream);
        initStream.scrollTop = initStream.scrollHeight;
        checkStreamScroll(initStream);
    }

    btnWaScrollBottom?.addEventListener('click', function() {
        const stream = document.getElementById('timelineList');
        if (stream) {
            stream.scrollTo({
                top: stream.scrollHeight + 500,
                behavior: 'smooth'
            });
        }
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
        }, 320);

        // Scroll stream ke bawah setelah animasi berjalan
        requestAnimationFrame(() => {
            const stream = document.getElementById('timelineList');
            if (stream) stream.scrollTop = stream.scrollHeight;
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
</script>
@endpush
