<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center overflow-y-auto z-99999 p-4">
    <!-- Backdrop -->
    <div
      v-if="fullScreenBackdrop"
      class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm transition-opacity"
      @click="$emit('close')"
    ></div>
    
    <!-- Default slot content (modal body) -->
    <div 
      :class="[widthClass, contentClass]"
      class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-h-[94vh] mx-auto transform transition-all duration-300 scale-100 opacity-100"
      role="dialog"
      aria-modal="true"
    >
      <slot></slot> <!-- Default slot cho content -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface ModalProps {
  show?: boolean
  fullScreenBackdrop?: boolean
  maxWidth?: 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl' | '5xl' | '6xl' | '7xl' | 'screen'
  contentClass?: string
}

const props = withDefaults(defineProps<ModalProps>(), {
  show: false,
  fullScreenBackdrop: true,
  maxWidth: '4xl',
  contentClass: '',
})
defineEmits(['close'])

const widthClass = computed(() => {
  const widths = {
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
    '5xl': 'max-w-5xl',
    '6xl': 'max-w-6xl',
    '7xl': 'max-w-7xl',
    screen: 'max-w-[calc(100vw-2rem)]',
  }

  return widths[props.maxWidth] || widths['4xl']
})
</script>
