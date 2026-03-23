<template>
    <Teleport to="body">
        <div v-if="show" class="bk-overlay" @click.self="onOverlayClick">
            <div class="bk-modal">

                <!-- Header -->
                <div class="bk-header">
                    <div class="bk-logo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <span>Bakong KHQR</span>
                    </div>
                    <button type="button" class="bk-close" aria-label="Close" @click="$emit('close')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <!-- Loading state -->
                <div v-if="loading" class="bk-loading">
                    <div class="bk-spinner"></div>
                    <p>Generating your QR code…</p>
                </div>

                <!-- Error state -->
                <div v-else-if="error" class="bk-error-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p>{{ error }}</p>
                    <button type="button" class="bk-retry-btn" @click="$emit('retry')">Try Again</button>
                </div>

                <!-- QR ready state -->
                <div v-else-if="qrImage" class="bk-qr-body">

                    <!-- Amount -->
                    <div class="bk-amount">
                        <span class="bk-amount-label">Amount to pay</span>
                        <span class="bk-amount-value">${{ amount.toFixed(2) }} USD</span>
                    </div>

                    <!-- QR Image -->
                    <div class="bk-qr-wrap" :class="{ 'paid': paid }">
                        <div v-html="qrImage" class="bk-qr-img"></div>
                        <!-- Paid overlay -->
                        <div v-if="paid" class="bk-paid-overlay">
                            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>Payment Received!</span>
                        </div>
                    </div>

                    <!-- Instruction -->
                    <p v-if="!paid" class="bk-instruction">
                        Open your <strong>Bakong</strong> or any KHQR-compatible bank app and scan this QR code. When prompted, enter <strong>${{ amount.toFixed(2) }} USD</strong> as the payment amount.
                    </p>

                    <!-- Status row -->
                    <div v-if="!paid" class="bk-poll-row">
                        <span class="bk-dot pulse"></span>
                        <span class="bk-poll-text">Scan &amp; pay, then tap the button below</span>
                    </div>

                    <!-- Countdown timer -->
                    <div v-if="!paid && timeLeft > 0" class="bk-timer">
                        QR expires in <strong>{{ formattedTime }}</strong>
                    </div>
                    <div v-if="!paid && timeLeft <= 0" class="bk-expired">
                        QR code has expired. <button type="button" class="bk-link" @click="$emit('retry')">Generate a new one</button>
                    </div>

                    <!-- Manual confirm button -->
                    <div v-if="!paid && timeLeft > 0" class="bk-manual-row">
                        <p class="bk-manual-hint">Already paid? Tap the button below.</p>
                        <button type="button" class="bk-manual-btn" @click="confirmManually">
                            ✓ I've Paid
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    show:        { type: Boolean, required: true },
    loading:     { type: Boolean, default: false },
    error:       { type: String,  default: '' },
    qrImage:     { type: String,  default: '' },
    billNumber:  { type: String,  default: '' },
    md5:         { type: String,  default: '' },
    amount:      { type: Number,  default: 0 },
    lifetime:    { type: Number,  default: 300 }, // seconds
    bakongToken: { type: String,  default: '' },  // used to poll Bakong directly from browser
});

const emit = defineEmits(['close', 'paid', 'retry']);

const paid     = ref(false);
const polling  = ref(false);
const timeLeft = ref(props.lifetime);

let countdownInterval = null;

// ── Countdown timer ──────────────────────────────────────
const formattedTime = computed(() => {
    const m = Math.floor(timeLeft.value / 60);
    const s = timeLeft.value % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
});

function startCountdown() {
    timeLeft.value = props.lifetime;
    clearInterval(countdownInterval);
    countdownInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value -= 1;
        } else {
            clearInterval(countdownInterval);
        }
    }, 1000);
}

function stopPolling() {
    clearInterval(countdownInterval);
    polling.value = false;
}

// Manual confirmation — customer taps "I've Paid" after scanning.
// Brief 600ms delay so the "Payment Received!" overlay is visible,
// then emit paid immediately — parent shows confirmation modal right away.
function confirmManually() {
    paid.value    = true;
    polling.value = false;
    stopPolling();
    setTimeout(() => emit('paid'), 600);
}

// ── Watchers ──────────────────────────────────────────────
watch(() => props.show, (val) => {
    if (!val) {
        stopPolling();
        paid.value = false;
    }
});

watch(() => props.billNumber, (val) => {
    paid.value = false;
    stopPolling();
    if (val && props.show) {
        startCountdown();
    }
}, { immediate: true });

