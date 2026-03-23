<template>
    <section ref="rootRef" class="life-section" :class="{ 'is-visible': isVisible }">
        <div class="life-shell">
            <div class="life-head">
                <span class="life-kicker">Shop By Lifestyle</span>
                <h2>Choose the mood first. We will take you to the right shelf.</h2>
            </div>

            <div class="life-grid">
                <button
                    v-for="(card, index) in cards"
                    :key="card.title"
                    type="button"
                    class="life-card"
                    :style="{ '--card-index': index }"
                    @click="$emit('explore-category', card.category)"
                >
                    <span class="life-step">0{{ index + 1 }}</span>
                    <div class="life-body">
                        <h3>{{ card.title }}</h3>
                        <p>{{ card.description }}</p>
                    </div>
                    <span class="life-arrow">+</span>
                </button>
            </div>
        </div>
    </section>
</template>

<script setup>
import { useRepeatInView } from '../../composables/useRepeatInView';

defineEmits(['explore-category']);

const cards = [
    {
        title: 'Office to After-Hours',
        description: 'Clean daily pairs with enough polish for city movement and long wear.',
        category: 'everyday',
    },
    {
        title: 'Weekend Escape',
        description: 'Pack-light silhouettes built for airports, cafes, and walking-heavy trips.',
        category: 'travel',
    },
    {
        title: 'Gym and Street',
        description: 'Performance-ready pairs with grip, bounce, and a sharper profile.',
        category: 'activity',
    },
    {
        title: 'Easy Rotation',
        description: 'The fastest way back to versatile everyday styles when you want less friction.',
        category: 'everyday',
    },
];

const { rootRef, isVisible } = useRepeatInView({ threshold: 0.18 });
</script>

<style scoped>
.life-section {
    padding: 34px 0 92px;
    background: linear-gradient(180deg, #0a101b 0%, #090e18 100%);
    overflow: hidden;
}

.life-shell {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

.life-head {
    max-width: 660px;
    margin-bottom: 32px;
}

.life-kicker,
.life-head h2 {
    opacity: 0;
    transition: opacity 0.55s ease, transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

.life-kicker {
    display: inline-block;
    margin-bottom: 14px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
    transform: translateY(14px);
}

.life-head h2 {
    margin: 0;
    font-size: clamp(30px, 4vw, 48px);
    letter-spacing: -0.03em;
    color: #fff;
    line-height: 1.08;
    transform: translateY(20px);
    transition-delay: 0.08s;
}

.life-section.is-visible .life-kicker,
.life-section.is-visible .life-head h2 {
    opacity: 1;
    transform: translateY(0);
}

.life-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.life-card {
    position: relative;
    isolation: isolate;
    padding: 26px;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: left;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)),
        #101726;
    border: 1px solid rgba(255, 255, 255, 0.08);
    cursor: pointer;
    overflow: hidden;
    opacity: 0;
    clip-path: inset(0 50% 0 50%);
    transform: perspective(1200px) rotateY(16deg) translateY(24px);
    transition:
        opacity 0.45s ease,
        clip-path 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.25s ease,
        background 0.25s ease;
    transition-delay: calc(var(--card-index) * 0.08s + 0.08s);
}

.life-card:nth-child(even) {
    transform: perspective(1200px) rotateY(-16deg) translateY(24px);
}

.life-card::before {
    content: '';
    position: absolute;
    inset: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    opacity: 0;
    transform: scale(1.06);
    transition: opacity 0.3s ease, transform 0.45s ease;
}

.life-section.is-visible .life-card {
    opacity: 1;
    clip-path: inset(0 0 0 0);
    transform: perspective(1200px) rotateY(0deg) translateY(0);
}

.life-card:hover {
    border-color: rgba(255, 255, 255, 0.16);
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03)),
        #141c2d;
}

.life-card:hover::before {
    opacity: 1;
    transform: scale(1);
}

.life-step {
    font-size: 58px;
    line-height: 1;
    letter-spacing: -0.05em;
    color: rgba(255, 255, 255, 0.12);
    font-weight: 800;
}

.life-body h3 {
    margin: 0 0 10px;
    font-size: 26px;
    line-height: 1.05;
    letter-spacing: -0.03em;
    color: #fff;
    max-width: 280px;
}

.life-body p {
    margin: 0;
    max-width: 330px;
    font-size: 14px;
    line-height: 1.75;
    color: rgba(255, 255, 255, 0.44);
}

.life-arrow {
    position: absolute;
    right: 24px;
    bottom: 22px;
    font-size: 22px;
    color: rgba(255, 255, 255, 0.4);
    transition: transform 0.3s ease, color 0.3s ease;
}

.life-card:hover .life-arrow {
    color: #fff;
    transform: rotate(90deg) scale(1.1);
}

@media (max-width: 860px) {
    .life-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .life-shell {
        padding: 0 16px;
    }

    .life-card {
        min-height: 190px;
        padding: 20px;
    }

    .life-body h3 {
        font-size: 22px;
    }

    .life-step {
        font-size: 44px;
    }
}
</style>
