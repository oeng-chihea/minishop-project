<template>
    <div>
        <SiteHeader />
        <HeroSection />
        <TrendingBanner />
        <CollectionsSection id="collections" />
        <ValueStrip id="values" />

        <ProductGrid :products="products" @add="addToCart" />

        <WhySection id="why-us" />

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

        <button type="button" class="cart-toggle" :class="{ 'is-open': isCartOpen }" aria-label="Toggle cart" @click="isCartOpen = !isCartOpen">
            <!-- Bag icon -->
            <svg v-if="!isCartOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <!-- Close icon -->
            <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            <span v-if="!isCartOpen && totalItems > 0" class="cart-badge">{{ totalItems }}</span>
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
import WhySection from './shop/WhySection.vue';

const products = ref([
    // For Everyday
    {
        id: 1,
        name: 'Classic Low-Top Sneakers',
        price: 79.00,
        category: 'everyday',
        tagline: 'Sleek everyday sneakers for casual wear',
        image: '/images/products/Classic Low-Top Sneakers.webp'
    },
    {
        id: 2,
        name: 'Urban Slip-On Shoes',
        price: 64.00,
        category: 'everyday',
        tagline: 'Easy on, easy off for your daily routine',
        image: '/images/products/Urban Slip-On Shoes.jpg'
    },
    {
        id: 3,
        name: 'Canvas Street Kicks',
        price: 55.00,
        category: 'everyday',
        tagline: 'Lightweight canvas for all-day comfort',
        image: '/images/products/Canvas Street Kicks.jpg'
    },
    // For Travel
    {
        id: 4,
        name: 'Comfort Walk Loafers',
        price: 89.00,
        category: 'travel',
        tagline: 'Cushioned soles built for long walks',
        image: '/images/products/Comfort Walk Loafers.jpg'
    },
    {
        id: 5,
        name: 'Foldable Travel Flats',
        price: 48.00,
        category: 'travel',
        tagline: 'Pack-friendly flats for any destination',
        image: '/images/products/Foldable Travel Flats.jpg'
    },
    {
        id: 6,
        name: 'Lightweight Mesh Sneakers',
        price: 72.00,
        category: 'travel',
        tagline: 'Breathable mesh perfect for exploring cities',
        image: '/images/products/Lightweight Mesh Sneakers.jpg'
    },
    // For Activity
    {
        id: 7,
        name: 'Pro Runner Sneakers',
        price: 119.00,
        category: 'activity',
        tagline: 'High-performance shoes for serious runners',
        image: '/images/products/Pro Runner Sneakers.jpg'
    },
    {
        id: 8,
        name: 'Training Court Shoes',
        price: 95.00,
        category: 'activity',
        tagline: 'Stable grip for gym and court workouts',
        image: '/images/products/Training Court Shoes.jpg'
    },
    {
        id: 9,
        name: 'Trail Running Shoes',
        price: 109.00,
        category: 'activity',
        tagline: 'Rugged outsole for off-road adventures',
        image: '/images/products/Trail Running Shoes.jpg'
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
.cart-toggle {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border: 0;
    border-radius: 50%;
    background: #ffffff;
    color: #080d16;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
    transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.cart-toggle:hover {
    background: rgba(255, 255, 255, 0.88);
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.55);
}

.cart-toggle.is-open {
    background: #1a2234;
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.cart-toggle.is-open:hover {
    background: #222d44;
}

.cart-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 9px;
    background: #ef4444;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    line-height: 18px;
    text-align: center;
    letter-spacing: 0;
    pointer-events: none;
}
</style>
