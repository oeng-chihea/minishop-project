<template>
    <section ref="rootRef" class="wall-section" :class="{ 'is-visible': isVisible }">
        <div class="wall-shell">
            <div class="wall-head">
                <span class="wall-kicker">Community Wall</span>
                <h2>Styled in the real world, not only in product shots.</h2>
                <p>
                    A moving shelf of the full collection. Click any pair to add it to cart.
                </p>
            </div>
        </div>

        <div class="marquee-stack">
            <div class="marquee-wrap marquee-wrap-top">
                <div class="marquee-track marquee-track-top">
                    <div class="marquee-set" v-for="copyIndex in 2" :key="`top-${copyIndex}`" :aria-hidden="copyIndex === 2 ? 'true' : undefined">
                        <button
                            v-for="product in topRow"
                            :key="`${copyIndex}-${product.id}`"
                            type="button"
                            class="shoe-card"
                            @click="$emit('add', product)"
                        >
                            <div class="shoe-frame">
                                <img :src="product.image" :alt="product.name" />
                            </div>
                            <div class="shoe-meta">
                                <strong>{{ product.name }}</strong>
                                <span>${{ product.price.toFixed(2) }}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="marquee-wrap marquee-wrap-bottom">
                <div class="marquee-track marquee-track-bottom">
                    <div class="marquee-set" v-for="copyIndex in 2" :key="`bottom-${copyIndex}`" :aria-hidden="copyIndex === 2 ? 'true' : undefined">
                        <button
                            v-for="product in bottomRow"
                            :key="`${copyIndex}-${product.id}`"
                            type="button"
                            class="shoe-card shoe-card-alt"
                            @click="$emit('add', product)"
                        >
                            <div class="shoe-frame">
                                <img :src="product.image" :alt="product.name" />
                            </div>
                            <div class="shoe-meta">
                                <strong>{{ product.name }}</strong>
                                <span>${{ product.price.toFixed(2) }}</span>
                            </div>
                        </button>
                    </div>
                </div>
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

defineEmits(['add']);

const { rootRef, isVisible } = useRepeatInView({ threshold: 0.16 });

const topRow = computed(() => props.products.slice(0, Math.ceil(props.products.length / 2)));
const bottomRow = computed(() => props.products.slice(Math.ceil(props.products.length / 2)));
</script>

<style scoped>
.wall-section {
    padding: 88px 0;
    background:
        radial-gradient(circle at 20% 18%, rgba(59, 130, 246, 0.14), transparent 28%),
        radial-gradient(circle at 84% 42%, rgba(245, 158, 11, 0.1), transparent 24%),
        linear-gradient(180deg, #09101b 0%, #080d16 100%);
    overflow: hidden;
}

.wall-shell {
    max-width: 1240px;
    margin: 0 auto 34px;
    padding: 0 24px;
}

.wall-head {
    max-width: 720px;
}

.wall-kicker,
.wall-head h2,
.wall-head p {
    opacity: 0;
    transition:
        opacity 0.65s ease,
        transform 0.8s cubic-bezier(0.22, 1, 0.36, 1),
        filter 0.75s ease;
}

.wall-kicker {
    display: inline-block;
    margin-bottom: 14px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
    transform: translateY(10px);
}

.wall-head h2 {
    margin: 0;
    font-size: clamp(34px, 5vw, 62px);
    line-height: 0.98;
    letter-spacing: -0.05em;
    color: #fff;
    transform: translateY(18px);
    filter: blur(8px);
}

.wall-head p {
    margin: 16px 0 0;
    max-width: 520px;
    color: rgba(255, 255, 255, 0.44);
    line-height: 1.7;
    font-size: 15px;
    transform: translateY(18px);
    transition-delay: 0.1s;
}

.wall-section.is-visible .wall-kicker,
.wall-section.is-visible .wall-head h2,
.wall-section.is-visible .wall-head p {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
}

.marquee-stack {
    display: grid;
    gap: 18px;
}

.marquee-wrap {
    position: relative;
    overflow: hidden;
    opacity: 0;
    clip-path: inset(0 0 100% 0 round 0);
    transition:
        opacity 0.6s ease,
        clip-path 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.marquee-wrap::before,
.marquee-wrap::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 110px;
    z-index: 2;
    pointer-events: none;
}

.marquee-wrap::before {
    left: 0;
    background: linear-gradient(90deg, #09101b 0%, rgba(9, 16, 27, 0) 100%);
}

.marquee-wrap::after {
    right: 0;
    background: linear-gradient(270deg, #09101b 0%, rgba(9, 16, 27, 0) 100%);
}

.wall-section.is-visible .marquee-wrap {
    opacity: 1;
    clip-path: inset(0 0 0 0 round 0);
}

.wall-section.is-visible .marquee-wrap-bottom {
    transition-delay: 0.12s;
}

.marquee-track {
    display: flex;
    width: max-content;
    gap: 18px;
}

.marquee-track-top {
    animation: marqueeLeft 30s linear infinite;
}

.marquee-track-bottom {
    animation: marqueeRight 34s linear infinite;
}

.marquee-set {
    display: flex;
    gap: 18px;
    padding-right: 18px;
}

.shoe-card {
    width: 280px;
    flex: 0 0 auto;
    background: #eef1f5;
    border: 0;
    padding: 0;
    text-align: left;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 28px 60px rgba(0, 0, 0, 0.18);
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease;
}

.shoe-card:hover {
    transform: translateY(-8px) rotate(-1deg);
    box-shadow: 0 32px 72px rgba(0, 0, 0, 0.24);
}

.shoe-card-alt:hover {
    transform: translateY(-8px) rotate(1deg);
}

.shoe-frame {
    aspect-ratio: 1 / 1;
    background:
        radial-gradient(circle at 50% 40%, rgba(255, 255, 255, 0.95), rgba(230, 233, 238, 0.92)),
        #f4f5f7;
    overflow: hidden;
}

.shoe-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}

.shoe-card:hover .shoe-frame img {
    transform: scale(1.05);
}

.shoe-meta {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: center;
    padding: 16px 18px;
    background: #111827;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.shoe-meta strong {
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.35;
    letter-spacing: -0.01em;
}

.shoe-meta span {
    color: rgba(255, 255, 255, 0.45);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
}

@keyframes marqueeLeft {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(calc(-50% - 9px));
    }
}

@keyframes marqueeRight {
    from {
        transform: translateX(calc(-50% - 9px));
    }
    to {
        transform: translateX(0);
    }
}

@media (max-width: 860px) {
    .wall-shell {
        margin-bottom: 26px;
    }

    .shoe-card {
        width: 220px;
    }

    .marquee-wrap::before,
    .marquee-wrap::after {
        width: 64px;
    }
}

@media (max-width: 640px) {
    .marquee-track-top {
        animation-duration: 34s;
    }

    .marquee-track-bottom {
        animation-duration: 38s;
    }

    .shoe-card {
        width: 190px;
    }

    .marquee-wrap::before,
    .marquee-wrap::after {
        width: 44px;
    }
}

@media (max-width: 480px) {
    .wall-section {
        padding: 64px 0;
    }

    .wall-shell {
        padding: 0 16px;
    }

    .wall-head p {
        font-size: 14px;
    }

    .shoe-card {
        width: 170px;
    }

    .shoe-meta {
        padding: 12px 12px 14px;
    }

    .shoe-meta strong {
        font-size: 11px;
    }

    .shoe-meta span {
        font-size: 10px;
    }

    .marquee-wrap::before,
    .marquee-wrap::after {
        width: 36px;
    }
}
</style>
