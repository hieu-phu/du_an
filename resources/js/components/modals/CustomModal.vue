<template>
  <div :class="['fixed', 'inset-0', 'flex', 'items-center', 'justify-center', 'modal', props.z_index]">
    <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-sm" aria-hidden="true"  @click="onClose" >
    </div>
    <div :class="props.custom_class">

      <!-- Header - Fixed -->
      <div class="modal-header flex-shrink-0 px-6 py-4  border-gray-200 dark:border-gray-700">
        <!-- close btn -->
        <button @click="onClose"
          class="transition-color absolute right-4 top-4 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <CloseCircleIcont></CloseCircleIcont>
        </button>

        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90 pr-12">
          {{ props.title }}
        </h4>
      </div>

      <slot name="body">

      </slot>
    </div>
  </div>
</template>
<script setup>
import CloseCircleIcont from '@/icons/CloseCircleIcont.vue';
const props = defineProps({
  title: {
    type: String,
    default: ""
  },
  max_with: {
    type: String,
    default: 900
  },
  custom_class: {
    type: Array,
    default: () => [
      "relative",
      "w-full",
      "max-w-[900px]",
      "flex",
      "flex-col",
      "rounded-sm",
      "bg-white",
      "dark:bg-gray-900",
      "overflow-hidden",
      "max-h-[90vh]",
      "md:max-h-[85vh]",
    ]
  },
  z_index: {
    type: String,
    default: "z-9999"
  }
})

const emit = defineEmits(['close'])

const onClose = () => {
  emit('close')
}
</script>