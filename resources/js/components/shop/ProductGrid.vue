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
.card {
    background: var(--color-surface);
    border: 1px solid var(--color-line);
    border-radius: 0;
    padding: 30px 30px 18px;
    box-shadow: var(--shadow-card);
}

.favorites-head {
    margin-bottom: 22px;
}

.favorites-head h2 {
    margin: 0;
    font-size: 40px;
    text-align: center;
    letter-spacing: -0.02em;
    color: var(--color-text);
}

.tabs {
    margin-top: 18px;
    border-bottom: 1px solid var(--color-line);
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.tabs button {
    background: transparent;
    border: 0;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    padding: 12px 8px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-muted);
    cursor: pointer;
    transition: color 0.2s ease, border-bottom-color 0.2s ease;
}

.tabs button:hover {
    color: var(--color-text);
}

.tabs button.active {
    color: var(--color-text);
    border-bottom-color: var(--color-text);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-top: 10px;
}

.card-header h2 {
    margin: 0;
    font-size: 24px;
    letter-spacing: -0.02em;
    color: var(--color-text);
}

.card-header span {
    color: var(--color-muted);
    font-size: 14px;
}

/* Tab switch animation */
.fade-tab-enter-active,
.fade-tab-leave-active {
    transition: opacity 0.28s ease, transform 0.28s ease;
}

.fade-tab-enter-from {
    opacity: 0;
    transform: translateY(14px);
}

.fade-tab-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.product-card {
    border: 1px solid var(--color-line);
    background: var(--color-surface-soft);
    overflow: hidden;
}

.thumb-wrap {
    aspect-ratio: 4 / 3;
    background: #e8edf3;
    overflow: hidden;
}

.thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-body {
    padding: 14px;
}

h3 {
    margin: 0;
    color: var(--color-text);
}

p {
    margin: 5px 0 0;
    color: var(--color-muted);
    font-size: 14px;
}

.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 14px;
}

strong {
    font-size: 18px;
    color: var(--color-text);
}

button {
    border: 0;
    border-radius: 2px;
    background: var(--color-primary);
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 11px 16px;
    cursor: pointer;
}

button:hover {
    background: var(--color-primary-strong);
}

@media (max-width: 980px) {
    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .favorites-head h2 {
        font-size: 30px;
    }

    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>
