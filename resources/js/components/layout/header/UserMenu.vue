<template>
  <div ref="dropdownRef" class="relative">
    <button
      type="button"
      @click="toggleDropdown"
      class="flex min-w-[52px] items-center gap-3 rounded-xl border border-gray-200 bg-white px-2 py-2 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800 sm:min-w-[210px] sm:px-3"
    >
      <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 text-sm font-semibold text-white">
        <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name || 'User'" class="h-full w-full object-cover" />
        <span v-else>{{ initials }}</span>
      </div>

      <div class="hidden min-w-0 flex-1 text-left sm:block">
        <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">
          {{ user?.name || 'User' }}
        </div>
        <div class="truncate text-xs text-gray-500 dark:text-gray-400">
          {{ roleLabel }}
        </div>
      </div>

      <ChevronDownIcon class="hidden h-4 w-4 text-gray-500 transition sm:block" :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <Transition
      enter-active-class="transition-all duration-150 ease-out"
      enter-from-class="translate-y-1 opacity-0 scale-95"
      enter-to-class="translate-y-0 opacity-100 scale-100"
      leave-active-class="transition-all duration-100 ease-in"
      leave-from-class="translate-y-0 opacity-100 scale-100"
      leave-to-class="translate-y-1 opacity-0 scale-95"
    >
      <div v-if="dropdownOpen" class="absolute right-0 z-50 mt-2 w-72 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-100 px-4 py-4 dark:border-gray-800">
          <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ user?.name || 'User' }}</div>
          <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ user?.email || '' }}</div>
        </div>

        <div class="p-2">
          <Link
            href="/my-profile"
            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
            @click="closeDropdown"
          >
            <UserCircleIcon class="h-4 w-4" />
            Hồ sơ cá nhân
          </Link>
        </div>

        <div class="border-t border-gray-100 p-2 dark:border-gray-800">
          <button
            type="button"
            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
            @click="handleLogout"
          >
            <LogoutIcon class="h-4 w-4" />
            Đăng xuất
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { ChevronDownIcon, LogoutIcon } from '@/icons'
import UserCircleIcon from '@/icons/UserCircleIcon.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const initials = computed(() => {
  const name = user.value?.name || 'U'
  const parts = name.trim().split(/\s+/)
  if (parts.length < 2) return name.charAt(0).toUpperCase()
  return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase()
})

const roleLabel = computed(() => {
  const role = user.value?.primary_role
  return {
    admin: 'Admin',
    hr: 'HR',
    employee: 'Nhân viên',
  }[role] || 'Tài khoản'
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const handleLogout = () => {
    closeDropdown()
    router.post(route('logout'), {}, {
        onSuccess: () => {
            window.location.href = '/'
        },
        onError: () => {
            // Nếu lỗi (có thể do hết hạn session/419), vẫn đẩy ra trang chủ
            window.location.href = '/'
        },
        onFinish: () => {
            // Đảm bảo thoát hoàn toàn
            if (window.location.pathname !== '/') {
                window.location.href = '/'
            }
        }
    })
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
