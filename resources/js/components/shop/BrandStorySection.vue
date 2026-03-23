<template>
    <section class="bs-root" ref="rootRef">
        <!-- decorative large background lettering -->
        <span class="bs-deco" aria-hidden="true">KH</span>

        <div class="bs-wrap">
            <div class="bs-layout">

                <!-- ── Left: large statement text ── -->
                <div class="bs-left" :class="{ visible: inView }">
                    <span class="bs-eyebrow">OUR STORY</span>

                    <!--
                        Unique animation: LINE MASK RISE
                        Each line's parent has overflow:hidden, so the
                        inner span is invisible at translateY(108%).
                        On .visible it rises to translateY(0) — text
                        emerges from below its container boundary.
                        Completely different from the 16px fadeInUp used
                        everywhere else in this project.
                    -->
                    <h2 class="bs-headline">
                        <span
                            v-for="(line, li) in headlineLines"
                            :key="li"
                            class="bs-line"
                            :style="`--li: ${li}`"
                        >
                            <span class="bs-line-inner">{{ line }}</span>
                        </span>
                    </h2>

                    <p class="bs-body">
                        We believe the right pair of shoes changes how you move through
                        the world. Built for comfort, designed for life — every stitch
                        placed with intention.
                    </p>

                    <div class="bs-rule" :class="{ visible: inView }"></div>
                </div>

                <!-- ── Right: numbered story items ── -->
                <div class="bs-right">
                    <div
                        v-for="(item, ii) in items"
                        :key="ii"
                        class="bs-item"
                        :class="{ visible: inView }"
                        :style="`--ii: ${ii}`"
                    >
                        <div class="bs-num-wrap">
                            <span class="bs-num">{{ String(ii + 1).padStart(2, '0') }}</span>
                            <span class="bs-num-line"></span>
                        </div>
                        <div class="bs-item-body">
                            <h4 class="bs-item-title">{{ item.title }}</h4>
                            <p class="bs-item-desc">{{ item.desc }}</p>
                        </div>
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

const headlineLines = ['Made for', 'Your Pace.'];

const items = [
    {
        title: 'Born in Cambodia',
        desc:  'Rooted in local craft and community, with a vision that reaches far beyond borders.',
    },
    {
        title: 'Shaped by You',
        desc:  'Every design decision guided by real feedback from the people who wear them daily.',
    },
    {
        title: 'Made to Move',
        desc:  'Each sole is built for the life you actually live — not the one on paper.',
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
        { threshold: 0.22 }
    );
    if (rootRef.value) observer.observe(rootRef.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
/* ── Root ────────────────────────────────────────── */
.bs-root {
    background: #0a0f1a;
    padding: 104px 0 112px;
    position: relative;
    overflow: hidden;
}

/* large decorative background letters */
.bs-deco {
    position: absolute;
    right: -1%;
    top: 50%;
    transform: translateY(-50%);
    font-size: clamp(140px, 22vw, 320px);
    font-weight: 800;
    letter-spacing: -0.05em;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.035);
    line-height: 1;
    pointer-events: none;
    user-select: none;
}

.bs-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
    position: relative;
}

/* ── Two-column layout ───────────────────────────── */
.bs-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

/* ── Left column ─────────────────────────────────── */
.bs-eyebrow {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    color: rgba(255, 255, 255, 0.32);
    margin-bottom: 24px;
    text-transform: uppercase;
    opacity: 0;
    transition: opacity 0.5s ease 0.05s;
}

.bs-left.visible .bs-eyebrow {
    opacity: 1;
}

/* Headline with overflow-hidden lines */
.bs-headline {
    font-size: clamp(52px, 6.5vw, 90px);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.04em;
    line-height: 0.95;
    margin: 0 0 30px;
}

.bs-line {
    display: block;
    overflow: hidden;
    padding-bottom: 0.07em; /* prevents descender clipping */
}

.bs-line-inner {
    display: block;
    transform: translateY(108%);
    transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1);
    transition-delay: calc(var(--li) * 0.17s + 0.08s);
}

.bs-left.visible .bs-line-inner {
    transform: translateY(0);
}

.bs-body {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.43);
    line-height: 1.8;
    margin: 0 0 32px;
    max-width: 420px;
    opacity: 0;
    transition: opacity 0.7s ease 0.56s;
}

.bs-left.visible .bs-body {
    opacity: 1;
}

/* animated rule — width expands from 0 */
.bs-rule {
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #a78bfa);
    border-radius: 2px;
    transition: width 1s cubic-bezier(0.22, 1, 0.36, 1) 0.62s;
}
.bs-rule.visible {
    width: 56px;
}

/* ── Right column: numbered items ────────────────── */
.bs-right {
    display: flex;
    flex-direction: column;
}

.bs-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 28px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    opacity: 0;
    transform: translateX(44px);
    transition:
        opacity  0.65s ease,
        transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: calc(var(--ii) * 0.14s + 0.32s);
}

.bs-item:first-child {
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    padding-top: 28px;
}

.bs-item.visible {
    opacity: 1;
    transform: translateX(0);
}

/* number badge */
.bs-num-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    padding-top: 2px;
}

.bs-num {
    font-size: 10.5px;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.2);
    letter-spacing: 0.06em;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

.bs-num-line {
    display: block;
    width: 1px;
    height: 24px;
    background: rgba(255, 255, 255, 0.1);
}

.bs-item-body {
    flex: 1;
}

.bs-item-title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px;
    letter-spacing: -0.01em;
}

.bs-item-desc {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.42);
    line-height: 1.7;
    margin: 0;
}

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 860px) {
    .bs-layout {
        grid-template-columns: 1fr;
        gap: 52px;
    }

    .bs-deco { display: none; }
}

@media (max-width: 540px) {
    .bs-root    { padding: 72px 0 80px; }
    .bs-item    { gap: 14px; padding: 22px 0; }
    .bs-headline { font-size: clamp(44px, 10vw, 60px); }
}
</style>
