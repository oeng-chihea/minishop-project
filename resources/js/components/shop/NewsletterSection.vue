<template>
    <section class="container newsletter fade-in-up">
        <div class="text-col">
            <p class="kicker">Join NovaStore Club</p>
            <h3>Get launches, early offers and seasonal drops.</h3>
            <p class="desc">Subscribe to our newsletter and receive updates like an official ecommerce brand site.</p>
        </div>

        <form class="form-col" @submit.prevent>
            <input type="email" placeholder="Enter your email" aria-label="Email address" />
            <button type="submit">Subscribe</button>
        </form>
    </section>
</template>

<style scoped>
.container {
    max-width: 1240px;
    margin: 28px auto 68px;
    padding: 0 22px;
}

.newsletter {
    border: 1px solid var(--color-line);
    background: linear-gradient(140deg, #1f3a5f 0%, #223f65 60%, #32557f 100%);
    color: #fff;
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 16px;
    padding: 26px;
}

.kicker {
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
    opacity: 0.86;
}

h3 {
    margin: 10px 0 0;
    font-size: 34px;
    line-height: 1.15;
}

.desc {
    margin: 10px 0 0;
    color: rgba(255, 255, 255, 0.86);
}

.form-col {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-self: center;
}

input {
    width: 100%;
    border: 1px solid rgba(255, 255, 255, 0.5);
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    padding: 12px;
    font-size: 15px;
}

input::placeholder {
    color: rgba(255, 255, 255, 0.8);
}

button {
    border: 0;
    background: #ffffff;
    color: #1f3a5f;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 12px 16px;
    cursor: pointer;
}

@media (max-width: 900px) {
    .newsletter {
        grid-template-columns: 1fr;
    }

    h3 {
        font-size: 28px;
    }
}

@media (max-width: 640px) {
    .form-col {
        grid-template-columns: 1fr;
    }
}
</style>
