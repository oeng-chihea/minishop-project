<template>
    <section class="faq-section" ref="sectionRef" id="faq">
        <div class="container">
            <div class="faq-inner" :class="{ 'is-visible': isVisible }">
                <div class="faq-head">
                    <span class="kicker">Help Center</span>
                    <h2>Frequently Asked Questions</h2>
                    <p>Everything you need to know about shopping at minishopkh.</p>
                </div>

                <div class="faq-list">
                    <div
                        class="faq-item"
                        v-for="(item, i) in faqs"
                        :key="i"
                        :style="{ '--delay': (i * 0.08) + 's' }"
                        :class="{ open: openIndex === i }"
                    >
                        <button class="faq-question" @click="toggle(i)" :aria-expanded="openIndex === i">
                            <span>{{ item.question }}</span>
                            <span class="faq-icon">
                                <svg
                                    viewBox="0 0 24 24"
                                    width="18"
                                    height="18"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    :style="{ transform: openIndex === i ? 'rotate(45deg)' : 'rotate(0deg)', transition: 'transform 0.35s ease' }"
                                >
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </span>
                        </button>
                        <div class="faq-answer" :class="{ open: openIndex === i }">
                            <p>{{ item.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const sectionRef = ref(null);
const isVisible = ref(false);
const openIndex = ref(null);
let observer;

const toggle = (i) => {
    openIndex.value = openIndex.value === i ? null : i;
};

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

const faqs = [
    {
        question: 'How long does shipping take?',
        answer: 'Standard shipping takes 3–5 business days within Cambodia. Orders over $100 qualify for free shipping. Express delivery (1–2 days) is available at checkout for an additional fee.'
    },
    {
        question: 'What is your return policy?',
        answer: 'We offer a 30-day hassle-free return policy on all items in original condition. Simply contact our support team and we will guide you through the return process. Refunds are processed within 5–7 business days.'
    },
    {
        question: 'What payment methods do you accept?',
        answer: 'We accept payments via ABA PayWay — Cambodia\'s most trusted payment platform. You can pay using ABA Mobile, KHQR, Visa, Mastercard, and more through a secure checkout flow.'
    },
    {
        question: 'How do I track my order?',
        answer: 'Once your order ships, you will receive a confirmation email with a tracking number. You can use this number on our delivery partner\'s website to monitor your package in real time.'
    },
    {
        question: 'Are your products authentic?',
        answer: 'Absolutely. Every product in our collection is sourced directly from trusted suppliers and goes through a quality check before being listed. We stand behind every item we sell.'
    },
    {
        question: 'Can I change or cancel my order?',
        answer: 'Orders can be changed or cancelled within 2 hours of placing them. After that, the order enters our fulfilment process and changes may not be possible. Contact our support team as soon as possible and we will do our best to help.'
    }
];
</script>

<style scoped>
.faq-section {
    padding: 80px 0;
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.faq-inner {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.faq-inner.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.faq-head {
    text-align: center;
    margin-bottom: 52px;
}

.kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--color-accent);
    margin-bottom: 12px;
}

h2 {
    margin: 0 0 12px;
    font-size: 40px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--color-text);
}

.faq-head p {
    color: var(--color-muted);
    font-size: 16px;
    margin: 0;
}

/* List */
.faq-list {
    max-width: 760px;
    margin: 0 auto;
}

.faq-item {
    border-bottom: 1px solid var(--color-line);
    opacity: 0;
    transform: translateY(12px);
    transition:
        opacity 0.5s ease var(--delay, 0s),
        transform 0.5s ease var(--delay, 0s);
}

.faq-inner.is-visible .faq-item {
    opacity: 1;
    transform: translateY(0);
}

.faq-item:first-child {
    border-top: 1px solid var(--color-line);
}

/* Question button */
.faq-question {
    width: 100%;
    background: transparent;
    border: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 0;
    text-align: left;
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text);
    cursor: pointer;
    font-family: inherit;
    transition: color 0.2s ease;
}

.faq-question:hover {
    color: var(--color-primary);
    filter: none;
}

.faq-item.open .faq-question {
    color: var(--color-primary);
}

.faq-icon {
    flex-shrink: 0;
    color: var(--color-muted);
    display: flex;
    align-items: center;
    transition: color 0.25s ease;
}

.faq-item.open .faq-icon {
    color: var(--color-primary);
}

/* Answer */
.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1);
}

.faq-answer.open {
    max-height: 220px;
}

.faq-answer p {
    margin: 0;
    padding: 0 0 22px;
    color: var(--color-muted);
    font-size: 15px;
    line-height: 1.7;
}

@media (max-width: 640px) {
    .faq-section {
        padding: 52px 0;
    }

    h2 {
        font-size: 28px;
    }

    .faq-question {
        font-size: 15px;
        padding: 18px 0;
    }
}
</style>
