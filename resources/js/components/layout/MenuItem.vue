<template>
    <li>
        <!-- Item có subItems → button mở submenu -->
        <button v-if="item.subItems" @click="toggleSubmenuLocal"
            class="flex items-center gap-3 rounded-lg px-1 py-2.5 text-sm font-medium transition-all duration-200 group w-full text-left"
            :class="[
                {
                    'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400':
                        isSubmenuOpenLocal || hasActiveChild,
                    'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800':
                        !(isSubmenuOpenLocal || hasActiveChild),
                },
                !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
            ]" :style="{ paddingLeft: level > 0 ? `${level * 1.5}rem` : '' }">
            <!-- ICON: chỉ hiện icon thật ở cấp 0, các cấp con dùng chấm tròn -->
            <component v-if="level === 0 && item.icon" :is="getIconComponent(item.icon)" />
            <!-- Tên menu -->
            <span v-if="isExpanded || isHovered || isMobileOpen" class="flex-1 text-left">
                {{ item.name }}
            </span>

            <!-- Mũi tên Chevron -->
            <ChevronDownIcon v-if="isExpanded || isHovered || isMobileOpen"
                class="ml-auto w-5 h-5 transition-transform duration-200"
                :class="{ 'rotate-180 text-brand-500': isSubmenuOpenLocal }" />
        </button>

        <!-- Item lá có path → Link bình thường -->
        <a target="_blank" v-else-if="item.path" :href="item.path"
            class="flex items-center gap-3 rounded-lg px-1 py-2.5 text-sm font-medium transition-all duration-200 group w-full"
            :class="[
                {
                    'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400': isActive(item.path),
                    'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800': !isActive(item.path),
                },
                !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
            ]" :style="{ paddingLeft: level > 0 ? `${level}rem` : '' }">
            <!-- ICON cho item lá: cấp 0 có icon thật, cấp con dùng chấm tròn -->
            <component v-if="level === 0 && item.icon" :is="getIconComponent(item.icon)" />

            <span v-if="isExpanded || isHovered || isMobileOpen" class="flex-1 text-left">
                {{ item.name }}
            </span>
        </a>

        <!-- Item không có path/subItems (hiếm, ví dụ placeholder) -->
        <div v-else class="flex items-center gap-3 px-1 py-2.5 text-sm font-medium text-gray-500"
            :style="{ paddingLeft: level > 0 ? `${level * 1.5}rem` : '' }">
            <span v-if="isExpanded || isHovered || isMobileOpen">
                {{ item.name }}
            </span>
        </div>

        <!-- Submenu con (đệ quy) -->
        <transition @enter="startTransition" @after-enter="endTransition" @before-leave="startTransition"
            @after-leave="endTransition">
            <div v-show="isSubmenuOpenLocal && item.subItems && (isExpanded || isHovered || isMobileOpen)">
                <ul :class="['mt-2 space-y-1', level === 0 ? 'ml-9' : 'ml-1']">
                    <MenuItem v-for="subItem in item.subItems" :key="subItem.name" :item="subItem"
                        :groupIndex="groupIndex" :itemIndex="`${itemIndex}-${subItem.name}`" :level="level + 1"
                        :isExpanded="isExpanded" :isHovered="isHovered" :isMobileOpen="isMobileOpen" />
                </ul>
            </div>
        </transition>
    </li>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ChevronDownIcon } from '../../icons'
import { useSidebar } from '@/composables/useSidebar'
import MenuItem from './MenuItem.vue' // tự import để đệ quy

const props = defineProps({
    item: { type: Object, required: true },
    groupIndex: { type: Number, required: true },
    itemIndex: { type: [Number, String], required: true },
    level: { type: Number, default: 0 },
    isExpanded: Boolean,
    isHovered: Boolean,
    isMobileOpen: Boolean,
})

const page = usePage()
const { toggleSubmenu, isSubmenuOpen } = useSidebar()

const menuKey = computed(() => `${props.groupIndex}-${props.itemIndex}`)
const isSubmenuOpenLocal = computed(() => isSubmenuOpen(menuKey.value))

const toggleSubmenuLocal = () => {
    toggleSubmenu(menuKey.value)
}

const isActive = (path: string) => {
    const currentUrl = page.url.split('?')[0]
    try {
        const pathClean = new URL(path, window.location.origin).pathname
        return currentUrl === pathClean || currentUrl.startsWith(pathClean + '/')
    } catch {
        return false
    }
}

const hasActiveChild = computed(() => {
    if (!props.item.subItems) return false
    const checkActive = (items: any[]): boolean => {
        return items.some((subItem) => {
            if (subItem.path && isActive(subItem.path)) return true
            if (subItem.subItems) return checkActive(subItem.subItems)
            return false
        })
    }
    return checkActive(props.item.subItems)
})

// Load tất cả icon
const iconModules = import.meta.glob('../../icons/*.vue', { eager: true })

const getIconComponent = (iconName: string) => {
    if (!iconName) return null
    const modulePath = `../../icons/${iconName}.vue`
    if (iconModules[modulePath]) {
        return (iconModules[modulePath] as any).default
    }
    console.warn(`Icon ${iconName} not found`)
    return null
}

// Animation mở submenu
const startTransition = (el: HTMLElement) => {
    el.style.height = '0px'
    el.offsetHeight // reflow
    el.style.height = `${el.scrollHeight}px`
}

const endTransition = (el: HTMLElement) => {
    el.style.height = ''
}
</script>