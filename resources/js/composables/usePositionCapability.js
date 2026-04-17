import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePositionCapability() {
    const page = usePage()

    const capabilities = computed(
        () => page.props.auth?.position_capabilities ?? {}
    )

    const can = (capability) => capabilities.value[capability] === true
    const canAny = (...caps) => caps.some(can)
    const canAll = (...caps) => caps.every(can)

    return { can, canAny, canAll, capabilities }
}
