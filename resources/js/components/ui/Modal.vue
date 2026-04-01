<template>
  <div class="fixed inset-0 flex items-center justify-center overflow-y-auto z-99999 p-4">
    <!-- Backdrop -->
    <div
      v-if="fullScreenBackdrop"
      class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm transition-opacity"
      @click="$emit('close')"
    ></div>
    
    <!-- Default slot content (modal body) -->
    <div 
      v-if="show" 
      class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] mx-auto transform transition-all duration-300 scale-100 opacity-100"
      role="dialog"
      aria-modal="true"
    >
      <slot></slot> <!-- Default slot cho content -->
    </div>
  </div>
</template>

<script setup lang="ts">
interface ModalProps {
  show?: boolean
  fullScreenBackdrop?: boolean
}

const props = defineProps<ModalProps>()
defineEmits(['close'])
</script>