<template>
    <div class="w-full">
        <!-- Label -->
        <label v-if="label" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ required ? label : `${label} (nếu có)` }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <!-- Input wrapper -->
        <div class="relative flex">
            <!-- Prefix slot -->
            <slot name="prefix" />

            <input :type="type" :value="modelValue" @input="handleInput($event)"
                :placeholder="placeholder" :readonly="readonly" :disabled="disabled" :class="[
                    'w-full px-4 py-3 border bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500',
                    error ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-700',
                    $slots.prefix ? 'rounded-r-sm' : 'rounded-sm'
                ]" />

            <!-- Unit -->
            <span v-if="unit" class="absolute inset-y-0 right-3 flex items-center text-sm
               text-gray-500 dark:text-gray-400 pointer-events-none mr-10">
                {{ unit }}
            </span>
        </div>


        <!-- Error -->
        <p v-if="error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
            {{ isString(error) ? error : error[0] }}
        </p>
    </div>
</template>


<script setup>
import { isString } from 'lodash'

const props = defineProps({  // ← thêm "const props ="
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    modelValue: [String, Number],
    placeholder: String,
    error: Array,
    required: Boolean,
    disabled: Boolean,
    readonly: {
        type: Boolean,
        default: false
    },
    unit: {
        type: String,
        default: ''
    },
    prefix: {
        type: String,
        default: ''
    },
    allowDecimal: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'input']) 

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
