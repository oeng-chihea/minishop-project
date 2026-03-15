<template>
    <section class="strip-wrap">
        <div class="strip-inner">

            <!-- Header -->
            <div class="strip-header" :class="{ 'header-in': sectionVisible }" ref="labelRef">
                <span class="strip-eyebrow">Why shop with us</span>
                <h3 class="strip-heading">Built around your experience</h3>
            </div>

            <!-- Cards -->
            <div class="cards-row">
                <div
                    class="card-wrap"
                    v-for="(item, i) in values"
                    :key="item.title"
                    :class="{ 'card-entered': enteredCards[i] }"
                    :style="{ '--delay': i * 0.12 + 's' }"
                    :ref="el => cardRefs[i] = el"
                >
                    <article class="value-card">
                        <div class="card-content">
                            <span class="card-num">0{{ i + 1 }}</span>
                            <h4 class="card-title">{{ item.title }}</h4>
                            <p class="card-desc">{{ item.desc }}</p>
                        </div>
                    </article>
                </div>
            </div>

        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const values = [
    {
        title: 'Free Shipping',
        desc: 'Free standard shipping on every order over $100, delivered worldwide.',
        accent: '#3b82f6'
    },
    {
        title: 'Easy Returns',
        desc: '30-day hassle-free returns with simple door pickup at no extra cost.',
        accent: '#10b981'
    },
    {
        title: 'Secure Checkout',
        desc: 'Powered by Bakong KHQR — Cambodia\'s leading digital payment solution.',
        accent: '#a78bfa'
    },
    {
        title: 'Sustainable Materials',
        desc: 'Low-impact fabrics and mindful packaging in every box we ship.',
        accent: '#f59e0b'
    }
];

const cardRefs       = ref([]);
const labelRef       = ref(null);
const enteredCards   = ref(values.map(() => false));
const sectionVisible = ref(false);

let observer = null;

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                if (entry.target === labelRef.value) {
                    sectionVisible.value = true;
                    observer.unobserve(entry.target);
                    return;
                }
                const i = cardRefs.value.indexOf(entry.target);
                if (i !== -1) {
                    enteredCards.value[i] = true;
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1 }
    );
    if (labelRef.value) observer.observe(labelRef.value);
    cardRefs.value.forEach((el) => { if (el) observer.observe(el); });
});

onBeforeUnmount(() => { if (observer) observer.disconnect(); });
</script>

<style scoped>
/* ─── Section ─────────────────────────────────── */
.strip-wrap {
    background: #0a0f1a;
    padding: 88px 0 100px;
    overflow: hidden;
}

.strip-inner {
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ─── Header ──────────────────────────────────── */
.strip-header {
    margin-bottom: 60px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.strip-header.header-in {
    opacity: 1;
    transform: translateY(0);
}
.strip-eyebrow {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    margin-bottom: 12px;
}
.strip-heading {
    margin: 0;
    font-size: clamp(26px, 3vw, 36px);
    font-weight: 700;
    letter-spacing: -0.03em;
    color: #ffffff;
    line-height: 1.1;
}

/* ─── Grid ────────────────────────────────────── */
.cards-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
    align-items: end;
    /* Unique ValueStrip animation: 3D perspective for rotateY flip */
    perspective: 1400px;
}

/* ─── Card wrapper ────────────────────────────── */

/* BEFORE scroll-in: edge-on (invisible in 3D space) */
.card-wrap {
    opacity: 0;
    transform: rotateY(-90deg);
    pointer-events: none;
    transition: opacity 0.01s;
}

/* AFTER scroll-in: flips upright like a playing card */
.card-wrap.card-entered {
    opacity: 1;
    transform: rotateY(0deg);
    pointer-events: auto;
    transition:
        opacity  0.4s ease var(--delay),
        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1) var(--delay),
        box-shadow 0.35s ease;
}

/* Hover: gentle lift */
.card-wrap.card-entered:hover {
    transform: rotateY(0deg) translateY(-8px) scale(1.02);
    box-shadow: 0 28px 56px rgba(0, 0, 0, 0.5);
}

/* ─── Card article ────────────────────────────── */
.value-card {
    position: relative;
    background: #111827;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px;
    overflow: hidden;
    transition: background 0.35s ease;
}

.card-wrap.card-entered:hover .value-card {
    background: #141c2e;
}

/* ─── Content ─────────────────────────────────── */
.card-content {
    padding: 32px 28px 36px 32px;
}

.card-num {
    display: block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    color: rgba(255,255,255,0.25);
    margin-bottom: 18px;
    transition: color 0.3s ease;
}
.card-wrap.card-entered:hover .card-num {
    color: rgba(255,255,255,0.5);
}

.card-title {
    margin: 0 0 12px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.015em;
    color: #ffffff;
    line-height: 1.25;
}

.card-desc {
    margin: 0;
    font-size: 13.5px;
    line-height: 1.72;
    color: rgba(255,255,255,0.4);
    transition: color 0.35s ease;
}
.card-wrap.card-entered:hover .card-desc {
    color: rgba(255,255,255,0.7);
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 980px) {
    .cards-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 540px) {
    .cards-row {
        grid-template-columns: 1fr;
        gap: 16px;
        perspective: none; /* disable 3D on small screens for performance */
    }

    /* Replace rotateY flip with simple translateY on mobile */
    .card-wrap        { transform: translateY(24px); }
    .card-wrap.card-entered {
        transform: translateY(0);
        transition:
            opacity  0.45s ease var(--delay),
            transform 0.55s cubic-bezier(0.22, 1, 0.36, 1) var(--delay);
    }
    .card-wrap.card-entered:hover {
        transform: translateY(-6px) scale(1.01);
    }

    .strip-wrap { padding: 64px 0 72px; }
}

@media (max-width: 420px) {
    .strip-wrap   { padding: 48px 0 56px; }
    .strip-inner  { padding: 0 16px; }
    .strip-heading { font-size: 22px; }
    .strip-header  { margin-bottom: 40px; }
    .card-content  { padding: 24px 20px 28px; }
}
</style>
