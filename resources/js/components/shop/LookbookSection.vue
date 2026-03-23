<template>
    <section class="lb-root" ref="rootRef">
        <div class="lb-wrap">

            <!-- Header -->
            <div class="lb-head" :class="{ visible: inView }">
                <span class="lb-eyebrow">STYLE GALLERY</span>
                <h2 class="lb-title">Wear It Your Way</h2>
                <p class="lb-sub">Every pair tells a different story.</p>
            </div>

            <!--
                Grid: 5-tile asymmetric layout.
                Tile 0 spans both rows on the left (tall).
                Tiles 1–4 fill a 2×2 grid on the right.
            -->
            <div class="lb-grid">
                <div
                    v-for="(tile, i) in tiles"
                    :key="i"
                    class="lb-tile"
                    :class="[`lb-t${i}`, { visible: inView }]"
                    :style="`--i: ${i}`"
                    :ref="el => { if (el) tileEls[i] = el }"
                    @mousemove="handleTilt($event, i)"
                    @mouseleave="resetTilt(i)"
                >
                    <!--
                        The inner div takes the 3D tilt transform on hover
                        while clip-path stays on the outer .lb-tile.
                    -->
                    <div
                        class="lb-inner"
                        :ref="el => { if (el) innerEls[i] = el }"
                    >
                        <img
                            :src="tile.img"
                            :alt="tile.name"
                            class="lb-img"
                            loading="lazy"
                        />

                        <!-- always-visible bottom label -->
                        <div class="lb-caption">
                            <span class="lb-cat">{{ tile.category }}</span>
                            <span class="lb-name">{{ tile.name }}</span>
                            <span class="lb-price">${{ tile.price.toFixed(2) }}</span>
                        </div>

                        <!-- hover spotlight overlay -->
                        <div class="lb-spotlight" :ref="el => { if (el) spotEls[i] = el }"></div>
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

/* plain arrays — we only need stable DOM refs, not reactivity */
const tileEls  = [];
const innerEls = [];
const spotEls  = [];

const tiles = [
    {
        img:      '/images/products/Pro Runner Sneakers.jpg',
        name:     'Pro Runner',
        price:    10.00,
        category: 'Activity',
    },
    {
        img:      '/images/products/Classic Low-Top Sneakers.webp',
        name:     'Classic Low-Top',
        price:    8.00,
        category: 'Everyday',
    },
    {
        img:      '/images/products/Canvas Street Kicks.jpg',
        name:     'Canvas Street',
        price:    4.00,
        category: 'Everyday',
    },
    {
        img:      '/images/products/Trail Running Shoes.jpg',
        name:     'Trail Runner',
        price:    1.00,
        category: 'Activity',
    },
    {
        img:      '/images/products/Comfort Walk Loafers.jpg',
        name:     'Comfort Walk',
        price:    9.00,
        category: 'Travel',
    },
];

/* ── 3D magnetic tilt on hover ──────────────────── */
const handleTilt = (e, i) => {
    const outer = tileEls[i];
    const inner = innerEls[i];
    const spot  = spotEls[i];
    if (!outer || !inner) return;

    const rect = outer.getBoundingClientRect();
    const xRel = (e.clientX - rect.left)  / rect.width;   // 0 → 1
    const yRel = (e.clientY - rect.top)   / rect.height;
    const x    = xRel - 0.5;   // -0.5 → 0.5
    const y    = yRel - 0.5;

    inner.style.transition = 'transform 0.08s linear';
    inner.style.transform  =
        `perspective(680px) rotateY(${x * 13}deg) rotateX(${-y * 13}deg) scale(1.035)`;

    /* move spotlight gradient to follow cursor */
    if (spot) {
        spot.style.background =
            `radial-gradient(circle at ${xRel * 100}% ${yRel * 100}%,
             rgba(255,255,255,0.13) 0%, transparent 60%)`;
        spot.style.opacity = '1';
    }
};

const resetTilt = (i) => {
    const inner = innerEls[i];
    const spot  = spotEls[i];
    if (inner) {
        inner.style.transition = 'transform 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
        inner.style.transform  = '';
    }
    if (spot) {
        spot.style.opacity = '0';
    }
};

