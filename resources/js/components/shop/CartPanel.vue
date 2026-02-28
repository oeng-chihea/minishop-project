<template>
    <div class="drawer-wrap" :class="{ open }">
        <div class="backdrop" @click="$emit('close')"></div>

        <aside class="panel" :class="{ 'cart-slide': isSliding }">
            <!-- Header -->
            <div class="panel-head">
                <div class="head-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span class="panel-title">Your Cart</span>
                </div>
                <div class="head-right">
                    <span class="item-count">{{ totalItems }} {{ totalItems === 1 ? 'item' : 'items' }}</span>
                    <button type="button" class="close-btn" aria-label="Close cart" @click="$emit('close')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="!items.length" class="empty-state">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <p>Your cart is empty.</p>
                <span>Add products from the shop below.</span>
            </div>

            <!-- Cart items -->
            <TransitionGroup v-else name="cart-item" tag="ul" class="cart-list">
                <li v-for="item in items" :key="item.id" class="cart-item">
                    <div class="item-meta">
                        <h4>{{ item.name }}</h4>
                        <p class="unit-price">${{ item.price.toFixed(2) }} each</p>
                    </div>
                    <div class="item-actions">
                        <strong class="item-total">${{ (item.qty * item.price).toFixed(2) }}</strong>
                        <div class="qty-control" aria-label="Quantity controls">
                            <button
                                type="button"
                                class="qty-btn"
                                aria-label="Decrease quantity"
                                @click="$emit('removeOne', item.id)"
                            >−</button>
                            <span class="qty-val">{{ item.qty }}</span>
                            <button
                                type="button"
                                class="qty-btn"
                                aria-label="Increase quantity"
                                @click="$emit('addOne', item.id)"
                            >+</button>
                        </div>
                        <button
                            type="button"
                            class="trash-btn"
                            aria-label="Remove item"
                            @click="$emit('removeItem', item.id)"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                        </button>
                    </div>
                </li>
            </TransitionGroup>

            <!-- Footer -->
            <div class="panel-footer">
                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-amount">${{ totalPrice.toFixed(2) }}</span>
                </div>

                <button
                    type="button"
                    class="btn-checkout"
                    :disabled="!items.length || checkoutLoading"
                    @click="$emit('checkout')"
                >
                    <svg v-if="!checkoutLoading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <svg v-else class="spin-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>{{ checkoutLoading ? 'Processing…' : 'Checkout with ABA PayWay' }}</span>
                </button>

                <p v-if="checkoutError" class="checkout-error">{{ checkoutError }}</p>

                <button
                    type="button"
                    class="btn-clear"
                    :disabled="!items.length"
                    @click="$emit('clear')"
                >Clear cart</button>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        required: true
    },
    items: {
        type: Array,
        required: true
    },
    totalItems: {
        type: Number,
        required: true
    },
    totalPrice: {
        type: Number,
        required: true
    },
    slideTrigger: {
        type: Number,
        required: true
    },
    checkoutLoading: {
        type: Boolean,
        default: false
    },
    checkoutError: {
        type: String,
        default: ''
    }
});

const isSliding = ref(false);
let slideTimeout;

watch(() => props.slideTrigger, () => {
    isSliding.value = true;
    clearTimeout(slideTimeout);
    slideTimeout = setTimeout(() => {
        isSliding.value = false;
    }, 420);
});

onBeforeUnmount(() => {
    clearTimeout(slideTimeout);
});
</script>

<style scoped>
/* ─── Overlay wrap ─────────────────────────────── */
.drawer-wrap {
    position: fixed;
    inset: 0;
    z-index: 70;
    pointer-events: none;
}

.drawer-wrap.open {
    pointer-events: auto;
}

/* ─── Backdrop ─────────────────────────────────── */
.backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    opacity: 0;
    transition: opacity 0.35s ease;
}

.drawer-wrap.open .backdrop {
    opacity: 1;
}

