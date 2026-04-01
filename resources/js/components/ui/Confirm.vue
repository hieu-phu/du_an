<template>
    <transition name="fade">
        <div v-if="visible" class="fixed inset-0 bg-black/50 flex items-center justify-center" style="z-index: 99999;">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm animate-scale">
                
                <!-- Icon Section -->
                <div v-if="showIcon" class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-green-100 flex items-center justify-center">
                        <img src="/images/icons/check-success.png" alt="Success"
                            class="w-15 h-15 object-contain" />
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    {{ title }}
                </h2>

                <!-- Message -->
                <p class="text-gray-600 mb-6" v-html="message"></p>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button @click="cancel"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        {{ cancelText }}
                    </button>

                    <button @click="confirmAction" :class="classBtnOkText">
                        {{ okText }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { nextTick, ref } from "vue";

const visible = ref(false);
const message = ref("");
const title = ref("Xác nhận");
const okText = ref("Đồng ý");
const cancelText = ref("Hủy");
const classBtnOkText = ref([]);
const showIcon = ref(false);

let resolver = null;

const open = async (options = {}) => {
    // Reset về giá trị mặc định trước
    title.value = options.title || "Xác nhận";
    message.value = options.message || "Bạn có chắc chắn muốn thực hiện hành động này?";
    okText.value = options.okText || "Đồng ý";
    cancelText.value = options.cancelText || "Hủy bỏ";
    classBtnOkText.value = options.classBtnOkText || [
        "px-4",
        "py-2",
        "rounded-lg",
        "bg-red-600",
        "text-white",
        "hover:bg-red-700",
        "transition"
    ];
    showIcon.value = options.icon === true; // Chỉ true khi icon === true

    visible.value = true;

    await nextTick();

    return new Promise((resolve) => {
        resolver = resolve;
    });
};

const confirmAction = () => {
    visible.value = false;
    if (resolver) resolver(true);
};

const cancel = () => {
    visible.value = false;
    if (resolver) resolver(false);
};

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