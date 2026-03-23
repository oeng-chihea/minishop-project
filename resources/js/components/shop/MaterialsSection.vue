<template>
    <section class="ms-root" ref="rootRef">
        <!-- subtle dot-grid bg -->
        <div class="ms-grid-bg" aria-hidden="true"></div>

        <div class="ms-wrap">
            <!-- Header -->
            <div class="ms-head" :class="{ visible: inView }">
                <span class="ms-eyebrow">QUALITY FIRST</span>
                <h2 class="ms-title">Built to Last</h2>
                <p class="ms-subtitle">Three principles guide every pair we create.</p>
            </div>

            <!-- Cards -->
            <div class="ms-cards">
                <div
                    v-for="(card, idx) in cards"
                    :key="idx"
                    class="ms-card"
                    :class="{ visible: inView }"
                    :style="`--idx: ${idx}; --clr: ${card.color}`"
                >
                    <!-- animated scan line -->
                    <span class="ms-scanline" aria-hidden="true"></span>

                    <!-- corner brackets -->
                    <i class="ms-corner ms-tl" aria-hidden="true"></i>
                    <i class="ms-corner ms-tr" aria-hidden="true"></i>
                    <i class="ms-corner ms-bl" aria-hidden="true"></i>
                    <i class="ms-corner ms-br" aria-hidden="true"></i>

                    <!-- icon -->
                    <div class="ms-icon-box">
                        <svg
                            viewBox="0 0 52 52"
                            fill="none"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="ms-svg"
                            :style="`stroke: ${card.color}`"
                        >
                            <!-- Diamond: premium materials -->
                            <template v-if="idx === 0">
                                <path d="M26 5 L44 18 L38 47 L14 47 L8 18 Z" class="ms-path" />
                                <path d="M8 18 L26 5 L44 18" class="ms-path" />
                                <path d="M8 18 L26 29 L44 18" class="ms-path" />
                                <path d="M26 29 L14 47 M26 29 L38 47" class="ms-path" />
                            </template>
                            <!-- Precision hexagon -->
                            <template v-else-if="idx === 1">
                                <path d="M26 6 L40 14 L40 30 L26 38 L12 30 L12 14 Z" class="ms-path" />
                                <circle cx="26" cy="22" r="5" class="ms-path" />
                                <path
                                    d="M26 6 L26 17 M40 14 L31 19 M40 30 L31 25 M26 38 L26 27 M12 30 L21 25 M12 14 L21 19"
                                    class="ms-path"
                                />
                            </template>
                            <!-- Shield: durability -->
                            <template v-else>
                                <path
                                    d="M26 5 L7 13 L7 27 C7 38 16 46 26 49 C36 46 45 38 45 27 L45 13 Z"
                                    class="ms-path"
                                />
                                <polyline points="17,26 24,33 36,19" class="ms-path" />
                            </template>
                        </svg>
                    </div>

                    <h3 class="ms-card-title">{{ card.title }}</h3>
                    <p class="ms-card-desc">{{ card.desc }}</p>

                    <div class="ms-card-foot">
                        <strong class="ms-stat-num">{{ card.stat }}</strong>
                        <span class="ms-stat-lbl">{{ card.statLabel }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const rootRef = ref(null);
const inView  = ref(false);

const cards = [
    {
        color:     '#60a5fa',
        title:     'Premium Materials',
        desc:      'Hand-selected uppers and insoles built for breathability, softness, and long-term shape retention.',
        stat:      '100%',
        statLabel: 'Responsibly Sourced',
    },
    {
        color:     '#a78bfa',
        title:     'Precision Fit',
        desc:      'Each last is engineered around real foot anatomy — supporting your arch, heel, and toe naturally.',
        stat:      '±0.5 mm',
        statLabel: 'Fit Tolerance',
    },
    {
        color:     '#34d399',
        title:     'Built to Last',
        desc:      'Vulcanised rubber soles and triple-stitched seams that outlast daily wear across any terrain.',
        stat:      '2×',
        statLabel: 'Avg. Lifespan vs Fast Fashion',
    },
];

let observer = null;

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                inView.value = true;
            } else {
                inView.value = false;
            }
        },
        { threshold: 0.18 }
    );
    if (rootRef.value) observer.observe(rootRef.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
/* ── Root ────────────────────────────────────────── */
.ms-root {
    background: #070c18;
    padding: 96px 0 104px;
    position: relative;
    overflow: hidden;
}

/* dot-grid texture */
.ms-grid-bg {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
    background-size: 36px 36px;
    pointer-events: none;
}

.ms-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
    position: relative;
}

