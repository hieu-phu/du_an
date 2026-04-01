<template>
  <div class="date-range-picker-wrapper">
    <label v-if="label"
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <div class="date-picker-container">
      <VueDatePicker v-model="internalValue" range :locale="computedLocale" :preset-dates="computedPresetDates"
        :enable-time-picker="false" :placeholder="placeholder" :disabled="disabled" :clearable="clearable"
        :auto-apply="true" :formats="{ input: displayFormat, preview: previewFormat }" :model-type="modelType"
        :min-date="minDate" :max-date="maxDate" :disabled-dates="disabledDates" :start-date="startDate"
        :multi-calendars="false" :teleport="false" :inline="inline" :text-input="textInput"
        :month-change-on-scroll="monthChangeOnScroll" :six-weeks="sixWeeks" menu-class-name="lexend-datepicker"
        ref="datepicker" @update:model-value="handleUpdate" @open="handleOpen" @closed="handleClosed"
        @cleared="handleCleared">
      </VueDatePicker>
    </div>

    <!-- Error message -->
    <transition enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition-all duration-150 ease-in" leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-2 opacity-0">
      <p v-if="error" class="text-sm text-red-600 dark:text-red-400 flex items-center gap-1 mt-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd" />
        </svg>
        <span>{{ Array.isArray(error) ? error[0] : error }}</span>
      </p>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { VueDatePicker } from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { vi } from 'date-fns/locale'
import { format } from 'date-fns'

