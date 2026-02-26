<template>
    <section class="container hero-wrap">
        <div class="hero-banner">
            <aside class="menu-card">
                <h4>Featured</h4>
                <a href="#shop">Men's Shoes</a>
                <a href="#shop">Women's Shoes</a>
                <a href="#shop">New Arrivals</a>
            </aside>

            <div class="hero-content">
                <h1>New Colors, Unearthed</h1>
                <p>Curated drops inspired by natural textures and everyday comfort.</p>
                <div class="hero-actions">
                    <a href="#shop" class="btn btn-solid">Shop Men</a>
                    <a href="#shop" class="btn btn-outline">Shop Women</a>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 22px;
}

.hero-wrap {
    margin-top: 18px;
}

.hero-banner {
    position: relative;
    border-radius: 4px;
    min-height: 540px;
    overflow: hidden;
    background-image:
        linear-gradient(90deg, rgba(59, 42, 26, 0.68) 0%, rgba(59, 42, 26, 0.24) 45%, rgba(59, 42, 26, 0.28) 100%),
        url('https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
    box-shadow: var(--shadow-hero);
}

.menu-card {
    position: absolute;
    top: 98px;
    left: 44px;
    background: rgba(255, 255, 255, 0.96);
    padding: 28px 28px 24px;
    width: 220px;
    box-shadow: var(--shadow-card);
    animation: fadeUp 0.7s ease;
}

.menu-card h4 {
    margin: 0 0 14px;
    font-size: 20px;
    color: var(--color-text);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.menu-card a {
    display: flex;
    text-decoration: none;
    color: var(--color-text);
    font-size: 17px;
    margin: 10px 0;
    font-weight: 500;
}

.hero-content {
    position: absolute;
    right: 46px;
    bottom: 56px;
    max-width: 480px;
    color: #fff;
    text-align: left;
    animation: fadeUp 0.9s ease;
}

.hero-content h1 {
    margin: 0;
    font-size: 62px;
    line-height: 1.05;
    letter-spacing: -0.03em;
}

.hero-content p {
    margin: 10px 0 0;
    font-size: 22px;
    color: rgba(255, 255, 255, 0.92);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border-radius: 2px;
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.hero-actions {
    margin-top: 20px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-solid {
    background: #ffffff;
    color: var(--color-text);
}

.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.7);
    color: #fff;
    background: #fff;
}

.btn-outline {
    background: transparent;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 900px) {
    .hero-banner {
        min-height: 500px;
    }

    .menu-card {
        position: static;
        width: auto;
        margin: 24px;
    }

    .hero-content {
        left: 24px;
        right: 24px;
        bottom: 24px;
    }

    .hero-content h1 {
        font-size: 42px;
    }

    .hero-content p {
        font-size: 18px;
    }
}
</style>
