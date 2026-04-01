<template>
    <div class="relative">
        <button @click="toggleDropdown" class="relative flex items-center justify-center text-gray-500
            transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11
            hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400
            dark:hover:bg-gray-800 dark:hover:text-white">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                    fill="" />
            </svg>
            <!-- Connection status indicator -->
            <span v-if="isEchoConnected"
                class="absolute top-0 left-0 w-2 h-2 bg-green-500 rounded-full border border-white dark:border-gray-900">
                <span class="absolute inset-0 w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
            </span>
            <span v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 rounded-full"
                :class="priorityBadgeClass">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <transition name="fade">
            <div v-if="showDropdown"
                class="fixed sm:absolute right-2 sm:right-0 left-2 sm:left-auto mt-3 flex h-[85vh] sm:h-[500px] w-auto sm:w-[400px] flex-col rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 z-50 overflow-hidden">
                <!-- Header -->
                <div
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                    <h5 class="text-base font-semibold text-gray-900 dark:text-white">Thông báo</h5>
                    <div class="flex items-center gap-3">
                        <button @click="markAllAsRead" v-if="unreadCount > 0"
                            class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            Đọc tất cả
                        </button>
                        <button @click="toggleDropdown"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                                    fill="" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filter tabs -->
                <!-- Filter tabs with dropdown -->
                <div
                    class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4">
                    <!-- "Tất cả" tab - always visible -->
                    <button @click="activeCategory = 'all'"
                        class="px-4 py-3 text-sm transition-all whitespace-nowrap flex-shrink-0 flex items-center gap-2 relative group"
                        :class="activeCategory === 'all'
                            ? getCategoryColorClass('blue', true).active + ' font-medium'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                        <span>Tất cả</span>
                        <span v-if="getCategoryCount('all') > 0"
                            class="px-2 py-0.5 text-xs font-medium rounded-full min-w-[20px] text-center" :class="activeCategory === 'all'
                                ? 'bg-white/20 text-white'
                                : getCategoryColorClass('blue').badge">
                            {{ getCategoryCount('all') }}
                        </span>
                        <span v-if="activeCategory === 'all'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-t"
                            :class="getCategoryColorClass('blue').border"></span>
                        <span v-if="activeCategory !== 'all'"
                            class="absolute bottom-0 left-1/2 right-1/2 h-0.5 bg-transparent group-hover:bg-gray-300 dark:group-hover:bg-gray-600 transition-all duration-200"></span>
                    </button>

                    <!-- Divider -->
                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>

                    <!-- Category selector -->
                    <div class="relative" ref="dropdownRef">
                        <button @click="showCategoryDropdown = !showCategoryDropdown"
                            class="px-4 py-3 text-sm transition-all whitespace-nowrap flex-shrink-0 flex items-center gap-2 relative group"
                            :class="activeCategory !== 'all'
                                ? getCategoryColorClass(categories.find(c => c.value === activeCategory)?.color || 'blue', true).active + ' font-medium'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">

                            <span v-if="activeCategory !== 'all'" class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                    :class="getCategoryColorClass(categories.find(c => c.value === activeCategory)?.color || 'blue').border"></span>
                                {{categories.find(c => c.value === activeCategory)?.label}}
                                <span v-if="getCategoryCount(activeCategory) > 0"
                                    class="px-2 py-0.5 text-xs font-medium rounded-full min-w-[20px] text-center"
                                    :class="activeCategory !== 'all'
                                        ? 'bg-white/20 text-white'
                                        : getCategoryColorClass(categories.find(c => c.value === activeCategory)?.color || 'blue').badge">
                                    {{ getCategoryCount(activeCategory) }}
                                </span>
                            </span>

                            <span v-else class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                <span>Danh mục</span>
                            </span>

                            <svg class="w-4 h-4 transition-transform" :class="showCategoryDropdown ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>

                            <span v-if="activeCategory !== 'all'"
                                class="absolute bottom-0 left-0 right-0 h-0.5 rounded-t"
                                :class="getCategoryColorClass(categories.find(c => c.value === activeCategory)?.color || 'blue').border"></span>
                            <span v-else
                                class="absolute bottom-0 left-1/2 right-1/2 h-0.5 bg-transparent group-hover:bg-gray-300 dark:group-hover:bg-gray-600 transition-all duration-200"></span>
                        </button>

                        <!-- Dropdown menu -->
                        <transition enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 -translate-y-2"
                            enter-to-class="transform opacity-100 translate-y-0"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 translate-y-0"
                            leave-to-class="transform opacity-0 -translate-y-2">
                            <div v-if="showCategoryDropdown"
                                class="absolute right-0 top-full mt-1 w-64 rounded-lg shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-2 z-50 max-h-80 overflow-y-auto">
                                <div
                                    class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Chọn danh mục
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                                    <button v-for="cat in categories.filter(c => c.value !== 'all')" :key="cat.value"
                                        @click="activeCategory = cat.value; showCategoryDropdown = false"
                                        class="w-full px-4 py-2.5 text-sm text-left transition-colors flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-750 group/item"
                                        :class="activeCategory === cat.value
                                            ? getCategoryColorClass(cat.color, true).active + ' font-medium'
                                            : 'text-gray-700 dark:text-gray-300'">
                                        <span class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                                :class="getCategoryColorClass(cat.color).border"></span>
                                            <span>{{ cat.label }}</span>
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span v-if="getCategoryCount(cat.value) > 0"
                                                class="px-2 py-0.5 text-xs font-medium rounded-full min-w-[20px] text-center"
                                                :class="activeCategory === cat.value
                                                    ? 'bg-white/20 text-white'
                                                    : getCategoryColorClass(cat.color).badge">
                                                {{ getCategoryCount(cat.value) }}
                                            </span>
                                            <svg v-if="activeCategory === cat.value" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Notifications list -->
                <ul class="flex-1 overflow-y-auto custom-scrollbar" @scroll="handleScroll">
                    <li v-if="filteredNotifications.length === 0"
                        class="p-8 text-center text-gray-500 dark:text-gray-400 flex flex-col items-center justify-center h-full">
                        <div id="bell-notifi">
                            <BellIcon />
                        </div>
                        <p class="font-medium text-sm">Không có thông báo</p>
                    </li>

                    <li v-for="notification in filteredNotifications" :key="notification.id">
                        <a @click.prevent="handleNotificationClick(notification)"
                            class="flex gap-3 border-b border-gray-100 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer transition-all duration-150 group relative"
                            :class="!notification.read_at ? 'bg-blue-50/30 dark:bg-blue-900/5' : ''" href="#">

                            <!-- Content -->
                            <div class="flex-1 min-w-0 space-y-2">
                                <!-- Title -->
                                <div class="flex items-start justify-between gap-2">
                                    <h6 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1 flex-1"
                                        :class="!notification.read_at ? 'font-bold' : ''">
                                        {{ notification.title }}
                                    </h6>
                                    <button @click.stop="deleteNotification(notification.id)"
                                        class="flex-shrink-0 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all duration-150 opacity-0 group-hover:opacity-100 p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Message -->
                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                    {{ notification.message }}
                                </p>

                                <!-- Meta info and badges -->
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <!-- Time -->
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatTime(notification.created_at) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span v-if="notification.category"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium"
                                            :class="getCategoryColorClass(getCategoryColor(notification.category)).badge">

                                            <!-- SVG với fill="currentColor" sẽ lấy màu từ class text-* -->
                                            <svg class="w-3.5 h-3.5" viewBox="0 -960 960 960" fill="currentColor"
                                                :class="getCategoryColorClass(getCategoryColor(notification.category)).icon">
                                                <path
                                                    d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h440q19 0 36 8.5t28 23.5l216 288-216 288q-11 15-28 23.5t-36 8.5H160Zm0-80h440l180-240-180-240H160v480Zm220-240Z" />
                                            </svg>

                                            <span>{{ getCategoryLabel(notification.category) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>

                    <!-- Loading more indicator -->
                    <li v-if="isLoadingMore" class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2 text-gray-500 dark:text-gray-400">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="text-sm">Đang tải thêm...</span>
                        </div>
                    </li>

                    <!-- End of list indicator -->
                    <li v-if="!hasMoreNotifications && filteredNotifications.length > 0" class="p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Đã hiển thị tất cả thông báo</p>
                    </li>
                </ul>
            </div>
        </transition>

        <!-- Overlay để đóng dropdown khi click ngoài -->
        <div v-if="showDropdown" @click="showDropdown = false" class="fixed inset-0 z-40"></div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue3-toastify'
import UserCircleIcon from '@/icons/UserCircleIcon.vue'
import BellIcon from '@/icons/BellIcon.vue'

const showDropdown = ref(false);
const showCategoryDropdown = ref(false);
const notifications = ref([]);
const activeCategory = ref('all');
const currentPage = ref(1);
const hasMoreNotifications = ref(true);
const isLoadingMore = ref(false);
const unreadCounts = ref({
    all: 0,
    general: 0,
    user: 0,
    order: 0,
    system: 0,
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const broadcastEnabled = ref(false);
const isEchoConnected = ref(false);
let userChannel = null;


const categories = [
    { value: 'all', label: 'Tất cả', color: 'blue' },
    { value: 'user', label: 'Nhân sự', color: 'purple' },
    { value: 'system', label: 'Hệ thống', color: 'orange' },
    { value: 'purchase_order', label: 'Đơn mua', color: 'green' },
    { value: 'sale_order', label: 'Đơn bán', color: 'red' },
    { value: 'import_warehouse', label: 'Nhập kho', color: 'teal' },
    { value: 'export_warehouse', label: 'Xuất kho', color: 'cyan' },
];

// Thêm helper function để lấy class màu
const getCategoryColorClass = (color, isActive = false) => {
    const colors = {
        blue: {
            badge: isActive
                ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/50',
            active: 'text-blue-600 dark:text-blue-400',
            border: 'bg-blue-600 dark:bg-blue-400',
            icon: 'text-blue-600 dark:text-blue-400',
            badgeActive: 'bg-blue-600 dark:bg-blue-500 text-white'
        },
        purple: {
            badge: isActive
                ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800'
                : 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-800/50',
            active: 'text-purple-600 dark:text-purple-400',
            border: 'bg-purple-600 dark:bg-purple-400',
            icon: 'text-purple-600 dark:text-purple-400',
            badgeActive: 'bg-purple-600 dark:bg-purple-500 text-white'
        },
        orange: {
            badge: isActive
                ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800'
                : 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800/50',
            active: 'text-orange-600 dark:text-orange-400',
            border: 'bg-orange-600 dark:bg-orange-400',
            icon: 'text-orange-600 dark:text-orange-400',
            badgeActive: 'bg-orange-600 dark:bg-orange-500 text-white'
        },
        green: {
            badge: isActive
                ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800'
                : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800/50',
            active: 'text-green-600 dark:text-green-400',
            border: 'bg-green-600 dark:bg-green-400',
            icon: 'text-green-600 dark:text-green-400',
            badgeActive: 'bg-green-600 dark:bg-green-500 text-white'
        },
        red: {
            badge: isActive
                ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800'
                : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800/50',
            active: 'text-red-600 dark:text-red-400',
            border: 'bg-red-600 dark:bg-red-400',
            icon: 'text-red-600 dark:text-red-400',
            badgeActive: 'bg-red-600 dark:bg-red-500 text-white'
        },
        teal: {
            badge: isActive
                ? 'bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800'
                : 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 border border-teal-100 dark:border-teal-800/50',
            active: 'text-teal-600 dark:text-teal-400',
            border: 'bg-teal-600 dark:bg-teal-400',
            icon: 'text-teal-600 dark:text-teal-400',
            badgeActive: 'bg-teal-600 dark:bg-teal-500 text-white'
        },
        cyan: {
            badge: isActive
                ? 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800'
                : 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 border border-cyan-100 dark:border-cyan-800/50',
            active: 'text-cyan-600 dark:text-cyan-400',
            border: 'bg-cyan-600 dark:bg-cyan-400',
            icon: 'text-cyan-600 dark:text-cyan-400',
            badgeActive: 'bg-cyan-600 dark:bg-cyan-500 text-white'
        },
        gray: {
            badge: isActive
                ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600'
                : 'bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-600/50',
            active: 'text-gray-600 dark:text-gray-400',
            border: 'bg-gray-600 dark:bg-gray-400',
            icon: 'text-gray-600 dark:text-gray-400',
            badgeActive: 'bg-gray-600 dark:bg-gray-500 text-white'
        }
    };

    return colors[color] || colors.blue;
};
const dropdownRef = ref(null);

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        showCategoryDropdown.value = false;
    }
};

const getCategoryColor = (categoryValue) => {
    const category = categories.find(c => c.value === categoryValue);
    return category?.color || 'gray';
};


onMounted(() => {
    document.addEventListener('click', handleClickOutside);

});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const filteredNotifications = computed(() => {
    if (activeCategory.value === 'all') return notifications.value;
    return notifications.value.filter(n => n.category === activeCategory.value);
});

const unreadCount = computed(() => unreadCounts.value.all);

const priorityBadgeClass = computed(() => {
    const urgentCount = notifications.value.filter(
        n => !n.read_at && n.data?.priority === 'urgent'
    ).length;
    return urgentCount > 0 ? 'bg-red-600 animate-pulse' : 'bg-blue-600';
});

const getCategoryCount = (category) => {
    return unreadCounts.value[category] || 0;
};

const getPriorityClass = (priority) => {
    const classes = {
        urgent: 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
        high: 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300',
        normal: 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
        low: 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    };
    return classes[priority] || classes.normal;
};

const getPriorityBorderClass = (priority) => {
    if (priority === 'urgent') return 'border-l-[3px] !border-l-red-500 dark:!border-l-red-400';
    if (priority === 'high') return 'border-l-[3px] !border-l-orange-500 dark:!border-l-orange-400';
    return '';
};

const getPriorityLabel = (priority) => {
    const labels = {
        urgent: 'Khẩn cấp',
        high: 'Quan trọng',
        normal: 'Bình thường',
        low: 'Thấp',
    };
    return labels[priority] || labels.normal;
};

const getCategoryLabel = (category) => {
    const cat = categories.find(c => c.value === category);
    return cat ? cat.label : category;
};

const getAssetUrl = (path) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return `${window.location.origin}/storage/${path}`;
};

const handleImageError = (e) => {
    e.target.src = '/images/default-avatar.png';
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) {
        activeCategory.value = 'all';
        showCategoryDropdown.value = false; // đảm bảo đóng menu danh mục
        currentPage.value = 1;
        hasMoreNotifications.value = true;
        fetchNotifications(true);
        fetchUnreadCounts();
    }
};