const props = defineProps({
  modelValue: {
    type: Array,
    default: null
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Chọn khoảng thời gian'
  },
  error: {
    type: [String, Array],
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  locale: {
    type: Object,
    default: null
  },
  displayFormat: {
    type: [String, Function],
    default: 'dd/MM/yyyy'
  },
  previewFormat: {
    type: [String, Function],
    default: 'dd/MM/yyyy'
  },
  modelType: {
    type: String,
    default: 'yyyy-MM-dd'
  },
  usePresetRanges: {
    type: Boolean,
    default: true
  },
  customPresetRanges: {
    type: Array,
    default: null
  },
  defaultRange: {
    type: String,
    default: null
  },
  autoSetDefaultRange: {
    type: Boolean,
    default: true
  },
  enableTimePicker: {
    type: Boolean,
    default: false
  },
  minDate: {
    type: [Date, String, Number],
    default: null
  },
  maxDate: {
    type: [Date, String, Number],
    default: () => new Date()
  },
  disabledDates: {
    type: [Array, Function],
    default: null
  },
  startDate: {
    type: [Date, String, Number],
    default: null
  },
  multiCalendars: {
    type: [Boolean, Number, Object],
    default: false
  },
  teleport: {
    type: [Boolean, String],
    default: true
  },
  inline: {
    type: [Boolean, Object],
    default: false
  },
  textInput: {
    type: [Boolean, Object],
    default: false
  },
  monthChangeOnScroll: {
    type: [Boolean, String],
    default: true
  },
  sixWeeks: {
    type: [Boolean, String],
    default: false
  }
})

const emit = defineEmits([
  'update:modelValue',
  'change',
  'open',
  'closed',
  'cleared'
])

const datepicker = ref(null)

// Tạo locale custom với tên ngày trong tuần là T2, T3... và tháng viết tắt là Thg
const customLocale = computed(() => {
  if (props.locale) return props.locale

  const customViLocale = {
    ...vi,
    localize: {
      ...vi.localize,
      day: (n) => {
        const days = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
        return days[n]
      },
      month: (n) => {
        const months = [
          'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
          'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
        ]
        return months[n]
      }
    },
    formatLong: {
      ...vi.formatLong,
      month: () => 'MMMM',
    }
  }

  return customViLocale
})

const computedLocale = computed(() => {
  return props.locale || customLocale.value
})

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const startOfDay = (d) => {
  const date = new Date(d)
  date.setHours(0, 0, 0, 0)
  return date
}

const endOfDay = (d) => {
  const date = new Date(d)
  date.setHours(23, 59, 59, 999)
  return date
}

const defaultPresetRanges = computed(() => {
  const today = new Date()

  return [
    {
      label: 'Hôm nay',
      value: [startOfDay(today), endOfDay(today)]
    },
    {
      label: 'Hôm qua',
      value: [
        startOfDay(new Date(today.getTime() - 86400000)),
        endOfDay(new Date(today.getTime() - 86400000))
      ]
    },
    {
      label: '7 ngày gần nhất',
      value: [
        startOfDay(new Date(today.getTime() - 6 * 86400000)),
        endOfDay(today)
      ]
    },
    {
      label: '30 ngày gần nhất',
      value: [
        startOfDay(new Date(today.getTime() - 29 * 86400000)),
        endOfDay(today)
      ]
    },
    {
      label: 'Tuần này',
      value: (() => {
        const curr = new Date()
        const first = new Date(curr.setDate(curr.getDate() - curr.getDay() + 1))
        const last = new Date(first)
        last.setDate(first.getDate() + 6)
        return [startOfDay(first), endOfDay(last)]
      })()
    },
    {
      label: 'Tháng này',
      value: [
        startOfDay(new Date(today.getFullYear(), today.getMonth(), 1)),
        endOfDay(new Date(today.getFullYear(), today.getMonth() + 1, 0))
      ]
    },
    {
      label: 'Tháng trước',
      value: [
        startOfDay(new Date(today.getFullYear(), today.getMonth() - 1, 1)),
        endOfDay(new Date(today.getFullYear(), today.getMonth(), 0))
      ]
    },
    {
      label: 'Quý này',
      value: (() => {
        const quarter = Math.floor(today.getMonth() / 3)
        return [
          startOfDay(new Date(today.getFullYear(), quarter * 3, 1)),
          endOfDay(new Date(today.getFullYear(), quarter * 3 + 3, 0))
        ]
      })()
    },
    {
      label: 'Năm nay',
      value: [
        startOfDay(new Date(today.getFullYear(), 0, 1)),
        endOfDay(new Date(today.getFullYear(), 11, 31))
      ]
    }
  ]
})

const computedPresetDates = computed(() => {
  if (!props.usePresetRanges) return []
  return props.customPresetRanges || defaultPresetRanges.value
})

const handleUpdate = (value) => {
  emit('update:modelValue', value)
  emit('change', value)
}

const handleCleared = () => {
  emit('cleared')
  emit('update:modelValue', null)
}

const handleOpen = () => {
  emit('open')
  // Force font update after menu opens
  nextTick(() => {
    setTimeout(() => {
      applyLexendFont()
      removeTimePickerElements()
    }, 100)
  })
}

const handleClosed = () => {
  emit('closed')
}

// Function to apply Lexend font to all elements
const applyLexendFont = () => {
  const datepickerElements = document.querySelectorAll('.lexend-datepicker, .lexend-datepicker *')
  datepickerElements.forEach(el => {
    if (el.style) {
      el.style.fontFamily = "'Lexend', sans-serif !important"
    }
  })

  // Also apply to specific VueDatePicker elements
  const vuePickerElements = document.querySelectorAll('.dp__menu, .dp__menu *')
  vuePickerElements.forEach(el => {
    if (el.style) {
      el.style.fontFamily = "'Lexend', sans-serif !important"
    }
  })
}

// Function to remove time picker elements
const removeTimePickerElements = () => {
  // Remove time picker button from input
  const timeButtons = document.querySelectorAll('.dp__time_picker_overlay, .dp__time_picker, .dp__time_col, .dp__pm_am, .dp__time_input, .dp__time_display_container')
  timeButtons.forEach(el => {
    if (el && el.parentNode) {
      el.parentNode.removeChild(el)
    }
  })
}

const getDefaultRangeValue = () => {
  if (!props.defaultRange) return null

  const rangeMap = {
    'today': 0,
    'yesterday': 1,
    '7days': 2,
    '30days': 3,
    'thisWeek': 4,
    'thisMonth': 5,
    'lastMonth': 6,
    'thisQuarter': 7,
    'thisYear': 8
  }

  const index = rangeMap[props.defaultRange]
  if (index !== undefined && defaultPresetRanges.value[index]) {
    return defaultPresetRanges.value[index].value
  }

  return null
}

onMounted(() => {

  if (!props.modelValue && props.defaultRange && props.autoSetDefaultRange) {
    const defaultValue = getDefaultRangeValue()
    if (defaultValue) {
      emit('update:modelValue', defaultValue)
    }
  }

  // Apply font on mount
  setTimeout(() => {
    applyLexendFont()
    removeTimePickerElements()
  }, 200)
})

const formatRange = (dates) => {
  if (!dates || !Array.isArray(dates) || dates.length !== 2) return ''
  const start = format(dates[0], 'dd/MM/yyyy')
  const end = format(dates[1], 'dd/MM/yyyy')
  return `${start} - ${end}`
}

defineExpose({
  formatRange,
  datepicker
})
</script>

<style scoped>
.date-range-picker-wrapper {
  width: 100%;
  font-family: 'Lexend', sans-serif;
}

.date-picker-container {
  width: 100%;
  position: relative;
}
</style>

<style>
/* Import font */
@import url('https://fonts.bunny.net/css?family=Lexend:400,500,600');

/* FORCE LEXEND FONT ON ALL DATEPICKER ELEMENTS */
.lexend-datepicker,
.lexend-datepicker *,
.dp__menu.lexend-datepicker,
.dp__menu.lexend-datepicker *,
.dp__outer_menu_wrap.lexend-datepicker,
.dp__outer_menu_wrap.lexend-datepicker * {
  font-family: 'Lexend', sans-serif !important;
}

/* Specific overrides for VueDatePicker internal elements */
.dp__menu,
.dp__menu *,
.dp__calendar_header,
.dp__calendar_header *,
.dp__calendar,
.dp__calendar *,
.dp__cell_inner,
.dp__cell_inner *,
.dp__month_year_row,
.dp__month_year_row *,
.dp__month_year_select,
.dp__month_year_select *,
.dp__preset_ranges,
.dp__preset_ranges *,
.dp__preset_range,
.dp__preset_range *,
.dp__input,
.dp__input * {
  font-family: 'Lexend', sans-serif !important;
}

/* COMPLETELY REMOVE TIME PICKER */
.dp__time_display_container,
.dp__time_display,
.dp__time_col,
.dp__pm_am,
.dp__time_input,
.dp__time_picker_overlay,
.dp__time_picker,
.dp__clock,
.dp__time_picker_wrap,
.dp__time_picker_container {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  height: 0 !important;
  width: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  border: none !important;
}

/* Remove time picker icon from input */
.dp__input_icon {
  display: none !important;
}

/* ===== CĂN POPUP THẲNG VỚI INPUT ===== */
.dp__outer_menu_wrap {
  position: absolute !important;
  left: 0 !important;
  top: calc(100% + 8px) !important;
  transform: none !important;
  margin-left: 0 !important;
  z-index: 9999 !important;
}

/* ===== KHUNG WRAPPER BÊN NGOÀI ===== */
.dp__menu_content_wrapper {
  background: white !important;
  border: 1px solid #e0e0e0 !important;
  border-radius: 8px !important;
}

/* ===== MENU CONTAINER ===== */
.dp__menu.lexend-datepicker {
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
  border: 1px solid #e0e0e0 !important;
  overflow: hidden !important;
  margin-top: 0px !important;
  background: white !important;
  font-family: 'Lexend', sans-serif !important;
  width: auto !important;
  min-width: 400px !important;
  left: 0 !important;
  transform: none !important;
}

/* ===== PRESET SIDEBAR - COMPACT ===== */
.dp__sidebar_left {
  background: #f8f9fa !important;
  border-right: 1px solid #dee2e6 !important;
  padding: 12px 8px !important;
  width: 140px !important;
  font-family: 'Lexend', sans-serif !important;
  min-height: auto !important;
}

.dp__preset_ranges {
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
  padding: 0 !important;
  margin: 0 !important;
  background: transparent !important;
  border: none !important;
  font-family: 'Lexend', sans-serif !important;
}

.dp__preset_range {
  all: unset !important;
  display: block !important;
  padding: 8px 10px !important;
  margin: 0 !important;
  border-radius: 6px !important;
  font-size: 12px !important;
  font-weight: 400 !important;
  font-family: 'Lexend', sans-serif !important;
  transition: all 0.15s ease !important;
  background: white !important;
  color: #495057 !important;
  border: 1px solid #dee2e6 !important;
  text-align: left !important;
  cursor: pointer !important;
  width: 100% !important;
}

.dp__preset_range:hover {
  background: #e9ecef !important;
  color: #212529 !important;
  border-color: #adb5bd !important;
  transform: none !important;
  box-shadow: none !important;
}

/* ===== CALENDAR CONTAINER ===== */
.dp__calendar {
  padding: 0px !important;
  background: white !important;
  font-family: 'Lexend', sans-serif !important;
  width: 260px !important;
}

/* ===== MONTH/YEAR ROW - COMPACT ===== */
.dp__month_year_row {
  margin-bottom: 8px !important;
  padding: 8px 12px !important;
  background: transparent !important;
  border-radius: 6px !important;
  border: 1px solid #dee2e6 !important;
  font-family: 'Lexend', sans-serif !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
}

.dp__month_year_select {
  font-weight: 500 !important;
  font-size: 13px !important;
  font-family: 'Lexend', sans-serif !important;
  color: #212529 !important;
  padding: 4px 8px !important;
  border-radius: 4px !important;
  transition: all 0.15s !important;
  background: white !important;
  border: 1px solid #dee2e6 !important;
  cursor: pointer !important;
  margin: 0 !important;
}

.dp__month_year_select:hover {
  background: #f8f9fa !important;
  color: #0d6efd !important;
  border-color: #0d6efd !important;
}

/* ===== NAVIGATION ARROWS ===== */
.dp__inner_nav {
  width: 28px !important;
  height: 35px !important;
  border-radius: 4px !important;
  transition: all 0.15s !important;
  color: #6c757d !important;
  background: white !important;
  border: 1px solid #dee2e6 !important;
  cursor: pointer !important;
  font-family: 'Lexend', sans-serif !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 4px !important;
}

.dp__inner_nav:hover {
  background: #f8f9fa !important;
  color: #0d6efd !important;
  border-color: #0d6efd !important;
}

/* ===== CALENDAR HEADER - COMPACT ===== */
.dp__calendar_header {
  padding: 0 !important;
  margin: 0 0 6px 0 !important;
  border-bottom: none !important;
  background: transparent !important;
  font-family: 'Lexend', sans-serif !important;
}

.dp__calendar_header_item {
  font-size: 12px !important;
  color: #6c757d !important;
  font-weight: 400 !important;
  font-family: 'Lexend', sans-serif !important;
  text-transform: uppercase !important;
  padding: 15px 0 !important;
  width: 32px !important;
  text-align: center !important;
}

/* ===== DATE CELLS - COMPACT ===== */
.dp__calendar_item {
  padding: 1px !important;
}

.dp__cell_inner {
  all: unset !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 4px !important;
  font-size: 15px !important;
  font-weight: 400 !important;
  font-family: 'Lexend', sans-serif !important;
  height: 30px !important;
  width: 30px !important;
  margin: 0 !important;
  transition: all 0.15s ease !important;
  color: #212529 !important;
  border: 1px solid transparent !important;
  cursor: pointer !important;
  box-sizing: border-box !important;
}

.dp__cell_inner:hover {
  background: #0059b3 !important;
  color: #ffffff !important;
  border-color: #0050a0 !important;
}

.dp__today {
  border: 1px solid #0d6efd !important;
  font-weight: 500 !important;
  background: #e7f1ff !important;
  color: #0a58ca !important;
}

.dp__active_date,
.dp__range_start,
.dp__range_end {
  background: #0d6efd !important;
  color: white !important;
  font-weight: 500 !important;
  border-color: #0a58ca !important;
}

.dp__range_between {
  background: #cfe2ff !important;
  color: #084298 !important;
  border-radius: 2px !important;
  font-weight: 400 !important;
  border-color: transparent !important;
}

/* ===== DISABLED DATES (LIGHT MODE) ===== */
.dp__cell_inner.dp__cell_disabled {
  color: #d1d5db !important;
  cursor: not-allowed !important;
  opacity: 0.4 !important;
  background: transparent !important;
}

.dp__cell_inner.dp__cell_disabled:hover {
  background: transparent !important;
  color: #d1d5db !important;
  border-color: transparent !important;
}

/* ===== INPUT - CLEAN ===== */
.dp__input_wrap {
  position: relative !important;
  display: flex !important;
  flex-direction: row-reverse !important;
  align-items: center !important;
}

.dp__input {
  border-radius: 6px !important;
  border: 1px solid #ced4da !important;
  padding: 6px 35px 6px 10px !important;
  font-family: 'Lexend', sans-serif !important;
  font-size: 15px !important;
  font-weight: 400 !important;
  transition: all 0.15s !important;
  background: white !important;
  color: #212529 !important;
  width: 100% !important;
  order: 2 !important;
}

.dp__input:hover {
  border-color: #adb5bd !important;
}

.dp__input:focus {
  border-color: #0d6efd !important;
  box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1) !important;
  outline: none !important;
}

