<template>
    <section class="testimonials" ref="sectionRef" id="testimonials">
        <div class="container">
            <div class="section-head" :class="{ 'is-visible': isVisible }">
                <span class="kicker">Happy Customers</span>
                <h2>What people are saying</h2>
                <p class="section-desc">Real reviews from real customers who love what they find at minishopkh.</p>
            </div>

            <div class="testimonials-grid" :class="{ 'is-visible': isVisible }">
                <article
                    class="testimonial-card"
                    v-for="(t, i) in testimonials"
                    :key="i"
                    :style="{ '--delay': (i * 0.14) + 's' }"
                >
                    <div class="card-top">
                        <div class="stars" :aria-label="`${t.rating} out of 5 stars`">
                            <span v-for="s in 5" :key="s" :class="s <= t.rating ? 'star-filled' : 'star-empty'">★</span>
                        </div>
                        <span class="verified">✓ Verified</span>
                    </div>

                    <p class="review-text">"{{ t.review }}"</p>

                    <div class="reviewer">
                        <div class="avatar" :style="{ '--avatar-bg': t.color }">
                            {{ t.name[0] }}
                        </div>
                        <div class="reviewer-info">
                            <strong>{{ t.name }}</strong>
                            <span>{{ t.location }}</span>
                        </div>
                        <div class="product-tag">{{ t.product }}</div>
                    </div>
                </article>
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
        { threshold: 0.1 }
    );
    if (sectionRef.value) observer.observe(sectionRef.value);
});

onBeforeUnmount(() => observer?.disconnect());

const testimonials = [
    {
        name: 'Sophea Mok',
        location: 'Phnom Penh, KH',
        rating: 5,
        review: 'The Basic T-Shirt is honestly the softest thing I own. Great quality for the price and arrived super fast. Will definitely be ordering more colors.',
        product: 'Basic T-Shirt',
        color: '#4a7fa5'
    },
    {
        name: 'Dara Chea',
        location: 'Siem Reap, KH',
        rating: 5,
        review: 'Bought the Trail Backpack for a trip and it held up perfectly. Tons of space, looks clean, and the build quality exceeded my expectations at this price point.',
        product: 'Trail Backpack',
        color: '#5e8a6a'
    },
    {
        name: 'Leakena Ros',
        location: 'Battambang, KH',
        rating: 5,
        review: 'My Canvas Tote Bag gets compliments every single time I use it. The checkout was smooth and the ABA PayWay payment worked instantly. Love the experience.',
        product: 'Canvas Tote Bag',
        color: '#8a5e7a'
    },
    {
        name: 'Virak Phorn',
        location: 'Kampot, KH',
        rating: 5,
        review: 'Runner Sneakers are incredibly lightweight. Wore them for a full day walking around and zero discomfort. minishopkh has become my go-to for everyday essentials.',
        product: 'Runner Sneakers',
        color: '#7a6a4a'
    },
    {
        name: 'Chanthy Lim',
        location: 'Phnom Penh, KH',
        rating: 5,
        review: 'The Gym Water Bottle keeps my drinks cold for hours. Looks premium, feels solid, and no leaks at all. Really happy with this purchase!',
        product: 'Gym Water Bottle',
        color: '#4a6a8a'
    },
    {
        name: 'Borey Kim',
        location: 'Kandal, KH',
        rating: 5,
        review: 'Free shipping arrived faster than expected. The Packing Cubes Set made packing my bag so much easier. Great product, great service — what more can you ask for?',
        product: 'Packing Cubes Set',
        color: '#6a7a4a'
    }
];
</script>

<style scoped>
/* ─── Section ─────────────────────────────────── */
.testimonials {
    padding: 88px 0 100px;
    background: #0a0f1a;
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ─── Head ────────────────────────────────────── */
.section-head {
    text-align: center;
    margin-bottom: 56px;
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.section-head.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.28);
    margin-bottom: 14px;
}

h2 {
    margin: 0 0 12px;
    font-size: clamp(28px, 3.5vw, 40px);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
}

.section-desc {
    color: rgba(255,255,255,0.4);
    font-size: 16px;
    margin: 0;
    line-height: 1.5;
}

/* ─── Grid ────────────────────────────────────── */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.testimonial-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.06);
    padding: 28px;
    opacity: 0;
    transform: translateY(24px);
    transition:
        opacity 0.6s ease var(--delay, 0s),
        transform 0.6s ease var(--delay, 0s),
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.testimonials-grid.is-visible .testimonial-card {
    opacity: 1;
    transform: translateY(0);
}

.testimonial-card:hover {
    box-shadow: 0 20px 48px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.12);
}

/* ─── Card top ────────────────────────────────── */
.card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stars {
    display: flex;
    gap: 2px;
}

.star-filled {
    color: #f5a623;
    font-size: 15px;
}

.star-empty {
    color: rgba(255,255,255,0.15);
    font-size: 15px;
}

.verified {
    font-size: 11px;
    font-weight: 700;
    color: #10b981;
    letter-spacing: 0.04em;
}

/* ─── Review text ─────────────────────────────── */
.review-text {
    font-size: 14.5px;
    line-height: 1.7;
    color: rgba(255,255,255,0.65);
    margin: 0 0 24px;
    font-style: italic;
}

/* ─── Reviewer ────────────────────────────────── */
.reviewer {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--avatar-bg);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    flex-shrink: 0;
}

.reviewer-info {
    flex: 1;
}

.reviewer-info strong {
    display: block;
    font-size: 13px;
    color: #ffffff;
    font-weight: 700;
}

.reviewer-info span {
    display: block;
    font-size: 12px;
    color: rgba(255,255,255,0.3);
    margin-top: 2px;
}

.product-tag {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: rgba(255,255,255,0.4);
    background: rgba(255,255,255,0.06);
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 980px) {
    .testimonials-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .testimonials {
        padding: 60px 0 72px;
    }

    .testimonials-grid {
        grid-template-columns: 1fr;
    }

    .product-tag {
        display: none;
    }
}
</style>
