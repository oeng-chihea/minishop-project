<template>
    <section class="strip-wrap">
        <div class="container strip fade-in-up">
            <article v-for="item in values" :key="item.title">
                <h4>{{ item.title }}</h4>
                <p>{{ item.description }}</p>
            </article>
        </div>
    </section>
</template>

<script setup>
const values = [
    {
        title: 'Free Shipping',
        description: 'Free standard shipping for all orders over $100.'
    },
    {
        title: 'Easy Returns',
        description: '30-day returns with simple pickup and refund process.'
    },
    {
        title: 'Secure Checkout',
        description: 'Protected payment flow with trusted checkout providers.'
    },
    {
        title: 'Sustainable Materials',
        description: 'Low-impact fabrics and mindful packaging choices.'
    }
];
</script>

<style scoped>
.strip-wrap {
    margin-top: 24px;
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    border: 1px solid var(--color-line);
    background: var(--color-surface);
}

.strip article {
    padding: 18px;
    border-left: 1px solid var(--color-line);
}

.strip article:first-child {
    border-left: 0;
}

.strip h4 {
    margin: 0;
    color: var(--color-text);
    font-size: 16px;
}

.strip p {
    margin: 8px 0 0;
    color: var(--color-muted);
    font-size: 14px;
    line-height: 1.45;
}

@media (max-width: 980px) {
    .strip {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .strip article:nth-child(3) {
        border-left: 0;
    }
}

@media (max-width: 640px) {
    .strip {
        grid-template-columns: 1fr;
    }

    .strip article {
        border-left: 0;
        border-top: 1px solid var(--color-line);
    }

    .strip article:first-child {
        border-top: 0;
    }
}
</style>
