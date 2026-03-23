<template>
    <section class="card fade-in-up" id="shop" ref="sectionRef" :class="{ 'grid-in': gridVisible }">
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
                    v-for="(product, index) in filteredProducts"
                    :key="product.id"
                    class="product-card scale-on-hover"
                    :style="{ '--delay': (index * 0.13) + 's' }"
                >
                    <div class="thumb-wrap">
                        <img :src="product.image" :alt="product.name" class="thumb" />
                    </div>

                    <div class="product-body">
                        <h3>{{ product.name }}</h3>
                        <p>{{ product.tagline }}</p>

                        <div class="actions">
                            <strong>${{ product.price.toFixed(2) }}</strong>
                            <button
                                type="button"
                                :class="{ 'btn-added': addedId === product.id }"
                                @click="flyToCart($event, product)"
                            >
                                <span v-if="addedId === product.id">✓ Added</span>
                                <span v-else>Add to cart</span>
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </Transition>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
    products: { type: Array, required: true },
    selectedCategory: {
        type: String,
        default: 'everyday'
    }
});

const emit = defineEmits(['add', 'category-change']);

const allowedTabs = ['everyday', 'travel', 'activity'];
const activeTab  = ref(allowedTabs.includes(props.selectedCategory) ? props.selectedCategory : 'everyday');
const sectionRef = ref(null);
const gridVisible = ref(false);
const addedId    = ref(null);   // tracks which product just got added
let gridObserver = null;
let addedTimer   = null;

const setTab = (tab) => {
    activeTab.value = tab;
    emit('category-change', tab);
};

const filteredProducts = computed(() =>
    props.products.filter(p => p.category === activeTab.value)
);

watch(() => props.selectedCategory, (category) => {
    if (allowedTabs.includes(category) && category !== activeTab.value) {
        activeTab.value = category;
    }
});

// ── Flying image animation ──────────────────────────────────────────
function flyToCart(event, product) {
    // Add to cart immediately
    emit('add', product);

    // Flash the button state
    addedId.value = product.id;
    clearTimeout(addedTimer);
    addedTimer = setTimeout(() => { addedId.value = null; }, 1100);

    // Locate the thumbnail and the cart toggle button
    const article = event.currentTarget.closest('article');
    const thumb   = article?.querySelector('.thumb');
    const cartBtn = document.querySelector('.cart-toggle');
    if (!thumb || !cartBtn) return;

    const tRect = thumb.getBoundingClientRect();
    const cRect = cartBtn.getBoundingClientRect();

    // Flying clone — fixed size, centered on thumbnail
    const SIZE = 80;
    const startX = tRect.left + tRect.width  / 2;
    const startY = tRect.top  + tRect.height / 2;
    const endX   = cRect.left + cRect.width  / 2;
    const endY   = cRect.top  + cRect.height / 2;

    const clone = document.createElement('img');
    clone.src = product.image;
    Object.assign(clone.style, {
        position:     'fixed',
        left:         `${startX - SIZE / 2}px`,
        top:          `${startY - SIZE / 2}px`,
        width:        `${SIZE}px`,
        height:       `${SIZE}px`,
        objectFit:    'cover',
        borderRadius: '6px',
        zIndex:       '99999',
        pointerEvents:'none',
        boxShadow:    '0 8px 28px rgba(0,0,0,0.55)',
        willChange:   'transform, opacity, left, top',
        transition:   'border-radius 0.5s ease, box-shadow 0.4s ease',
    });
    document.body.appendChild(clone);

    // Arc control point — curve upward between start and end
    const cpX = (startX + endX) / 2;
    const cpY = Math.min(startY, endY) - Math.abs(endX - startX) * 0.4 - 60;

    const DURATION = 720;
    const startTime = performance.now();
    let rafId;

    // Ease-in-out cubic
    function ease(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function step(now) {
        const t  = Math.min((now - startTime) / DURATION, 1);
        const et = ease(t);

        // Quadratic Bézier
        const bx = (1 - et) * (1 - et) * startX + 2 * (1 - et) * et * cpX + et * et * endX;
        const by = (1 - et) * (1 - et) * startY + 2 * (1 - et) * et * cpY + et * et * endY;

        // Shrink from SIZE → 36px and fade out in last 30%
        const scale   = 1 - et * 0.55;
        const opacity = t > 0.7 ? Math.max(0, 1 - (t - 0.7) / 0.3) : 1;

        Object.assign(clone.style, {
            left:    `${bx - SIZE / 2}px`,
            top:     `${by - SIZE / 2}px`,
            transform:`scale(${scale})`,
            opacity: opacity,
        });

        if (t < 1) {
            rafId = requestAnimationFrame(step);
        } else {
            if (document.body.contains(clone)) document.body.removeChild(clone);
        }
    }

    // Morph to circle as it flies
    requestAnimationFrame(() => {
        clone.style.borderRadius = '50%';
        clone.style.boxShadow    = '0 4px 14px rgba(0,0,0,0.3)';
    });

    rafId = requestAnimationFrame(step);

    // Safety cleanup
    setTimeout(() => {
        cancelAnimationFrame(rafId);
        if (document.body.contains(clone)) document.body.removeChild(clone);
    }, DURATION + 100);
}
// ───────────────────────────────────────────────────────────────────

onMounted(() => {
    gridObserver = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                gridVisible.value = true;
            } else {
                gridVisible.value = false;
            }
        },
        { threshold: 0.08 }
    );
    if (sectionRef.value) gridObserver.observe(sectionRef.value);
});

onBeforeUnmount(() => {
    gridObserver?.disconnect();
    clearTimeout(addedTimer);
});
</script>

<style scoped>
/* ─── Section wrapper ─────────────────────────── */
.card {
    background: #080d16;
    border: none;
    padding: 72px 30px 80px;
    scroll-margin-top: 96px;
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
/* Unique ProductGrid animation: blur + scale fade-in, staggered per card */
@keyframes blurFadeIn {
    from {
        opacity: 0;
        filter: blur(8px);
        transform: scale(0.88) translateY(18px);
    }
    to {
        opacity: 1;
        filter: blur(0px);
        transform: scale(1) translateY(0);
    }
}

.product-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
    opacity: 0;   /* hidden by default; animation lives only in .grid-in */
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.35s ease,
                border-color 0.35s ease;
}

/* Animation fires fresh each time .grid-in is (re-)applied */
.grid-in .product-card {
    animation: blurFadeIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) var(--delay, 0s) forwards;
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
    min-width: 110px;
    transition: background 0.2s ease, color 0.2s ease, transform 0.15s ease;
}

button:hover {
    background: rgba(255,255,255,0.85);
}

button:active {
    transform: scale(0.95);
}

/* "Added" state — green flash */
button.btn-added {
    background: #22c55e;
    color: #ffffff;
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

    .card-header {
        flex-wrap: wrap;
        gap: 8px;
    }

    .actions {
        align-items: stretch;
    }

    .actions button {
        min-width: 0;
    }
}

@media (max-width: 480px) {
    .tabs {
        max-width: 100%;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .tabs::-webkit-scrollbar {
        display: none;
    }

    .tabs button {
        min-width: 110px;
        font-size: 10px;
        letter-spacing: 0.07em;
        padding: 11px 4px;
    }

    .actions {
        flex-wrap: wrap;
    }

    .actions strong,
    .actions button {
        width: 100%;
    }
}

@media (max-width: 420px) {
    .card { padding: 44px 14px 52px; }

    .favorites-head h2 { font-size: 26px; }

    .card-header { padding-top: 6px; margin-bottom: 14px; }

    .card-header h2 { font-size: 15px; }
}
</style>