const toggleCategoryDropdown = (e) => {
    e.stopPropagation();           // rất quan trọng
    showCategoryDropdown.value = !showCategoryDropdown.value;
};

const getSubdomain = () => {
    const host = window.location.hostname;
    const parts = host.split('.');
    if (parts.length > 2) {
        return parts[0];
    }
    return 'main';
};

const fetchUnreadCounts = async () => {
    try {
        const response = await axios.get('/api/notifications/unread-count');
        if (response.data.success && response.data.by_category) {
            unreadCounts.value = response.data.by_category;
        }
    } catch (error) {
        console.error('Error fetching unread counts:', error);
    }
};

const fetchNotifications = async (reset = false) => {
    try {
        if (reset) {
            currentPage.value = 1;
            hasMoreNotifications.value = true;
        }

        const response = await axios.get('/api/notifications', {
            params: { page: currentPage.value }
        });

        const newNotifications = response.data.data.data || [];

        if (reset) {
            notifications.value = newNotifications;
        } else {
            notifications.value = [...notifications.value, ...newNotifications];
        }
        hasMoreNotifications.value = response.data.data.next_page_url !== null;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const handleNotificationClick = async (notification) => {
    await markAsRead(notification);
    if (notification.url_link) {
        window.location.href = notification.url_link;
    } else if (notification.data?.action_url) {
        window.location.href = notification.data.action_url;
    }
};

const markAsRead = async (notification) => {
    if (!notification.read_at) {
        try {
            await axios.post(`/api/notifications/${notification.id}/mark-as-read`);
            notification.read_at = new Date().toISOString();
            fetchUnreadCounts();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/api/notifications/mark-all-read');
        notifications.value.forEach(n => {
            n.read_at = new Date().toISOString();
        });
        fetchUnreadCounts();
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

const deleteNotification = async (id) => {
    try {
        await axios.delete(`/api/notifications/${id}`);
        notifications.value = notifications.value.filter(n => n.id !== id);
        fetchUnreadCounts();
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
};

const loadMoreNotifications = async () => {
    if (!hasMoreNotifications.value || isLoadingMore.value) return;

    isLoadingMore.value = true;
    currentPage.value++;
    await fetchNotifications(false);
    isLoadingMore.value = false;
};

const handleScroll = (event) => {
    const element = event.target;
    const scrollPercentage = (element.scrollTop + element.clientHeight) / element.scrollHeight;

    if (scrollPercentage > 0.8 && hasMoreNotifications.value && !isLoadingMore.value) {
        loadMoreNotifications();
    }
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'Vừa xong';
    if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} ngày trước`;
    return date.toLocaleDateString('vi-VN');
};

const setupRealtime = () => {
    if (broadcastEnabled.value) {
        console.log('Broadcasting already enabled, skipping setup');
        return;
    }
    if (!user.value) {
        console.warn('User not available');
        return;
    }

    if (!window.Echo) {
        console.warn('Echo not available - broadcasting disabled, using polling fallback');
        isEchoConnected.value = false;
        return;
    }

    try {
        // Monitor connection status
        if (window.Echo.connector?.pusher) {
            const pusher = window.Echo.connector.pusher;

            pusher.connection.bind('connected', () => {
                isEchoConnected.value = true;
                console.log('✅ Echo connected successfully');
            });

            pusher.connection.bind('disconnected', () => {
                isEchoConnected.value = false;
                console.warn('❌ Echo disconnected');
            });

            pusher.connection.bind('failed', () => {
                isEchoConnected.value = false;
                console.error('❌ Echo connection failed - switching to polling');
            });

            pusher.connection.bind('error', (err) => {
                isEchoConnected.value = false;
                console.error('❌ Echo connection error:', err);
            });
            if (pusher.connection.state === 'connected') {
                isEchoConnected.value = true;
            } else if (pusher.connection.state === 'unavailable' || pusher.connection.state === 'failed') {
                return;
            }
        }
        if (!userChannel) {
            userChannel = joinChannel(`user.${user.value.id}.${getSubdomain()}.notifications`);
        }


        broadcastEnabled.value = true;
        console.log('✅ Broadcasting enabled');
    } catch (error) {
        console.error('❌ Failed to setup broadcasting:', error);
        isEchoConnected.value = false;
    }
};

const joinChannel = (channel) => {
    return window.Echo.private(channel)
        .listen('.notification.created', (event) => {
            const notification = event;
            window.dispatchEvent(new CustomEvent('notification-received', {
                detail: notification
            }));

            // Kiểm tra nếu tab không được kích hoạt
            if (document.hidden) {
                // Sử dụng thông báo của trình duyệt
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(notification.title, {
                        body: notification.message,
                        icon: '/logo.png',
                        badge: '/logo.png',
                        tag: notification.id,
                        onClick: () => {
                            window.focus();
                            if (notification.url_link) {
                                window.location.href = notification.url_link;
                            }
                        }
                    });
                } else if ('Notification' in window && Notification.permission !== 'denied') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            new Notification(notification.title, {
                                body: notification.message,
                                icon: '/logo.png',
                                badge: '/logo.png',
                                tag: notification.id,
                                onClick: () => {
                                    window.focus();
                                    if (notification.url_link) {
                                        window.location.href = notification.url_link;
                                    }
                                }
                            });
                        }
                    });
                }
            } else {
                // Tab đang được kích hoạt, hiển thị toast
                toast.success(notification.message || 'Bạn có một thông báo mới.', {
                    onClick: () => {
                        if (notification.url_link) {
                            window.location.href = notification.url_link;
                        } else if (notification.data?.action_url) {
                            window.location.href = notification.data.action_url;
                        }
                    },
                    style: {
                        cursor: notification.url_link || notification.data?.action_url ? 'pointer' : 'default'
                    }
                });
            }

            fetchUnreadCounts();

            // Thông báo khẩn cấp luôn hiển thị browser notification
            if (notification.data?.priority === 'urgent' && 'Notification' in window) {
                if (Notification.permission === 'granted') {
                    new Notification(notification.title, {
                        body: notification.message,
                        icon: '/logo.png',
                        requireInteraction: true
                    });
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            new Notification(notification.title, {
                                body: notification.message,
                                icon: '/logo.png',
                                requireInteraction: true
                            });
                        }
                    });
                }
            }
        })
        .error((error) => {
            console.error('❌ Error subscribing to channel:', error);
            isEchoConnected.value = false;
            setupPolling();
        });
}
onMounted(() => {
    fetchNotifications(true);
    fetchUnreadCounts();
    setTimeout(() => {
        setupRealtime();
    }, 100);
});

onUnmounted(() => {
    if (userChannel && window.Echo) {
        window.Echo.leave(`user.${user.value.id}.${getSubdomain()}.notifications`);
        userChannel = null;
    }

});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .custom-scrollbar {
    scrollbar-color: #475569 transparent;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }

    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-in;
}

span,
p,
button {
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

@media (max-width: 640px) {
    .line-clamp-2 {
        -webkit-line-clamp: 1;
    }

    div[class*="absolute"][class*="right-0"] {
        right: -0.5rem !important;
        max-width: min(380px, calc(100vw - 1rem));
    }
}

.group:hover .group-hover\:opacity-100 {
    transition: opacity 0.15s ease;
}

.dark .bg-gray-750 {
    background-color: rgb(31 41 55 / 1);
}

.dark .dark\:bg-gray-750 {
    background-color: rgb(31 41 55 / 1);
}

#bell-notifi svg {
    width: 50px;
    height: 50px;
}
</style>