/* CĂN ICON CLEAR VÀ CALENDAR SANG TRÁI */
.dp__clear_icon,
.dp__input_icon {
  position: absolute !important;
  left: auto !important;
  right: 10px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  order: 1 !important;
  z-index: 2 !important;
}

/* Xóa icon padding */
.dp__input_icon_pad {
  padding-right: 35px !important;
  padding-left: 10px !important;
}

.dp__menu button[data-test-id="open-time-picker-btn"],
.lexend-datepicker button[data-test-id="open-time-picker-btn"] {
  display: none !important;
}

.dp__menu .dp__overlay_action {
  display: none !important;
}

/* ===== DARK MODE - Browser Preference ===== */
@media (prefers-color-scheme: dark) {
  .dp__menu_content_wrapper {
    background: #2d3748 !important;
    border-color: #4a5568 !important;
  }

  .dp__menu.lexend-datepicker {
    background: #2d3748 !important;
    border-color: #4a5568 !important;
  }

  .dp__sidebar_left {
    background: #30394b !important;
    border-right-color: #4a5568 !important;
  }

  .dp__preset_range {
    background: #374151 !important;
    color: #e5e7eb !important;
    border-color: #4b5563 !important;
  }

  .dp__preset_range:hover {
    background: #4b5563 !important;
    color: #f9fafb !important;
  }

  .dp__calendar {
    background: #2d3748 !important;
  }

  .dp__month_year_row {
    background: #374151 !important;
    border-color: #4b5563 !important;
  }

  .dp__month_year_select {
    color: #f9fafb !important;
    background: #374151 !important;
    border-color: #4b5563 !important;
  }

  .dp__input {
    background: #30394b !important;
    border-color: #374151 !important;
    color: #e5e7eb !important;
  }

  .dp__cell_inner {
    color: #e5e7eb !important;
  }

  .dp__today {
    background: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
  }

  .dp__range_between {
    background: #1e40af !important;
    color: #dbeafe !important;
  }

  /* ===== DISABLED DATES IN DARK MODE ===== */
  .dp__cell_inner.dp__cell_disabled {
    color: #6b7280 !important;
    cursor: not-allowed !important;
    opacity: 0.3 !important;
    background: transparent !important;
  }

  .dp__cell_inner.dp__cell_disabled:hover {
    background: transparent !important;
    color: #6b7280 !important;
    border-color: transparent !important;
  }

  .dp__calendar_header_item {
    color: #9ca3af !important;
  }

  .dp__inner_nav {
    background: #374151 !important;
    border-color: #4b5563 !important;
    color: #e5e7eb !important;
  }

  .dp__inner_nav:hover {
    background: #4b5563 !important;
    color: #ffffff !important;
  }
}

