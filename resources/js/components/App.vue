<template>
    <div>
        <SiteHeader />
        <HeroSection />
        <CollectionsSection />
        <ValueStrip />

        <main class="container main-single">
            <ProductGrid :products="products" @add="addToCart" />
        </main>

        <CartPanel
            :open="isCartOpen"
            :items="cart"
            :total-items="totalItems"
            :total-price="totalPrice"
            :slide-trigger="cartSlideTrigger"
            :checkout-loading="checkoutLoading"
            :checkout-error="checkoutError"
            @remove-one="removeOne"
            @add-one="addOne"
            @remove-item="removeItem"
            @clear="clearCart"
            @checkout="checkoutWithAba"
            @close="isCartOpen = false"
        />

        <button type="button" class="cart-toggle" @click="isCartOpen = !isCartOpen">
            {{ isCartOpen ? 'Close Cart' : `Cart (${totalItems})` }}
        </button>

        <section class="container why" id="why-us">
            <article>
                <h3>Premium materials</h3>
                <p>Designed with soft-touch fabrics and durable details for daily wear.</p>
            </article>
            <article>
                <h3>Modern component flow</h3>
                <p>Modular Vue components keep the storefront easy to expand and maintain.</p>
            </article>
            <article>
                <h3>Checkout-ready foundation</h3>
                <p>This layout is ready for Laravel APIs, payment integration, and real product records.</p>
            </article>
        </section>

        <NewsletterSection />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import SiteHeader from './layout/SiteHeader.vue';
import HeroSection from './shop/HeroSection.vue';
import CollectionsSection from './shop/CollectionsSection.vue';
import ValueStrip from './shop/ValueStrip.vue';
import ProductGrid from './shop/ProductGrid.vue';
import CartPanel from './shop/CartPanel.vue';
import NewsletterSection from './shop/NewsletterSection.vue';

const products = ref([
    {
        id: 1,
        name: 'Basic T-Shirt',
        price: 19.00,
        tagline: 'Everyday essential cotton fit',
        image: 'https://via.placeholder.com/480x360/dae2ec/1f3a5f?text=Basic+T-Shirt'
    },
    {
        id: 2,
        name: 'Canvas Tote Bag',
        price: 24.00,
        tagline: 'Durable carryall for your daily run',
        image: 'https://via.placeholder.com/480x360/e5ebf2/1f3a5f?text=Canvas+Tote+Bag'
    },
    {
        id: 3,
        name: 'Coffee Mug',
        price: 14.00,
        tagline: 'Minimal mug for your morning routine',
        image: 'https://via.placeholder.com/480x360/dfe7f0/1f3a5f?text=Coffee+Mug'
    },
    {
        id: 4,
        name: 'Runner Sneakers',
        price: 89.00,
        tagline: 'Lightweight pair for city movement',
        image: 'https://via.placeholder.com/480x360/d6e0ea/1f3a5f?text=Runner+Sneakers'
    },
    {
        id: 5,
        name: 'Trail Backpack',
        price: 68.00,
        tagline: 'Organized storage for day trips',
        image: 'https://via.placeholder.com/480x360/e8edf3/1f3a5f?text=Trail+Backpack'
    },
    {
        id: 6,
        name: 'Comfy Socks',
        price: 12.00,
        tagline: 'Breathable comfort for all-day use',
        image: 'https://via.placeholder.com/480x360/d9e3ee/1f3a5f?text=Comfy+Socks'
    }
]);

const cart = ref([]);
const cartSlideTrigger = ref(0);
const isCartOpen = ref(false);
const checkoutLoading = ref(false);
const checkoutError  = ref('');

const addToCart = (product) => {
    cartSlideTrigger.value += 1;
    isCartOpen.value = true;

    const existing = cart.value.find((item) => item.id === product.id);

    if (existing) {
        existing.qty += 1;
        return;
    }

    cart.value.push({ ...product, qty: 1 });
};

const removeOne = (productId) => {
    const index = cart.value.findIndex((item) => item.id === productId);

    if (index === -1) {
        return;
    }

    if (cart.value[index].qty > 1) {
        cart.value[index].qty -= 1;
        return;
    }

    cart.value.splice(index, 1);
};

const addOne = (productId) => {
    const index = cart.value.findIndex((item) => item.id === productId);

    if (index === -1) {
        return;
    }

    cart.value[index].qty += 1;
};

const removeItem = (productId) => {
    const index = cart.value.findIndex((item) => item.id === productId);

    if (index === -1) {
        return;
    }

    cart.value.splice(index, 1);
};

const clearCart = () => {
    cart.value = [];
};

const checkoutWithAba = async () => {
    if (!cart.value.length || checkoutLoading.value) {
        return;
    }

    checkoutError.value  = '';
    checkoutLoading.value = true;

    try {
        // Build the item payload expected by the Laravel controller
        const items = cart.value.map((item) => ({
            name:  item.name,
            qty:   item.qty,
            price: item.price,
        }));

        const { data } = await window.axios.post('/api/checkout', { items });

        // Build a hidden form and auto-submit it to ABA PayWay checkout page
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = data.checkout_url;

        const fields = [
            'merchant_id', 'tran_id', 'amount', 'items', 'currency',
            'type', 'payment_option', 'req_time', 'return_url', 'cancel_url',
            'continue_success_url', 'skip_success_page', 'lifetime', 'hash',
        ];

        fields.forEach((key) => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = key;
            input.value = data[key];
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();

    } catch (err) {
        checkoutLoading.value = false;
        checkoutError.value   = err?.response?.data?.message
            || 'Could not initiate checkout. Please try again.';
    }
};

const totalItems = computed(() =>
    cart.value.reduce((sum, item) => sum + item.qty, 0)
);

const totalPrice = computed(() =>
    cart.value.reduce((sum, item) => sum + item.qty * item.price, 0)
);
</script>

<style scoped>
.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.main-single {
    margin-top: 26px;
}

.cart-toggle {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 60;
    border: 0;
    border-radius: 2px;
    background: var(--color-primary);
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 16px;
    cursor: pointer;
    box-shadow: var(--shadow-card);
}

.cart-toggle:hover {
    background: var(--color-primary-strong);
}

.why {
    margin-top: 24px;
    margin-bottom: 60px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.why article {
    background: var(--color-surface);
    border: 1px solid var(--color-line);
    border-radius: 0;
    padding: 18px;
    box-shadow: var(--shadow-card);
}

.why h3 {
    margin: 0;
    font-size: 21px;
}

.why p {
    margin: 10px 0 0;
    color: var(--color-muted);
    line-height: 1.5;
}

@media (max-width: 860px) {
    .why {
        grid-template-columns: 1fr;
    }
}
</style>
