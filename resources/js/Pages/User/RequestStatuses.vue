<template>
    <AdminLayout title="Yêu cầu tài khoản">
        <PageBreadcrumb title="Yêu cầu tài khoản" :items="breadcrumbItems" />

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="mb-4 flex flex-wrap gap-3">
                <button
                    v-for="tab in approvalTabs"
                    :key="tab.key"
                    type="button"
                    @click="activeApprovalTab = tab.key"
                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition"
                    :class="activeApprovalTab === tab.key ? tab.activeClass : tab.inactiveClass"
                >
                    <span>{{ tab.label }}</span>
                    <span
                        class="inline-flex min-w-[24px] justify-center rounded-full px-2 py-0.5 text-xs font-bold"
                        :class="activeApprovalTab === tab.key ? tab.badgeActiveClass : tab.badgeInactiveClass"
                    >
                        {{ tab.count }}
                    </span>
                </button>
            </div>

            <div class="rounded-xl border p-4" :class="activeApprovalPanel.wrapperClass">
                <div class="mb-3">
                    <div class="text-sm font-semibold" :class="activeApprovalPanel.titleClass">{{ activeApprovalPanel.title }}</div>
                    <div class="text-xs" :class="activeApprovalPanel.subtitleClass">{{ activeApprovalPanel.description }}</div>
                </div>

                <div v-if="activeApprovalItems.length" class="space-y-3">
                    <div
                        v-for="item in activeApprovalItems"
                        :key="`${activeApprovalTab}-${item.id}`"
                        class="rounded-lg border bg-white px-4 py-3"
                        :class="activeApprovalPanel.itemBorderClass"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-gray-900">{{ item.payload.name || '-' }}</div>
                                <div class="text-sm text-gray-600">{{ item.payload.email || '-' }}</div>
                                <div class="text-sm text-gray-600">{{ item.payload.role_label || '-' }}</div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="activeApprovalPanel.statusBadgeClass">
                                {{ activeApprovalPanel.badgeLabel }}
                            </span>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            {{ activeApprovalPanel.timeLabel }} {{ formatDateTime(activeApprovalTime(item)) }}
                        </div>

                        <div v-if="activeApprovalTab === 'rejected'" class="mt-2 rounded-lg px-3 py-2 text-sm" :class="activeApprovalPanel.noteClass">
                            {{ item.review_note || 'Admin chưa nhập lý do từ chối.' }}
                        </div>

                        <div v-if="activeApprovalTab === 'approved'" class="mt-2 text-sm text-emerald-700">
                            Đã được {{ item.reviewed_by?.name || 'Admin' }} duyệt.
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-lg border border-dashed bg-white px-4 py-6 text-sm" :class="activeApprovalPanel.emptyClass">
                    {{ activeApprovalPanel.emptyText }}
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
    approvalSummary: { type: Object, default: null },
})

const activeApprovalTab = ref('approved')
const approvedApprovals = computed(() => props.approvalSummary?.approved || [])
const pendingApprovals = computed(() => props.approvalSummary?.pending || [])
const rejectedApprovals = computed(() => props.approvalSummary?.rejected || [])

const breadcrumbItems = [
    { text: 'HCNS', link: null },
    { text: 'Yêu cầu tài khoản', link: null },
]

const approvalTabs = computed(() => [
    {
        key: 'approved',
        label: 'Tài khoản được duyệt',
        count: approvedApprovals.value.length,
        activeClass: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        inactiveClass: 'border-gray-200 bg-white text-gray-700 hover:border-emerald-200 hover:text-emerald-700',
        badgeActiveClass: 'bg-emerald-100 text-emerald-800',
        badgeInactiveClass: 'bg-gray-100 text-gray-700',
    },
    {
        key: 'rejected',
        label: 'Tài khoản từ chối',
        count: rejectedApprovals.value.length,
        activeClass: 'border-rose-200 bg-rose-50 text-rose-800',
        inactiveClass: 'border-gray-200 bg-white text-gray-700 hover:border-rose-200 hover:text-rose-700',
        badgeActiveClass: 'bg-rose-100 text-rose-800',
        badgeInactiveClass: 'bg-gray-100 text-gray-700',
    },
    {
        key: 'pending',
        label: 'Tài khoản chờ duyệt',
        count: pendingApprovals.value.length,
        activeClass: 'border-amber-200 bg-amber-50 text-amber-800',
        inactiveClass: 'border-gray-200 bg-white text-gray-700 hover:border-amber-200 hover:text-amber-700',
        badgeActiveClass: 'bg-amber-100 text-amber-800',
        badgeInactiveClass: 'bg-gray-100 text-gray-700',
    },
])

const activeApprovalItems = computed(() => ({
    approved: approvedApprovals.value,
    rejected: rejectedApprovals.value,
    pending: pendingApprovals.value,
}[activeApprovalTab.value] || []))

const activeApprovalPanel = computed(() => ({
    approved: {
        title: 'Tài khoản đã được duyệt',
        description: 'Các yêu cầu đã được Admin phê duyệt và tạo tài khoản thành công',
        wrapperClass: 'border-emerald-200 bg-emerald-50/70',
        titleClass: 'text-emerald-900',
        subtitleClass: 'text-emerald-700',
        itemBorderClass: 'border-emerald-200',
        statusBadgeClass: 'bg-emerald-100 text-emerald-800',
        badgeLabel: 'Được duyệt',
        timeLabel: 'Duyệt lúc:',
        emptyClass: 'border-emerald-200 text-emerald-800',
        emptyText: 'Hiện chưa có tài khoản nào được duyệt.',
        noteClass: '',
    },
    rejected: {
        title: 'Tài khoản bị từ chối',
        description: 'Các yêu cầu đã bị Admin từ chối và ghi chú phản hồi',
        wrapperClass: 'border-rose-200 bg-rose-50/70',
        titleClass: 'text-rose-900',
        subtitleClass: 'text-rose-700',
        itemBorderClass: 'border-rose-200',
        statusBadgeClass: 'bg-rose-100 text-rose-800',
        badgeLabel: 'Từ chối',
        timeLabel: 'Xử lý lúc:',
        emptyClass: 'border-rose-200 text-rose-800',
        emptyText: 'Hiện chưa có tài khoản nào bị từ chối.',
        noteClass: 'bg-rose-50 text-rose-900',
    },
    pending: {
        title: 'Tài khoản đang chờ duyệt',
        description: 'Các yêu cầu tạo tài khoản Nhân sự đang chờ Admin xử lý',
        wrapperClass: 'border-amber-200 bg-amber-50/70',
        titleClass: 'text-amber-900',
        subtitleClass: 'text-amber-700',
        itemBorderClass: 'border-amber-200',
        statusBadgeClass: 'bg-amber-100 text-amber-800',
        badgeLabel: 'Chờ duyệt',
        timeLabel: 'Gửi lúc:',
        emptyClass: 'border-amber-200 text-amber-800',
        emptyText: 'Hiện chưa có tài khoản nào đang chờ duyệt.',
        noteClass: '',
    },
}[activeApprovalTab.value]))

const formatDateTime = (value) => {
    if (!value) return '-'
    return new Date(value).toLocaleString('vi-VN')
}

const activeApprovalTime = (item) => {
    if (activeApprovalTab.value === 'pending') {
        return item.submitted_at
    }

    return item.reviewed_at
}
</script>
