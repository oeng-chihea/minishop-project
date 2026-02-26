<template>
    <div class="drawer-wrap" :class="{ open }">
        <div class="backdrop" @click="$emit('close')"></div>

        <aside class="card" :class="{ 'cart-slide': isSliding }">
            <div class="head">
                <h2>Cart</h2>
                <div class="head-right">
                    <span>{{ totalItems }} items</span>
                    <button type="button" class="close-btn" @click="$emit('close')">✕</button>
                </div>
            </div>

            <p v-if="!items.length" class="empty">Your cart is empty. Add products from the shop section.</p>

            <TransitionGroup v-else name="cart-item" tag="ul" class="cart-list">
                <li v-for="item in items" :key="item.id" class="cart-item">
                    <div class="item-meta">
                        <h4>{{ item.name }}</h4>
                        <p>${{ item.price.toFixed(2) }} each</p>
                    </div>
                    <div class="item-actions">
                        <strong>${{ (item.qty * item.price).toFixed(2) }}</strong>
                        <div class="qty-control" aria-label="Quantity controls">
                            <button
                                type="button"
                                class="qty-btn"
                                aria-label="Decrease quantity"
                                @click="$emit('removeOne', item.id)"
                            >
                                −
                            </button>
                            <span>{{ item.qty }}</span>
                            <button
                                type="button"
                                class="qty-btn"
                                aria-label="Increase quantity"
                                @click="$emit('addOne', item.id)"
                            >
                                +
                            </button>
                        </div>
                        <button
                            type="button"
                            class="trash-btn"
                            aria-label="Remove item"
                            @click="$emit('removeItem', item.id)"
                        >
                            🗑
                        </button>
                    </div>
                </li>
            </TransitionGroup>

            <div class="total-row">
                <p>Total</p>
                <h3>${{ totalPrice.toFixed(2) }}</h3>
            </div>

            <button
                type="button"
                class="checkout"
                :disabled="!items.length || checkoutLoading"
                @click="$emit('checkout')"
            >
                <span v-if="checkoutLoading">Processing…</span>
                <span v-else>Checkout with ABA PayWay</span>
            </button>

            <p v-if="checkoutError" class="checkout-error">{{ checkoutError }}</p>

            <button type="button" class="clear" :disabled="!items.length" @click="$emit('clear')">Clear cart</button>
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
.drawer-wrap {
    position: fixed;
    inset: 0;
    z-index: 70;
    pointer-events: none;
}

.drawer-wrap.open {
    pointer-events: auto;
}

.backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.32);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.drawer-wrap.open .backdrop {
    opacity: 1;
}

.card {
    position: absolute;
    right: 0;
    top: 0;
    width: min(430px, 92vw);
    height: 100%;
    overflow-y: auto;
    background: var(--color-surface);
    border-left: 1px solid var(--color-line);
    border-radius: 0;
    padding: 22px;
    box-shadow: 0 16px 28px rgba(2, 6, 23, 0.08);
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.42s ease;
    will-change: transform;
}

.drawer-wrap.open .card {
    transform: translateX(0);
}

.cart-slide {
    transform: translateX(-8px);
    box-shadow: 0 24px 36px rgba(31, 58, 95, 0.18);
}

.head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.head-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.head h2 {
    margin: 0;
    font-size: 34px;
}

.head span {
    color: var(--color-muted);
    font-size: 14px;
}

.close-btn {
    border: 1px solid var(--color-line);
    background: var(--color-surface);
    color: var(--color-text);
    border-radius: 2px;
    width: 30px;
    height: 30px;
    cursor: pointer;
}

.empty {
    color: var(--color-muted);
    line-height: 1.45;
}

.cart-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.cart-item {
    border-top: 1px solid var(--color-line);
    padding: 13px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.cart-item:first-child {
    border-top: 0;
}

h4 {
    margin: 0;
}

.item-meta p {
    margin: 6px 0 0;
    color: var(--color-muted);
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.qty-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--color-line);
    border-radius: 2px;
    overflow: hidden;
}

.qty-btn {
    width: 28px;
    height: 28px;
    border: 0;
    background: var(--color-surface);
    color: var(--color-text);
    cursor: pointer;
    font-size: 17px;
    line-height: 1;
}

.qty-control span {
    min-width: 24px;
    text-align: center;
    font-weight: 700;
    font-size: 13px;
}

.trash-btn {
    border: 1px solid var(--color-line);
    background: var(--color-surface);
    color: var(--color-text);
    border-radius: 2px;
    width: 30px;
    height: 30px;
    padding: 0;
    cursor: pointer;
}

.total-row {
    border-top: 1px solid var(--color-line);
    margin-top: 12px;
    padding-top: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.total-row p {
    margin: 0;
    color: var(--color-muted);
}

.total-row h3 {
    margin: 0;
    font-size: 40px;
    letter-spacing: -0.02em;
}

.clear {
    width: 100%;
    margin-top: 14px;
    border: 0;
    border-radius: 2px;
    background: var(--color-primary);
    color: #ffffff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px;
    cursor: pointer;
}

.checkout {
    width: 100%;
    margin-top: 14px;
    border: 0;
    border-radius: 2px;
    background: var(--color-text);
    color: #ffffff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 12px;
    cursor: pointer;
}

.checkout:hover {
    opacity: 0.92;
}

.checkout:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.checkout-error {
    margin: 8px 0 0;
    color: #c0392b;
    font-size: 13px;
    text-align: center;
}

.clear:hover {
    background: var(--color-primary-strong);
}

.clear:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.cart-item-enter-active,
.cart-item-leave-active {
    transition: all 0.35s ease;
}

.cart-item-enter-from,
.cart-item-leave-to {
    opacity: 0;
    transform: translateX(18px);
}

.cart-item-move {
    transition: transform 0.35s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 640px) {
    .head h2 {
        font-size: 28px;
    }

    .total-row h3 {
        font-size: 34px;
    }
}
</style>
