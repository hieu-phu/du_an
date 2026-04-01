import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { watch } from 'vue'

export function useFormErrors() {
    const page = usePage()

    // Hiển thị chỉ lỗi đầu tiên
    const showFirstError = (errors) => {
        if (!errors || typeof errors !== 'object') return

        // Lấy field đầu tiên có lỗi
        const firstField = Object.keys(errors)[0]
        if (!firstField) return

        // Lấy message đầu tiên của field đó
        const message = Array.isArray(errors[firstField]) 
            ? errors[firstField][0] 
            : errors[firstField]
        
        if (message) {
            toast.error(message, {
                position: 'top-right',
                autoClose: 3000,
            })
        }
    }

    // Hiển thị tất cả errors (giữ lại để có thể dùng khi cần)
    const showValidationErrors = (errors) => {
        if (!errors || typeof errors !== 'object') return

        Object.keys(errors).forEach(field => {
            const message = Array.isArray(errors[field]) 
                ? errors[field][0] 
                : errors[field]
            
            toast.error(message, {
                position: 'top-right',
                autoClose: 3000,
            })
        })
    }

    const showValidationErrorsGrouped = (errors) => {
        if (!errors || typeof errors !== 'object') return

        const errorMessages = Object.values(errors)
            .flat()
            .join('\n')

        if (errorMessages) {
            toast.error(errorMessages, {
                position: 'top-right',
                autoClose: 5000,
            })
        }
    }

    const watchErrors = () => {
        watch(
            () => page.props.errors,
            (errors) => {
                if (errors && Object.keys(errors).length > 0) {
                    showFirstError(errors) // Thay đổi ở đây
                }
            },
            { deep: true }
        )
    }

    const showFlashMessage = () => {
        const flash = page.props.flash || {}
        
        if (flash.success) {
            toast.success(flash.success)
        }
        
        if (flash.error) {
            toast.error(flash.error)
        }

        if (flash.warning) {
            toast.warning(flash.warning)
        }

        if (flash.info) {
            toast.info(flash.info)
        }
    }

    return {
        showFirstError, 
        showValidationErrors,
        showValidationErrorsGrouped,
        watchErrors,
        showFlashMessage
    }
}