/* ── Header ──────────────────────────────────────── */
.ms-head {
    text-align: center;
    margin-bottom: 64px;
    opacity: 0;
    transform: translateY(22px);
    transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}
.ms-head.visible {
    opacity: 1;
    transform: translateY(0);
}

.ms-eyebrow {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    color: rgba(255, 255, 255, 0.32);
    margin-bottom: 14px;
    text-transform: uppercase;
}

.ms-title {
    font-size: clamp(32px, 4vw, 50px);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.03em;
    line-height: 1.06;
    margin: 0 0 14px;
}

.ms-subtitle {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.42);
    margin: 0;
    line-height: 1.65;
}

/* ── Card grid ───────────────────────────────────── */
.ms-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

/* ── Single card ─────────────────────────────────── */
.ms-card {
    position: relative;
    background: rgba(255, 255, 255, 0.028);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 4px;
    padding: 44px 30px 36px;
    overflow: hidden;

    /* start state for flipInX */
    opacity: 0;
    transform: perspective(900px) rotateX(-72deg) translateY(28px);
}

/* ── Unique animation: 3D X-axis flip (rotateX) ─────
   Different from ValueStrip which uses rotateY.
   Cards "open" like a lid being lifted toward you.    */
@keyframes flipInX {
    from {
        opacity: 0;
        transform: perspective(900px) rotateX(-72deg) translateY(28px);
    }
    to {
        opacity: 1;
        transform: perspective(900px) rotateX(0deg) translateY(0);
    }
}

.ms-card.visible {
    animation: flipInX 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--idx) * 0.14s + 0.15s);
}

/* ── Scan line: sweeps down each card on repeat ───── */
@keyframes scanDown {
    0%   { top: -1px; opacity: 0; }
    6%   { opacity: 1; }
    90%  { opacity: 0.65; }
    100% { top: 100%; opacity: 0; }
}

.ms-scanline {
    display: block;
    position: absolute;
    left: 0;
    right: 0;
    top: -1px;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, var(--clr) 40%, var(--clr) 60%, transparent 100%);
    pointer-events: none;
    opacity: 0;
}

.ms-card.visible .ms-scanline {
    animation: scanDown 3.4s ease-in-out infinite;
    animation-delay: calc(var(--idx) * 0.6s + 1.3s);
}

/* ── SVG icon ────────────────────────────────────── */
.ms-icon-box {
    width: 52px;
    height: 52px;
    margin-bottom: 26px;
}

.ms-svg {
    width: 52px;
    height: 52px;
    overflow: visible;
}

/* ── SVG stroke draw animation ───────────────────── */
.ms-path {
    stroke-dasharray: 400;
    stroke-dashoffset: 400;
    transition: stroke-dashoffset 1.6s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: calc(var(--idx) * 0.14s + 0.7s);
}

.ms-card.visible .ms-path {
    stroke-dashoffset: 0;
}

/* ── Card text ───────────────────────────────────── */
.ms-card-title {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 12px;
    letter-spacing: -0.01em;
}

.ms-card-desc {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.44);
    line-height: 1.72;
    margin: 0 0 28px;
}

/* ── Stat footer ─────────────────────────────────── */
.ms-card-foot {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.ms-stat-num {
    font-size: 21px;
    font-weight: 800;
    color: var(--clr);
    letter-spacing: -0.02em;
}

.ms-stat-lbl {
    font-size: 10.5px;
    color: rgba(255, 255, 255, 0.28);
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

/* ── Corner brackets ─────────────────────────────── */
.ms-corner {
    display: block;
    position: absolute;
    width: 11px;
    height: 11px;
    border-color: var(--clr);
    border-style: solid;
    opacity: 0;
    transition: opacity 0.4s ease calc(var(--idx) * 0.14s + 0.9s);
}
.ms-card.visible .ms-corner { opacity: 0.45; }
.ms-tl { top: 0;    left: 0;  border-width: 1px 0 0 1px; }
.ms-tr { top: 0;    right: 0; border-width: 1px 1px 0 0; }
.ms-bl { bottom: 0; left: 0;  border-width: 0 0 1px 1px; }
.ms-br { bottom: 0; right: 0; border-width: 0 1px 1px 0; }

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 860px) {
    .ms-cards {
        grid-template-columns: 1fr;
        max-width: 460px;
        margin: 0 auto;
    }
}

@media (max-width: 540px) {
    .ms-root  { padding: 72px 0 80px; }
    .ms-card  { padding: 32px 22px 28px; }
    .ms-head  { margin-bottom: 48px; }
}
</style>
