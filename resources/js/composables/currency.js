// resources/js/composables/useCurrency.js
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

export function useCurrency() {
    const page = usePage();
    const currency = computed(() => {
        return page.props.auth?.currency ?? null;
    });
    return { currency };
}


export function useUser() {
    const page = usePage();
    const user = computed(() => {
        return page.props.auth?.user ?? null;
    });

    return { user };
}
