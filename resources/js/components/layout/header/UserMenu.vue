<template>
  <div class="relative" ref="dropdownRef">
    <!-- User Avatar Button -->
    <button @click.prevent="toggleDropdown" class="relative flex items-center gap-2 p-2 pr-3.5 rounded-xl transition-all duration-200 
         hover:bg-gray-100 dark:hover:bg-gray-800 group border border-gray-200 dark:border-gray-700
         min-w-[52px] xs:min-w-[60px] sm:min-w-[180px] md:min-w-[220px] lg:min-w-[240px]
         ml-1.5 sm:ml-1.5 md:ml-2 lg:ml-2" :class="{ 'bg-gray-50 dark:bg-gray-800 shadow-sm': dropdownOpen }">
      <!-- Avatar + status dot -->
      <div class="relative flex-shrink-0">
        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white dark:border-gray-800 
                bg-gradient-to-br from-blue-100 to-purple-100">
          <img :src="user?.avatar || defaultAvatar" :alt="user?.name || 'User'" class="w-full h-full object-cover" />
        </div>
        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full 
                border-2 border-white dark:border-gray-800"></div>
      </div>

      <!-- Tên user -->
      <div class="hidden sm:flex flex-col items-start flex-1 min-w-0">
        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight truncate 
                 max-w-[140px] md:max-w-[160px] lg:max-w-[180px]">
          {{ user?.name || 'User' }}
        </span>

        <span class="hidden md:block text-xs text-gray-500 dark:text-gray-400 leading-tight truncate 
                 max-w-[140px] lg:max-w-[160px]">
          {{ user?.email || '' }}
        </span>
      </div>

      <!-- Icon mũi tên -->
      <ChevronDownIcon class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500 dark:text-gray-400 
           transition-transform duration-200 group-hover:text-gray-700 dark:group-hover:text-gray-300 
           flex-shrink-0 ml-auto" :class="{ 'rotate-180': dropdownOpen }" />

      <!-- Badge thông báo -->
      <div v-if="notificationsCount > 0" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white 
           text-[10px] font-bold rounded-full min-w-[16px] h-4 
           flex items-center justify-center px-1 border-2 border-white dark:border-gray-900">
        {{ notificationsCount > 99 ? '99+' : notificationsCount }}
      </div>
    </button>

    <!-- Dropdown Menu -->
    <Transition enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition-all duration-150 ease-in" leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95">
      <div v-if="dropdownOpen"
        class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
        <!-- User Info Section -->
        <div class="p-4 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-3">
            <div class="relative">
              <div
                class="w-12 h-12 rounded-full overflow-hidden border-2 border-gray-100 dark:border-gray-800 bg-gradient-to-br from-blue-100 to-purple-100">
                <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover" />
                <div v-else
                  class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-500">
                  <span class="text-base font-bold text-white">{{ getUserInitials(user?.name || 'User') }}</span>
                </div>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                {{ user?.name || 'User Name' }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 truncate mt-0.5">
                {{ user?.email || 'user@example.com' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Menu Items -->
        <div class="p-2">
          <div class="space-y-0.5">
            <a href="/settings"
              class="group w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              <div
                class="w-7 h-7 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                <SettingsIcon
                  class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400 group-hover:text-blue-500 transition-colors" />
              </div>
              <span class="text-sm font-medium">Cài đặt</span>
            </a>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="p-3 border-t border-gray-100 dark:border-gray-800">
          <a href="/logout" @click="signOut"
            class="group w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
            <LogoutIcon class="w-3.5 h-3.5" />
            <span class="text-sm font-semibold">Đăng xuất</span>
          </a>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ChevronDownIcon, LogoutIcon, SettingsIcon } from '@/icons'
import { usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const defaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.value?.name || 'User') + '&background=465fff&color=fff'

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const getUserInitials = (name) => {
  if (!name || name.trim() === '') return 'U'
  const words = name.trim().split(/\s+/)
  if (words.length >= 2) return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase()
  return name.charAt(0).toUpperCase()
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const signOut = () => {
  closeDropdown()
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar,
.asfy-modal-scroll::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track,
.asfy-modal-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb,
.asfy-modal-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.3);
  border-radius: 999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover,
.asfy-modal-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0, 0, 0, 0.5);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb,
.dark .asfy-modal-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.2);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
.dark .asfy-modal-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.4);
}
</style>