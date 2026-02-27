<template>
    <section class="hero-wrap">
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
.hero-wrap {
    width: 100%;
    margin: 0;
    padding: 0;
}

.hero-banner {
    position: relative;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    background-image:
        linear-gradient(180deg, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.35) 60%, rgba(0, 0, 0, 0.6) 100%),
        linear-gradient(90deg, rgba(59, 42, 26, 0.55) 0%, rgba(59, 42, 26, 0.10) 50%, transparent 100%),
        url('https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1920&q=85');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

.menu-card {
    position: absolute;
    top: 120px;
    left: 60px;
    background: rgba(255, 255, 255, 0.96);
    padding: 28px 28px 24px;
    width: 220px;
    box-shadow: var(--shadow-card);
    animation: fadeUp 0.7s ease both;
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
    transition: color 0.2s ease, transform 0.2s ease;
}

.menu-card a:hover {
    color: var(--color-primary);
    transform: translateX(4px);
    filter: none;
}

.hero-content {
    position: absolute;
    right: 80px;
    bottom: 100px;
    max-width: 520px;
    color: #fff;
    text-align: left;
    animation: fadeUp 0.9s ease 0.2s both;
}

.hero-content h1 {
    margin: 0;
    font-size: 72px;
    line-height: 1.02;
    letter-spacing: -0.03em;
    text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}

.hero-content p {
    margin: 14px 0 0;
    font-size: 22px;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.5;
    text-shadow: 0 1px 8px rgba(0,0,0,0.25);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border-radius: 2px;
    padding: 14px 28px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    filter: none;
}

.hero-actions {
    margin-top: 28px;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.btn-solid {
    background: #ffffff;
    color: var(--color-text);
}

.btn-outline {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.8);
    color: #fff;
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.12);
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Tablet */
@media (max-width: 980px) {
    .hero-banner {
        min-height: 100vh;
        background-attachment: scroll;
    }

    .menu-card {
        top: 80px;
        left: 32px;
        width: 190px;
        padding: 22px;
    }

    .hero-content {
        right: 32px;
        bottom: 80px;
        max-width: 420px;
    }

    .hero-content h1 {
        font-size: 52px;
    }

    .hero-content p {
        font-size: 18px;
    }
}

/* Mobile */
@media (max-width: 640px) {
    .hero-banner {
        min-height: 100vh;
        background-attachment: scroll;
    }

    .menu-card {
        position: absolute;
        top: 60px;
        left: 20px;
        right: 20px;
        width: auto;
        padding: 18px 20px;
    }

    .hero-content {
        left: 20px;
        right: 20px;
        bottom: 60px;
        max-width: 100%;
    }

    .hero-content h1 {
        font-size: 38px;
    }

    .hero-content p {
        font-size: 16px;
    }

    .btn {
        padding: 12px 20px;
        font-size: 13px;
    }
}
</style>
