<template>
    <section class="card fade-in-up" id="shop">
        <div class="favorites-head">
            <h2>Our Favorites</h2>
            <nav class="tabs" aria-label="Product categories">
                <button
                    type="button"
                    :class="{ active: activeTab === 'everyday' }"
                    @click="setTab('everyday')"
                >For Everyday</button>
                <button
                    type="button"
                    :class="{ active: activeTab === 'travel' }"
                    @click="setTab('travel')"
                >For Travel</button>
                <button
                    type="button"
                    :class="{ active: activeTab === 'activity' }"
                    @click="setTab('activity')"
                >For Activity</button>
            </nav>
        </div>

        <div class="card-header">
            <h2>Shop Section</h2>
            <span>{{ filteredProducts.length }} products</span>
        </div>

        <Transition name="fade-tab" mode="out-in">
            <div class="product-grid" :key="activeTab">
                <article
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="product-card scale-on-hover"
                >
                    <div class="thumb-wrap">
                        <img :src="product.image" :alt="product.name" class="thumb" />
                    </div>

                    <div class="product-body">
                        <h3>{{ product.name }}</h3>
                        <p>{{ product.tagline }}</p>

                        <div class="actions">
                            <strong>${{ product.price.toFixed(2) }}</strong>
                            <button type="button" @click="$emit('add', product)">Add to cart</button>
                        </div>
                    </div>
                </article>
            </div>
        </Transition>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
});

defineEmits(['add']);

const activeTab = ref('everyday');

const setTab = (tab) => {
    activeTab.value = tab;
};

const filteredProducts = computed(() =>
    props.products.filter(p => p.category === activeTab.value)
);
</script>

<style scoped>
/* ─── Section wrapper ─────────────────────────── */
.card {
    background: #080d16;
    border: none;
    padding: 72px 30px 80px;
}

/* ─── Header ──────────────────────────────────── */
.favorites-head {
    margin-bottom: 36px;
    text-align: center;
}

.favorites-head h2 {
    margin: 0;
    font-size: clamp(32px, 4vw, 48px);
    letter-spacing: -0.03em;
    color: #ffffff;
    font-weight: 800;
}

/* ─── Tabs ────────────────────────────────────── */
.tabs {
    margin-top: 22px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
}

.tabs button {
    background: transparent;
    border: 0;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    padding: 13px 8px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255,255,255,0.35);
    cursor: pointer;
    transition: color 0.2s ease, border-bottom-color 0.2s ease;
    font-family: inherit;
}

.tabs button:hover {
    color: rgba(255,255,255,0.7);
}

.tabs button.active {
    color: #ffffff;
    border-bottom-color: #ffffff;
}

/* ─── Sub-header ──────────────────────────────── */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-top: 10px;
    max-width: 1180px;
    margin-left: auto;
    margin-right: auto;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    letter-spacing: -0.01em;
    color: rgba(255,255,255,0.5);
    font-weight: 500;
}

.card-header span {
    color: rgba(255,255,255,0.25);
    font-size: 13px;
}

/* ─── Tab transition ──────────────────────────── */
.fade-tab-enter-active,
.fade-tab-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-tab-enter-from {
    opacity: 0;
    transform: translateY(16px);
}

.fade-tab-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* ─── Product grid ────────────────────────────── */
.product-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    max-width: 1180px;
    margin: 0 auto;
}

/* ─── Product card ────────────────────────────── */
.product-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.35s ease,
                border-color 0.35s ease;
}

.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.12);
}

/* ─── Thumbnail ───────────────────────────────── */
.thumb-wrap {
    aspect-ratio: 4 / 3;
    background: #1a2234;
    overflow: hidden;
}

.thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.product-card:hover .thumb {
    transform: scale(1.04);
}

/* ─── Body ────────────────────────────────────── */
.product-body {
    padding: 18px 18px 20px;
}

h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.01em;
}

p {
    margin: 6px 0 0;
    color: rgba(255,255,255,0.4);
    font-size: 13px;
    line-height: 1.5;
}

.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 16px;
}

strong {
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
}

button {
    border: 0;
    border-radius: 2px;
    background: #ffffff;
    color: #0a0f1a;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 12px;
    padding: 10px 16px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s ease, color 0.2s ease;
}

button:hover {
    background: rgba(255,255,255,0.85);
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 980px) {
    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .card {
        padding: 52px 20px 60px;
    }

    .favorites-head h2 {
        font-size: 30px;
    }

    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>
