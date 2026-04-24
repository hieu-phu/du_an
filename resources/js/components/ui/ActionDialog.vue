<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed inset-0 z-[10000] flex items-center justify-center px-4 py-6"
      @keydown.esc="handleEscape"
    >
      <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]" @click="handleBackdrop"></div>

      <div :class="panelClass" class="relative z-10 w-full overflow-hidden bg-white shadow-2xl">
        <div :class="accentClass" class="h-1.5 w-full"></div>

        <div class="border-b border-slate-200 px-6 py-5">
          <div class="flex items-start gap-4">
            <div :class="iconWrapClass" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path :d="iconPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
              </svg>
            </div>

            <div class="min-w-0 flex-1">
              <p v-if="resolvedEyebrow" :class="eyebrowClass" class="text-xs font-semibold uppercase tracking-[0.18em]">
                {{ resolvedEyebrow }}
              </p>
              <h3 class="mt-1 text-xl font-semibold text-slate-900">{{ title }}</h3>
              <p v-if="message" class="mt-3 text-sm leading-6 text-slate-600">{{ message }}</p>
            </div>

            <button
              v-if="showCloseButton"
              type="button"
              class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
              aria-label="Đóng"
              @click="cancel"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="space-y-4 px-6 py-5">
          <div v-if="mode === 'prompt'" class="space-y-2">
            <label v-if="inputLabel" class="block text-sm font-semibold text-slate-700">{{ inputLabel }}</label>
            <textarea
              v-if="inputType === 'textarea'"
              ref="inputRef"
              v-model="inputValue"
              :rows="rows"
              :placeholder="placeholder"
              :class="inputClass"
            />
            <input
              v-else
              ref="inputRef"
              v-model="inputValue"
              :type="inputType"
              :placeholder="placeholder"
              :class="inputClass"
              @keydown.enter="confirm"
            />
          </div>

          <div
            v-if="hint"
            :class="hintClass"
            class="rounded-2xl border px-4 py-3 text-sm"
          >
            {{ hint }}
          </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50/60 px-6 py-4">
          <button
            v-if="showCancel"
            type="button"
            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            @click="cancel"
          >
            {{ cancelText }}
          </button>
          <button
            type="button"
            :class="confirmButtonClass"
            @click="confirm"
          >
            {{ okText }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue'

const visible = ref(false)
const mode = ref('alert')
const eyebrow = ref('')
const title = ref('Thông báo')
const message = ref('')
const hint = ref('')
const okText = ref('Đồng ý')
const cancelText = ref('Hủy')
const inputLabel = ref('')
const inputType = ref('text')
const placeholder = ref('')
const rows = ref(4)
const inputValue = ref('')
const variant = ref('primary')
const maxWidth = ref('lg')
const closeOnBackdrop = ref(true)
const closeOnEscape = ref(true)
const showCloseButton = ref(true)
const inputRef = ref(null)

let resolver = null

const variantMap = {
  primary: {
    accent: 'bg-blue-600',
    iconWrap: 'bg-blue-50 text-blue-700',
    eyebrow: 'text-blue-700',
    confirmButton: 'bg-blue-600 hover:bg-blue-700',
    hint: 'border-blue-200 bg-blue-50 text-blue-700',
    input: 'focus:border-blue-500 focus:ring-blue-100',
    iconPath: 'M12 16v.01M12 8v5m9 0A9 9 0 1 1 3 13a9 9 0 0 1 18 0Z',
  },
  success: {
    accent: 'bg-emerald-600',
    iconWrap: 'bg-emerald-50 text-emerald-700',
    eyebrow: 'text-emerald-700',
    confirmButton: 'bg-emerald-600 hover:bg-emerald-700',
    hint: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    input: 'focus:border-emerald-500 focus:ring-emerald-100',
    iconPath: 'm5 13 4 4L19 7',
  },
  warning: {
    accent: 'bg-amber-500',
    iconWrap: 'bg-amber-50 text-amber-700',
    eyebrow: 'text-amber-700',
    confirmButton: 'bg-amber-500 hover:bg-amber-600',
    hint: 'border-amber-200 bg-amber-50 text-amber-700',
    input: 'focus:border-amber-500 focus:ring-amber-100',
    iconPath: 'M12 9v4m0 4h.01M10.29 3.86 1.82 18A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-3l-8.47-14.14a2 2 0 0 0-3.42 0Z',
  },
  danger: {
    accent: 'bg-rose-600',
    iconWrap: 'bg-rose-50 text-rose-700',
    eyebrow: 'text-rose-700',
    confirmButton: 'bg-rose-600 hover:bg-rose-700',
    hint: 'border-rose-200 bg-rose-50 text-rose-700',
    input: 'focus:border-rose-500 focus:ring-rose-100',
    iconPath: 'M12 9v4m0 4h.01M10.29 3.86 1.82 18A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-3l-8.47-14.14a2 2 0 0 0-3.42 0Z',
  },
}

const widthMap = {
  md: 'max-w-md rounded-2xl',
  lg: 'max-w-lg rounded-[26px]',
  xl: 'max-w-xl rounded-[28px]',
}

const resolvedVariant = computed(() => variantMap[variant.value] || variantMap.primary)
const resolvedEyebrow = computed(() => eyebrow.value || defaultEyebrow())
const showCancel = computed(() => mode.value !== 'alert')
const accentClass = computed(() => resolvedVariant.value.accent)
const iconWrapClass = computed(() => resolvedVariant.value.iconWrap)
const eyebrowClass = computed(() => resolvedVariant.value.eyebrow)
const iconPath = computed(() => resolvedVariant.value.iconPath)
const hintClass = computed(() => resolvedVariant.value.hint)
const panelClass = computed(() => widthMap[maxWidth.value] || widthMap.lg)
const inputClass = computed(() => `w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2 ${resolvedVariant.value.input}`)
const confirmButtonClass = computed(() => `rounded-xl px-4 py-2 text-sm font-semibold text-white transition ${resolvedVariant.value.confirmButton}`)

function defaultEyebrow() {
  if (mode.value === 'prompt') return 'Nhập thông tin'
  if (mode.value === 'confirm') return 'Xác nhận thao tác'
  return variant.value === 'success' ? 'Hoàn tất' : 'Thông báo'
}

async function open(options = {}) {
  mode.value = options.mode || 'alert'
  eyebrow.value = options.eyebrow || ''
  title.value = options.title || 'Thông báo'
  message.value = options.message || ''
  hint.value = options.hint || ''
  okText.value = options.okText || defaultOkText(options.mode || 'alert', options.variant || inferVariant(options.mode))
  cancelText.value = options.cancelText || 'Đóng'
  inputLabel.value = options.inputLabel || ''
  inputType.value = options.inputType || 'text'
  placeholder.value = options.placeholder || ''
  rows.value = options.rows || 4
  inputValue.value = options.defaultValue ?? ''
  variant.value = options.variant || inferVariant(options.mode)
  maxWidth.value = options.maxWidth || (mode.value === 'prompt' ? 'xl' : 'lg')
  closeOnBackdrop.value = options.closeOnBackdrop ?? true
  closeOnEscape.value = options.closeOnEscape ?? true
  showCloseButton.value = options.showCloseButton ?? true
  visible.value = true

  await nextTick()
  if (mode.value === 'prompt') {
    inputRef.value?.focus?.()
    inputRef.value?.select?.()
  }

  return new Promise((resolve) => {
    resolver = resolve
  })
}

function inferVariant(dialogMode) {
  if (dialogMode === 'confirm') return 'warning'
  if (dialogMode === 'prompt') return 'primary'
  return 'primary'
}

function defaultOkText(dialogMode, tone) {
  if (dialogMode === 'prompt') return 'Lưu'
  if (dialogMode === 'confirm') return tone === 'danger' ? 'Xác nhận' : 'Tiếp tục'
  return 'Đã hiểu'
}

function close(result) {
  visible.value = false
  if (resolver) {
    resolver(result)
    resolver = null
  }
}

function confirm() {
  if (mode.value === 'prompt') {
    close(inputValue.value)
    return
  }

  close(true)
}

function cancel() {
  if (mode.value === 'alert') {
    close(true)
    return
  }

  close(null)
}

function handleBackdrop() {
  if (!closeOnBackdrop.value) return
  cancel()
}

function handleEscape() {
  if (!closeOnEscape.value) return
  cancel()
}

function openAlert(options = {}) {
  return open({ ...options, mode: 'alert' })
}

function openConfirm(options = {}) {
  return open({ ...options, mode: 'confirm' })
}

function openPrompt(options = {}) {
  return open({ ...options, mode: 'prompt' })
}

defineExpose({
  open,
  openAlert,
  openConfirm,
  openPrompt,
  close,
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.18s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
