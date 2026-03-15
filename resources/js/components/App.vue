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
            @checkout="checkoutWithBakong"
            @close="isCartOpen = false"
        />

        <BakongCheckoutModal
            :show="bakongModal.show"
            :loading="bakongModal.loading"
            :error="bakongModal.error"
            :qr-image="bakongModal.qrImage"
            :md5="bakongModal.md5"
            :amount="bakongModal.amount"
            :lifetime="300"
            @close="closeBakongModal"
            @paid="onBakongPaid"
            @retry="checkoutWithBakong"
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
import { computed, reactive, ref } from 'vue';
import SiteHeader from './layout/SiteHeader.vue';
import HeroSection from './shop/HeroSection.vue';
import TrendingBanner from './shop/TrendingBanner.vue';
import CollectionsSection from './shop/CollectionsSection.vue';
import ValueStrip from './shop/ValueStrip.vue';
import ProductGrid from './shop/ProductGrid.vue';
import CartPanel from './shop/CartPanel.vue';
import BakongCheckoutModal from './shop/BakongCheckoutModal.vue';
import TestimonialsSection from './shop/TestimonialsSection.vue';
import FAQSection from './shop/FAQSection.vue';
import NewsletterSection from './shop/NewsletterSection.vue';
import WhySection from './shop/WhySection.vue';

const products = ref([
    // For Everyday
    {
        id: 1,
        name: 'Classic Low-Top Sneakers',
        price: 8.00,
        category: 'everyday',
        tagline: 'Sleek everyday sneakers for casual wear',
        image: '/images/products/Classic Low-Top Sneakers.webp'
    },
    {
        id: 2,
        name: 'Urban Slip-On Shoes',
        price: 6.00,
        category: 'everyday',
        tagline: 'Easy on, easy off for your daily routine',
        image: '/images/products/Urban Slip-On Shoes.jpg'
    },
    {
        id: 3,
        name: 'Canvas Street Kicks',
        price: 4.00,
        category: 'everyday',
        tagline: 'Lightweight canvas for all-day comfort',
        image: '/images/products/Canvas Street Kicks.jpg'
    },
    // For Travel
    {
        id: 4,
        name: 'Comfort Walk Loafers',
        price: 9.00,
        category: 'travel',
        tagline: 'Cushioned soles built for long walks',
        image: '/images/products/Comfort Walk Loafers.jpg'
    },
    {
        id: 5,
        name: 'Foldable Travel Flats',
        price: 2.00,
        category: 'travel',
        tagline: 'Pack-friendly flats for any destination',
        image: '/images/products/Foldable Travel Flats.jpg'
    },
    {
        id: 6,
        name: 'Lightweight Mesh Sneakers',
        price: 7.00,
        category: 'travel',
        tagline: 'Breathable mesh perfect for exploring cities',
        image: '/images/products/Lightweight Mesh Sneakers.jpg'
    },
    // For Activity
    {
        id: 7,
        name: 'Pro Runner Sneakers',
        price: 10.00,
        category: 'activity',
        tagline: 'High-performance shoes for serious runners',
        image: '/images/products/Pro Runner Sneakers.jpg'
    },
    {
        id: 8,
        name: 'Training Court Shoes',
        price: 5.00,
        category: 'activity',
        tagline: 'Stable grip for gym and court workouts',
        image: '/images/products/Training Court Shoes.jpg'
    },
    {
        id: 9,
        name: 'Trail Running Shoes',
        price: 1.00,
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

// ── Bakong KHQR checkout ──────────────────────────────────
const bakongModal = reactive({
    show:    false,
    loading: false,
    error:   '',
    qrImage: '',
    md5:     '',
    amount:  0,
});

const checkoutWithBakong = async () => {
    if (!cart.value.length || checkoutLoading.value) {
        return;
    }

    checkoutError.value   = '';
    checkoutLoading.value = true;

    // Open modal in loading state
    bakongModal.show    = true;
    bakongModal.loading = true;
    bakongModal.error   = '';
    bakongModal.qrImage = '';
    bakongModal.md5     = '';
    bakongModal.amount  = 0;

    try {
        const items = cart.value.map((item) => ({
            name:  item.name,
            qty:   item.qty,
            price: item.price,
        }));

        const { data } = await window.axios.post('/api/bakong/checkout', { items });

        bakongModal.loading = false;
        bakongModal.qrImage = data.qr_image  || '';
        bakongModal.md5     = data.md5        || '';
        bakongModal.amount  = data.amount     || 0;

        // Close the cart panel once QR is ready
        isCartOpen.value = false;

    } catch (err) {
        bakongModal.loading = false;
        bakongModal.error   = err?.response?.data?.message
            || 'Could not generate Bakong QR. Please try again.';
    } finally {
        checkoutLoading.value = false;
    }
};

const closeBakongModal = () => {
    bakongModal.show    = false;
    bakongModal.loading = false;
    bakongModal.error   = '';
    bakongModal.qrImage = '';
    bakongModal.md5     = '';
};

const onBakongPaid = () => {
    bakongModal.show = false;
    clearCart();
    window.location.href = '/payment/result?status=0';
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
    /* respect iOS home indicator safe area */
    bottom: max(24px, env(safe-area-inset-bottom, 24px));
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

@media (max-width: 480px) {
    .cart-toggle { right: 16px; }
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
