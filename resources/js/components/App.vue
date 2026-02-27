<template>
    <div>
        <SiteHeader />
        <HeroSection />
        <TrendingBanner />
        <CollectionsSection id="collections" />
        <ValueStrip id="values" />

        <main class="container main-single" id="shop">
            <ProductGrid :products="products" @add="addToCart" />
        </main>

        <section class="container why" id="why-us">
            <article class="why-card reveal-card" style="--reveal-delay: 0s">
                <div class="why-icon">✦</div>
                <h3>Premium Quality</h3>
                <p>Every item in our collection passes a strict quality check. We source only soft-touch fabrics, durable builds, and materials that hold up beautifully over time.</p>
            </article>
            <article class="why-card reveal-card" style="--reveal-delay: 0.1s">
                <div class="why-icon">◈</div>
                <h3>Style for Everyone</h3>
                <p>From street-ready essentials to travel companions and active gear, our curated range covers every lifestyle, occasion, and budget.</p>
            </article>
            <article class="why-card reveal-card" style="--reveal-delay: 0.2s">
                <div class="why-icon">⬡</div>
                <h3>Fast &amp; Secure Checkout</h3>
                <p>Pay safely with ABA PayWay — Cambodia's most trusted payment platform. Your data is encrypted and your order confirmed in seconds.</p>
            </article>
        </section>

        <TestimonialsSection />
        <FAQSection />

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

        <NewsletterSection id="newsletter" />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import SiteHeader from './layout/SiteHeader.vue';
import HeroSection from './shop/HeroSection.vue';
import TrendingBanner from './shop/TrendingBanner.vue';
import CollectionsSection from './shop/CollectionsSection.vue';
import ValueStrip from './shop/ValueStrip.vue';
import ProductGrid from './shop/ProductGrid.vue';
import CartPanel from './shop/CartPanel.vue';
import TestimonialsSection from './shop/TestimonialsSection.vue';
import FAQSection from './shop/FAQSection.vue';
import NewsletterSection from './shop/NewsletterSection.vue';

