<template>
  <div v-if="alerts.length" class="mb-5 space-y-3">
    <div
      v-for="alert in alerts"
      :key="alert.type"
      class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm"
      :class="alert.className"
    >
      <div class="mt-0.5 text-base leading-none">{{ alert.icon }}</div>
      <div class="min-w-0">
        <div class="font-semibold">{{ alert.title }}</div>
        <div class="mt-0.5 break-words">{{ alert.message }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const alertMap = {
  success: {
    title: 'Thành công',
    icon: '✓',
    className: 'border-emerald-200 bg-emerald-50 text-emerald-800',
  },
  error: {
    title: 'Có lỗi',
    icon: '✕',
    className: 'border-red-200 bg-red-50 text-red-800',
  },
  warning: {
    title: 'Lưu ý',
    icon: '⚠',
    className: 'border-amber-200 bg-amber-50 text-amber-800',
  },
  info: {
    title: 'Thông tin',
    icon: 'ℹ',
    className: 'border-blue-200 bg-blue-50 text-blue-800',
  },
}

const alerts = computed(() => {
  const flash = page.props.flash || {}

  return Object.entries(alertMap)
    .map(([type, config]) => ({
      type,
      message: flash[type],
      ...config,
    }))
    .filter((item) => item.message)
})
</script>

