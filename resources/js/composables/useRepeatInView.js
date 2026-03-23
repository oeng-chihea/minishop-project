import { ref, onMounted, onBeforeUnmount } from 'vue';

export function useRepeatInView(options = {}) {
    const rootRef = ref(null);
    const isVisible = ref(false);
    let observer = null;

    onMounted(() => {
        observer = new IntersectionObserver(
            ([entry]) => {
                isVisible.value = entry.isIntersecting;
            },
            {
                threshold: options.threshold ?? 0.2,
                rootMargin: options.rootMargin ?? '0px',
            }
        );

        if (rootRef.value) {
            observer.observe(rootRef.value);
        }
    });

    onBeforeUnmount(() => observer?.disconnect());

    return {
        rootRef,
        isVisible,
    };
}
