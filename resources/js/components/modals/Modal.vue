<template>
  <div class="fixed inset-0 flex items-center justify-center overflow-y-auto modal z-9999">
    <div class="fixed inset-0 h-full w-full bg-black/70 " aria-hidden="true" @click="$emit('close')">
    </div>
    <slot name="body"></slot>
  </div>
</template>

<style>
form .asfy-modal-scroll {
  scrollbar-width: thin;
  /* Firefox */
  scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
  /* Firefox */
}

/* Chrome, Edge, Safari */
form .asfy-modal-scroll::-webkit-scrollbar {
  width: 6px;
  /* độ thon của thanh scroll */
}

form .asfy-modal-scroll::-webkit-scrollbar-track {
  background: transparent;
}

form .asfy-modal-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.3);
  border-radius: 999px;
}

form .asfy-modal-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0, 0, 0, 0.5);
}


.asfy-modal-scroll {
  scrollbar-width: thin;
  /* Firefox */
  scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
  /* Firefox */
}

/* Chrome, Edge, Safari */
.asfy-modal-scroll::-webkit-scrollbar {
  width: 6px;
  /* độ thon của thanh scroll */
}

.asfy-modal-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.asfy-modal-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.3);
  border-radius: 999px;
}

.asfy-modal-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0, 0, 0, 0.5);
}

</style>