const products = ref([
    // For Everyday
    {
        id: 1,
        name: 'Classic Low-Top Sneakers',
        price: 79.00,
        category: 'everyday',
        tagline: 'Sleek everyday sneakers for casual wear',
        image: 'https://via.placeholder.com/480x360/dae2ec/1f3a5f?text=Classic+Low-Top'
    },
    {
        id: 2,
        name: 'Urban Slip-On Shoes',
        price: 64.00,
        category: 'everyday',
        tagline: 'Easy on, easy off for your daily routine',
        image: 'https://via.placeholder.com/480x360/e5ebf2/1f3a5f?text=Urban+Slip-On'
    },
    {
        id: 3,
        name: 'Canvas Street Kicks',
        price: 55.00,
        category: 'everyday',
        tagline: 'Lightweight canvas for all-day comfort',
        image: 'https://via.placeholder.com/480x360/dfe7f0/1f3a5f?text=Canvas+Street+Kicks'
    },
    // For Travel
    {
        id: 4,
        name: 'Comfort Walk Loafers',
        price: 89.00,
        category: 'travel',
        tagline: 'Cushioned soles built for long walks',
        image: 'https://via.placeholder.com/480x360/e8edf3/1f3a5f?text=Comfort+Walk+Loafers'
    },
    {
        id: 5,
        name: 'Foldable Travel Flats',
        price: 48.00,
        category: 'travel',
        tagline: 'Pack-friendly flats for any destination',
        image: 'https://via.placeholder.com/480x360/d6e0ea/1f3a5f?text=Travel+Flats'
    },
    {
        id: 6,
        name: 'Lightweight Mesh Sneakers',
        price: 72.00,
        category: 'travel',
        tagline: 'Breathable mesh perfect for exploring cities',
        image: 'https://via.placeholder.com/480x360/dde5f0/1f3a5f?text=Mesh+Sneakers'
    },
    // For Activity
    {
        id: 7,
        name: 'Pro Runner Sneakers',
        price: 119.00,
        category: 'activity',
        tagline: 'High-performance shoes for serious runners',
        image: 'https://via.placeholder.com/480x360/d6e0ea/1f3a5f?text=Pro+Runner+Sneakers'
    },
    {
        id: 8,
        name: 'Training Court Shoes',
        price: 95.00,
        category: 'activity',
        tagline: 'Stable grip for gym and court workouts',
        image: 'https://via.placeholder.com/480x360/d9e3ee/1f3a5f?text=Training+Court+Shoes'
    },
    {
        id: 9,
        name: 'Trail Running Shoes',
        price: 109.00,
        category: 'activity',
        tagline: 'Rugged outsole for off-road adventures',
        image: 'https://via.placeholder.com/480x360/dae4ef/1f3a5f?text=Trail+Running+Shoes'
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

const isMobileDevice = () => /Android|iPhone|iPad|iPod|Mobile/i.test(window.navigator.userAgent);

const openQrFallback = (checkoutData) => {
    if (!checkoutData?.qrImage) {
        return false;
    }

    const popup = window.open('', '_blank');

    if (!popup) {
        return false;
    }

    popup.document.write(`
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width,initial-scale=1" />
            <title>ABA PayWay Checkout</title>
            <style>
                body{font-family:Segoe UI,Roboto,Arial,sans-serif;background:#f5f8fc;color:#10233f;margin:0;padding:24px;text-align:center}
                .card{max-width:440px;margin:0 auto;background:#fff;border:1px solid #d6deea;padding:20px;border-radius:8px}
                img{max-width:100%;height:auto;border:1px solid #d6deea}
                a{display:inline-block;margin-top:14px;padding:10px 14px;background:#2f4f7f;color:#fff;text-decoration:none;border-radius:4px;font-weight:600}
                p{margin:8px 0;color:#4a596d}
            </style>
        </head>
        <body>
            <div class="card">
                <h2>Complete payment with ABA</h2>
                <p>Scan this QR in ABA Mobile app to pay.</p>
                <img src="${checkoutData.qrImage}" alt="ABA payment QR" />
                ${checkoutData.abapay_deeplink ? `<a href="${checkoutData.abapay_deeplink}">Open ABA App</a>` : ''}
            </div>
        </body>
        </html>
    `);
    popup.document.close();

    return true;
};

const checkoutWithAba = async () => {
    if (!cart.value.length || checkoutLoading.value) {
        return;
    }

    checkoutError.value  = '';
    checkoutLoading.value = true;

    try {
        const items = cart.value.map((item) => ({
            name:  item.name,
            qty:   item.qty,
            price: item.price,
        }));

        const { data } = await window.axios.post('/api/checkout', { items });

        const deepLink       = data?.abapay_deeplink || '';
        const webCheckoutUrl = data?.checkout_url   || '';
        const hasQr          = !!(data?.qrImage);
        const isSandbox      = (data?.environment || '').toLowerCase() === 'sandbox';
        const onMobile       = isMobileDevice();

        checkoutLoading.value = false;

        if (webCheckoutUrl && /^https?:\/\//i.test(webCheckoutUrl)) {
            window.location.href = webCheckoutUrl;
            return;
        }

        if (onMobile && deepLink) {
            window.location.href = deepLink;

            if (hasQr) {
                setTimeout(() => {
                    if (document.visibilityState === 'visible') {
                        openQrFallback(data);
                        checkoutError.value = isSandbox
                            ? 'Sandbox payment opened. If ABA app shows "transaction not found", test with the sandbox-compatible ABA environment.'
                            : 'If ABA app did not open, use the QR page to complete payment.';
                    }
                }, 1400);
            }

            return;
        }

        if (hasQr && openQrFallback(data)) {
            checkoutError.value = isSandbox
                ? 'Scan the QR code in your ABA Mobile app to pay. (Sandbox mode)'
                : 'Scan the QR code in your ABA Mobile app to pay.';
            return;
        }

        checkoutError.value = 'Could not open ABA checkout. Please try again.';

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

/* Why section */
.why {
    margin-top: 24px;
    margin-bottom: 0;
    padding-bottom: 60px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.why-card {
    background: var(--color-surface);
    border: 1px solid var(--color-line);
    padding: 28px 22px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: revealCard 0.6s ease var(--reveal-delay, 0s) forwards;
}

@keyframes revealCard {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.why-icon {
    font-size: 22px;
    color: var(--color-accent);
    margin-bottom: 14px;
    display: block;
    line-height: 1;
}

.why h3 {
    margin: 0 0 10px;
    font-size: 20px;
    letter-spacing: -0.01em;
}

.why p {
    margin: 0;
    color: var(--color-muted);
    line-height: 1.6;
    font-size: 14px;
}

@media (max-width: 860px) {
    .why {
        grid-template-columns: 1fr;
    }
}
</style>
