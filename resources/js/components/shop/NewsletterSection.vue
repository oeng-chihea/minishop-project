<template>
    <section class="newsletter-wrap" ref="sectionRef">
        <div class="newsletter-inner" :class="{ 'is-visible': isVisible }">
            <div class="text-col">
                <span class="kicker">Join NovaStore Club</span>
                <h3>Get launches, early offers and seasonal drops.</h3>
                <p class="desc">Subscribe to our newsletter and receive updates like an official ecommerce brand site.</p>
            </div>

            <form class="form-col" @submit.prevent>
                <input type="email" placeholder="Enter your email" aria-label="Email address" />
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const sectionRef = ref(null);
const isVisible = ref(false);
let observer;

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                isVisible.value = true;
                observer.disconnect();
            }
        },
        { threshold: 0.1 }
    );
    if (sectionRef.value) observer.observe(sectionRef.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
/* ─── Section ─────────────────────────────────── */
.newsletter-wrap {
    background: #0a0f1a;
    padding: 88px 0 100px;
    border-top: 1px solid rgba(255,255,255,0.06);
}

/* ─── Inner ───────────────────────────────────── */
.newsletter-inner {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 48px;
    align-items: center;
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.newsletter-inner.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ─── Text ────────────────────────────────────── */
.kicker {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    margin-bottom: 16px;
}

h3 {
    margin: 0 0 12px;
    font-size: clamp(28px, 3.5vw, 40px);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
    line-height: 1.15;
}

.desc {
    margin: 0;
    color: rgba(255,255,255,0.4);
    font-size: 15px;
    line-height: 1.6;
}

/* ─── Form ────────────────────────────────────── */
.form-col {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
}

input {
    width: 100%;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.05);
    color: #fff;
    padding: 14px 16px;
    font-size: 14px;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s ease, background 0.2s ease;
}

input:focus {
    border-color: rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.08);
}

input::placeholder {
    color: rgba(255,255,255,0.3);
}

button {
    border: 0;
    background: #ffffff;
    color: #080d16;
    font-weight: 700;
    font-family: inherit;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 12px;
    padding: 14px 22px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s ease;
}

button:hover {
    background: rgba(255,255,255,0.85);
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 860px) {
    .newsletter-inner {
        grid-template-columns: 1fr;
        gap: 32px;
    }
}

@media (max-width: 540px) {
    .newsletter-wrap {
        padding: 64px 0 72px;
    }

    .form-col {
        grid-template-columns: 1fr;
    }
}
</style>
