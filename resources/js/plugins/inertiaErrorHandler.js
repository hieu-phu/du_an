import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

export default {
    install(app) {
        router.on('error', (event) => {
            const errors = event.detail.errors
            
            if (errors && Object.keys(errors).length > 0) {
                Object.values(errors).flat().forEach(error => {
                    toast.error(error)
                })
            }
        })

        router.on('success', (event) => {
            const page = event.detail.page
            const flash = page.props.flash

            if (flash?.success) {
                toast.success(flash.success)
            }
            if (flash?.error) {
                toast.error(flash.error)
            }
            if (flash?.warning) {
                toast.warning(flash.warning)
            }
            if (flash?.info) {
                toast.info(flash.info)
            }
        })
    }
}