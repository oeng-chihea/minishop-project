<template>
    <section ref="rootRef" class="best-section" :class="{ 'is-visible': isVisible }">
        <div class="best-shell">
            <div class="best-head">
                <div>
                    <span class="best-kicker">Best Sellers</span>
                    <h2>The pairs that carry the homepage with proof, not noise.</h2>
                </div>
            </div>

            <div class="best-stage">
                <article
                    v-for="(product, index) in featuredProducts"
                    :key="product.id"
                    class="best-card"
                    :class="{ 'is-active': visibleCards[index] }"
                    :style="{ '--card-index': index }"
                    :ref="(el) => setCardRef(el, index)"
                >
                    <div class="best-card-top">
                        <div class="best-rank-wrap">
                            <span class="best-rank">#{{ index + 1 }}</span>
                            <span class="best-rank-label">{{ taglines[index] }}</span>
                        </div>
                    </div>

                    <div class="best-image-wrap">
                        <div class="best-image-frame">
                            <img :src="product.image" :alt="product.name" class="best-image" />
                            <span class="best-scan-line"></span>
                        </div>
                        <div class="best-detail-connector">
                            <div class="best-detail-group best-detail-group-left">
                                <svg viewBox="0 0 1480 360" class="best-curve" preserveAspectRatio="none" aria-hidden="true">
                                    <circle
                                        class="best-curve-start-dot"
                                        :cx="leftStartDots[index].x"
                                        :cy="leftStartDots[index].y"
                                        r="7"
                                    />
                                    <path
                                        class="best-curve-path"
                                        :d="leftDetailPaths[index]"
                                    />
                                </svg>
                                <div class="best-detail-card best-detail-card-left">
                                    <span class="best-detail-tag">{{ leftDetailTags[index] }}</span>
                                    <strong>{{ leftCalloutDetails[index] }}</strong>
                                </div>
                            </div>

                            <div class="best-detail-group best-detail-group-right">
                                <svg viewBox="0 0 1480 360" class="best-curve" preserveAspectRatio="none" aria-hidden="true">
                                    <circle
                                        class="best-curve-start-dot"
                                        :cx="rightStartDots[index].x"
                                        :cy="rightStartDots[index].y"
                                        r="7"
                                    />
                                    <path
                                        class="best-curve-path"
                                        :d="rightDetailPaths[index]"
                                    />
                                </svg>
                                <div class="best-detail-card best-detail-card-right">
                                    <span class="best-detail-tag">{{ rightDetailTags[index] }}</span>
                                    <strong>{{ rightCalloutDetails[index] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRepeatInView } from '../../composables/useRepeatInView';

const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
});

defineEmits(['add', 'explore-category']);

const { rootRef, isVisible } = useRepeatInView({ threshold: 0.22 });
const cardRefs = ref([]);
const visibleCards = ref({});
let cardObserver = null;

const taglines = ['Fastest Pick', 'Daily Favorite', 'Travel Core'];
const leftDetailTags = ['Performance Base', 'Classic Shape', 'Travel Cushion'];
const leftCalloutDetails = ['Light rebound midsole', 'Low profile leather body', 'Heel padding for long routes'];
const rightDetailTags = ['Engineered Motion', 'Clean Essential', 'Long-Walk Comfort'];
const rightCalloutDetails = ['Breathable mesh upper', 'Classic court profile', 'Soft long-walk support'];
const leftStartDots = [
    { x: 552, y: 226 },
    { x: 664, y: 238 },
    { x: 586, y: 232 },
];
const rightStartDots = [
    { x: 942, y: 226 },
    { x: 910, y: 232 },
    { x: 966, y: 224 },
];
const leftDetailPaths = [
    'M 552 226 L 240 226 L 240 66 L 40 66',
    'M 664 238 L 240 238 L 240 66 L 40 66',
    'M 586 232 L 240 232 L 240 66 L 40 66',
];
const rightDetailPaths = [
    'M 942 226 L 1220 226 L 1220 66 L 1468 66',
    'M 910 232 L 1220 232 L 1220 66 L 1468 66',
    'M 966 224 L 1220 224 L 1220 66 L 1468 66',
];

const featuredProducts = computed(() => {
    const preferredIds = [7, 1, 4];

    return preferredIds
        .map((id) => props.products.find((product) => product.id === id))
        .filter(Boolean);
});

function setCardRef(el, index) {
    if (el) {
        cardRefs.value[index] = el;
    }
}

