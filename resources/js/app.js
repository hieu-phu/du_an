import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import Vue3Toasity from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import PrimeVue from 'primevue/config'
import { setupProgress } from './plugins/progress'
// import '../css/app.css'
import './bootstrap'
import './echo'

import { router } from '@inertiajs/vue3'

// Setup NProgress
setupProgress()

// Global Inertia error handling
router.on('error', (event) => {
  // Handle 419 (Page Expired) - automatically reload to refresh CSRF token
  if (event.detail?.errors?.status === 419 || event.detail?.status === 419) {
    window.location.reload()
  }
})

createInertiaApp({
  // ✅ Hiện tại: eager: true → load ALL pages ngay khi vào app (tăng bundle size)
  // ✅ Fix: Bỏ eager để lazy load theo route
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue') // Bỏ { eager: true }
    return pages[`./Pages/${name}.vue`]()             // Thêm () để gọi dynamic import
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(Vue3Toasity, { autoClose: 3000, position: 'top-right' })
      .use(PrimeVue)
      .mount(el)
  },
})
