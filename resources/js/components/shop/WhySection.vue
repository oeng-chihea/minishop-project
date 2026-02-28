<template>
    <section class="why-wrap" ref="sectionRef">
        <div class="why-inner">

            <!-- Left: static label + counter -->
            <div class="why-label" :class="{ 'label-in': visible }">
                <span class="why-eyebrow">Why choose us</span>
                <div class="why-counter">
                    <span class="counter-current">0{{ activeIndex + 1 }}</span>
                    <span class="counter-sep">/</span>
                    <span class="counter-total">0{{ cards.length }}</span>
                </div>
                <!-- Dot nav -->
                <div class="why-dots">
                    <button
                        v-for="(_, i) in cards"
                        :key="i"
                        class="why-dot"
                        :class="{ 'dot-active': activeIndex === i }"
                        @click="jumpTo(i)"
                        :aria-label="`Show card ${i + 1}`"
                    ></button>
                </div>
            </div>

            <!-- Right: cycling card -->
            <div class="card-stage">
                <transition name="card-cycle" mode="out-in">
                    <div
                        class="cycle-card"
                        :key="activeIndex"
                    >
                        <div class="cycle-bar" :style="{ '--accent': cards[activeIndex].accent }"></div>
                        <div class="cycle-body">
                            <h3 class="cycle-title">{{ cards[activeIndex].title }}</h3>
                            <p class="cycle-desc">{{ cards[activeIndex].desc }}</p>
                        </div>
                        <!-- Progress line that fills over the duration -->
                        <div class="cycle-progress">
                            <div
                                class="cycle-fill"
                                :style="{ '--accent': cards[activeIndex].accent, animationDuration: duration + 'ms' }"
                                :key="activeIndex + '-fill'"
                            ></div>
                        </div>
                    </div>
                </transition>
            </div>

        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const cards = [
    {
        title: 'Premium Quality',
        desc: 'Every item passes a strict quality check. We source only soft-touch fabrics, durable builds, and materials that hold up beautifully over time.',
        accent: '#3b82f6'
    },
    {
        title: 'Style for Everyone',
        desc: 'From street-ready essentials to travel companions and active gear, our curated range covers every lifestyle, occasion, and budget.',
        accent: '#10b981'
    },
    {
        title: 'Fast & Secure Checkout',
        desc: 'Pay safely with ABA PayWay — Cambodia\'s most trusted payment platform. Your data is encrypted and your order confirmed in seconds.',
        accent: '#a78bfa'
    }
];

const duration    = 3200;   // ms each card is visible
const sectionRef  = ref(null);
const activeIndex = ref(0);
const visible     = ref(false);
let timer    = null;
let observer = null;
let paused   = false;

function next() {
    activeIndex.value = (activeIndex.value + 1) % cards.length;
}

function jumpTo(i) {
    activeIndex.value = i;
    clearInterval(timer);
    if (!paused) startCycle();
}

function startCycle() {
    clearInterval(timer);
    timer = setInterval(next, duration);
}

function stopCycle() {
    clearInterval(timer);
}

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                visible.value = true;
                paused = false;
                startCycle();
            } else {
                paused = true;
                stopCycle();
            }
        },
        { threshold: 0.25 }
    );
    if (sectionRef.value) observer.observe(sectionRef.value);
});

onBeforeUnmount(() => {
    stopCycle();
    if (observer) observer.disconnect();
});
</script>

<style scoped>
/* ─── Wrapper ─────────────────────────────────── */
.why-wrap {
    background: #0a0f1a;
    padding: 88px 0 100px;
    overflow: hidden;
}

.why-inner {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 64px;
    align-items: center;
}

/* ─── Left label ──────────────────────────────── */
.why-label {
    opacity: 0;
    transform: translateX(-20px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.why-label.label-in {
    opacity: 1;
    transform: translateX(0);
}

.why-eyebrow {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    margin-bottom: 28px;
}

.why-counter {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 32px;
}
.counter-current {
    font-size: 52px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #fff;
    line-height: 1;
}
.counter-sep {
    font-size: 20px;
    color: rgba(255,255,255,0.2);
}
.counter-total {
    font-size: 20px;
    color: rgba(255,255,255,0.35);
}

/* ─── Dot nav ─────────────────────────────────── */
.why-dots {
    display: flex;
    gap: 10px;
}
.why-dot {
    width: 28px;
    height: 3px;
    border: none;
    border-radius: 2px;
    background: rgba(255,255,255,0.15);
    cursor: pointer;
    padding: 0;
    transition: background 0.3s, transform 0.3s;
}
.why-dot.dot-active {
    background: #fff;
    transform: scaleX(1.3);
    transform-origin: left;
}

/* ─── Card stage ──────────────────────────────── */
.card-stage {
    position: relative;
    min-height: 260px;
    display: flex;
    align-items: center;
}

/* ─── Cycling card ────────────────────────────── */
.cycle-card {
    width: 100%;
    background: #111827;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px;
    overflow: hidden;
    position: relative;
    padding-bottom: 0;
}

/* Left accent bar */
.cycle-bar {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--accent);
}

.cycle-body {
    padding: 44px 48px 52px 52px;
}

.cycle-title {
    margin: 0 0 18px;
    font-size: clamp(28px, 3vw, 38px);
    font-weight: 700;
    letter-spacing: -0.025em;
    color: #fff;
    line-height: 1.1;
}

.cycle-desc {
    margin: 0;
    font-size: 16px;
    line-height: 1.75;
    color: rgba(255,255,255,0.55);
    max-width: 560px;
}

/* Bottom progress bar */
.cycle-progress {
    height: 2px;
    background: rgba(255,255,255,0.06);
    overflow: hidden;
}
.cycle-fill {
    height: 100%;
    background: var(--accent);
    width: 0%;
    animation: fillBar linear forwards;
    animation-duration: inherit;
}

@keyframes fillBar {
    from { width: 0%; }
    to   { width: 100%; }
}

/* ─── Transition ──────────────────────────────── */
.card-cycle-enter-active {
    transition: opacity 0.45s ease, transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
}
.card-cycle-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.card-cycle-enter-from {
    opacity: 0;
    transform: translateY(30px);
}
.card-cycle-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 860px) {
    .why-inner {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .why-label {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px 36px;
        margin-bottom: 0;
    }
    .why-eyebrow { margin-bottom: 0; }
    .why-counter { margin-bottom: 0; }
    .card-stage { min-height: 200px; }
    .cycle-body { padding: 32px 28px 36px 32px; }
}

@media (max-width: 540px) {
    .why-wrap { padding: 64px 0 72px; }
    .counter-current { font-size: 40px; }
    .cycle-title { font-size: 26px; }
    .cycle-desc { font-size: 14px; }
}
</style>
