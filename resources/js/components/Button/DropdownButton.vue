<template>
  <div ref="dropdownRef" class="relative inline-block text-left">
    <!-- Button trigger -->
    <Button
      ref="buttonRef"
      :variant="variant"
      :size="size"
      :startIcon="startIcon"
      :endIcon="ChevronDownIcon"
      @click="toggleDropdown"
      class="shadow-sm transform transition-all"
    >
      {{ label }}
    </Button>

    <!-- Dropdown menu -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl ring-1 ring-black/5 z-50"
    >
      <div class="py-1">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Button from '@/components/ui/Button.vue'
import ChevronDownIcon from '@/icons/ChevronDownIcon.vue'

const props = defineProps({
  label: String,
  variant: {
    type: String,
    default: 'primary'
  },
  size: {
    type: String,
    default: 'md'
  },
  startIcon: Object
})

const isOpen = ref(false)
const buttonRef = ref(null)
const dropdownRef = ref(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

// Handle click outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside, true)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside, true)
})

defineExpose({
  closeDropdown
})
</script>