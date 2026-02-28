<template>
    <section class="collections-scroll" ref="sectionRef">

        <!-- Sticky viewport that holds the visual -->
        <div class="sticky-stage">

            <!-- Progress bar (vertical line + dot) -->
            <div class="progress-rail">
                <div class="progress-line">
                    <div class="progress-fill" :style="{ height: fillPct + '%' }"></div>
                </div>
                <div
                    class="progress-dot"
                    v-for="(item, i) in collections"
                    :key="i"
                    :class="{ active: activeIndex >= i }"
                    :style="{ top: (i / (collections.length - 1)) * 100 + '%' }"
                    @click="scrollToSlide(i)"
                ></div>
            </div>

            <!-- Cards -->
            <div
                class="slide"
                v-for="(item, i) in collections"
                :key="item.title"
                :class="{ 'is-active': activeIndex === i, 'is-past': activeIndex > i }"
            >
                <!-- Image side -->
                <div class="slide-image">
                    <img :src="item.image" :alt="item.title" />
                    <div class="image-overlay"></div>
                </div>

                <!-- Content side -->
                <div class="slide-content">
                    <span class="slide-index">0{{ i + 1 }} / 0{{ collections.length }}</span>
                    <p class="slide-kicker">{{ item.kicker }}</p>
                    <h2 class="slide-title">{{ item.title }}</h2>
                    <p class="slide-desc">{{ item.desc }}</p>
                    <a href="#shop" class="slide-cta">Shop collection</a>
                </div>
            </div>

        </div>

        <!-- Scroll spacer — one screen height per slide -->
        <div class="scroll-spacer"></div>

    </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const collections = [
    {
        kicker: 'Daily Comfort',
        title: 'Urban Walkers',
        desc: 'Crafted for the streets you own every day. Lightweight builds, clean silhouettes, and cushioning that keeps up from morning commute to late-night errands.',
        image: '/images/collections/Urban Walkers.jpg'
    },
    {
        kicker: 'Travel Ready',
        title: 'Road Essentials',
        desc: 'Pack less, move more. Our travel-ready silhouettes fold flat, resist odor, and look sharp at the airport or on cobblestone lanes halfway around the world.',
        image: '/images/collections/Road Essentials.jpg'
    },
    {
        kicker: 'Outdoor Active',
        title: 'Trail Focus',
        desc: 'From forest paths to rocky ridgelines, these shoes grip where it counts. Rugged outsoles and breathable uppers built for the trails you\'ve been waiting to run.',
        image: '/images/collections/Trail Focus.jpg'
    }
];

const sectionRef = ref(null);
const scrollProgress = ref(0);   // 0 → 1 over the full scroll range

const activeIndex = computed(() => {
    const step = 1 / collections.length;
    return Math.min(
        collections.length - 1,
        Math.floor(scrollProgress.value / step)
    );
});

// Fill % for the vertical line (0–100)
const fillPct = computed(() => scrollProgress.value * 100);

function onScroll() {
    if (!sectionRef.value) return;
    const rect = sectionRef.value.getBoundingClientRect();
    const totalScrollable = sectionRef.value.offsetHeight - window.innerHeight;
    if (totalScrollable <= 0) return;
    const scrolled = Math.max(0, -rect.top);
    scrollProgress.value = Math.min(1, scrolled / totalScrollable);
}

function scrollToSlide(index) {
    if (!sectionRef.value) return;
    const totalScrollable = sectionRef.value.offsetHeight - window.innerHeight;
    const target = sectionRef.value.offsetTop + (index / collections.length) * totalScrollable;
    window.scrollTo({ top: target, behavior: 'smooth' });
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));
</script>

<style scoped>
/* ─── Outer section ───────────────────────────────────── */
.collections-scroll {
    position: relative;
    /* 100vh sticky stage + 1 screen per extra slide */
    height: calc(100vh * (1 + 2));   /* 3 slides = 300vh total */
}

/* ─── Sticky stage ───────────────────────────────────── */
.sticky-stage {
    position: sticky;
    top: 0;
    height: 100vh;
    overflow: hidden;
    background: #0a0f1a;
}

/* ─── Slides ─────────────────────────────────────────── */
.slide {
    position: absolute;
    inset: 0;
    display: grid;
    grid-template-columns: 1fr 1fr;
    opacity: 0;
    pointer-events: none;
    transform: translateY(40px);
    transition:
        opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
}