/* ===== DARK MODE - App Theme (.dark class) ===== */
.dark .dp__menu_content_wrapper {
  background: #2d3748 !important;
  border-color: #4a5568 !important;
}

.dark .dp__menu.lexend-datepicker {
  background: #2d3748 !important;
  border-color: #4a5568 !important;
}

.dark .dp__sidebar_left {
  background: #1a202c !important;
  border-right-color: #4a5568 !important;
}

.dark .dp__preset_range {
  background: #374151 !important;
  color: #e5e7eb !important;
  border-color: #4b5563 !important;
}

.dark .dp__preset_range:hover {
  background: #4b5563 !important;
  color: #f9fafb !important;
}

.dark .dp__calendar {
  background: #2d3748 !important;
}

.dark .dp__month_year_row {
  background: #374151 !important;
  border-color: #4b5563 !important;
}

.dark .dp__input {
  background: #1f2937 !important;
  border-color: #374151 !important;
  color: #e5e7eb !important;
}

.dark .dp__cell_inner {
  color: #e5e7eb !important;
}

.dark .dp__today {
  background: #0d6efd !important;
  color: white !important;
  border-color: #0d6efd !important;
}

.dark .dp__range_between {
  background: #1e40af !important;
  color: #dbeafe !important;
}

/* ===== DISABLED DATES IN DARK MODE (.dark class) ===== */
.dark .dp__cell_inner.dp__cell_disabled {
  color: #6b7280 !important;
  cursor: not-allowed !important;
  opacity: 0.3 !important;
  background: transparent !important;
}

