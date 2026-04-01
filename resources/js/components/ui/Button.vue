<template>
  <button :title="title" :class="[
    'group relative inline-flex items-center justify-center font-medium gap-2 transition-all duration-300 uppercase',
    sizeClasses[size],
    variantClasses[variant],
    className,
    { 'cursor-not-allowed opacity-50': disabled },
  ]" @click="onClick" :disabled="disabled">

    <!-- Background layer -->
    <span v-if="variant === 'primary'"
      class="absolute inset-0 rounded-sm bg-brand-500 group-hover:bg-brand-600 transition-colors duration-300"></span>
    <span v-if="variant === 'outline'"
      class="absolute inset-0 rounded-sm border-2 border-gray-300 group-hover:border-gray-400 transition-colors duration-300 dark:border-gray-700 dark:group-hover:border-gray-600"></span>

    <span v-if="variant === 'orange'"
      class="absolute inset-0 rounded-sm bg-orange-500 group-hover:bg-orange-600 transition-colors duration-300"></span>
    <!-- Hover effect -->
    <span
      class="absolute inset-0 rounded-sm bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>

    <!-- Content -->
    <span class="relative z-10 flex items-center gap-2">
      <span v-if="startIcon" class="flex items-center">
        <component :is="startIcon" />
      </span>
      <slot></slot>
      <span v-if="endIcon" class="flex items-center">
        <component :is="endIcon" />
      </span>
    </span>

    <!-- Ripple effect (optional) -->
    <span class="absolute inset-0 rounded-sm overflow-hidden">
      <span
        class="absolute inset-0 bg-white/20 scale-0 group-hover:scale-100 transition-transform duration-500 opacity-0 group-hover:opacity-100"></span>
    </span>
  </button>
</template>

<script setup lang="ts">

interface ButtonProps {
  size?: 'sm' | 'md'
  variant?: 'primary' | 'outline' | 'orange'
  startIcon?: object
  endIcon?: object
  onClick?: () => void
  className?: string
  disabled?: boolean
  title: ""
}

const props = withDefaults(defineProps<ButtonProps>(), {
  size: 'md',
  variant: 'primary',
  className: '',
  disabled: false,
})

const sizeClasses = {
  sm: 'px-4 py-2 text-sm rounded-sm',
  md: 'px-4 py-2 text-sm rounded-sm',
}

const variantClasses = {
  primary: 'text-white shadow-md hover:shadow-lg active:shadow-sm active:translate-y-0.5',
  outline: 'text-gray-800 dark:text-gray-300 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md active:shadow-none active:translate-y-0.5',
  orange: 'text-white shadow-md',

}

const onClick = () => {
  if (!props.disabled && props.onClick) {
    props.onClick()
  }
}
</script>