// ── Cleanup ───────────────────────────────────────────────
onBeforeUnmount(() => stopPolling());

// Prevent closing while payment is being confirmed
function onOverlayClick() {
    if (!paid.value) {
        emit('close');
    }
}
</script>

<style scoped>
/* ── Overlay ─────────────────────────────────────── */
.bk-overlay {
    position: fixed;
    inset: 0;
    z-index: 200;
    background: rgba(0, 0, 0, 0.72);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

/* ── Modal card ──────────────────────────────────── */
.bk-modal {
    background: #0d1220;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    width: 100%;
    max-width: 380px;
    max-height: min(92vh, 760px);
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6);
    overflow: hidden;
    overflow-y: auto;
}

/* ── Header ──────────────────────────────────────── */
.bk-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}

.bk-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.02em;
}

.bk-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    background: transparent;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
}

.bk-close:hover {
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* ── Loading ─────────────────────────────────────── */
.bk-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    padding: 48px 24px;
    color: rgba(255, 255, 255, 0.5);
    font-size: 14px;
}

.bk-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid rgba(255, 255, 255, 0.1);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Error state ─────────────────────────────────── */
.bk-error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    padding: 40px 24px;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    font-size: 14px;
}

.bk-retry-btn {
    padding: 10px 24px;
    background: #ffffff;
    color: #080d16;
    border: 0;
    border-radius: 4px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    transition: opacity 0.2s;
}

.bk-retry-btn:hover { opacity: 0.85; }

/* ── QR body ─────────────────────────────────────── */
.bk-qr-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 20px 24px;
    gap: 14px;
}

/* ── Amount ──────────────────────────────────────── */
.bk-amount {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.bk-amount-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

.bk-amount-value {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
}

/* ── QR image ────────────────────────────────────── */
.bk-qr-wrap {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #ffffff;
    padding: 8px;
    line-height: 0;
    width: min(240px, calc(100% - 16px));
}

.bk-qr-img {
    width: 100%;
    aspect-ratio: 1;
    display: block;
}
.bk-qr-img :deep(svg) {
    width: 100%;
    height: 100%;
    display: block;
}

/* ── Paid overlay ────────────────────────────────── */
.bk-paid-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.78);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #22c55e;
    font-weight: 700;
    font-size: 15px;
}

.bk-sender {
    font-size: 12px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.75);
    letter-spacing: 0.01em;
}

/* ── Instruction ─────────────────────────────────── */
.bk-instruction {
    margin: 0;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.45);
    text-align: center;
    line-height: 1.6;
    max-width: 280px;
}

/* ── Poll status row ─────────────────────────────── */
.bk-poll-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.bk-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
}

.bk-dot.pulse {
    background: #22c55e;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(0.8); }
}

.bk-poll-text {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.35);
}

/* ── Timer ───────────────────────────────────────── */
.bk-timer {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.3);
}

.bk-timer strong {
    color: rgba(255, 255, 255, 0.6);
}

/* ── Expired ─────────────────────────────────────── */
.bk-expired {
    font-size: 12px;
    color: #f87171;
    text-align: center;
}

.bk-link {
    background: none;
    border: none;
    color: #ffffff;
    cursor: pointer;
    text-decoration: underline;
    font-size: inherit;
    padding: 0;
}

/* ── Manual confirm ──────────────────────────────── */
.bk-manual-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding-top: 4px;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.bk-manual-hint {
    margin: 0;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.3);
}

.bk-manual-btn {
    width: 100%;
    padding: 11px;
    background: #22c55e;
    color: #fff;
    border: 0;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: opacity 0.2s;
}

.bk-manual-btn:hover { opacity: 0.88; }

/* ── Mobile responsive ───────────────────────────── */
@media (max-width: 420px) {
    .bk-overlay  { padding: 10px; }
    .bk-modal    { border-radius: 10px; }
    .bk-header   { padding: 13px 16px; }
    .bk-qr-body  { padding: 16px 16px 20px; gap: 12px; }
    .bk-amount-value { font-size: 22px; }
    .bk-instruction  { font-size: 12px; max-width: 100%; }
    .bk-loading, .bk-error-state { padding: 32px 16px; }
    .bk-retry-btn { padding: 9px 20px; font-size: 12px; }
}

@media (max-height: 720px) {
    .bk-overlay {
        align-items: flex-end;
    }
}
</style>