.dark .dp__cell_inner.dp__cell_disabled:hover {
  background: transparent !important;
  color: #6b7280 !important;
  border-color: transparent !important;
}

.dark .dp__calendar_header_item {
  color: #9ca3af !important;
}

.dark .dp__inner_nav {
  background: #374151 !important;
  border-color: #4b5563 !important;
  color: #e5e7eb !important;
}

.dark .dp__inner_nav:hover {
  background: #4b5563 !important;
  color: #ffffff !important;
}

/* ================================================= */
/* FULL DARK THEME – HIGH CONTRAST & EASY READ       */
/* ================================================= */

.dark .dp__menu,
.dark .dp__menu_content_wrapper,
.dark .dp__calendar,
.dark .dp__sidebar_left {
  background: #020617 !important;
  /* slate-950 */
  color: #ffffff !important;
  border-color: #1e293b !important;
}

/* ================= PRESET SIDEBAR ================= */
.dark .dp__sidebar_left {
  background: #020617 !important;
}

.dark .dp__preset_range {
  background: transparent !important;
  color: #cbd5f5 !important;
  /* readable */
  border: none !important;
  font-size: 13px !important;
}

.dark .dp__preset_range:hover {
  background: #1e293b !important;
  color: #ffffff !important;
}

.dark .dp__preset_range.dp__preset_active {
  background: #2563eb !important;
  color: #ffffff !important;
}

