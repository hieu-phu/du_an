<template>
    <div class="w-full">
        <label v-if="label" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ required ? label : `${label} (nếu có)` }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input :type="type" :value="inputValue" @input="handleInput" :placeholder="placeholder" :readonly="readonly"
                :disabled="disabled" :class="[
                    'w-full px-4 py-3 rounded-sm border bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500',
                    error ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-700'
                ]" />
            <span v-if="unit"
                class="absolute inset-y-0 right-3 flex items-center text-sm text-gray-500 dark:text-gray-400 pointer-events-none">
                {{ unit }}
            </span>
        </div>

        <p v-if="error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
            {{ isString(error) ? error : error[0] }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { isString } from 'lodash';

const props = defineProps({
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    // Hỗ trợ v-model
    modelValue: [String, Number],
    // Hỗ trợ :value
    value: [String, Number],
    placeholder: String,
    error: Array,
    required: {
        type: Boolean,
        default: false
    },
    disabled: Boolean,
    readonly: {
        type: Boolean,
        default: false
    },
    unit: {
        type: String,
        default: ''
    },
    allowDecimal: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'input'])

// Computed để xác định giá trị hiển thị
// Ưu tiên modelValue (v-model) trước, sau đó mới đến value
const inputValue = computed(() => {
    return props.modelValue !== undefined ? props.modelValue : props.value
})

// Xử lý sự kiện input
const handleInput = (event) => {
    let newValue = event.target.value
    if (!props.allowDecimal && props.type === 'number') {
        newValue = newValue.replace(/[.,].*/g, '')
        event.target.value = newValue        
    }
    emit('update:modelValue', newValue)
    emit('input', event)
}
</script>

<style scoped>
input[type="number"] {
    appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>