<script setup>
import { computed, onMounted, watch } from 'vue'
import FormInput from '@/components/ui/FormInput.vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  helperText: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: Array,
  },
  autoGenerate: {
    type: Boolean,
    default: true
  },
  prefix: {
    type: String,
    default: 'DH' // cho phép tái sử dụng cho loại mã khác
  },
})

const emit = defineEmits(['update:modelValue'])

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const generateCode = () => {
  const now = new Date()
  const year = now.getFullYear().toString().slice(-2)
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  const timestamp = now.getTime().toString().slice(-6) // 6 chữ số cuối của timestamp
  const random = Math.floor(100 + Math.random() * 900) // 3 chữ số
  value.value = `${props.prefix}${day}${month}${year}-${timestamp}${random}`
}

watch(
  () => props.prefix,
  () => {
    if (props.autoGenerate) {
      generateCode()
    }
  }
)

// Chỉ tự động generate code khi chưa có giá trị (tạo mới)
if (!props.modelValue) {
  generateCode();
}
defineExpose({
  generateCode
})

watch(
  () => props.prefix,
  (newVal, oldVal) => {
    generateCode()
  }
)

</script>

<template>
  <div class="relative">
    <FormInput :label="label" v-model="value" :placeholder="placeholder" :helperText="helperText" :required="required"
      :error="error" :disabled="disabled" />

    <button v-show="!props.disabled" type="button" @click="generateCode"
      class="absolute end-0 top-11 flex items-center pe-3 text-gray-500 hover:text-blue-600 transition-colors"
      title="Tự động sinh mã">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5" fill="#8e94a3">
        <path
          d="M204-318q-22-38-33-78t-11-82q0-134 93-228t227-94h7l-64-64 56-56 160 160-160 160-56-56 64-64h-7q-100 0-170 70.5T240-478q0 26 6 51t18 49l-60 60ZM481-40 321-200l160-160 56 56-64 64h7q100 0 170-70.5T720-482q0-26-6-51t-18-49l60-60q22 38 33 78t11 82q0 134-93 228t-227 94h-7l64 64-56 56Z" />
      </svg>
    </button>
  </div>
</template>
