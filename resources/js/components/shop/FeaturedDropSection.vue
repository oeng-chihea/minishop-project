<template>
    <section ref="rootRef" class="drop-section" :class="{ 'is-visible': isVisible }">
        <div class="drop-shell">
            <div class="drop-intro">
                <span class="drop-kicker">Featured Drop</span>
                <h2 class="drop-title">
                    <span class="title-line"><span>Fresh pairs with</span></span>
                    <span class="title-line"><span>stage presence.</span></span>
                </h2>
                <p class="drop-copy">
                    A sharper edit of the catalog for shoppers who want a fast
                    answer, not an endless grid.
                </p>
            </div>

            <div class="drop-grid">
                <article
                    v-for="(product, index) in featuredProducts"
                    :key="product.id"
                    class="drop-card"
                    :style="{ '--card-index': index }"
                >
                    <div class="drop-media">
                        <img :src="product.image" :alt="product.name" />
                        <span class="drop-badge">0{{ index + 1 }}</span>
                    </div>

                    <div class="drop-body">
                        <div class="drop-meta">
                            <span>{{ categoryLabel(product.category) }}</span>
                            <strong>${{ product.price.toFixed(2) }}</strong>
                        </div>
                        <h3>{{ product.name }}</h3>
                        <p>{{ product.tagline }}</p>
                        <div class="drop-actions">
                            <button type="button" class="ghost-btn" @click="$emit('explore-category', product.category)">
                                Explore
                            </button>
                            <button type="button" class="solid-btn" @click="$emit('add', product)">
                                Quick Add
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { useRepeatInView } from '../../composables/useRepeatInView';

const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
});

defineEmits(['add', 'explore-category']);

const { rootRef, isVisible } = useRepeatInView({ threshold: 0.24 });

const featuredProducts = computed(() => {
    const preferredIds = [7, 1, 4];

    return preferredIds
        .map((id) => props.products.find((product) => product.id === id))
        .filter(Boolean);
});

function categoryLabel(category) {
    if (category === 'travel') return 'Travel Edit';
    if (category === 'activity') return 'Motion Edit';
    return 'Daily Edit';
}
</script>

<style scoped>
.drop-section {
    position: relative;
    padding: 92px 0 36px;
    background:
        radial-gradient(circle at 18% 22%, rgba(85, 127, 176, 0.18), transparent 36%),
        radial-gradient(circle at 84% 30%, rgba(245, 158, 11, 0.16), transparent 28%),
        linear-gradient(180deg, #080d16 0%, #0a101b 100%);
    overflow: hidden;
}

.drop-section::before {
    content: '';
    position: absolute;
    inset: 6% 18% auto;
    height: 280px;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0) 68%);
    filter: blur(18px);
    opacity: 0;
    transform: scale(0.7) rotate(-8deg);
    transition: opacity 0.9s ease, transform 1.05s cubic-bezier(0.22, 1, 0.36, 1);
}

.drop-section.is-visible::before {
    opacity: 1;
    transform: scale(1) rotate(0deg);
}

.drop-shell {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

.drop-intro {
    max-width: 620px;
    margin-bottom: 40px;
}

.drop-kicker,
.drop-copy {
    opacity: 0;
    transition: opacity 0.65s ease;
}

.drop-kicker {
    display: inline-block;
    margin-bottom: 18px;
    font-size: 11px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.34);
    font-weight: 700;
}

.drop-title {
    margin: 0;
    font-size: clamp(36px, 5vw, 64px);
    letter-spacing: -0.04em;
    line-height: 0.95;
}

.title-line {
    display: block;
    overflow: hidden;
}

.title-line > span {
    display: block;
    transform: translateY(115%);
    transition: transform 0.95s cubic-bezier(0.16, 1, 0.3, 1);
}

.title-line:nth-child(2) > span {
    transition-delay: 0.12s;
}

.drop-copy {
    max-width: 500px;
    margin: 20px 0 0;
    color: rgba(255, 255, 255, 0.48);
    font-size: 15px;
    line-height: 1.75;
    transition-delay: 0.28s;
}

.drop-section.is-visible .drop-kicker,
.drop-section.is-visible .drop-copy {
    opacity: 1;
}

.drop-section.is-visible .title-line > span {
    transform: translateY(0);
}

.drop-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.drop-card {
    position: relative;
    background: rgba(14, 20, 32, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.07);
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.24);
    opacity: 0;
    filter: blur(10px);
    transform-origin: center bottom;
    transform:
        translateY(86px)
        rotateX(20deg)
        rotateZ(calc((var(--card-index) - 1) * 3deg))
        scale(0.88);
    transition:
        opacity 0.75s ease,
        filter 0.9s ease,
        transform 0.95s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.3s ease,
        box-shadow 0.3s ease;
    transition-delay: calc(var(--card-index) * 0.1s + 0.16s);
}

.drop-section.is-visible .drop-card {
    opacity: 1;
    filter: blur(0);
    transform: translateY(0) rotateX(0deg) rotateZ(0deg) scale(1);
}

.drop-card:hover {
    border-color: rgba(255, 255, 255, 0.16);
    box-shadow: 0 32px 70px rgba(0, 0, 0, 0.34);
}

.drop-media {
    position: relative;
    aspect-ratio: 4 / 4.6;
    overflow: hidden;
}

.drop-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.07);
    transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

.drop-card:hover .drop-media img {
    transform: scale(1.01);
}

.drop-media::after {
    content: '';
    position: absolute;
    inset: auto 0 0;
    height: 55%;
    background: linear-gradient(180deg, rgba(8, 13, 22, 0) 0%, rgba(8, 13, 22, 0.8) 100%);
}

.drop-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 1;
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.14em;
}

.drop-body {
    padding: 22px 22px 24px;
}

.drop-meta {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
}

.drop-meta span {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.32);
    letter-spacing: 0.14em;
    text-transform: uppercase;
    font-weight: 700;
}

.drop-meta strong {
    color: #fff;
    font-size: 14px;
}

.drop-body h3 {
    margin: 0;
    font-size: 22px;
    letter-spacing: -0.03em;
    color: #fff;
}

.drop-body p {
    margin: 10px 0 0;
    color: rgba(255, 255, 255, 0.48);
    line-height: 1.65;
    font-size: 14px;
}

.drop-actions {
    display: flex;
    gap: 10px;
    margin-top: 22px;
}

.drop-actions button {
    border: 0;
    cursor: pointer;
    font-family: inherit;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 12px 16px;
}

.ghost-btn {
    background: transparent;
    color: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
}

.solid-btn {
    background: #ffffff;
    color: #080d16;
}

@media (max-width: 920px) {
    .drop-grid {
        grid-template-columns: 1fr;
    }

    .drop-section {
        padding-top: 72px;
    }
}

@media (max-width: 480px) {
    .drop-shell {
        padding: 0 16px;
    }

    .drop-body {
        padding: 18px 18px 20px;
    }

    .drop-body h3 {
        font-size: 20px;
    }
}

@media (max-width: 640px) {
    .drop-media {
        aspect-ratio: 4 / 3.5;
    }

    .drop-actions {
        flex-direction: column;
    }

    .drop-actions button {
        width: 100%;
    }
}
</style>
