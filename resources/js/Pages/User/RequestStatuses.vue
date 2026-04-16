<template>
    <AdminLayout title="Yeu cau cua toi">
        <PageBreadcrumb title="Yeu cau cua toi" :items="breadcrumbItems" />

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
                                <div class="font-medium text-gray-900">{{ requestTypeLabel(item.request_type) }}</div>
                                <div class="text-sm text-gray-600">{{ requestHeadline(item) }}</div>
                                <div class="text-sm text-gray-600">{{ requestSubline(item) }}</div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="activeApprovalPanel.statusBadgeClass">
                                {{ activeApprovalPanel.badgeLabel }}
                            </span>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            {{ activeApprovalPanel.timeLabel }} {{ formatDateTime(activeApprovalTime(item)) }}
                        </div>

                        <div v-if="activeApprovalTab === 'rejected'" class="mt-2 rounded-lg px-3 py-2 text-sm" :class="activeApprovalPanel.noteClass">
                            {{ item.review_note || 'Admin chua nhap ly do tu choi.' }}
                        </div>

                        <div v-if="activeApprovalTab === 'approved'" class="mt-2 text-sm text-emerald-700">
                            Da duoc {{ item.reviewed_by?.name || 'Admin' }} duyet.
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
    { text: 'Yeu cau cua toi', link: null },
]

const approvalTabs = computed(() => [
    {
        key: 'approved',
        label: 'Da duyet',
        count: approvedApprovals.value.length,
        activeClass: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        inactiveClass: 'border-gray-200 bg-white text-gray-700 hover:border-emerald-200 hover:text-emerald-700',
        badgeActiveClass: 'bg-emerald-100 text-emerald-800',
        badgeInactiveClass: 'bg-gray-100 text-gray-700',
    },
    {
        key: 'rejected',
        label: 'Tu choi',
        count: rejectedApprovals.value.length,
        activeClass: 'border-rose-200 bg-rose-50 text-rose-800',
        inactiveClass: 'border-gray-200 bg-white text-gray-700 hover:border-rose-200 hover:text-rose-700',
        badgeActiveClass: 'bg-rose-100 text-rose-800',
        badgeInactiveClass: 'bg-gray-100 text-gray-700',
    },
    {
        key: 'pending',
        label: 'Cho duyet',
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
        title: 'Yeu cau da duyet',
        description: 'Cac yeu cau da duoc Admin phe duyet',
        wrapperClass: 'border-emerald-200 bg-emerald-50/70',
        titleClass: 'text-emerald-900',
        subtitleClass: 'text-emerald-700',
        itemBorderClass: 'border-emerald-200',
        statusBadgeClass: 'bg-emerald-100 text-emerald-800',
        badgeLabel: 'Duoc duyet',
        timeLabel: 'Duyet luc:',
        emptyClass: 'border-emerald-200 text-emerald-800',
        emptyText: 'Hien chua co yeu cau nao duoc duyet.',
        noteClass: '',
    },
    rejected: {
        title: 'Yeu cau bi tu choi',
        description: 'Cac yeu cau da bi tu choi',
        wrapperClass: 'border-rose-200 bg-rose-50/70',
        titleClass: 'text-rose-900',
        subtitleClass: 'text-rose-700',
        itemBorderClass: 'border-rose-200',
        statusBadgeClass: 'bg-rose-100 text-rose-800',
        badgeLabel: 'Tu choi',
        timeLabel: 'Xu ly luc:',
        emptyClass: 'border-rose-200 text-rose-800',
        emptyText: 'Hien chua co yeu cau nao bi tu choi.',
        noteClass: 'bg-rose-50 text-rose-900',
    },
    pending: {
        title: 'Yeu cau cho duyet',
        description: 'Cac yeu cau dang cho Admin xu ly',
        wrapperClass: 'border-amber-200 bg-amber-50/70',
        titleClass: 'text-amber-900',
        subtitleClass: 'text-amber-700',
        itemBorderClass: 'border-amber-200',
        statusBadgeClass: 'bg-amber-100 text-amber-800',
        badgeLabel: 'Cho duyet',
        timeLabel: 'Gui luc:',
        emptyClass: 'border-amber-200 text-amber-800',
        emptyText: 'Hien chua co yeu cau nao dang cho duyet.',
        noteClass: '',
    },
}[activeApprovalTab.value]))

const requestTypeLabel = (type) => ({
    user_create: 'Tao tai khoan',
    user_salary_change: 'Doi luong co ban',
}[type] || '-')

const requestHeadline = (item) => {
    if (item.request_type === 'user_create') {
        return item.payload?.name || '-'
    }

    return item.payload?.employee_name || '-'
}

const requestSubline = (item) => {
    if (item.request_type === 'user_create') {
        return item.payload?.email || '-'
    }

    const oldSalary = item.payload?.old_salary
    const newSalary = item.payload?.new_salary
    return `${formatCurrency(oldSalary)} -> ${formatCurrency(newSalary)}`
}

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '-'
    return `${new Intl.NumberFormat('vi-VN').format(Number(value))} VND`
}

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