function observeCards() {
    cardObserver?.disconnect();

    cardObserver = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                const index = Number(entry.target.dataset.cardIndex);
                visibleCards.value[index] = entry.isIntersecting;
            }
        },
        { threshold: 0.35 }
    );

    cardRefs.value.forEach((el, index) => {
        if (!el) return;
        el.dataset.cardIndex = String(index);
        cardObserver.observe(el);
    });
}

onMounted(async () => {
    await nextTick();
    observeCards();
});

watch(featuredProducts, async () => {
    await nextTick();
    observeCards();
});

onBeforeUnmount(() => cardObserver?.disconnect());
</script>

<style scoped>
.best-section {
    --annotation: rgba(85, 127, 176, 0.9);
    --annotation-soft: rgba(85, 127, 176, 0.14);
    --annotation-border: rgba(85, 127, 176, 0.24);
    --detail-offset: 228px;
    --detail-end-left: 2.7%;
    --detail-end-right: 99.2%;
    --detail-end-y: 18.33%;
    --detail-card-gap: 12px;
    --detail-reveal-shift: 20px;
    padding: 88px 0;
    background:
        radial-gradient(circle at center top, rgba(255, 255, 255, 0.06), transparent 28%),
        #080d16;
    overflow: hidden;
}

.best-shell {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

.best-head {
    margin-bottom: 28px;
}

.best-kicker,
.best-head h2 {
    opacity: 0;
    transition: opacity 0.6s ease, filter 0.8s ease, transform 0.8s ease;
}

.best-kicker {
    display: block;
    margin-bottom: 14px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
    filter: blur(6px);
}

.best-head h2 {
    margin: 0;
    max-width: 640px;
    font-size: clamp(30px, 4vw, 48px);
    line-height: 1.06;
    letter-spacing: -0.03em;
    color: #fff;
    transform: translateY(16px);
}

.best-section.is-visible .best-kicker,
.best-section.is-visible .best-head h2 {
    opacity: 1;
    filter: blur(0);
    transform: translateY(0);
}

.best-stage {
    display: grid;
    grid-template-columns: 1fr;
    gap: 22px;
    max-width: 1440px;
    margin: 0 auto;
    padding-inline: var(--detail-offset);
}

.best-card {
    position: relative;
    opacity: 0;
    transform: translateY(42px);
    transition: opacity 0.55s ease, transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: 0.08s;
}

.best-card.is-active {
    opacity: 1;
    transform: translateY(0);
}

.best-card-top {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 14px;
}

.best-rank {
    display: inline-block;
    color: rgba(255, 255, 255, 0.46);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.best-rank-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.best-rank-label {
    color: rgba(255, 255, 255, 0.28);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.best-image-wrap {
    position: relative;
    width: 100%;
    overflow: visible;
    transform-origin: center;
    transform: perspective(1400px) rotateX(18deg) scale(0.96);
    transition: transform 0.95s cubic-bezier(0.16, 1, 0.3, 1);
    transition-delay: 0.08s;
}

.best-card.is-active .best-image-wrap {
    transform: perspective(1400px) rotateX(0deg) scale(1);
}

.best-image-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 8;
    overflow: hidden;
    background: linear-gradient(180deg, #f5f6f8 0%, #eceff4 100%);
}

.best-image {
    width: 100%;
    height: 100%;
    aspect-ratio: auto;
    object-fit: cover;
    object-position: center center;
    display: block;
    transform: scale(1.08);
}

.best-scan-line {
    position: absolute;
    left: -12%;
    top: 0;
    bottom: 0;
    width: 18%;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.58), rgba(255, 255, 255, 0));
    transform: skewX(-14deg) translateX(-140%);
    opacity: 0;
    transition:
        transform 1.1s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.35s ease;
    transition-delay: 0.18s;
}

.best-card.is-active .best-scan-line {
    opacity: 1;
    transform: skewX(-14deg) translateX(780%);
}

.best-tag {
    display: inline-block;
    color: rgba(255, 255, 255, 0.3);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.best-detail-connector {
    position: absolute;
    top: 0;
    right: calc(-1 * var(--detail-offset));
    bottom: 0;
    left: calc(-1 * var(--detail-offset));
    height: 100%;
    pointer-events: none;
    z-index: 3;
}

.best-detail-group {
    position: absolute;
    inset: 0;
}

.best-curve {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: visible;
    filter: drop-shadow(0 0 10px rgba(85, 127, 176, 0.18));
}

.best-curve-path {
    fill: none;
    stroke: var(--annotation);
    stroke-width: 3.2;
    stroke-linecap: round;
    stroke-linejoin: miter;
    stroke-dasharray: 1400;
    stroke-dashoffset: 1400;
    vector-effect: non-scaling-stroke;
    transition: stroke-dashoffset 1.25s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: 0.26s;
}

.best-curve-start-dot {
    fill: #ffffff;
    stroke: rgba(85, 127, 176, 0.22);
    stroke-width: 8;
    opacity: 0;
    transform: scale(0.35);
    transform-box: fill-box;
    transform-origin: center;
    transition:
        opacity 0.3s ease,
        transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.1s;
}

.best-detail-card {
    --detail-card-width: 188px;
    position: absolute;
    top: var(--detail-end-y);
    width: var(--detail-card-width);
    min-width: var(--detail-card-width);
    max-width: var(--detail-card-width);
    padding: 14px 14px 13px;
    border: 1px solid var(--annotation-border);
    background: linear-gradient(180deg, rgba(11, 18, 29, 0.98) 0%, rgba(9, 15, 24, 0.98) 100%);
    box-shadow: 0 10px 22px rgba(4, 8, 14, 0.22);
    opacity: 0;
    transform: translateX(20px) scale(0.96);
    transition:
        opacity 0.45s ease,
        transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: 0.72s;
}

.best-detail-card-left {
    left: var(--detail-end-left);
    transform: translate(calc(-100% - var(--detail-card-gap) - var(--detail-reveal-shift)), -50%) scale(0.96);
}

.best-detail-card-right {
    left: var(--detail-end-right);
    right: auto;
    transform: translate(calc(var(--detail-card-gap) + var(--detail-reveal-shift)), -50%) scale(0.96);
}

.best-detail-tag {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, 0.58);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.best-detail-card strong {
    display: block;
    color: #ffffff;
    font-size: 13px;
    line-height: 1.45;
    letter-spacing: -0.01em;
}

.best-card.is-active .best-curve-path {
    stroke-dashoffset: 0;
}

.best-card.is-active .best-curve-start-dot {
    opacity: 1;
    transform: scale(1);
}

.best-card.is-active .best-detail-card-left {
    opacity: 1;
    transform: translate(calc(-100% - var(--detail-card-gap)), -50%) scale(1);
}

.best-card.is-active .best-detail-card-right {
    opacity: 1;
    transform: translate(var(--detail-card-gap), -50%) scale(1);
}

@media (max-width: 1100px) {
    .best-section {
        --detail-offset: 192px;
        --detail-card-gap: 10px;
        --detail-reveal-shift: 16px;
    }

    .best-detail-card {
        --detail-card-width: 168px;
    }
}

@media (max-width: 980px) {
    .best-section {
        --detail-offset: 0px;
        padding: 78px 0;
    }

    .best-stage {
        padding-inline: 0;
        gap: 18px;
    }

    .best-card-top {
        margin-bottom: 10px;
    }

    .best-rank-wrap {
        flex-wrap: wrap;
        row-gap: 6px;
    }

    .best-image-wrap {
        padding-top: 74px;
    }

    .best-image-frame {
        aspect-ratio: 16 / 9;
    }

    .best-image {
        transform: scale(1.06);
    }

    .best-detail-connector {
        right: 0;
        left: 0;
    }

    .best-detail-card {
        top: 0;
        width: auto;
        min-width: 144px;
        max-width: 160px;
        padding: 11px 11px 10px;
    }

    .best-detail-card-left {
        left: 0;
        transform: translate(0, 0) scale(0.96);
    }

    .best-detail-card-right {
        left: auto;
        right: 0;
        transform: translate(0, 0) scale(0.96);
    }

    .best-curve {
        opacity: 0.7;
    }

    .best-detail-card strong {
        font-size: 11px;
    }

    .best-detail-tag {
        font-size: 9px;
    }

    .best-card.is-active .best-detail-card-left,
    .best-card.is-active .best-detail-card-right {
        transform: translate(0, 0) scale(1);
    }
}

@media (max-width: 760px) {
    .best-section {
        padding: 72px 0;
    }

    .best-image-frame {
        aspect-ratio: 4 / 3;
    }

    .best-detail-card {
        min-width: 132px;
        max-width: 146px;
        padding: 10px 10px 9px;
    }

    .best-detail-card strong {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .best-shell {
        padding: 0 16px;
    }

    .best-card-top {
        margin-bottom: 8px;
    }

    .best-image-wrap {
        padding-top: 68px;
    }

    .best-detail-card {
        min-width: 118px;
        max-width: 132px;
        padding: 9px 9px 8px;
    }

    .best-detail-card strong {
        font-size: 10px;
    }

    .best-curve-path {
        stroke-width: 2.6;
    }
}
</style>
