<template>
    <header>
        <div class="promo-bar">
            Free shipping over $100 • New arrivals are live now
            <a href="#shop" @click="scrollToSection($event, 'shop')">Shop now</a>
        </div>

        <div class="site-header">
            <div class="container header-inner">
                <nav class="left-nav">
                    <a href="#shop" @click="scrollToSection($event, 'shop')">Men</a>
                    <a href="#collections" @click="scrollToSection($event, 'collections')">Women</a>
                    <a href="#values" @click="scrollToSection($event, 'values')">Kids</a>
                    <a href="#newsletter" @click="scrollToSection($event, 'newsletter')">Sale</a>
                </nav>

                <a href="#" class="brand">NovaStore</a>

                <nav class="right-nav">
                    <a href="#why-us" @click="scrollToSection($event, 'why-us')">Sustainability</a>
                    <a href="#why-us" @click="scrollToSection($event, 'why-us')">Stores</a>
                    <button type="button" aria-label="Search">⌕</button>
                    <button type="button" aria-label="Cart">🛒</button>
                </nav>
            </div>
        </div>
    </header>
</template>

<script setup>
const scrollToSection = (event, sectionId) => {
    event.preventDefault();

    const target = document.getElementById(sectionId);

    if (!target) {
        return;
    }

    const stickyHeader = document.querySelector('.site-header');
    const headerOffset = stickyHeader ? stickyHeader.offsetHeight + 10 : 82;
    const targetTop = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

    window.scrollTo({
        top: targetTop,
        behavior: 'smooth'
    });
};
</script>

<style scoped>
.promo-bar {
    background: var(--color-primary);
    color: #fff;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.01em;
    padding: 8px 12px;
}

.promo-bar a {
    color: #e6eefb;
    margin-left: 6px;
}

.site-header {
    position: sticky;
    top: 0;
    z-index: 30;
    backdrop-filter: blur(8px);
    background: rgba(255, 255, 255, 0.88);
    border-bottom: 1px solid var(--color-line);
}

.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.header-inner {
    min-height: 72px;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
}

.brand {
    font-size: 34px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--color-text);
    text-decoration: none;
    justify-self: center;
}

.left-nav,
.right-nav {
    display: flex;
    align-items: center;
    gap: 16px;
}

.left-nav a,
.right-nav a {
    text-decoration: none;
    color: var(--color-text);
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.right-nav {
    justify-content: flex-end;
}

.right-nav button {
    border: 0;
    background: transparent;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
}

@media (max-width: 980px) {
    .left-nav a:nth-child(3),
    .left-nav a:nth-child(4),
    .right-nav a {
        display: none;
    }

    .brand {
        font-size: 28px;
    }
}

@media (max-width: 640px) {
    .header-inner {
        grid-template-columns: 1fr auto;
    }

    .left-nav {
        display: none;
    }
}
</style>
