 <template>
    <section class="container collections fade-in-up">
        <article class="collection-card" v-for="item in collections" :key="item.title">
            <img :src="item.image" :alt="item.title" />
            <div class="overlay">
                <p>{{ item.kicker }}</p>
                <h3>{{ item.title }}</h3>
                <a href="#shop">Shop collection</a>
            </div>
        </article>
    </section>
</template>

<script setup>
const collections = [
    {
        kicker: 'Daily Comfort',
        title: 'Urban Walkers',
        image: 'https://via.placeholder.com/800x520/e4e9ef/23344d?text=Urban+Walkers'
    },
    {
        kicker: 'Travel Ready',
        title: 'Road Essentials',
        image: 'https://via.placeholder.com/800x520/dde4ec/23344d?text=Road+Essentials'
    },
    {
        kicker: 'Outdoor Active',
        title: 'Trail Focus',
        image: 'https://via.placeholder.com/800x520/e8edf2/23344d?text=Trail+Focus'
    }
];
</script>

<style scoped>
.container {
    max-width: 1240px;
    margin: 22px auto 0;
    padding: 0 22px;
}

.collections {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.collection-card {
    position: relative;
    overflow: hidden;
    border: 1px solid var(--color-line);
    min-height: 290px;
    box-shadow: var(--shadow-card);
}

.collection-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.overlay {
    position: absolute;
    inset: auto 0 0 0;
    background: linear-gradient(180deg, rgba(9, 17, 31, 0.08), rgba(9, 17, 31, 0.75));
    color: #fff;
    padding: 16px;
}

.overlay p {
    margin: 0;
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.overlay h3 {
    margin: 8px 0;
    font-size: 28px;
    line-height: 1.1;
}

.overlay a {
    color: #fff;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
}

@media (max-width: 980px) {
    .collections {
        grid-template-columns: 1fr;
    }
}
</style>
