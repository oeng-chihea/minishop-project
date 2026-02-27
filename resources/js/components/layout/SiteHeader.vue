<template>
    <header>
        <div class="site-header">
            <div class="container header-inner">
                <nav class="left-nav">
                    <a href="#shop" @click="scrollToSection($event, 'shop')">Men</a>
                    <a href="#collections" @click="scrollToSection($event, 'collections')">Women</a>
                    <a href="#values" @click="scrollToSection($event, 'values')">Kids</a>
                    <a href="#newsletter" @click="scrollToSection($event, 'newsletter')">Sale</a>
                </nav>

                <a href="#" class="brand" aria-label="minishopkh home">
                    <span class="brand-text">{{ displayText }}</span><span class="brand-cursor">|</span>
                </a>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const displayText = ref('');
const fullText    = 'minishopkh';

const TYPE_SPEED          = 100;   // ms per character while typing
const DELETE_SPEED        = 55;    // ms per character while deleting
const PAUSE_AFTER_TYPED   = 2000;  // pause once fully typed
const PAUSE_AFTER_DELETED = 500;   // pause once fully deleted

let charIndex  = 0;
let isDeleting = false;
let timerId    = null;
let stopped    = false;

const tick = () => {
    if (stopped) return;

    if (!isDeleting) {
        charIndex++;
        displayText.value = fullText.slice(0, charIndex);

        if (charIndex === fullText.length) {
            isDeleting = true;
            timerId = setTimeout(tick, PAUSE_AFTER_TYPED);
        } else {
            timerId = setTimeout(tick, TYPE_SPEED);
        }
    } else {
        charIndex--;
        displayText.value = fullText.slice(0, charIndex);

        if (charIndex === 0) {
            isDeleting = false;
            timerId = setTimeout(tick, PAUSE_AFTER_DELETED);
        } else {
            timerId = setTimeout(tick, DELETE_SPEED);
        }
    }
};

const scrollToSection = (event, sectionId) => {
    event.preventDefault();

    const target = document.getElementById(sectionId);

    if (!target) return;

    const stickyHeader = document.querySelector('.site-header');
    const headerOffset = stickyHeader ? stickyHeader.offsetHeight + 10 : 72;
    const targetTop    = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

    window.scrollTo({ top: targetTop, behavior: 'smooth' });
};

onMounted(() => {
    timerId = setTimeout(tick, 400);
});

onBeforeUnmount(() => {
    stopped = true;
    clearTimeout(timerId);
});
</script>

<style scoped>
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
    display: flex;
    align-items: center;
    position: relative;
}

/* ── Brand ─────────────────────────────────────── */
.brand {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: baseline;
}

.brand-text {
    font-size: 42px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--color-text);
    line-height: 1;
    min-width: 1px;
}

/* Always-blinking cursor */
.brand-cursor {
    font-size: 42px;
    font-weight: 300;
    color: var(--color-primary);
    line-height: 1;
    margin-left: 2px;
    animation: blink 0.75s step-end infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0; }
}

/* ── Left nav ───────────────────────────────────── */
.left-nav {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    z-index: 1;
}

.left-nav a {
    text-decoration: none;
    color: var(--color-text);
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── Tablet ─────────────────────────────────────── */
@media (max-width: 980px) {
    .left-nav a:nth-child(3),
    .left-nav a:nth-child(4) {
        display: none;
    }

    .brand-text,
    .brand-cursor {
        font-size: 34px;
    }
}

/* ── Mobile ─────────────────────────────────────── */
@media (max-width: 640px) {
    .left-nav {
        display: none;
    }

    .brand {
        position: static;
        transform: none;
        width: 100%;
        justify-content: center;
    }

    .header-inner {
        justify-content: center;
    }

    .brand-text,
    .brand-cursor {
        font-size: 28px;
    }
}
</style>
