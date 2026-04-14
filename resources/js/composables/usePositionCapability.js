import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Composable kiểm tra quyền hạn nghiệp vụ của chức vụ hiện tại.
 *
 * Dữ liệu `position_capabilities` được share từ AppServiceProvider
 * dưới dạng: { approve_attendance: true, view_salary: false, ... }
 *
 * Cách dùng trong Vue component:
 *   const { can, isAdmin } = usePositionCapability()
 *   can('approve_attendance')   // true/false
 *
 *   // Trong template:
 *   <button v-if="can('approve_attendance')">Duyệt công</button>
 */
export function usePositionCapability() {
    const page = usePage()

    const capabilities = computed(
        () => page.props.auth?.position_capabilities ?? {}
    )

    const roles = computed(
        () => page.props.auth?.user?.roles ?? []
    )

    const isAdmin = computed(() => roles.value.includes('admin'))

    /**
     * Kiểm tra user có quyền hạn này không.
     * Admin luôn trả về true.
     */
    const can = (capability) => {
        if (isAdmin.value) return true
        return capabilities.value[capability] === true
    }

    /**
     * Kiểm tra có ÍT NHẤT MỘT quyền trong danh sách.
     */
    const canAny = (...caps) => caps.some(can)

    /**
     * Kiểm tra có TẤT CẢ CÁC QUYỀN trong danh sách.
     */
    const canAll = (...caps) => caps.every(can)

    return { can, canAny, canAll, isAdmin, capabilities }
}
