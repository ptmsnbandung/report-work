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

    /* WhatsApp Direct Inline Input Bar */
    .wa-chat-input-bar {
        background: var(--neu-surface, #f0f2f5);
        padding: 0.55rem 0.85rem;
        border-top: 1px solid var(--neu-border-subtle, rgba(194,204,217,0.45));
        display: flex;
        align-items: flex-end;
        gap: 0.55rem;
        position: relative;
    }

    .wa-attach-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: transparent;
        border: none;
        color: #54656f;
        font-size: 1.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .wa-attach-btn:hover {
        background: rgba(0, 0, 0, 0.06);
        color: #1b39da;
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
        background: #ffffff;
        border: 1px solid rgba(203, 213, 225, 0.8);
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 40px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .wa-input-wrapper:focus-within {
        border-color: #2C7FFF;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05), 0 0 0 3px rgba(44, 127, 255, 0.15);
    }

    .wa-chat-textarea {
        border: none;
        outline: none;
        background: transparent;
        width: 100%;
        resize: none;
        font-size: 0.84rem;
        color: #0f172a;
        max-height: 100px;
        line-height: 1.35;
        padding: 3px 0;
    }

    .wa-chat-textarea::placeholder {
        color: #94a3b8;
    }

    .wa-attach-preview-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.25rem;
        padding-top: 0.25rem;
        border-top: 1px dashed #e2e8f0;
    }

    .wa-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(44, 127, 255, 0.4);
        cursor: pointer;
        transition: transform 0.15s ease, background 0.15s ease;
    }

    .wa-send-btn:hover {
        background: linear-gradient(135deg, #1b39da 0%, #122894 100%);
        transform: scale(1.05);
    }

    .wa-send-btn:active {
        transform: scale(0.95);
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
        .wa-chat-container {
            border-radius: 14px;
        }
        .wa-chat-stream {
            min-height: clamp(380px, calc(100dvh - 300px), 650px);
            max-height: clamp(380px, calc(100dvh - 300px), 650px);
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

    <!-- ── TOP HEADER HERO BANNER (BRAND BLUE FULL-WIDTH) ── -->
    <div class="card border-0 shadow-lg rounded-xl mb-3 overflow-hidden text-white" style="background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 30px rgba(7, 21, 43, 0.35);">
        <div class="card-body p-3.5 p-md-4">
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
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-3 py-1 ms-1 fw-bold" id="headerStatusBadge">
                                <i class="bi bi-check-circle-fill me-1"></i> CLOSE
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
                <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
                    <a href="{{ route('tiket.index') }}" class="btn btn-hero-action btn-sm rounded-pill px-3.5">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>

                    @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'OPEN')
                    <a href="{{ route('tiket.edit', $tiket->id) }}" class="btn btn-warning btn-sm rounded-pill px-3.5 shadow-xs text-dark fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    @endif

                    @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3.5 shadow-xs fw-bold"
                            data-bs-toggle="modal" data-bs-target="#closeTiketModal">
                        <i class="bi bi-check-circle-fill me-1"></i> Closing Tiket
                    </button>
                    @endif

                    <!-- Export Buttons -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-hero-action btn-sm dropdown-toggle rounded-pill px-3.5" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-1"></i> Export Laporan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
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

    <!-- ── TABBED NAVIGATION (MOBILE SCROLLABLE) ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-5 pb-4 mb-md-4 pb-md-0">
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
            </ul>
        </div>

        <div class="card-body p-1 p-sm-2.5 p-md-4">
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
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn btn-cjp-teal btn-sm d-flex align-items-center justify-content-center gap-1.5 rounded-pill px-3 shadow-xs wa-header-add-btn flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addKronologisModal" title="Update Kronologis">
                                <i class="bi bi-plus-lg"></i>
                                <span class="d-none d-sm-inline">Update Kronologis</span>
                            </button>
                            @endif
                        </div>

                        <!-- Chat Messages Stream Area -->
                        <div id="timelineWrapper">
                            @if($tiket->kronologis->count() > 0)
                                <div class="wa-chat-stream" id="timelineList">
                                    @php 
                                        $lastDate = null; 
                                        $currentUserId = auth()->id();
                                        $nameColors = ['#075e54', '#128c7e', '#0284c7', '#7c3aed', '#d97706', '#059669', '#2563eb'];
                                    @endphp
                                    @foreach($tiket->kronologis as $krono)
                                        @php 
                                            $currentDate = $krono->timestamp->format('Y-m-d'); 
                                            $isMe = ($krono->user_id === $currentUserId);
                                            $colorIndex = abs(crc32($krono->user?->name ?? 'User')) % count($nameColors);
                                            $senderColor = $nameColors[$colorIndex];
                                            $initials = strtoupper(substr($krono->user?->name ?? 'U', 0, 2));
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
                                            <div class="wa-avatar" style="background-color: {{ $senderColor }};" title="{{ $krono->user?->name }}">
                                                {{ $initials }}
                                            </div>
                                            @endif

                                            <div class="wa-bubble {{ $isMe ? 'wa-bubble-outgoing' : 'wa-bubble-incoming' }}">
                                                <!-- Bubble Header: Sender, Role & Category Tag -->
                                                <div class="wa-bubble-header">
                                                    <div class="wa-sender-info">
                                                        <span class="wa-sender-name" style="color: {{ $isMe ? '#0f766e' : $senderColor }};">
                                                            {{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}
                                                        </span>
                                                        <span class="wa-role-pill">{{ $krono->user?->role_short ?? '-' }}</span>
                                                    </div>

                                                    <div class="wa-bubble-actions">
                                                        <span class="wa-kategori-tag tag-{{ strtolower($krono->kategori) }}" title="{{ $krono->kategori_label }}">
                                                            <i class="bi {{ $krono->kategori_icon }}"></i>
                                                            <span>{{ $krono->kategori }}</span>
                                                        </span>

                                                        @if(auth()->user()->hasRole('admin'))
                                                        <form action="{{ route('tiket.kronologis.destroy', [$tiket->id, $krono->id]) }}" method="POST" class="d-inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan kronologis ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="wa-del-btn" title="Hapus pesan (Admin)">
                                                                <i class="bi bi-trash3"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($krono->kategori_label && $krono->kategori_label !== $krono->kategori)
                                                <div class="wa-kategori-sublabel">
                                                    <i class="bi bi-tag-fill me-1 opacity-75"></i>{{ $krono->kategori_label }}
                                                </div>
                                                @endif

                                                <!-- Message Text Body -->
                                                <div class="wa-msg-text">{{ $krono->informasi }}</div>

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

                        <!-- WhatsApp Direct Inline Chat Input Bar (WhatsApp-Native Pro) -->
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <form action="{{ route('tiket.kronologis.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="waDirectChatForm" class="wa-chat-input-bar">
                            @csrf
                            <input type="hidden" name="kategori" value="LAIN">
                            <input type="hidden" name="latitude" id="waChatLatitude" value="">
                            <input type="hidden" name="longitude" id="waChatLongitude" value="">
                            <input type="file" name="foto" id="waChatFotoInput" accept="image/*" class="d-none">

                            <!-- Paperclip Attachment Dropdown Menu -->
                            <div class="dropup position-relative">
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
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaCameraFoto">
                                            <div class="wa-attach-icon bg-success-subtle text-success">
                                                <i class="bi bi-camera-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small text-dark">Ambil Foto (Kamera)</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Potret langsung menggunakan kamera</div>
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
                            <div class="wa-input-wrapper">
                                <textarea name="informasi" id="waChatTextInput" class="wa-chat-textarea" rows="1" placeholder="Ketik update koordinasi lapangan..." required></textarea>
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

                            <!-- Submit Button -->
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
                    <h6 class="modal-title fw-bold">
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
                    <h6 class="modal-title fw-bold">
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
                    <h6 class="modal-title fw-bold">
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
                <h6 class="modal-title fw-bold"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Perbaikan di Peta</h6>
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
                    <h6 class="modal-title fw-bold">
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
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm text-white px-4">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Foto
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
                <h6 class="modal-title fw-bold"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Lokasi Dokumentasi di Peta</h6>
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
                    <h6 class="modal-title fw-bold">
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
                    <h6 class="modal-title fw-bold" id="addKronologisModalLabel">
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
                <h6 class="modal-title fw-bold"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Koordinat di Peta</h6>
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

<!-- ── MODAL ZOOM PHOTO LIGHTBOX ── -->
<div class="modal fade" id="photoZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 py-2">
                <span class="text-white small fw-semibold" id="photoZoomTitle">Foto Lapangan</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="#" id="photoZoomImg" class="img-fluid rounded-bottom" style="max-height: 80vh;" alt="Zoom Foto">
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL CLOSING TIKET ── -->
@if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
<div class="modal fade" id="closeTiketModal" tabindex="-1" aria-labelledby="closeTiketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.close', $tiket->id) }}" method="POST" id="formCloseTiket">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold" id="closeTiketModalLabel">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>Penutupan Tiket [{{ $tiket->no_tiket }}]
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Saat tiket di-close, sistem akan mengunci data dan secara otomatis menghitung durasi total gangguan (MTTR) serta memeriksa kepatuhan SLA.
                    </div>

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
                            <span class="small text-muted">Estimasi MTTR:</span>
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
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Closing Tiket
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
function zoomPhoto(url, title) {
    const zoomImg = document.getElementById('photoZoomImg');
    const zoomTitle = document.getElementById('photoZoomTitle');
    const modalEl = document.getElementById('photoZoomModal');

    if (zoomImg && modalEl) {
        zoomImg.src = url;
        if (zoomTitle) zoomTitle.textContent = title || 'Foto Dokumentasi Kronologis';
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

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
function compressImageFile(file, customOptions = {}) {
    return new Promise((resolve) => {
        if (!file || !file.type.startsWith('image/')) {
            return resolve(file);
        }

        const options = {
            maxWidth: 1600,
            maxHeight: 1600,
            quality: 0.82,
            maxSizeMB: 0.8,
            ...customOptions
        };

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let width = img.naturalWidth || img.width;
                let height = img.naturalHeight || img.height;

                // Hitung aspek rasio agar tidak terdistorsi
                if (width > options.maxWidth || height > options.maxHeight) {
                    if (width > height) {
                        height = Math.round((height * options.maxWidth) / width);
                        width = options.maxWidth;
                    } else {
                        width = Math.round((width * options.maxHeight) / height);
                        height = options.maxHeight;
                    }
                } else if (file.size <= options.maxSizeMB * 1024 * 1024 && file.type === 'image/jpeg') {
                    // File sudah kecil dan dimensi aman
                    return resolve(file);
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');

                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to JPEG blob with quality 0.82
                canvas.toBlob(
                    function(blob) {
                        if (!blob || blob.size >= file.size) {
                            return resolve(file);
                        }

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
            img.onerror = function() {
                resolve(file);
            };
            img.src = e.target.result;
        };
        reader.onerror = function() {
            resolve(file);
        };
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
                const compressed = await compressImageFile(file, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 });
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

    // ── 8. REALTIME AJAX POLLING FOR TIMELINE (EVERY 30 SECONDS) ──
    const tiketId = {{ $tiket->id }};
    const timelineApiUrl = "{{ route('tiket.kronologis.index', $tiket->id) }}";
    let knownCount = {{ $tiket->kronologis->count() }};

    function pollTimeline() {
        fetch(timelineApiUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.count !== knownCount) {
                knownCount = res.count;
                const badgeEl = document.getElementById('kronologisCountBadge');
                if (badgeEl) badgeEl.textContent = knownCount;
                renderTimelineFromData(res.data);
            }
        })
        .catch(err => console.debug('Timeline polling error:', err));
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

        const nameColors = ['#075e54', '#128c7e', '#0284c7', '#7c3aed', '#d97706', '#059669', '#2563eb'];
        function getSenderColor(name) {
            let hash = 0;
            for (let i = 0; i < (name || 'User').length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return nameColors[Math.abs(hash) % nameColors.length];
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/\n/g, '<br>');
        }

        const currentUserId = {{ auth()->id() ?? 0 }};
        const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
        const destroyUrlBase = "{{ url('/tiket/' . $tiket->id . '/kronologis') }}";
        const csrfToken = "{{ csrf_token() }}";

        function buildSingleKronoHtml(k) {
            const isMe = (k.user_id === currentUserId);
            const initials = (k.user_name || 'U').substring(0, 2).toUpperCase();
            const senderColor = getSenderColor(k.user_name);
            const kategoriLower = (k.kategori || 'lain').toLowerCase();

            return `
                <div class="wa-msg-row ${isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming'}" id="krono-item-${k.id}">
                    ${!isMe ? `
                    <div class="wa-avatar" style="background-color: ${senderColor};" title="${k.user_name}">
                        ${initials}
                    </div>` : ''}

                    <div class="wa-bubble ${isMe ? 'wa-bubble-outgoing' : 'wa-bubble-incoming'}">
                        <div class="wa-bubble-header">
                            <div class="wa-sender-info">
                                <span class="wa-sender-name" style="color: ${isMe ? '#0f766e' : senderColor};">
                                    ${isMe ? 'Anda' : (k.user_name || 'User')}
                                </span>
                                <span class="wa-role-pill">${k.user_role || '-'}</span>
                            </div>

                            <div class="wa-bubble-actions">
                                <span class="wa-kategori-tag tag-${kategoriLower}">
                                    <i class="bi ${k.kategori_icon || 'bi-chat-dots-fill'}"></i>
                                    <span>${k.kategori}</span>
                                </span>

                                ${isAdmin ? `
                                <form action="${destroyUrlBase}/${k.id}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan kronologis ini?');">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="wa-del-btn" title="Hapus pesan (Admin)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>` : ''}
                            </div>
                        </div>

                        ${k.kategori_label && k.kategori_label !== k.kategori ? `
                        <div class="wa-kategori-sublabel">
                            <i class="bi bi-tag-fill me-1 opacity-75"></i>${k.kategori_label}
                        </div>` : ''}

                        <div class="wa-msg-text">${escapeHtml(k.informasi || '')}</div>

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

        function appendSingleKronoToTimeline(k) {
            if (!k || !k.id) return;

            knownCount = (knownCount || 0) + 1;
            const badgeEl = document.getElementById('kronologisCountBadge');
            if (badgeEl) badgeEl.textContent = knownCount;

            const wrapper = document.getElementById('timelineWrapper');
            let stream = document.getElementById('timelineList');

            const emptyEl = document.getElementById('emptyTimeline');
            if (emptyEl) {
                emptyEl.remove();
            }

            if (!stream && wrapper) {
                wrapper.innerHTML = '<div class="wa-chat-stream" id="timelineList"></div>';
                stream = document.getElementById('timelineList');
                attachStreamScrollListener(stream);
            }

            if (!stream) return;

            // Jangan append jika sudah ada di DOM
            if (document.getElementById('krono-item-' + k.id)) return;

            // Cek apakah perlu menambahkan date divider baru
            const lastDivider = stream.querySelector('.wa-date-divider:last-of-type .wa-date-chip');
            const lastDateText = lastDivider ? lastDivider.textContent.trim() : '';
            if (k.formatted_date && (!lastDateText || !lastDateText.includes(k.formatted_date))) {
                const dividerHtml = `
                    <div class="wa-date-divider">
                        <span class="wa-date-chip">
                            <i class="bi bi-calendar3 me-1"></i> ${k.formatted_date}
                        </span>
                    </div>`;
                stream.insertAdjacentHTML('beforeend', dividerHtml);
            }

            // Kunci posisi window agar tidak melompat ke atas saat DOM berubah
            const savedWindowY = window.scrollY || window.pageYOffset;

            const singleHtml = buildSingleKronoHtml(k);
            stream.insertAdjacentHTML('beforeend', singleHtml);

            // Restore posisi window langsung setelah DOM diubah
            window.scrollTo(0, savedWindowY);

            // Scroll container chat ke bawah secara instan
            stream.scrollTop = stream.scrollHeight;

            // Pastikan scroll sudah di bawah setelah paint berikutnya
            requestAnimationFrame(() => {
                stream.scrollTop = stream.scrollHeight;
                window.scrollTo(0, savedWindowY);
            });

            // Highlight pesan baru
            const newEl = document.getElementById('krono-item-' + k.id);
            if (newEl) {
                newEl.classList.add('wa-bubble-new-highlight');
                setTimeout(() => newEl.classList.remove('wa-bubble-new-highlight'), 4000);
            }

            // Kembalikan fokus ke textarea chat tanpa scroll window
            const waChatTextInput = document.getElementById('waChatTextInput');
            if (waChatTextInput) {
                waChatTextInput.focus({ preventScroll: true });
            }
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
        
        // Prevent window scroll jumping by locking wrapper min-height during DOM update
        const prevHeight = wrapper.offsetHeight;
        if (prevHeight > 0) {
            wrapper.style.minHeight = prevHeight + 'px';
        }

        wrapper.innerHTML = html;

        setTimeout(() => {
            wrapper.style.minHeight = '';
        }, 150);

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

    // ── 15. WHATSAPP DIRECT INLINE CHAT INPUT & ATTACHMENTS ──
    const waDirectChatForm = document.getElementById('waDirectChatForm');
    if (waDirectChatForm) {
        const waChatTextInput = document.getElementById('waChatTextInput');
        const waChatFotoInput = document.getElementById('waChatFotoInput');
        const waChatLat = document.getElementById('waChatLatitude');
        const waChatLng = document.getElementById('waChatLongitude');
        const btnWaUploadFoto = document.getElementById('btnWaUploadFoto');
        const btnWaCameraFoto = document.getElementById('btnWaCameraFoto');
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

        let currentWaCompressedPhoto = null;
        let waCompressionPromise = null;

        // Auto-expand textarea on typing
        waChatTextInput?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Enter key to submit (Shift+Enter for newline)
        waChatTextInput?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0 || (waChatFotoInput && waChatFotoInput.files && waChatFotoInput.files.length > 0) || currentWaCompressedPhoto) {
                    waDirectChatForm.requestSubmit();
                }
            }
        });

        // Upload Foto (Galeri)
        btnWaUploadFoto?.addEventListener('click', function() {
            if (waChatFotoInput) {
                waChatFotoInput.removeAttribute('capture');
                waChatFotoInput.click();
            }
        });

        // Ambil Foto (Kamera HP/Webcam)
        btnWaCameraFoto?.addEventListener('click', function() {
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

                // Proses kompresi gambar otomatis di background
                waCompressionPromise = compressImageFile(originalFile, {
                    maxWidth: 1600,
                    maxHeight: 1600,
                    quality: 0.82
                }).then(compressedFile => {
                    currentWaCompressedPhoto = compressedFile;

                    const origSize = formatFileSize(originalFile.size);
                    const compSize = formatFileSize(compressedFile.size);

                    if (waPhotoSizeBadge) {
                        waPhotoSizeBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5';
                        waPhotoSizeBadge.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${compSize}`;
                        waPhotoSizeBadge.title = `Ukuran asli ${origSize} dikompresi menjadi ${compSize}`;
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
        if (!streamEl || !btnWaScrollBottom) return;
        const distanceFromBottom = streamEl.scrollHeight - streamEl.scrollTop - streamEl.clientHeight;

        if (distanceFromBottom > 80) {
            btnWaScrollBottom.classList.remove('d-none');
            btnWaScrollBottom.classList.add('d-flex');
        } else {
            btnWaScrollBottom.classList.add('d-none');
            btnWaScrollBottom.classList.remove('d-flex');
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
});
</script>
@endpush
