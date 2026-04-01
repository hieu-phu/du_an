<template>
  <div class="space-y-2 font-sans">
    <label v-if="label"
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-200">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <div class="relative group">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors duration-200">
        <svg class="w-5 h-5 transition-colors duration-200" :class="[
          error ? 'text-red-500' : 'text-gray-400 ',
          disabled ? 'text-gray-300 dark:text-gray-600' : ''
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <flat-pickr v-model="internalValue" :config="finalConfig" :class="[
        'w-full pl-11 pr-10 py-3 rounded-xl border-2',
        'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
        'outline-none transition-all duration-200 asfy_input_date',
        error ? [
          'border-red-500',
          'focus:ring-red-500/20 focus:border-red-500',
          'dark:border-red-400'
        ] : [],
        disabled ? [
          'bg-gray-100 dark:bg-gray-800',
          'text-gray-400 dark:text-gray-500',
          'cursor-not-allowed',
          'border-gray-200 dark:border-gray-700'
        ] : [
          'bg-white dark:bg-gray-900',
          'text-gray-900 dark:text-white'
        ],
        'placeholder:text-gray-400 dark:placeholder:text-gray-500',
        'asfy_input_date',
        attrs.class
      ]" :placeholder="placeholder" :disabled="disabled" />
      <button v-if="internalValue && !disabled && clearable" @click="clear" @mousedown.prevent type="button"
        class="absolute inset-y-0 right-0 pr-3 flex items-center transition-all duration-200" :class="[
          'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300',
          'hover:scale-110 active:scale-95'
        ]" aria-label="Xóa ngày">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <div
        class="absolute inset-0 rounded-xl pointer-events-none border-2 border-transparent transition-all duration-200"
        :class="{
          '': !error,
          '': error
        }" />
    </div>
    <transition enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition-all duration-150 ease-in" leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-2 opacity-0">
      <p v-if="error" class="text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
        <span>{{ error[0] }}</span>
      </p>
    </transition>
    <transition enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition-all duration-150 ease-in" leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-2 opacity-0">
      <p v-if="$slots.helper" class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <slot name="helper" />
      </p>
    </transition>
  </div>
</template>


<script setup>
import { computed, onMounted, watch } from 'vue'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import 'flatpickr/dist/themes/material_blue.css'
import { useAttrs } from 'vue'

const attrs = useAttrs()


const props = defineProps({
  modelValue: [String, Date, null],
  label: String,
  placeholder: { type: String, default: 'Chọn ngày' },
  error: String,
  required: Boolean,
  disabled: Boolean,
  clearable: { type: Boolean, default: true },
  config: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['update:modelValue', 'change'])

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const defaultConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  locale: {
    firstDayOfWeek: 1,
    weekdays: {
      shorthand: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
      longhand: ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy']
    },
    months: {
      shorthand: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
      longhand: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']
    }
  }
}

const finalConfig = computed(() => ({
  ...defaultConfig,
  ...props.config
}))

// Clear date
const clear = () => {
  internalValue.value = null
}

// Emit change event
watch(internalValue, (newVal) => {
  emit('change', newVal)
})


onMounted(() => {
  const inputs = document.querySelectorAll('.flatpickr-calendar');
  const input_date = document.querySelectorAll('input.asfy_input_date');
  
  if (input_date.length > 0) {
    input_date.forEach(item => {
      item.style.borderRadius = '0.25rem';
    })
  }

  if (inputs.length > 0) {
    inputs.forEach(input => {
      input.style.boxShadow = 'none';
      let flatpickrWeekdays = input.querySelector('.flatpickr-weekdays');
      if (flatpickrWeekdays) {
        flatpickrWeekdays.style.margin = '0';
        flatpickrWeekdays.style.setProperty(
          'background',
          '#455eff',
          'important'
        );
        let spanFlatpickrweekdaysday = flatpickrWeekdays.querySelectorAll('.flatpickr-weekday');
        spanFlatpickrweekdaysday.forEach(day => {
          day.style.setProperty(
            'color',
            '#fff',
            'important'
          );
          day.style.setProperty(
            'background',
            '#455eff',
            'important'
          );
          day.style.setProperty(
            'font-weight',
            '600',
            'important'
          );
        });
      }

      let flatpickrMonth = input.querySelector('.flatpickr-month');
      if (flatpickrMonth) {
        flatpickrMonth.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'shadow-lg', 'hover:shadow-xl', 'transition-shadow');
        flatpickrMonth.style.background = '#455eff';
        let flatpickrCurrentMonthNumInputWrapper = flatpickrMonth.querySelector('.flatpickr-current-month .numInputWrapper input');
        if (flatpickrCurrentMonthNumInputWrapper) {
          flatpickrCurrentMonthNumInputWrapper.style.setProperty(
            'color',
            '#fff',
            'important'
          );
          flatpickrCurrentMonthNumInputWrapper.style.setProperty(
            'font-weight',
            '600',
            'important'
          );
        }
        let flatpickrMonthDropdownMonths = flatpickrMonth.querySelector('.flatpickr-current-month .flatpickr-monthDropdown-months');
        if (flatpickrMonthDropdownMonths) {
          flatpickrMonthDropdownMonths.style.setProperty(
            'background',
            '#455eff',
            'important'
          );
        }
      }
    });
  }
})

</script>

<style scoped>
:deep(.flatpickr-calendar) {
  font-family: 'Inter', sans-serif;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

/* Dark mode styles */
@media (prefers-color-scheme: dark) {
  :deep(.flatpickr-calendar) {
    background: #1f2937;
    border-color: #4b5563;
    color: white;
  }

  :deep(.flatpickr-day) {
    color: white;
  }

  :deep(.flatpickr-day:hover) {
    background: #374151;
  }

  :deep(.flatpickr-disabled) {
    color: #6b7280;
  }
}

/* Animation cho focus state */
@keyframes pulse-ring {
  0% {
    transform: scale(0.95);
    opacity: 0.75;
  }

  100% {
    transform: scale(1.05);
    opacity: 0;
  }
}

.group:focus-within .focus-indicator {
  animation: pulse-ring 2s infinite;
}

input.asfy_input_date {
  padding-block: 7px;
  border-radius: 0.25rem;
}
</style>