/* ─── Panel ────────────────────────────────────── */
.panel {
    position: absolute;
    right: 0;
    top: 0;
    width: min(420px, 92vw);
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #0d1220;
    border-left: 1px solid rgba(255, 255, 255, 0.07);
    box-shadow: -24px 0 64px rgba(0, 0, 0, 0.5);
    transform: translateX(100%);
    transition: transform 0.38s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.38s ease;
    will-change: transform;
}

.drawer-wrap.open .panel {
    transform: translateX(0);
}

.cart-slide {
    transform: translateX(-6px) !important;
    box-shadow: -32px 0 72px rgba(0, 0, 0, 0.65);
}

/* ─── Header ───────────────────────────────────── */
.panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 22px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    flex-shrink: 0;
}

.head-left {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.5);
}

.panel-title {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #ffffff;
}

.head-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.item-count {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.3);
    letter-spacing: 0.04em;
}

.close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    background: transparent;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
}

.close-btn:hover {
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
}

/* ─── Empty state ──────────────────────────────── */
.empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 40px 24px;
    text-align: center;
}

.empty-icon {
    color: rgba(255, 255, 255, 0.12);
}

.empty-state p {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
}

.empty-state span {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.25);
}

/* ─── Cart list ────────────────────────────────── */
.cart-list {
    flex: 1;
    list-style: none;
    margin: 0;
    padding: 0 22px;
    overflow-y: auto;
    overscroll-behavior: contain;
}

.cart-list::-webkit-scrollbar {
    width: 4px;
}

.cart-list::-webkit-scrollbar-track {
    background: transparent;
}

.cart-list::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}

/* ─── Cart item ────────────────────────────────── */
.cart-item {
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    padding: 16px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.cart-item:first-child {
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    margin-top: 8px;
}

h4 {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    letter-spacing: -0.01em;
}

.unit-price {
    margin: 5px 0 0;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.3);
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.item-total {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    min-width: 52px;
    text-align: right;
}

/* ─── Qty control ──────────────────────────────── */
.qty-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    overflow: hidden;
}

.qty-btn {
    width: 26px;
    height: 26px;
    border: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    transition: background 0.15s ease, color 0.15s ease;
}

.qty-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
}

.qty-val {
    min-width: 22px;
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    color: #ffffff;
}

/* ─── Trash button ─────────────────────────────── */
.trash-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 2px;
    background: transparent;
    color: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
}

.trash-btn:hover {
    border-color: rgba(239, 68, 68, 0.4);
    color: #ef4444;
    background: rgba(239, 68, 68, 0.08);
}

/* ─── Footer ───────────────────────────────────── */
.panel-footer {
    padding: 18px 22px 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    flex-shrink: 0;
}

.total-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.total-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

.total-amount {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
}

/* ─── Checkout button ──────────────────────────── */
.btn-checkout {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 0;
    border-radius: 2px;
    background: #ffffff;
    color: #080d16;
    font-weight: 700;
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 12px;
    padding: 14px 16px;
    cursor: pointer;
    transition: background 0.2s ease, opacity 0.2s ease;
}

.btn-checkout:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.88);
}

.btn-checkout:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* ─── Spinner ──────────────────────────────────── */
.spin-icon {
    animation: spin 0.9s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ─── Error ────────────────────────────────────── */
.checkout-error {
    margin: 10px 0 0;
    color: #f87171;
    font-size: 12px;
    line-height: 1.55;
    text-align: center;
}

/* ─── Clear button ─────────────────────────────── */
.btn-clear {
    width: 100%;
    margin-top: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    background: transparent;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 600;
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 11px;
    padding: 11px;
    cursor: pointer;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
}

.btn-clear:hover:not(:disabled) {
    border-color: rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.04);
}

.btn-clear:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

/* ─── Item transitions ─────────────────────────── */
.cart-item-enter-active,
.cart-item-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.cart-item-enter-from,
.cart-item-leave-to {
    opacity: 0;
    transform: translateX(16px);
}

.cart-item-move {
    transition: transform 0.3s ease;
}

/* ─── Responsive ───────────────────────────────── */
@media (max-width: 640px) {
    .total-amount {
        font-size: 24px;
    }
}
</style>