/* ── IntersectionObserver ───────────────────────── */
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
        { threshold: 0.12 }
    );
    if (rootRef.value) observer.observe(rootRef.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
/* ── Root ────────────────────────────────────────── */
.lb-root {
    background: #080d16;
    padding: 96px 0 108px;
    overflow: hidden;
}

.lb-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ── Header ──────────────────────────────────────── */
.lb-head {
    text-align: center;
    margin-bottom: 56px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}
.lb-head.visible {
    opacity: 1;
    transform: translateY(0);
}

.lb-eyebrow {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    color: rgba(255, 255, 255, 0.3);
    margin-bottom: 14px;
    text-transform: uppercase;
}

.lb-title {
    font-size: clamp(32px, 4vw, 50px);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.03em;
    line-height: 1.06;
    margin: 0 0 12px;
}

.lb-sub {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.4);
    margin: 0;
}

/* ── Asymmetric 5-tile grid ──────────────────────── */
.lb-grid {
    display: grid;
    grid-template-columns: 1.35fr 1fr 1fr;
    grid-template-rows: 295px 295px;
    gap: 11px;
}

/* tile 0: tall, spans both rows */
.lb-t0 { grid-row: 1 / 3; }
/* tiles 1–4 fill the 2×2 right zone */
.lb-t1 { grid-column: 2; grid-row: 1; }
.lb-t2 { grid-column: 3; grid-row: 1; }
.lb-t3 { grid-column: 2; grid-row: 2; }
.lb-t4 { grid-column: 3; grid-row: 2; }

/* ── Single tile ─────────────────────────────────── */
.lb-tile {
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;

    /*
        Unique animation: RADIAL CIRCLE BURST REVEAL
        Starts fully clipped (circle radius = 0%) and expands
        to circle(150%) — covering the whole element.
        This is a radial reveal from the tile's center, unlike
        all other animations in this project which use linear
        clip-path (inset), translateY, rotateX/Y, or blur-scale.
    */
    clip-path: circle(0% at 50% 50%);
}

@keyframes radialBurst {
    from { clip-path: circle(0%   at 50% 50%); }
    to   { clip-path: circle(150% at 50% 50%); }
}

.lb-tile.visible {
    animation: radialBurst 0.82s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--i) * 0.11s + 0.05s);
}

/* ── Inner (receives 3D tilt transform) ──────────── */
.lb-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    will-change: transform;
}

/* ── Image ───────────────────────────────────────── */
.lb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

.lb-tile:hover .lb-img {
    transform: scale(1.06);
}

/* ── Caption ─────────────────────────────────────── */
.lb-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 36px 18px 18px;
    background: linear-gradient(to top, rgba(4, 8, 18, 0.88) 0%, transparent 100%);
    display: flex;
    flex-direction: column;
    gap: 3px;
    transform: translateY(6px);
    transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}

.lb-tile:hover .lb-caption {
    transform: translateY(0);
}

.lb-cat {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.5);
}

.lb-name {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.01em;
}

.lb-price {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.55);
}

/* ── Spotlight overlay (follows mouse) ───────────── */
.lb-spotlight {
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: inherit;
}

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 860px) {
    .lb-grid {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 220px 220px 220px;
    }

    .lb-t0 { grid-row: 1; grid-column: 1 / 3; } /* wide on top */
    .lb-t1 { grid-column: 1; grid-row: 2; }
    .lb-t2 { grid-column: 2; grid-row: 2; }
    .lb-t3 { grid-column: 1; grid-row: 3; }
    .lb-t4 { grid-column: 2; grid-row: 3; }
}

@media (max-width: 540px) {
    .lb-root { padding: 64px 0 72px; }
    .lb-grid {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(5, 220px);
        gap: 8px;
    }
    .lb-t0, .lb-t1, .lb-t2, .lb-t3, .lb-t4 {
        grid-column: 1;
        grid-row: auto;
    }
    .lb-head { margin-bottom: 36px; }
}

@media (max-width: 380px) {
    .lb-grid {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(5, 200px);
    }
}
</style>
