<template>
    <Teleport to="body">
        <div v-if="show" class="oc-overlay">
            <div class="oc-modal">

                <!-- Header -->
                <div class="oc-header">
                    <div class="oc-logo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Order Confirmed</span>
                    </div>
                </div>

                <!-- Body -->
                <div class="oc-body">

                    <!-- Checkmark hero -->
                    <div class="oc-check-wrap">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <p class="oc-thanks">Thank you for your order!</p>
                    </div>

                    <!-- Order number -->
                    <div v-if="order" class="oc-order-number-wrap">
                        <span class="oc-order-label">Order Number</span>
                        <div class="oc-order-number-row">
                            <span class="oc-order-number">{{ order.order_number }}</span>
                            <button type="button" class="oc-copy-btn" :class="{ copied }" @click="copyOrderNumber" :title="copied ? 'Copied!' : 'Copy order number'">
                                <svg v-if="!copied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                                <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Items list -->
                    <div v-if="order && order.items && order.items.length" class="oc-items">
                        <div class="oc-items-title">Items Ordered</div>
                        <div v-for="item in order.items" :key="item.id" class="oc-item-row">
                            <img
                                v-if="item.product_image"
                                :src="item.product_image"
                                :alt="item.product_name"
                                class="oc-item-img"
                            />
                            <div class="oc-item-info">
                                <span class="oc-item-name">{{ item.product_name }}</span>
                                <span class="oc-item-qty">Qty: {{ item.quantity }}</span>
                            </div>
                            <span class="oc-item-price">${{ Number(item.subtotal).toFixed(2) }}</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div v-if="order" class="oc-total-row">
                        <span class="oc-total-label">Total Paid</span>
                        <span class="oc-total-value">${{ Number(order.total_amount).toFixed(2) }} {{ order.currency }}</span>
                    </div>

                    <!-- Payment method -->
                    <div class="oc-payment-method">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Paid via Bakong KHQR
                    </div>

                    <!-- CTA -->
                    <button type="button" class="oc-continue-btn" @click="$emit('close')">
                        Continue Shopping
                    </button>

                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    show:  { type: Boolean, required: true },
    order: { type: Object, default: null },
});

defineEmits(['close']);

const copied = ref(false);

const copyOrderNumber = async () => {
    const orderNumber = document.querySelector('.oc-order-number')?.textContent?.trim();
    if (!orderNumber) return;

    try {
        await navigator.clipboard.writeText(orderNumber);
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    } catch {
        // clipboard not available — silently ignore
    }
};
</script>

<style scoped>
/* ── Overlay ─────────────────────────────────────── */
.oc-overlay {
    position: fixed;
    inset: 0;
    z-index: 210;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

/* ── Modal card ──────────────────────────────────── */
.oc-modal {
    background: #0d1220;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    width: 100%;
    max-width: 400px;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6);
}

/* ── Header ──────────────────────────────────────── */
.oc-header {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}

.oc-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #22c55e;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.02em;
}

/* ── Body ────────────────────────────────────────── */
.oc-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px 20px 28px;
    gap: 18px;
}

/* ── Checkmark hero ──────────────────────────────── */
.oc-check-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.oc-thanks {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
}

/* ── Order number ────────────────────────────────── */
.oc-order-number-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    width: 100%;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    padding: 14px 16px;
}

.oc-order-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

.oc-order-number-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.oc-order-number {
    font-family: 'Courier New', Courier, monospace;
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.05em;
}

.oc-copy-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    background: transparent;
    color: rgba(255, 255, 255, 0.45);
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
}

.oc-copy-btn:hover {
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

.oc-copy-btn.copied {
    border-color: #22c55e;
}

/* ── Items list ──────────────────────────────────── */
.oc-items {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.oc-items-title {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

.oc-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.oc-item-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
}

.oc-item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.oc-item-name {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.85);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.oc-item-qty {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.35);
}

.oc-item-price {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    flex-shrink: 0;
}

/* ── Total ───────────────────────────────────────── */
.oc-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding-top: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.oc-total-label {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
}

.oc-total-value {
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.02em;
}

/* ── Payment method tag ──────────────────────────── */
.oc-payment-method {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.28);
}

/* ── CTA button ──────────────────────────────────── */
.oc-continue-btn {
    width: 100%;
    padding: 12px;
    background: #ffffff;
    color: #080d16;
    border: 0;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    cursor: pointer;
    transition: opacity 0.2s;
}

.oc-continue-btn:hover { opacity: 0.85; }

/* ── Mobile ──────────────────────────────────────── */
@media (max-width: 420px) {
    .oc-overlay  { padding: 10px; }
    .oc-modal    { border-radius: 10px; }
    .oc-header   { padding: 13px 16px; }
    .oc-body     { padding: 20px 16px 24px; gap: 16px; }
    .oc-order-number { font-size: 15px; }
    .oc-total-value  { font-size: 16px; }
}

@media (max-height: 720px) {
    .oc-overlay {
        align-items: flex-end;
    }
}
</style>
