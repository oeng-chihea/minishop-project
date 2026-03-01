<template>
    <section class="community" ref="sectionRef" id="community">
        <div class="container">
            <!-- Header -->
            <div class="head" :class="{ 'is-visible': isVisible }">
                <span class="kicker">Community Love</span>
                <h2>Loved by shoppers across Cambodia</h2>
                <p class="head-desc">Real numbers from real people who trust minishopkh.</p>
            </div>

            <!-- Stat grid -->
            <div class="stats-grid" :class="{ 'is-visible': isVisible }">
                <div
                    class="stat-cell"
                    v-for="(s, i) in stats"
                    :key="i"
                    :style="{ '--i': i }"
                >
                    <span class="stat-num">{{ s.displayed }}{{ s.suffix }}</span>
                    <span class="stat-bar" :style="{ '--accent': s.accent }"></span>
                    <span class="stat-label">{{ s.label }}</span>
                </div>
            </div>
        </div>

        <!-- Infinite marquee -->
        <div class="marquee-wrap" :class="{ 'is-visible': isVisible }">
            <div
                class="marquee-track"
                :style="{ animationPlayState: paused ? 'paused' : 'running' }"
                @mouseenter="paused = true"
                @mouseleave="paused = false"
            >
                <!-- Duplicate for seamless loop -->
                <div class="marquee-set" v-for="n in 2" :key="n" :aria-hidden="n === 2 ? 'true' : undefined">
                    <div v-for="(q, i) in quotes" :key="i" class="chip">
                        <span class="chip-stars">★★★★★</span>
                        <span class="chip-text">"{{ q.text }}"</span>
                        <span class="chip-sep">·</span>
                        <span class="chip-from">{{ q.from }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';

const sectionRef = ref(null);
const isVisible  = ref(false);
const paused     = ref(false);

const stats = reactive([
    { target: 1200, displayed: 0, suffix: '+', label: 'Happy Shoppers', accent: '#3b82f6' },
    { target: 98,   displayed: 0, suffix: '%', label: '5-Star Reviews',  accent: '#10b981' },
    { target: 9,    displayed: 0, suffix: '',  label: 'Curated Styles',  accent: '#a78bfa' },
    { target: 3,    displayed: 0, suffix: '',  label: 'Collections',     accent: '#f5a623' },
]);

const quotes = [
    { text: 'Absolutely love my sneakers',    from: 'Sophea M.' },
    { text: 'Fast delivery, perfect fit',     from: 'Dara C.' },
    { text: 'ABA checkout was instant',       from: 'Leakena R.' },
    { text: 'Best shoes I\'ve ever owned',    from: 'Virak P.' },
    { text: 'Super lightweight and comfy',    from: 'Chanthy L.' },
    { text: 'Arrived faster than expected',   from: 'Borey K.' },
    { text: 'Amazing quality for the price',  from: 'Maly S.' },
    { text: 'My go-to for everyday wear',     from: 'Sokha T.' },
];

function countUp() {
    const DURATION = 1600;
    const STEPS    = 60;
    stats.forEach(s => {
        let step = 0;
        const inc = s.target / STEPS;
        const t = setInterval(() => {
            step++;
            s.displayed = step < STEPS ? Math.round(inc * step) : s.target;
            if (step >= STEPS) clearInterval(t);
        }, DURATION / STEPS);
    });
}

let observer;
onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                isVisible.value = true;
                countUp();
                observer.disconnect();
            }
        },
        { threshold: 0.15 }
    );
    if (sectionRef.value) observer.observe(sectionRef.value);
});
onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
/* ─── Section ─────────────────────────────────── */
.community {
    padding: 88px 0 0;
    background: #0a0f1a;
    overflow: hidden;
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ─── Header ──────────────────────────────────── */
.head {
    text-align: center;
    margin-bottom: 64px;
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.head.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.28);
    margin-bottom: 14px;
}

h2 {
    margin: 0 0 12px;
    font-size: clamp(28px, 3.5vw, 40px);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
}

.head-desc {
    color: rgba(255, 255, 255, 0.4);
    font-size: 16px;
    margin: 0;
    line-height: 1.5;
}

/* ─── Stats grid ──────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.06);
    margin-bottom: 72px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease 0.18s, transform 0.7s ease 0.18s;
}
.stats-grid.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.stat-cell {
    background: #0e1420;
    padding: 44px 32px 36px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    transition: background 0.25s ease;
}
.stat-cell:hover {
    background: #111827;
}

.stat-num {
    font-size: clamp(42px, 4.5vw, 60px);
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #ffffff;
    line-height: 1;
}

.stat-bar {
    display: block;
    width: 32px;
    height: 3px;
    border-radius: 2px;
    background: var(--accent);
}

.stat-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.38);
}

/* ─── Marquee ─────────────────────────────────── */
.marquee-wrap {
    padding: 44px 0 88px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    overflow: hidden;
    opacity: 0;
    transition: opacity 0.8s ease 0.36s;
}
.marquee-wrap.is-visible {
    opacity: 1;
}

.marquee-track {
    display: flex;
    width: max-content;
    animation: slide 34s linear infinite;
}
@keyframes slide {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

.marquee-set {
    display: flex;
    gap: 12px;
    padding-right: 12px;
}

.chip {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #111827;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 100px;
    padding: 11px 22px;
    white-space: nowrap;
    cursor: default;
    transition: background 0.25s ease, border-color 0.25s ease;
}
.chip:hover {
    background: #1a2234;
    border-color: rgba(255, 255, 255, 0.14);
}

.chip-stars {
    font-size: 10px;
    color: #f5a623;
    letter-spacing: 1.5px;
}
.chip-text {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    font-style: italic;
}
.chip-sep {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.18);
}
.chip-from {
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.32);
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 860px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .stats-grid {
        margin-bottom: 52px;
    }
}

@media (max-width: 540px) {
    .community {
        padding-top: 60px;
    }
    .head {
        margin-bottom: 44px;
    }
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        margin-bottom: 40px;
    }
    .stat-cell {
        padding: 28px 20px 24px;
    }
    .marquee-wrap {
        padding: 36px 0 60px;
    }
}
</style>
