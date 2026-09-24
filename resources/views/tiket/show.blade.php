@extends('layouts.app')

@section('title', 'Detail Tiket ' . $tiket->no_tiket)

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       WHATSAPP CHAT-STYLE TIMELINE KRONOLOGIS
       ═══════════════════════════════════════════════════════════════════ */
    .wa-chat-container {
        background-color: #efeae2;
        background-image: 
            radial-gradient(rgba(17, 27, 33, 0.07) 1px, transparent 1px),
            radial-gradient(rgba(17, 27, 33, 0.04) 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        border-radius: var(--neu-radius, 18px);
        border: 1px solid var(--neu-border, rgba(255,255,255,0.8));
        overflow: hidden;
        box-shadow: var(--neu-flat, 7px 7px 16px #c2ccd9, -7px -7px 16px #ffffff);
        display: flex;
        flex-direction: column;
        transition: border-radius 0.25s ease;
    }

    /* ── FULLSCREEN MODE ── */
    .wa-chat-container.wa-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 99999;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
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
    }

    /* Sembunyikan bottom nav, sidebar, footer saat fullscreen */
    body.wa-chat-fullscreen-active .mobile-bottom-nav,
    body.wa-chat-fullscreen-active .app-sidebar,
    body.wa-chat-fullscreen-active .app-footer,
    body.wa-chat-fullscreen-active footer {
        display: none !important;
    }

    /* Hapus padding-bottom bawaan mobile agar input menempel di paling bawah */
    body.wa-chat-fullscreen-active {
        padding-bottom: 0 !important;
        overflow: hidden !important;
    }

    /* Tombol fullscreen */
    .wa-fullscreen-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid rgba(203, 213, 225, 0.6);
        background: rgba(255,255,255,0.6);
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.18s, color 0.18s, transform 0.15s;
        backdrop-filter: blur(4px);
    }

    .wa-fullscreen-btn:hover {
        background: rgba(44, 127, 255, 0.12);
        color: #2C7FFF;
        border-color: rgba(44, 127, 255, 0.35);
        transform: scale(1.08);
    }

    .wa-fullscreen-btn:active {
        transform: scale(0.93);
    }


    .wa-chat-header {
        background: var(--neu-surface, #e6ecf4);
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid var(--neu-border-subtle, rgba(194,204,217,0.45));
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
    }

    .wa-header-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00a884, #075e54);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 168, 132, 0.25);
    }

    .wa-header-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wa-header-meta {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wa-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        margin-right: 3px;
        animation: waPulse 1.8s infinite;
    }

    @keyframes waPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(34, 197, 94, 0); }
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
        scroll-behavior: smooth;
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
        margin: 0.5rem 0;
        position: sticky;
        top: 6px;
        z-index: 4;
    }

    .wa-date-chip {
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(8px);
        color: #54656f;
        font-size: 0.71rem;
        font-weight: 600;
        padding: 0.28rem 0.85rem;
        border-radius: 999px;
        box-shadow: 0 1px 3px rgba(11, 20, 26, 0.1);
        border: 1px solid rgba(203, 213, 225, 0.7);
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.15px;
    }

    /* Message Row & Bubble */
    .wa-msg-row {
        display: flex;
        gap: 0.45rem;
        align-items: flex-end;
        width: 100%;
        scroll-margin-bottom: 110px;
        scroll-margin-top: 90px;
    }

    .wa-msg-incoming {
        justify-content: flex-start;
    }

    .wa-msg-outgoing {
        justify-content: flex-end;
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
        box-shadow: 0 1px 2px rgba(11, 20, 26, 0.1);
        word-wrap: break-word;
    }

    .wa-bubble-incoming {
        background: var(--neu-surface, #e6ecf4);
        border: 1px solid var(--neu-border, rgba(255, 255, 255, 0.8));
        border-radius: 16px 16px 16px 4px;
        box-shadow: 3px 3px 7px var(--neu-shadow-dark, #c2ccd9), -3px -3px 7px var(--neu-shadow-light, #ffffff);
    }

    .wa-bubble-outgoing {
        background: #dbe6fe;
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 16px 16px 4px 16px;
        box-shadow: 3px 3px 7px #c4d3eb, -3px -3px 7px #ffffff;
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
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 2px 4px;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        line-height: 1;
        transition: all 0.15s ease;
        opacity: 0.6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .wa-bubble:hover .wa-msg-menu-btn,
    .wa-msg-menu-btn:focus,
    .wa-msg-menu-btn[aria-expanded="true"] {
        opacity: 1;
        color: #1e293b;
        background: rgba(0, 0, 0, 0.06);
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
        font-size: 0.84rem;
        color: #111b21;
        line-height: 1.45;
        white-space: pre-wrap;
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
    .wa-double-check {
        font-size: 0.82rem;
        color: #53bdeb;
        line-height: 1;
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
        gap: 0.35rem;
        padding: 0.32rem 0.75rem;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(203, 213, 225, 0.85);
        border-radius: 50rem;
        font-size: 0.76rem;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.16s ease;
        user-select: none;
        text-decoration: none;
        line-height: 1.2;
    }

    .wa-quick-chip:hover {
        background: #ffffff;
        border-color: #2C7FFF;
        color: #1b39da;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(44, 127, 255, 0.2);
    }

    .wa-quick-chip:active {
        transform: scale(0.95);
        background: #f1f5f9;
    }

    .wa-quick-chip-icon {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
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
            transform: scale(0.97);
            box-shadow: 0 0 0 0 rgba(13, 148, 136, 0.7);
        }
        35% {
            transform: scale(1.025);
            box-shadow: 0 0 0 8px rgba(13, 148, 136, 0.28), 0 8px 24px rgba(13, 148, 136, 0.2);
        }
        70% {
            transform: scale(1.01);
            box-shadow: 0 0 0 12px rgba(13, 148, 136, 0.1), 0 4px 16px rgba(13, 148, 136, 0.12);
        }
        100% {
            transform: scale(1);
            box-shadow: none;
        }
    }

    .wa-bubble-new-highlight .wa-bubble {
        animation: waBubblePulse 2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        outline: 2.5px solid var(--cjp-teal, #0d9488) !important;
        outline-offset: 2px !important;
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
        .closing-tile-arrow {
            display: none !important;
        }
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
                        <span class="d-inline-flex align-items-center text-white font-monospace px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); font-size:0.78rem; font-weight:700; letter-spacing:0.5px;">
                            <i class="bi bi-ticket-perforated-fill me-1.5 text-info"></i>{{ $tiket->no_tiket }}
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

                    @if(auth()->user()->hasRole(['admin', 'helpdesk']))
                    <button type="button" class="btn-tiket-hero btn-tiket-hero-wa"
                            id="btnCopyWaBroadcast"
                            title="Salin notifikasi tugas untuk ditempel ke Grup WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                        <span>Salin Info WA</span>
                    </button>
                    @endif

                    @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'OPEN')
                    <a href="{{ route('tiket.edit', $tiket->id) }}" class="btn-tiket-hero btn-tiket-hero-warning">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                    </a>
                    @endif

                    <!-- Stop / Resume Clock Button -->
                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
                        @if(!$tiket->is_stop_clock)
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-warning"
                                data-bs-toggle="modal" data-bs-target="#startStopClockModal"
                                title="Hentikan sementara penghitungan SLA (Stop Clock)">
                            <i class="bi bi-pause-circle"></i>
                            <span>Stop Clock</span>
                        </button>
                        @else
                        <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
                            @csrf
                            <button type="submit" class="btn-tiket-hero btn-tiket-hero-success"
                                    title="Lanjutkan perhitungan durasi SLA (Resume Clock)">
                                <i class="bi bi-play-circle-fill"></i>
                                <span>Resume Clock</span>
                            </button>
                        </form>
                        @endif
                    @endif

                    <!-- Oper Shift Button -->
                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
                    <button type="button" class="btn-tiket-hero btn-tiket-hero-purple"
                            data-bs-toggle="modal" data-bs-target="#handoverShiftModal"
                            title="Catat serah terima shift pekerjaan">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Oper Shift</span>
                    </button>
                    @endif

                    <!-- Teknisi Closing Awal Button -->
                    @if(auth()->user()->hasRole(['teknis', 'teknisi']) && $tiket->status === 'PROSES')
                    <button type="button" class="btn-tiket-hero btn-tiket-hero-primary"
                            data-bs-toggle="modal" data-bs-target="#closingAwalModal"
                            title="Selesaikan pekerjaan di lapangan dan ajukan verifikasi ke NOC">
                        <i class="bi bi-check2-all"></i>
                        <span>Closing Awal (Selesai Lapangan)</span>
                    </button>
                    @endif

                    <!-- Helpdesk Two-Step Verifikasi Button -->
                    @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                        @if($tiket->status === 'PENDING_VERIFIKASI')
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-danger"
                                data-bs-toggle="modal" data-bs-target="#rejectClosingAwalModal"
                                title="Kembalikan tiket ke teknisi lapangan jika hasil belum sesuai">
                            <i class="bi bi-x-circle"></i>
                            <span>Reject Closing</span>
                        </button>
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-success"
                                data-bs-toggle="modal" data-bs-target="#closeTiketModal"
                                title="Verifikasi kestabilan link dan tutup tiket">
                            <i class="bi bi-shield-check"></i>
                            <span>Verifikasi & Close Tiket</span>
                        </button>
                        @else
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-ghost text-white-50 opacity-75"
                                style="cursor: not-allowed; border-color: rgba(255, 255, 255, 0.15);"
                                title="NOC belum dapat menutup tiket ini karena teknisi belum melakukan Closing Awal di lapangan"
                                disabled>
                            <i class="bi bi-hourglass-split"></i>
                            <span>Menunggu Closing Awal</span>
                        </button>
                        @endif
                    @endif

                    <!-- Export Buttons -->
                    <div class="dropdown position-relative d-inline-block" style="z-index: 5;">
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-ghost dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download"></i>
                            <span>Export</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-1" style="min-width: 220px;">
                            <li>
                                <a class="dropdown-item py-2 small" href="{{ route('reports.export.tiket.pdf', $tiket->id) }}">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export Berita Acara PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 small" href="{{ route('reports.export.excel', ['search' => $tiket->no_tiket]) }}">
                                    <i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Data Excel
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
        <div id="fieldReportIntervalBanner" class="card border-0 shadow-sm rounded-xl mb-3 overflow-hidden {{ $fieldStatus === 'OVERDUE' ? 'border-2 border-danger' : ($fieldStatus === 'WARNING' ? 'border-2 border-warning' : 'border-2 border-warning') }}">
            <div class="card-body p-3 p-md-3.5">
                @if($showIntervalAlert)
                <div class="row align-items-center g-3" id="fieldIntervalStatusRow">
                    <!-- Left: Interval Monitor Status -->
                    <div class="col-12 col-md-7">
                        <div class="d-flex align-items-start gap-3">
                            @if($fieldStatus === 'OVERDUE')
                                <div class="rounded-circle bg-danger bg-opacity-15 p-2.5 text-danger d-flex align-items-center justify-content-center flex-shrink-0 animate__animated animate__pulse animate__infinite" style="width: 44px; height: 44px;">
                                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-danger text-white fw-bold">OVERDUE > 30 MENIT</span>
                                        <span class="fw-bold text-danger">Wajib Kirim Update Kondisi Lapangan!</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Laporan kronologis terakhir diupdate <strong>{{ $minsSinceLast !== null ? $minsSinceLast . ' menit yang lalu' : 'belum pernah ada laporan' }}</strong>.
                                        SOP mewajibkan minimal per <strong>30 menit</strong> teknisi memberikan info perkembangan di lapangan.
                                    </div>
                                </div>
                            @elseif($fieldStatus === 'WARNING')
                                <div class="rounded-circle bg-warning bg-opacity-20 p-2.5 text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="bi bi-hourglass-bottom fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark fw-bold">PERINGATAN 20-30 MENIT</span>
                                        <span class="fw-bold text-navy">Persiapkan Update Laporan Lapangan</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Laporan terakhir <strong>{{ $minsSinceLast }} menit yang lalu</strong>. Segera input update progress sebelum batas 30 menit terlewati.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Quick Action & Stats -->
                    <div class="col-12 col-md-5">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-md-end gap-2">
                            <div class="p-2 rounded bg-light border text-center flex-fill">
                                <div class="text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">Update Terakhir</div>
                                <div class="fw-bold text-navy" style="font-size: 0.85rem;">
                                    {{ $lastKronologis ? ($lastKronologis->timestamp ? $lastKronologis->timestamp->format('H:i') : $lastKronologis->created_at->format('H:i')) . ' WIB' : '-' }}
                                </div>
                            </div>
                            <div class="p-2 rounded bg-light border text-center flex-fill">
                                <div class="text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">Rata-rata Interval</div>
                                <div class="fw-bold text-navy" style="font-size: 0.85rem;">
                                    {{ $avgInterval ? $avgInterval . ' Menit' : '-' }}
                                </div>
                            </div>
                            @if(auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button type="button" class="btn {{ $fieldStatus === 'OVERDUE' ? 'btn-danger' : 'btn-primary' }} btn-sm px-3 py-2 fw-semibold rounded-pill shadow-xs d-inline-flex align-items-center justify-content-center gap-1.5 flex-fill" onclick="const kTab = document.getElementById('kronologis-tab'); if(kTab) kTab.click(); const waInp = document.getElementById('waChatTextInput') || document.getElementById('informasi'); if(waInp) { waInp.focus(); waInp.scrollIntoView({behavior: 'smooth', block: 'center'}); }">
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
                         onclick="const tab = document.getElementById('resume-tab'); if(tab) { tab.click(); document.getElementById('resume-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
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
                            id="material-tab" data-bs-toggle="tab" data-bs-target="#material-pane" type="button" role="tab">
                        <i class="bi bi-box-seam text-warning"></i>
                        <span>Material & Titik</span>
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
                            id="manuver-tab" data-bs-toggle="tab" data-bs-target="#manuver-pane" type="button" role="tab">
                        <i class="bi bi-shuffle text-teal"></i>
                        <span>Manuver Core</span>
                        <span class="badge bg-light text-navy border" id="manuverCountBadge">{{ $tiket->manuverCores->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="stopclock-tab" data-bs-toggle="tab" data-bs-target="#stopclock-pane" type="button" role="tab">
                        <i class="bi bi-pause-circle text-warning"></i>
                        <span>Stop Clock & Shift</span>
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
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-1.5 py-0.5 rounded-pill d-none d-sm-inline-flex align-items-center" style="font-size:0.65rem; font-weight:600;">
                                            <span class="wa-pulse-dot"></span> Live Sync (5s)
                                        </span>
                                    </div>
                                    <div class="wa-header-meta text-truncate">
                                        <span class="d-inline d-sm-none text-success fw-semibold"><span class="wa-pulse-dot"></span>Live &bull; </span>
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
                                        @endphp

                                        @if($currentDate !== $lastDate)
                                            <div class="wa-date-divider">
                                                <span class="wa-date-chip">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ $krono->timestamp->translatedFormat('l, d F Y') }}
                                                </span>
                                            </div>
                                            @php $lastDate = $currentDate; @endphp
                                        @endif

                                        <div class="wa-msg-row {{ $isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming' }}" id="krono-item-{{ $krono->id }}">
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
                                                @endphp
                                                @if($quoteSender)
                                                <div class="wa-quote-box">
                                                    <div class="wa-quote-sender"><i class="bi bi-reply-fill me-1"></i>{{ $quoteSender }}</div>
                                                    <div class="wa-quote-text">{{ $quoteText }}</div>
                                                </div>
                                                @endif
                                                <div class="wa-msg-text">{!! preg_replace('/(@[a-zA-Z0-9_\.\-]+(?:\s+[a-zA-Z0-9_\.\-]+)?)/u', '<span class="wa-mention-tag-highlight">$1</span>', nl2br(e($rawInfo))) !!}</div>

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

                                                <!-- Bubble Footer: Time & Double Checkmark -->
                                                <div class="wa-bubble-footer">
                                                    <span class="wa-time">{{ $krono->timestamp->format('H:i') }} WIB</span>
                                                    <i class="bi bi-check2-all wa-double-check"></i>
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

                        <!-- Floating Scroll-to-Bottom Button (WhatsApp Style) -->
                        <button type="button" id="btnWaScrollBottom" class="wa-scroll-bottom-btn d-none" title="Ke Pesan Terbaru">
                            <i class="bi bi-chevron-double-down"></i>
                        </button>

                        <!-- Gojek-Style Quick Reply Chips (Rekomendasi Chat Cepat) -->
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <div class="wa-quick-replies-wrapper" id="waQuickRepliesWrapper">
                            <button type="button" class="wa-quick-chip" data-text="Sedang menuju ke lokasi titik gangguan">
                                <span class="wa-quick-chip-icon">🚗</span>
                                <span>Menuju lokasi</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang investigasi di lapangan & pengukuran OTDR">
                                <span class="wa-quick-chip-icon">🔍</span>
                                <span>Investigasi & OTDR</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ditemukan kabel fiber optik putus / bending">
                                <span class="wa-quick-chip-icon">✂️</span>
                                <span>Kabel putus / bending</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang proses splicing / penyambungan core kabel">
                                <span class="wa-quick-chip-icon">⚡</span>
                                <span>Proses splicing core</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang ukur nilai redaman / power level optik">
                                <span class="wa-quick-chip-icon">📊</span>
                                <span>Ukur redaman optik</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Redaman sudah normal & link sudah UP kembali">
                                <span class="wa-quick-chip-icon">✅</span>
                                <span>Redaman normal & Link UP</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ada kendala di lapangan, mohon bantuan koordinasi / manuver core">
                                <span class="wa-quick-chip-icon">⚠️</span>
                                <span>Kendala / butuh manuver</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Melampirkan foto dokumentasi hasil perbaikan di lapangan">
                                <span class="wa-quick-chip-icon">📸</span>
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
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #0d9488; background: #f0fdfa;">
                                    <div class="resume-card-label text-teal">
                                        <i class="bi bi-diagram-2-fill"></i> Tipe Penanganan Lapangan
                                    </div>
                                    <div class="resume-card-text">
                                        @if($tiket->resume->tipe_penanganan === 'JOINTING_LURUS')
                                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-arrow-right me-1"></i> Jointing Lurus (Straight Splicing)
                                            </span>
                                        @elseif($tiket->resume->tipe_penanganan === 'MANUVER_CORE')
                                            <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-shuffle me-1"></i> Manuver Core / Tube
                                            </span>
                                        @elseif($tiket->resume->tipe_penanganan === 'LAINNYA')
                                            <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill">
                                                Penanganan Lainnya
                                            </span>
                                        @else
                                            <span class="text-muted small">Belum ditentukan</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #6366f1; background: #eef2ff;">
                                    <div class="resume-card-label" style="color: #4f46e5;">
                                        <i class="bi bi-box-seam-fill"></i> Info Joint Closure & Core
                                    </div>
                                    <div class="resume-card-text">
                                        <div class="small text-navy">
                                            <strong>Closure:</strong> {{ $tiket->resume->joint_closure_type ?: '-' }} &bull;
                                            <strong>Core Jointed:</strong> {{ $tiket->resume->core_count_jointed ? $tiket->resume->core_count_jointed . ' Core' : '-' }}
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

                <!-- ════ TAB 5: MANUVER CORE (FASE 5) ════ -->
                <div class="tab-pane fade" id="manuver-pane" role="tabpanel">
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

                    <!-- Tipe Penanganan (Point 4) -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-bold text-navy mb-2">
                            <i class="bi bi-diagram-2-fill text-teal me-1"></i> Tipe Penanganan Lapangan <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="tipe_penanganan" id="tipe_jointing_lurus" value="JOINTING_LURUS" {{ old('tipe_penanganan', $tiket->resume?->tipe_penanganan ?? $tiket->tipe_penanganan) === 'JOINTING_LURUS' ? 'checked' : '' }} required>
                                    <label class="form-check-label small fw-bold text-navy" for="tipe_jointing_lurus">
                                        Jointing Lurus
                                    </label>
                                    <div class="text-muted" style="font-size:0.68rem; margin-left: 1.5rem;">Splicing lurus kabel/core</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="tipe_penanganan" id="tipe_manuver_core" value="MANUVER_CORE" {{ old('tipe_penanganan', $tiket->resume?->tipe_penanganan ?? $tiket->tipe_penanganan) === 'MANUVER_CORE' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold text-navy" for="tipe_manuver_core">
                                        Manuver Core
                                    </label>
                                    <div class="text-muted" style="font-size:0.68rem; margin-left: 1.5rem;">Pindah alokasi core/tube</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-check p-2 bg-white rounded border">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="tipe_penanganan" id="tipe_lainnya" value="LAINNYA" {{ old('tipe_penanganan', $tiket->resume?->tipe_penanganan ?? $tiket->tipe_penanganan) === 'LAINNYA' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold text-navy" for="tipe_lainnya">
                                        Lainnya
                                    </label>
                                    <div class="text-muted" style="font-size:0.68rem; margin-left: 1.5rem;">Perapian/penggantian modul</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <label for="joint_closure_type" class="form-label small fw-semibold text-navy">Tipe Joint Closure / Perangkat</label>
                                <input type="text" class="form-control form-control-sm" id="joint_closure_type" name="joint_closure_type"
                                       value="{{ old('joint_closure_type', $tiket->resume?->joint_closure_type) }}"
                                       placeholder="Contoh: Closure Dome 24C / Inline 48C" list="closurePresets">
                                <datalist id="closurePresets">
                                    <option value="Closure Dome 24C">
                                    <option value="Closure Dome 48C">
                                    <option value="Closure Dome 96C">
                                    <option value="Closure Inline 24C">
                                    <option value="Closure Inline 48C">
                                    <option value="ODC / FDT Cabinet">
                                    <option value="OTB / ODF Rack">
                                </datalist>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="core_count_jointed" class="form-label small fw-semibold text-navy">Jumlah Core Di-Jointing / Dimanuver</label>
                                <input type="number" min="0" class="form-control form-control-sm" id="core_count_jointed" name="core_count_jointed"
                                       value="{{ old('core_count_jointed', $tiket->resume?->core_count_jointed) }}"
                                       placeholder="Contoh: 12">
                            </div>
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
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-camera-fill text-info me-2"></i>Upload Foto Dokumentasi Lapangan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
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

                        <!-- Multi-File Image Upload Zone -->
                        <div class="col-12">
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
                            <label class="form-label small fw-semibold text-navy d-flex justify-content-between">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat GPS Lokasi (Opsional)</span>
                                <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationDoc">
                                    <i class="bi bi-crosshair"></i> GPS Saya
                                </button>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="any" class="form-control" id="latitude_doc" name="latitude" placeholder="Latitude (-6.xxx)">
                                <input type="number" step="any" class="form-control" id="longitude_doc" name="longitude" placeholder="Longitude (106.xxx)">
                                <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerDoc" title="Pilih di Peta">
                                    <i class="bi bi-map"></i>
                                </button>
                            </div>
                            <div class="form-text small">Titik koordinat akan disematkan ke informasi foto dokumentasi.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-xs" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; color: #ffffff !important;">
                        <i class="bi bi-cloud-arrow-up-fill me-1 text-white"></i> Upload Foto
                    </button>
                </div>
            </form>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.manuver-core.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-shuffle text-teal me-2"></i>Tambah Record Manuver Core
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Titik JC -->
                    <div class="mb-3">
                        <label for="titik_manuver" class="form-label small fw-bold text-navy">
                            Titik Jointing / Closure <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-sm text-uppercase font-monospace"
                               id="titik_manuver"
                               name="titik"
                               placeholder="Contoh: JC1, JC2, dsb"
                               required>
                        <!-- Quick suggestions from existing Titik Perbaikan -->
                        @if($tiket->titikPerbaikans->count() > 0)
                        <div class="d-flex flex-wrap gap-1 mt-2 align-items-center">
                            <span class="small text-muted me-1">Pilih titik:</span>
                            @foreach($tiket->titikPerbaikans as $tp)
                            <button type="button" class="btn btn-xs btn-outline-primary titik-preset-btn py-0 px-2" data-titik="{{ $tp->nama_titik }}">
                                {{ $tp->nama_titik }}
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Tipe Manuver -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy d-block">
                            Tipe Manuver <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipe" id="tipeSebelum" value="SEBELUM" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="tipeSebelum">
                                <i class="bi bi-clock-history me-1"></i> SEBELUM (Kondisi Awal)
                            </label>

                            <input type="radio" class="btn-check" name="tipe" id="tipeSesudah" value="SESUDAH" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="tipeSesudah">
                                <i class="bi bi-check2-circle me-1"></i> SESUDAH (Setelah Perbaikan)
                            </label>
                        </div>
                    </div>

                    <!-- Core Asal -->
                    <div class="mb-3">
                        <label for="core_asal" class="form-label small fw-bold text-navy">
                            Core Asal / Input <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-sm font-monospace"
                               id="core_asal"
                               name="core_asal"
                               placeholder="Contoh: Tube 2 Core 1"
                               required>
                        <!-- Preset Helper Chips -->
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="badge bg-light text-navy border cursor-pointer core-asal-preset" data-val="Tube 1 Core 1">T1 C1</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-asal-preset" data-val="Tube 1 Core 2">T1 C2</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-asal-preset" data-val="Tube 2 Core 1">T2 C1</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-asal-preset" data-val="Tube 2 Core 2">T2 C2</span>
                        </div>
                    </div>

                    <!-- Core Tujuan -->
                    <div class="mb-0">
                        <label for="core_tujuan" class="form-label small fw-bold text-navy">
                            Core Tujuan / Output Sambungan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-sm font-monospace"
                               id="core_tujuan"
                               name="core_tujuan"
                               placeholder="Contoh: Tube 2 Core 1"
                               required>
                        <!-- Preset Helper Chips -->
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="badge bg-light text-navy border cursor-pointer core-tujuan-preset" data-val="Tube 1 Core 1">T1 C1</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-tujuan-preset" data-val="Tube 1 Core 2">T1 C2</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-tujuan-preset" data-val="Tube 2 Core 1">T2 C1</span>
                            <span class="badge bg-light text-navy border cursor-pointer core-tujuan-preset" data-val="Tube 2 Core 2">T2 C2</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-cjp-teal btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Simpan Manuver
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
                    <a href="#" id="photoZoomDownloadBtn" target="_blank" download="foto-lapangan.jpg" class="btn-lightbox-action" title="Buka / Unduh Foto Asli">
                        <i class="bi bi-box-arrow-up-right"></i>
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
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['tipe_penanganan'] ? str_replace('_', ' ', $tiket->tipe_penanganan) : 'Wajib dipilih di Resume (Jointing Lurus/Manuver)' }}</div>
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
        if (zoomDownloadBtn) zoomDownloadBtn.href = url;
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

            modalMttrPreview.textContent = `${hours} jam ${minutes} mnt (${diffMinutes} menit)`;

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
                if (res.data && res.data.length > 0) {
                    updateTimelineFromData(res.data);
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

            return `
                <div class="wa-msg-row ${isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming'}" id="krono-item-${k.id}">
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
                            <i class="bi bi-check2-all wa-double-check"></i>
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

    // ── 13. URL PARAMETER & HASH ACTIVE TAB SWITCHER ──
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const hashParam = window.location.hash;

    if (tabParam) {
        const targetTabBtn = document.getElementById(tabParam + '-tab');
        if (targetTabBtn) {
            const bsTab = new bootstrap.Tab(targetTabBtn);
            bsTab.show();
        }
    } else if (hashParam) {
        const cleanHash = hashParam.replace('#tab-', '').replace('#', '');
        const targetTabBtn = document.getElementById(cleanHash + '-tab');
        if (targetTabBtn) {
            const bsTab = new bootstrap.Tab(targetTabBtn);
            bsTab.show();
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

        if (!targetEl && hasKronoSuccess) {
            const items = document.querySelectorAll('.wa-msg-row');
            if (items.length > 0) {
                targetEl = items[items.length - 1];
            }
        }

        if (targetEl) {
            // 1. Pastikan tab kronologis aktif jika pengguna berada di tab lain
            const kronoTab = document.getElementById('kronologis-tab');
            if (kronoTab && typeof bootstrap !== 'undefined') {
                const bsTab = bootstrap.Tab.getOrCreateInstance(kronoTab);
                bsTab.show();
            }

            const stream = document.getElementById('timelineList');
            const isLatest = (!window.location.hash || window.location.hash.startsWith('#krono-item-')) &&
                             (targetEl === stream?.lastElementChild || targetEl.nextElementSibling === null || newKronoId || hasKronoSuccess);

            const performScroll = (instant = true) => {
                if (!targetEl) return;

                // A. Scroll internal container (#timelineList) secara presisi ke pesan target/bawah
                if (stream) {
                    if (isLatest) {
                        // Untuk pesan terbaru, scroll stream maksimal ke bawah
                        stream.scrollTo({
                            top: stream.scrollHeight + 500,
                            behavior: instant ? 'auto' : 'smooth'
                        });
                    } else {
                        const streamRect = stream.getBoundingClientRect();
                        const targetRect = targetEl.getBoundingClientRect();
                        const relativeTop = targetRect.top - streamRect.top + stream.scrollTop;
                        stream.scrollTo({
                            top: Math.max(0, relativeTop - (stream.clientHeight / 2) + (targetEl.clientHeight / 2)),
                            behavior: instant ? 'auto' : 'smooth'
                        });
                    }
                }

                // B. Posisikan window outer LANGSUNG secara instan (menghilangkan efek meluncur dari atas)
                const chatContainer = document.querySelector('.wa-chat-container') || targetEl;
                const chatRect = chatContainer.getBoundingClientRect();
                const currentWindowY = window.pageYOffset || document.documentElement.scrollTop;
                const chatPageY = chatRect.top + currentWindowY;
                const desiredWindowY = Math.max(0, chatPageY - 65);

                window.scrollTo({
                    top: desiredWindowY,
                    behavior: 'auto'
                });
            };

            // Highlight pulse bubble
            targetEl.classList.add('wa-bubble-new-highlight');
            setTimeout(() => {
                targetEl.classList.remove('wa-bubble-new-highlight');
            }, 4000);

            // Eksekusi scroll instan langsung tanpa delay visual dari atas
            performScroll(true);
            setTimeout(() => performScroll(true), 50);
            setTimeout(() => performScroll(false), 300);

            // Jika ada gambar di dalam chat stream yang masih loading, re-scroll setelah gambar selesai dimuat
            if (stream) {
                const streamImgs = stream.querySelectorAll('img');
                streamImgs.forEach(img => {
                    if (!img.complete) {
                        img.addEventListener('load', () => performScroll(true), { once: true });
                    }
                });
            }

            window.addEventListener('load', () => performScroll(true), { once: true });
        }
    }

    if (newKronoId || hasKronoSuccess || (window.location.hash && window.location.hash.startsWith('#krono-item-'))) {
        scrollToTargetKrono();
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

        // Submitting Chat Form via AJAX (No page reload, no scroll to top!)
        waDirectChatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const textVal = waChatTextInput ? waChatTextInput.value.trim() : '';
            const hasPhoto = (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0) || currentWaCompressedPhoto;
            const hasLoc = waChatLat && waChatLat.value !== '';

            if (!textVal && !hasPhoto && !hasLoc) return;

            // Tunggu jika proses kompresi foto masih berjalan
            if (waCompressionPromise) {
                if (btnWaSendMsg) {
                    btnWaSendMsg.disabled = true;
                    btnWaSendMsg.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 0.9rem; height: 0.9rem;"></span>';
                }
                try {
                    await waCompressionPromise;
                } catch(e) {}
            }

            if (btnWaSendMsg) {
                btnWaSendMsg.disabled = true;
                btnWaSendMsg.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 0.9rem; height: 0.9rem;"></span>';
            }

            const formData = new FormData(waDirectChatForm);
            
            // Pasang teks kutipan jika sedang membalas pesan (Reply)
            if (activeReplyData) {
                const snippet = (activeReplyData.text || '').substring(0, 80).replace(/\n/g, ' ');
                const quotedText = `[Membalas ${activeReplyData.sender}]: ${snippet}\n\n` + textVal;
                formData.set('informasi', quotedText);
                cancelReply();
            }

            // Pasang file foto yang sudah terkompresi otomatis
            if (currentWaCompressedPhoto) {
                formData.set('foto', currentWaCompressedPhoto, currentWaCompressedPhoto.name);
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
                if (btnWaSendMsg) {
                    btnWaSendMsg.disabled = false;
                    btnWaSendMsg.innerHTML = '<i class="bi bi-send-fill"></i>';
                }

                if (res.success) {
                    // Reset input fields
                    if (waChatTextInput) {
                        waChatTextInput.value = '';
                        waChatTextInput.style.height = 'auto';
                        // Tutup keyboard di mobile setelah kirim pesan
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

                    // Langsung append pesan baru ke timeline secara seamless
                    if (res.data) {
                        appendSingleKronoToTimeline(res.data);
                    } else {
                        pollTimeline();
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
                    alert('Gagal mengirim pesan: ' + (res.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(err => {
                if (btnWaSendMsg) {
                    btnWaSendMsg.disabled = false;
                    btnWaSendMsg.innerHTML = '<i class="bi bi-send-fill"></i>';
                }
                alert('Gagal mengirim pesan: ' + err.message);
            });
        });
    }

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

    function enterWaFullscreen() {
        if (!waContainer) return;
        // Simpan posisi scroll halaman saat ini sebelum masuk mode fixed fullscreen
        savedWindowScrollY = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;

        waContainer.classList.add('wa-fullscreen');
        document.body.classList.add('wa-chat-fullscreen-active');
        if (icoWaFullscreen) {
            icoWaFullscreen.classList.remove('bi-arrows-fullscreen');
            icoWaFullscreen.classList.add('bi-fullscreen-exit');
        }
        if (btnWaFullscreen) btnWaFullscreen.title = 'Perkecil chat';
        document.body.style.overflow = 'hidden';

        // Scroll stream ke bawah setelah resize selesai
        requestAnimationFrame(() => {
            const stream = document.getElementById('timelineList');
            if (stream) stream.scrollTop = stream.scrollHeight;
        });
    }

    function exitWaFullscreen() {
        if (!waContainer) return;
        waContainer.classList.remove('wa-fullscreen');
        document.body.classList.remove('wa-chat-fullscreen-active');
        if (icoWaFullscreen) {
            icoWaFullscreen.classList.remove('bi-fullscreen-exit');
            icoWaFullscreen.classList.add('bi-arrows-fullscreen');
        }
        if (btnWaFullscreen) btnWaFullscreen.title = 'Perbesar chat';
        document.body.style.overflow = '';

        // Pertahankan posisi scroll halaman tepat di elemen chat, tidak melompat ke paling atas halaman
        requestAnimationFrame(() => {
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
        });
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
