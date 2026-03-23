<template>
    <section class="hero-wrap" ref="heroWrap" :class="{ 'hero-in': heroIn }">
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

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const heroWrap = ref(null);
const heroIn   = ref(false);
let observer   = null;

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => { heroIn.value = entry.isIntersecting; },
        { threshold: 0.1 }
    );
    if (heroWrap.value) observer.observe(heroWrap.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

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
    /* start collapsed — animation fires when .hero-in is added */
    clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
}
.hero-wrap.hero-in .hero-banner {
    animation: curtainReveal 1.3s cubic-bezier(0.77, 0, 0.175, 1) forwards;
}

.menu-card {
    position: absolute;
    top: 120px;
    left: 60px;
    background: rgba(255, 255, 255, 0.96);
    padding: 28px 28px 24px;
    width: 220px;
    box-shadow: var(--shadow-card);
    opacity: 0;
}
.hero-wrap.hero-in .menu-card {
    animation: fadeUp 0.7s ease 1.0s forwards;
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
    opacity: 0;
}
.hero-wrap.hero-in .hero-content {
    animation: fadeUp 0.9s ease 1.25s forwards;
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

/* Unique Hero animation: polygon curtain drops open from the top */
@keyframes curtainReveal {
    from { clip-path: polygon(0 0, 100% 0, 100% 0, 0 0); }
    to   { clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); }
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

@media (max-width: 860px) {
    .hero-banner {
        min-height: auto;
        padding: 104px 24px 52px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 22px;
        background-position: center top;
    }

    .menu-card,
    .hero-content {
        position: static;
    }

    .menu-card {
        width: min(100%, 300px);
    }

    .hero-content {
        max-width: min(100%, 520px);
        margin-left: auto;
    }

    .hero-content h1 {
        font-size: 46px;
    }
}

/* Mobile */
@media (max-width: 640px) {
    .hero-banner {
        min-height: 100dvh; /* use dvh on mobile for better viewport handling */
        background-attachment: scroll;
        padding: 88px 20px 40px;
    }

    .menu-card {
        padding: 18px 20px;
    }

    .hero-content {
        max-width: 100%;
        margin-left: 0;
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

    .hero-actions .btn {
        flex: 1 1 180px;
    }
}

@media (max-width: 420px) {
    .menu-card {
        padding: 14px 16px;
    }

    .menu-card h4 { font-size: 16px; margin-bottom: 10px; }

    .menu-card a { font-size: 15px; margin: 8px 0; }

    .hero-content {
        max-width: 100%;
    }

    .hero-content h1 { font-size: 30px; }

    .hero-content p  { font-size: 14px; margin-top: 10px; }

    .hero-actions    { margin-top: 20px; gap: 10px; }

    .btn { padding: 11px 16px; font-size: 12px; }
}
</style>
