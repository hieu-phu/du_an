<template>
    <div class="w-full">
        <label
            v-if="label"
            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
        >
            {{ required ? label : `${label} (nếu có)` }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <textarea
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :disabled="disabled"
            :rows="rows"
            :class="[
                'w-full px-4 py-3 rounded-sm border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500 border-gray-300 dark:border-gray-700',
                error
                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10 dark:border-red-500'
                    : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800'
            ]"
        />
        
        <p v-if="error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
            {{ error[0] }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    modelValue: [String, Number],
    placeholder: String,
    error: String,
    required: Boolean,
    disabled: Boolean,
    rows: {
        type: Number,
        default: 4
    }
})

defineEmits(['update:modelValue'])
</script>
