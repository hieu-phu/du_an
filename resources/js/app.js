import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import VueApexCharts from 'vue3-apexcharts'
import Vue3Toasity from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import PrimeVue from 'primevue/config'
import store from './store'
import { setupProgress } from './plugins/progress'
// import '../css/app.css'
import './bootstrap'
import './echo'

// Setup NProgress
setupProgress()

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
      .use(VueApexCharts)
      .use(Vue3Toasity, { autoClose: 3000, position: 'top-right' })
      .use(store)
      .use(PrimeVue)
      .mount(el)
  },
})