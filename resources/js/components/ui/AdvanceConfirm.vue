<template>
    <transition name="fade">
        <div v-if="visible" class="fixed inset-0 bg-black/50 flex items-center justify-center" style="z-index: 9999;">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md animate-scale relative">
                <!-- Close Button (X) -->
                <button 
                    v-if="showCloseButton"
                    @click="cancel"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
                    aria-label="Đóng">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Icon Section -->
                <div v-if="showIcon" class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center">
                        <img src="/images/icons/accountant.png" alt="Success"
                            class="w-20 h-20 object-contain" />
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    {{ title }}
                </h2>

                <!-- Message -->
                <p class="text-gray-600 mb-6" v-html="message"></p>

                <!-- Actions - Flexible Layout -->
                <div :class="actionsLayoutClass">
                    <!-- Cancel Button (optional) -->
                    <button 
                        v-if="showCancelButton"
                        @click="cancel"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        {{ cancelText }}
                    </button>

                    <!-- Dynamic Action Buttons -->
                    <button 
                        v-for="(action, index) in actions" 
                        :key="index"
                        @click="handleAction(action.value)"
                        :class="action.class || defaultActionClass">
                        {{ action.text }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { nextTick, ref, computed } from "vue";

const visible = ref(false);
const message = ref("");
const title = ref("Xác nhận");
const cancelText = ref("Hủy");
const showIcon = ref(false);
const showCancelButton = ref(true);
const showCloseButton = ref(true);
const actions = ref([]);

const defaultActionClass = [
    "px-4",
    "py-2",
    "rounded-lg",
    "bg-blue-600",
    "text-white",
    "hover:bg-blue-700",
    "transition"
];

let resolver = null;

// Tính toán layout class dựa vào số lượng buttons
const actionsLayoutClass = computed(() => {
    const totalButtons = actions.value.length + (showCancelButton.value ? 1 : 0);
    
    if (totalButtons <= 2) {
        return "flex justify-end gap-3";
    } else if (totalButtons === 3) {
        return "flex flex-wrap justify-end gap-3";
    } else {
        return "grid grid-cols-2 gap-3";
    }
});

/**
 * Mở dialog với cấu hình tùy chỉnh
 * @param {Object} options - Cấu hình dialog
 * @param {string} options.title - Tiêu đề
 * @param {string} options.message - Nội dung thông báo
 * @param {boolean} options.icon - Hiển thị icon
 * @param {boolean} options.showCancel - Hiển thị nút hủy (mặc định: true)
 * @param {boolean} options.showClose - Hiển thị nút X đóng (mặc định: true)
 * @param {string} options.cancelText - Text nút hủy
 * @param {Array} options.actions - Danh sách các action buttons
 * @example
 * actions: [
 *   { text: "Thanh toán", value: "payment", class: ["bg-green-600", "hover:bg-green-700", "text-white", "px-4", "py-2", "rounded-lg"] },
 *   { text: "Công nợ", value: "debt", class: ["bg-yellow-600", "hover:bg-yellow-700", "text-white", "px-4", "py-2", "rounded-lg"] }
 * ]
 */
const open = async (options = {}) => {
    title.value = options.title || "Xác nhận";
    message.value = options.message;
    cancelText.value = options.cancelText || "Hủy bỏ";
    showIcon.value = options.icon === true;
    showCancelButton.value = options.showCancel !== false;
    showCloseButton.value = options.showClose !== false;
    
    // Cấu hình actions
    actions.value = options.actions || [];

    visible.value = true;
    await nextTick();

    return new Promise((resolve) => {
        resolver = resolve;
    });
};

/**
 * Xử lý khi click vào action button
 */
const handleAction = (value) => {
    visible.value = false;
    if (resolver) resolver(value);
};

/**
 * Hủy bỏ - trả về null
 */
const cancel = () => {
    visible.value = false;
    if (resolver) resolver(null);
};

/**
 * Đóng dialog
 */
const close = () => {
    cancel();
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes scale {
    from {
        transform: scale(0.9);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-scale {
    animation: scale 0.15s ease-out;
}
</style>