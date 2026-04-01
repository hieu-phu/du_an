import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import { router } from '@inertiajs/vue3'

export function setupProgress() {
    NProgress.configure({
        minimum: 0.12,
        easing: 'ease',
        speed: 650,
        trickle: true,
        trickleSpeed: 350,
        showSpinner: false,
    })

    const START_DELAY = 220     // tránh flicker
    const MIN_DURATION = 320    // đã hiện thì giữ tối thiểu
    const MAX_PROGRESS = 0.95   // đừng set lên 1 trước khi done

    let startTimer = null
    let startedAt = 0
    let activeRequests = 0
    let lastProgress = 0

    function scheduleStart() {
        clearTimeout(startTimer)
        startTimer = setTimeout(() => {
            // chỉ start nếu vẫn còn request
            if (activeRequests > 0) {
                startedAt = Date.now()
                lastProgress = NProgress.status ?? 0
                NProgress.start()
            }
        }, START_DELAY)
    }

    function finishSmoothly() {
        // nếu chưa start (còn đang delay) -> huỷ luôn, khỏi nhấp nháy
        if (startTimer) {
            clearTimeout(startTimer)
            startTimer = null
        }

        // nếu progress chưa từng start thì done luôn
        if (!NProgress.status) {
            NProgress.done(true)
            lastProgress = 0
            startedAt = 0
            return
        }

        const elapsed = Date.now() - (startedAt || Date.now())
        const remaining = Math.max(0, MIN_DURATION - elapsed)

        setTimeout(() => {
            NProgress.done(true)
            lastProgress = 0
            startedAt = 0
        }, remaining)
    }

    router.on('start', () => {
        activeRequests++
        if (activeRequests === 1) {
            startedAt = 0
            lastProgress = 0
            scheduleStart()
        }
    })

    router.on('progress', (event) => {
        const pct = event?.detail?.progress?.percentage
        if (typeof pct !== 'number') return
        if (!NProgress.status) return // chưa start thì khỏi set

        // chuẩn hoá về 0..1 và giới hạn max 0.95
        const p = Math.min(MAX_PROGRESS, Math.max(NProgress.settings.minimum, pct / 100))

        // chặn tụt progress (chỉ cho tăng)
        const next = Math.max(lastProgress || NProgress.status || 0, p)

        // tránh set quá dày gây giật (tuỳ chọn)
        if (next - (lastProgress || 0) >= 0.01) {
            NProgress.set(next)
            lastProgress = next
        }
    })

    router.on('finish', (event) => {
        activeRequests = Math.max(0, activeRequests - 1)
        if (activeRequests > 0) return

        // nếu visit fail / cancelled thì remove để khỏi kẹt
        if (!event?.detail?.visit?.completed) {
            finishSmoothly()
            NProgress.remove()
            return
        }

        finishSmoothly()
    })

    router.on('exception', () => {
        activeRequests = Math.max(0, activeRequests - 1)
        if (activeRequests === 0) {
            finishSmoothly()
            NProgress.remove()
        }
    })
}