/* ================= MONTH / YEAR ================= */
.dark .dp__month_year_row {
  background: #020617 !important;
  border: 1px solid #1e293b !important;
}

.dark .dp__month_year_select {
  background: #020617 !important;
  color: #ffffff !important;
  border: 1px solid #1e293b !important;
}

.dark .dp__inner_nav {
  background: #020617 !important;
  color: #ffffff !important;
  border: 1px solid #1e293b !important;
}

.dark .dp__inner_nav:hover {
  background: #1e293b !important;
}

/* ================= WEEK HEADER ================= */
.dark .dp__calendar_header_item {
  color: #c7d2fe !important;
  font-weight: 500 !important;
}

/* ================= DATE CELLS ================= */
.dark .dp__cell_inner {
  color: #e5e7eb !important;
  background: transparent !important;
  font-weight: 400 !important;
}

.dark .dp__cell_inner:hover {
  background: #2563eb !important;
  color: #ffffff !important;
}

/* Today */
.dark .dp__today {
  background: #1d4ed8 !important;
  color: #ffffff !important;
  border-color: #2563eb !important;
}

/* Selected */
.dark .dp__range_start,
.dark .dp__range_end {
  background: #2563eb !important;
  color: #ffffff !important;
}

/* Between range */
.dark .dp__range_between {
  background: rgba(37, 99, 235, 0.35) !important;
  color: #ffffff !important;
}

/* ================= DISABLED ================= */
.dark .dp__cell_inner.dp__cell_disabled {
  color: #64748b !important;
  opacity: 0.4 !important;
}

/* ================= INPUT ================= */
.dark .dp__input {
  background: #020617 !important;
  color: #ffffff !important;
  border-color: #1e293b !important;
}

.dark .dp__input::placeholder {
  color: #94a3b8 !important;
}

.dp__theme_light {
  --dp-text-color: #135fd1;
}
</style>