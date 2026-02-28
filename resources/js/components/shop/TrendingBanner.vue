<template>
    <div class="trending-wrap">
        <div class="marquee-viewport">
            <div class="marquee-track">
                <span v-for="i in 12" :key="i" class="marquee-item">
                    TRENDING NOW &nbsp;•&nbsp; NEW ARRIVALS &nbsp;•&nbsp; FREE SHIPPING OVER $100 &nbsp;•&nbsp; UP TO 40% OFF &nbsp;•&nbsp; SHOP THE LATEST DROPS &nbsp;•&nbsp;
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.trending-wrap {
    background: var(--color-text);
    color: #fff;
    padding: 14px 0;
    overflow: hidden;
    margin-top: 0;
}

.marquee-viewport {
    overflow: hidden;
    white-space: nowrap;
    width: 100%;
}

.marquee-track {
    display: inline-flex;
    animation: marquee-scroll 40s linear infinite;
    will-change: transform;
}

.marquee-track:hover {
    animation-play-state: paused;
}

.marquee-item {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.9);
    flex-shrink: 0;
}

@keyframes marquee-scroll {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}
</style>