.slide.is-active {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}

.slide.is-past {
    opacity: 0;
    transform: translateY(-40px);
    pointer-events: none;
}

/* ─── Image side ─────────────────────────────────────── */
.slide-image {
    position: relative;
    overflow: hidden;
}

.slide-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.06);
    transition: transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}

.slide.is-active .slide-image img {
    transform: scale(1);
}

.image-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent 60%, #0a0f1a 100%);
}

/* ─── Content side ───────────────────────────────────── */
.slide-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 56px 60px 48px;
    color: #fff;
}

.slide-index {
    font-size: 11px;
    letter-spacing: 0.18em;
    color: rgba(255,255,255,0.35);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 20px;

    opacity: 0;
    transform: translateY(14px);
    transition: opacity 0.5s ease 0.1s, transform 0.5s ease 0.1s;
}

.slide-kicker {
    margin: 0 0 10px;
    font-size: 12px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.55);
    font-weight: 600;

    opacity: 0;
    transform: translateY(14px);
    transition: opacity 0.5s ease 0.18s, transform 0.5s ease 0.18s;
}

.slide-title {
    margin: 0 0 18px;
    font-size: clamp(36px, 4.5vw, 58px);
    line-height: 1.05;
    letter-spacing: -0.025em;
    color: #fff;

    opacity: 0;
    transform: translateY(18px);
    transition: opacity 0.55s ease 0.26s, transform 0.55s ease 0.26s;
}

.slide-desc {
    margin: 0 0 32px;
    font-size: 15px;
    line-height: 1.7;
    color: rgba(255,255,255,0.65);
    max-width: 380px;

    opacity: 0;
    transform: translateY(14px);
    transition: opacity 0.55s ease 0.34s, transform 0.55s ease 0.34s;
}

.slide-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255,255,255,0.4);
    padding-bottom: 4px;
    width: fit-content;
    transition: border-color 0.2s, gap 0.2s;

    opacity: 0;
    transform: translateY(14px);
    /* transition stacked — last prop wins for opacity/transform */
    transition: opacity 0.5s ease 0.42s, transform 0.5s ease 0.42s, border-color 0.2s, gap 0.2s;
}

.slide-cta::after {
    content: '→';
    transition: transform 0.25s ease;
}

.slide-cta:hover {
    border-color: #fff;
    gap: 16px;
}

.slide-cta:hover::after {
    transform: translateX(4px);
}

/* Reveal children when slide becomes active */
.slide.is-active .slide-index,
.slide.is-active .slide-kicker,
.slide.is-active .slide-title,
.slide.is-active .slide-desc,
.slide.is-active .slide-cta {
    opacity: 1;
    transform: translateY(0);
}

/* ─── Progress rail ──────────────────────────────────── */
.progress-rail {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-line {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 1px;
    height: 100%;
    background: rgba(255,255,255,0.15);
    border-radius: 1px;
    overflow: hidden;
}

.progress-fill {
    width: 100%;
    background: #fff;
    border-radius: 1px;
    transition: height 0.1s linear;
    position: absolute;
    top: 0;
}

.progress-dot {
    position: absolute;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,0.35);
    background: transparent;
    cursor: pointer;
    transition: border-color 0.3s, background 0.3s, transform 0.3s;
}

.progress-dot.active {
    border-color: #fff;
    background: #fff;
    transform: translate(-50%, -50%) scale(1.2);
}

/* ─── Scroll spacer ──────────────────────────────────── */
.scroll-spacer {
    /* fills the non-sticky space so the section has the right scroll height */
    height: calc(100vh * 2);   /* extra 2 screens (3 total with the sticky 1) */
}

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 860px) {
    .slide {
        grid-template-columns: 1fr;
        grid-template-rows: 1fr auto;
    }

    .slide-image {
        height: 50vh;
    }

    .image-overlay {
        background: linear-gradient(180deg, transparent 50%, #0a0f1a 100%);
    }

    .slide-content {
        padding: 28px 24px 36px;
    }

    .slide-title {
        font-size: 32px;
    }

    .progress-rail {
        right: 14px;
        height: 80px;
    }
}
</style>
