<template>
    <Modal v-if="show" @close="handleClose">
        <template #body>
            <div
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-4xl w-full mx-4 my-8 max-h-[90vh] overflow-hidden">
                <div
                    class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl overflow-hidden">
                    <!-- Header Modal -->
                    <div
                        class="sticky top-0 z-10 px-8 py-6 border-b border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ isEdit ? 'Cập nhật ghi chú' : 'Lý do từ chối đơn hàng' }}
                            </h2>
                            <button @click="handleClose"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body Form - Scrollable -->
                    <form @submit.prevent="handleSubmit" class="p-8 space-y-10 asfy-modal-scroll"
                        style="max-height: calc(90vh - 140px); overflow-y: auto;">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-8">
                                <FormTextarea label="Nội dung" v-model="form.content" :error="form.errors.content"
                                    placeholder="Nhập nội dung chi tiết..." required :rows="6"
                                    icon="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="flex justify-end gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <CancelButton @cancel="handleClose" />
                            <SubmitButton :disabled="form.processing" :loading="form.processing"
                                extra-class="order-1 sm:order-2" />
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { route } from 'ziggy-js'

import Modal from '@/components/Modal.vue'
import FormTextarea from '@/Components/ui/FormTextare.vue'
import CancelButton from '@/Components/Button/CancelButton.vue'
import SubmitButton from '@/Components/Button/SubmitButton.vue'

const page = usePage()

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    note: {
        type: Object,
        default: null
    },
    store: String,
    orderId: {
        type: Number,
        default: null
    },
    orderPoId: {
        type: Number,
        default: null
    },
    module: {
        type: String,
        default: null
    },
})

const emit = defineEmits(['close', 'success'])

const isEdit = computed(() => !!props.note)


// Form
const form = useForm({
    content: '',
    order_id: props.orderId,
    order_po_id: props.orderPoId,
    order_type: props.orderId ? 'sales_order' : props.orderPoId ? 'purchase_order' : null,
    module: null,
})

// Watch note prop để load data khi edit
watch(() => props.note, (note) => {
    if (note) {
        form.content = note.content || ''
        form.clearErrors()
    } else {
        form.reset()
        form.content = ''
        form.clearErrors()
    }
}, { immediate: true })

const handleClose = () => {
    emit('close')
}

const handleSubmit = async () => {
    try {
        // set dữ liệu theo loại đơn
        if (props.orderId) {
            form.order_id = props.orderId;
            form.order_type = 'sales_order';
        }

        if (props.orderPoId) {
            form.order_po_id = props.orderPoId;
            form.order_type = 'purchase_order';
        }

        form.module = props.module

        const response = await fetch(
            props.store ?? route('notes.store'),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                body: JSON.stringify({
                    content: form.content,
                    module: form.module,
                    order_id: form.order_id,
                    order_po_id: form.order_po_id,
                    order_type: form.order_type,
                }),
            }
        );

        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                toast.error('Kiểm tra lại dữ liệu!');
                return;
            }

            throw new Error('Request failed');
        }

        const result = await response.json();

        toast.success('Thêm mới ghi chú thành công!');
        emit('success', result.data ?? result);

    } catch (error) {
        console.error(error);
        toast.error('Có lỗi xảy ra, vui lòng thử lại!');
    }
};

</script>