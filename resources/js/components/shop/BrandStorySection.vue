<template>
    <section class="brand-story" ref="sectionRef" id="about">
        <div class="container brand-story-inner" :class="{ 'is-visible': isVisible }">
            <div class="story-image-col">
                <div class="image-frame">
                    <img
                        src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&w=800&q=80"
                        alt="Our store"
                    />
                    <div class="image-badge">
                        <span class="badge-number">5+</span>
                        <span class="badge-label">Years of Craft</span>
                    </div>
                </div>
            </div>

            <div class="story-text-col">
                <span class="kicker">Our Story</span>
                <h2>Born from a love of quality and simplicity</h2>
                <p class="lead">
                    minishopkh started with a single idea — everyday products should be beautifully
                    made, thoughtfully designed, and accessible to everyone.
                </p>
                <p>
                    We obsessively curate items that stand the test of time, from street-ready
                    essentials to travel companions and active gear. Every product earns its place
                    in our collection through quality, design, and real-world usefulness.
                </p>
                <div class="story-stats">
                    <div class="stat">
                        <strong>9+</strong>
                        <span>Products curated</span>
                    </div>
                    <div class="stat">
                        <strong>3</strong>
                        <span>Collections</span>
                    </div>
                    <div class="stat">
                        <strong>100%</strong>
                        <span>Satisfaction</span>
                    </div>
                </div>
                <a href="#shop" class="story-cta">Explore the Collection</a>
            </div>
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
        { threshold: 0.12 }
    );
    if (sectionRef.value) observer.observe(sectionRef.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<style scoped>
.brand-story {
    padding: 80px 0;
    overflow: hidden;
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.brand-story-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 72px;
    align-items: center;
}

/* Scroll animation */
.brand-story-inner {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.brand-story-inner.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.story-image-col {
    transition-delay: 0s;
}

.story-text-col {
    transition-delay: 0.15s;
}

/* Image */
.image-frame {
    position: relative;
    border-radius: 2px;
    overflow: hidden;
    box-shadow: var(--shadow-hero);
}

.image-frame img {
    width: 100%;
    height: 480px;
    object-fit: cover;
    display: block;
    transition: transform 0.7s ease;
}

.image-frame:hover img {
    transform: scale(1.04);
}

.image-badge {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: var(--color-primary);
    color: #fff;
    padding: 14px 18px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,0.22);
}

.badge-number {
    display: block;
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.02em;
}

.badge-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    opacity: 0.85;
    margin-top: 4px;
}

/* Text */
.kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--color-accent);
    margin-bottom: 14px;
}

h2 {
    margin: 0 0 20px;
    font-size: 42px;
    font-weight: 800;
    line-height: 1.08;
    letter-spacing: -0.03em;
    color: var(--color-text);
}

.lead {
    font-size: 18px;
    color: var(--color-text);
    line-height: 1.55;
    margin: 0 0 16px;
    font-weight: 500;
}

p {
    color: var(--color-muted);
    line-height: 1.65;
    margin: 0 0 28px;
}

/* Stats */
.story-stats {
    display: flex;
    gap: 40px;
    margin-bottom: 32px;
    padding: 24px 0;
    border-top: 1px solid var(--color-line);
    border-bottom: 1px solid var(--color-line);
}

.stat strong {
    display: block;
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--color-primary);
    line-height: 1;
}

.stat span {
    display: block;
    font-size: 12px;
    color: var(--color-muted);
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-top: 4px;
}

/* CTA */
.story-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--color-text);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 14px 28px;
    border-radius: 2px;
    transition: background 0.25s ease, transform 0.25s ease;
}

.story-cta:hover {
    background: var(--color-primary);
    transform: translateY(-2px);
}

@media (max-width: 900px) {
    .brand-story-inner {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    h2 {
        font-size: 32px;
    }

    .image-frame img {
        height: 320px;
    }

    .story-stats {
        gap: 24px;
    }
}

@media (max-width: 640px) {
    .brand-story {
        padding: 52px 0;
    }

    h2 {
        font-size: 26px;
    }

    .story-stats {
        gap: 16px;
    }

    .stat strong {
        font-size: 24px;
    }
}
</style>
