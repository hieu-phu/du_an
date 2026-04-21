<template>
  <AdminLayout title="Phản hồi nội bộ">
    <PageBreadcrumb title="Phản hồi nội bộ" :items="[{ text: 'Phản hồi nội bộ', link: null }]" />

    <!-- Summary Stats -->
    <div :class="showInboxSection ? 'mb-8 grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6' : 'mb-8 grid grid-cols-1 gap-4 md:grid-cols-3'">
      <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="absolute -right-4 -top-4 size-24 rounded-full bg-slate-50 opacity-50"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Đã gửi</div>
        <div class="mt-2 text-3xl font-black text-slate-900">{{ summary.sent_total || 0 }}</div>
      </div>
      <div class="relative overflow-hidden rounded-2xl border border-amber-100 bg-linear-to-br from-amber-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-amber-700">Đã gửi chưa được phản hồi</div>
        <div class="mt-2 text-3xl font-black text-amber-600">{{ summary.sent_unprocessed || 0 }}</div>
      </div>
      <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-linear-to-br from-emerald-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Đã gửi đã được phản hồi</div>
        <div class="mt-2 text-3xl font-black text-emerald-600">{{ summary.sent_processed || 0 }}</div>
      </div>
      
      <div v-if="showInboxSection" class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Hộp thư</div>
        <div class="mt-2 text-3xl font-black text-slate-900">{{ summary.inbox_total || 0 }}</div>
      </div>
      <div v-if="showInboxSection" class="relative overflow-hidden rounded-2xl border border-orange-100 bg-linear-to-br from-orange-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-orange-700">Hộp thư chưa phản hồi</div>
        <div class="mt-2 text-3xl font-black text-orange-600">{{ summary.inbox_unprocessed || 0 }}</div>
      </div>
      <div v-if="showInboxSection" class="relative overflow-hidden rounded-2xl border border-teal-100 bg-linear-to-br from-teal-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-teal-700">Hộp thư đã xử lý</div>
        <div class="mt-2 text-3xl font-black text-teal-600">{{ summary.inbox_processed || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="mb-5 flex items-center gap-2">
        <div class="size-2 rounded-full bg-blue-500"></div>
        <h3 class="text-lg font-bold text-slate-900">Bộ lọc quản lý</h3>
      </div>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 ml-1">Trạng thái tin</label>
          <select v-model="filterForm.status" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20">
            <option value="">Tất cả</option>
            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 ml-1">Trạng thái phản hồi</label>
          <select v-model="filterForm.processing_state" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20">
            <option value="">Tất cả</option>
            <option v-for="option in processingOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div class="flex items-end gap-3">
          <button @click="applyFilters" class="flex-1 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition-all hover:bg-slate-800 hover:shadow-lg active:scale-95">Lọc dữ liệu</button>
          <button @click="resetFilters" class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition-all hover:bg-slate-50 active:scale-95">Bỏ lọc</button>
        </div>
      </div>
    </div>

    <!-- Create Feedback Form - Only show if there's someone to send to -->
    <div v-if="receiverOptions.length" class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="mb-5 flex items-center gap-2">
        <div class="size-2 rounded-full bg-indigo-500"></div>
        <h3 class="text-lg font-bold text-slate-900">Gửi phản hồi / đề xuất</h3>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 ml-1">Gửi tới cấp trên</label>
          <select v-model="form.receiver_position_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm transition-colors focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
            <option v-if="!receiverOptions.length" value="">Chưa có cấp trên nhận phản hồi</option>
            <option v-for="option in receiverOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div class="md:col-span-3 flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 ml-1">Tiêu đề</label>
          <input v-model="form.subject" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm transition-colors focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="Nhập tiêu đề phản hồi" />
        </div>
        <div class="md:col-span-4 flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 ml-1">Nội dung</label>
          <textarea v-model="form.message" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm transition-colors focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="Nhập nội dung chi tiết..."></textarea>
        </div>
      </div>
      <div class="mt-6 flex justify-end">
        <button :disabled="form.processing || !receiverOptions.length" @click="submitFeedback" class="group relative overflow-hidden rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white transition-all hover:bg-indigo-700 hover:shadow-xl active:scale-95 disabled:opacity-50">
          <span class="relative z-10 flex items-center gap-2">
            {{ form.processing ? 'Đang gửi...' : 'Gửi phản hồi' }}
            <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </span>
        </button>
      </div>
    </div>

    <div :class="showInboxSection ? 'grid grid-cols-1 gap-6 xl:grid-cols-2' : 'grid grid-cols-1 gap-6'">
      <!-- Sent List -->
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-2">
          <div class="size-2 rounded-full bg-blue-500"></div>
          <h3 class="text-lg font-bold text-slate-900">Phản hồi đang gửi</h3>
        </div>
        <div class="space-y-4">
          <div v-for="item in activeSent" :key="`sent-${item.id}`" class="group relative rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:border-blue-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1" @click="openDetail(item)">
                <div class="flex flex-wrap items-center gap-2 cursor-pointer">
                  <div class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ item.subject }}</div>
                  <span :class="feedbackFlowTone(item.action_state)" class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold">
                    {{ item.action_state_label }}
                  </span>
                  <span v-if="item.escalation_count" class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700">
                    Leo thang {{ item.escalation_count }} lần
                  </span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 cursor-pointer">
                  <span><span class="font-medium text-slate-700">Tới:</span> {{ item.receiver_group_label }}</span>
                  <span><span class="font-medium text-slate-700">Gửi lúc:</span> {{ formatDateTime(item.created_at) }}</span>
                  <span v-if="item.action_state !== 'resolved' && item.action_state !== 'closed'" :class="feedbackResponseTone(item.processing_state)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold">
                    {{ item.processing_state_label }}
                  </span>
                </div>
              </div>
              <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap">{{ item.status_label }}</span>
            </div>
            
            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
              <button @click="openDetail(item)" class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50">
                Xem chi tiết
              </button>
              <button v-if="item.can_sender_reply" @click="openDetail(item)" class="flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-bold text-blue-700 transition-colors hover:bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" />
                </svg>
                Nhắn tiếp
              </button>
            </div>
          </div>
          <div v-if="!activeSent.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 py-12 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8" /></svg>
            <p class="text-sm">Chưa có phản hồi nào đang xử lý.</p>
          </div>
        </div>
      </div>

      <!-- Inbox List -->
      <div v-if="showInboxSection" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-2">
          <div class="size-2 rounded-full bg-amber-500"></div>
          <h3 class="text-lg font-bold text-slate-900">Hộp thư cần xử lý</h3>
        </div>
        <div class="space-y-4">
          <div v-for="item in activeInbox" :key="`inbox-${item.id}`" class="group relative rounded-2xl border border-slate-200 bg-white p-5 transition-all hover:border-amber-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1" @click="openDetail(item)">
                <div class="flex flex-wrap items-center gap-2 cursor-pointer">
                  <div class="font-bold text-slate-900 group-hover:text-amber-600 transition-colors">{{ item.subject }}</div>
                  <span :class="feedbackFlowTone(item.action_state)" class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold">
                    {{ item.action_state_label }}
                  </span>
                  <span v-if="item.escalation_count" class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700">
                    Leo thang {{ item.escalation_count }} lần
                  </span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 cursor-pointer">
                  <span><span class="font-medium text-slate-700">Người gửi:</span> {{ item.sender?.name || '-' }}</span>
                  <span><span class="font-medium text-slate-700">Gửi lúc:</span> {{ formatDateTime(item.created_at) }}</span>
                </div>
              </div>
              <span class="rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700 whitespace-nowrap">{{ item.status_label }}</span>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
              <button @click="openDetail(item)" class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50">
                Xem chi tiết
              </button>
              <button v-if="item.status !== 'read'" @click="markRead(item)" class="rounded-lg border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50">Đánh dấu đã đọc</button>
              <button v-if="canSubmitReply && item.can_handler_reply" @click="openDetail(item)" class="flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-1.5 text-xs font-bold text-white transition-all hover:bg-amber-700 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>
                Trả lời
              </button>
            </div>
          </div>
          <div v-if="!activeInbox.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 py-12 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8" /></svg>
            <p class="text-sm">Không có phản hồi nào cần xử lý.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Feedback History Section -->
    <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50/50 p-6 shadow-sm overflow-hidden">
      <div class="mb-5 flex items-center gap-2">
        <div class="size-2 rounded-full bg-emerald-500"></div>
        <h3 class="text-lg font-bold text-slate-900">Lịch sử phản hồi</h3>
      </div>
      <div class="overflow-x-auto -mx-6">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-white border-y border-slate-100 text-left text-[10px] uppercase tracking-widest text-slate-500">
              <th class="px-6 py-4">Phân loại</th>
              <th class="px-6 py-4">Thời gian</th>
              <th class="px-6 py-4">Tiêu đề</th>
              <th class="px-6 py-4">Đối tượng</th>
              <th class="px-6 py-4">Trạng thái</th>
              <th class="px-6 py-4 text-center">Tác vụ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in feedbackHistory" :key="`history-${item.id}`" class="bg-white hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4">
                <span :class="item.is_sender ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700'" class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider">
                  {{ item.is_sender ? 'Phản hồi gửi' : 'Hộp thư đến' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-700 whitespace-nowrap">{{ formatDateTime(item.created_at) }}</td>
              <td class="px-6 py-4 text-slate-900 font-bold max-w-xs truncate" :title="item.subject">{{ item.subject }}</td>
              <td class="px-6 py-4 text-slate-600">
                <span v-if="item.is_sender"><span class="font-medium">Tới:</span> {{ item.receiver_group_label }}</span>
                <span v-else><span class="font-medium">Từ:</span> {{ item.sender?.name || '-' }}</span>
              </td>
              <td class="px-6 py-4">
                <span :class="feedbackFlowTone(item.action_state)" class="rounded-full px-2.5 py-1 text-[10px] font-bold">
                  {{ item.action_state_label }}
                </span>
              </td>
              <td class="px-6 py-4 text-center whitespace-nowrap">
                <button @click="openDetail(item)" class="text-blue-600 font-bold hover:text-blue-800 transition-colors">Xem chi tiết</button>
              </td>
            </tr>
            <tr v-if="!feedbackHistory.length">
              <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 font-medium bg-white">Chưa có lịch sử phản hồi.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    <!-- Mail Logs -->
    <div v-if="showInboxSection" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm overflow-hidden">
      <div class="mb-5 flex items-center gap-2">
        <div class="size-2 rounded-full bg-slate-400"></div>
        <h3 class="text-lg font-bold text-slate-900">Nhật ký gửi Mail phản hồi</h3>
      </div>
      <div class="overflow-x-auto -mx-6">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 border-y border-slate-100 text-left text-[10px] uppercase tracking-widest text-slate-500">
              <th class="px-6 py-4">Thời gian</th>
              <th class="px-6 py-4">Người gửi</th>
              <th class="px-6 py-4">Người nhận</th>
              <th class="px-6 py-4">Tiêu đề</th>
              <th class="px-6 py-4">Trạng thái</th>
              <th class="px-6 py-4">Lỗi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="log in mailLogs" :key="`mail-log-${log.id}`" class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4 text-slate-700 whitespace-nowrap">{{ formatDateTime(log.sent_at) }}</td>
              <td class="px-6 py-4 text-slate-700">{{ log.sender?.name || '-' }}</td>
              <td class="px-6 py-4 text-slate-700">{{ log.receiver_email }}</td>
              <td class="px-6 py-4 text-slate-700 font-medium">{{ log.subject }}</td>
              <td class="px-6 py-4">
                <span :class="log.status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'" class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider">
                  {{ log.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-rose-600 max-w-xs truncate" :title="log.error_message">{{ log.error_message || '-' }}</td>
            </tr>
            <tr v-if="!mailLogs.length">
              <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400 font-medium">Chưa có nhật ký gửi mail.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail & Reply Modal -->
    <CustomModal
      v-if="detailTarget"
      :title="detailTarget.subject"
      @close="closeDetail"
      :custom_class="['relative', 'w-full', 'max-w-[840px]', 'rounded-3xl', 'bg-white', 'shadow-2xl', 'overflow-hidden']"
    >
      <template #body>
        <div class="max-h-[85vh] overflow-y-auto bg-slate-50/30">
          <div class="p-8">
            <!-- Header Info -->
            <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <div class="flex items-start justify-between gap-4">
                <div class="space-y-3">
                  <div class="flex flex-wrap items-center gap-2">
                    <span :class="feedbackFlowTone(detailTarget.action_state)" class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider">
                      {{ detailTarget.action_state_label }}
                    </span>
                    <span v-if="detailTarget.escalation_count" class="rounded-full bg-indigo-100 px-3 py-1 text-[11px] font-bold text-indigo-700 uppercase tracking-wider">
                      Leo thang {{ detailTarget.escalation_count }} lần
                    </span>
                  </div>
                  <h2 class="text-2xl font-black text-slate-900 leading-tight">{{ detailTarget.subject }}</h2>
                  <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                    <div class="flex items-center gap-2 text-slate-600">
                      <span class="font-bold text-slate-400">Gửi từ:</span>
                      <span class="font-bold text-slate-900">{{ detailTarget.sender?.name || 'Bạn' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-600">
                      <span class="font-bold text-slate-400">Gửi tới:</span>
                      <span class="font-bold text-slate-900">{{ detailTarget.receiver_group_label }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-600">
                      <span class="font-bold text-slate-400">Thời gian:</span>
                      <span class="font-bold text-slate-700">{{ formatDateTime(detailTarget.created_at) }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                  <span class="rounded-full bg-slate-100 px-4 py-1.5 text-xs font-black uppercase tracking-widest text-slate-600">{{ detailTarget.status_label }}</span>
                </div>
              </div>
            </div>

            <!-- Original Message -->
            <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="bg-slate-50 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-100">Nội dung gốc</div>
              <div class="p-6 text-base leading-relaxed text-slate-800 whitespace-pre-wrap font-medium font-serif">
                {{ detailTarget.message }}
              </div>
            </div>

            <!-- Thread -->
            <div v-if="detailTarget.reply_history?.length || detailTarget.escalation_history?.length" class="space-y-6">
              <div class="flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-200"></div>
                <div class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Diễn biến hội thoại</div>
                <div class="h-px flex-1 bg-slate-200"></div>
              </div>

              <!-- Combined Timeline of Replies and Escalations -->
              <div class="space-y-4">
                <template v-for="event in combinedThread(detailTarget)" :key="event.unique_id">
                  <!-- Case: Reply -->
                  <div v-if="event.type === 'reply'" :class="event.replied_by === detailTarget.sender_id ? 'pr-12' : 'pl-12'">
                    <div :class="event.replied_by === detailTarget.sender_id ? 'bg-indigo-50 border-indigo-100' : 'bg-white border-slate-200'" class="rounded-2xl border p-5 shadow-sm">
                      <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ event.replier?.name || '-' }}</span>
                        <span class="text-[10px] font-bold text-slate-400">{{ formatDateTime(event.created_at) }}</span>
                      </div>
                      <div class="text-sm leading-relaxed text-slate-700 whitespace-pre-wrap">{{ event.message }}</div>
                    </div>
                  </div>

                  <!-- Case: Escalation -->
                  <div v-else-if="event.type === 'escalation'" class="flex justify-center px-8">
                    <div class="w-full rounded-2xl border border-amber-100 bg-amber-50/50 p-4 shadow-sm text-center">
                      <div class="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-2">Leo thang tự động - Lần {{ event.escalation_count }}</div>
                      <div class="flex items-center justify-center gap-3 text-xs font-bold text-amber-900">
                        <span class="px-3 py-1 rounded-full bg-white border border-amber-200">{{ formatPosition(event.from_position) }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        <span class="px-3 py-1 rounded-full bg-white border border-amber-200">{{ formatPosition(event.to_position) }}</span>
                      </div>
                      <div class="mt-2 text-[10px] text-amber-700 font-medium">Lúc {{ formatDateTime(event.escalated_at) }}</div>
                    </div>
                  </div>
                </template>
              </div>
            </div>

            <!-- Reply Form Section (only if not resolved/closed) -->
            <div v-if="detailTarget.action_state !== 'resolved' && detailTarget.action_state !== 'closed' && (detailTarget.can_sender_reply || detailTarget.can_handler_reply)" class="mt-12 rounded-3xl border border-indigo-100 bg-white p-8 shadow-xl shadow-indigo-500/5">
              <div class="mb-6 flex items-center gap-3">
                <div class="size-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>
                </div>
                <h3 class="text-xl font-black text-slate-900">Gửi phản hồi tiếp</h3>
              </div>

              <div class="flex flex-col gap-4">
                <textarea 
                  v-model="replyForm.reply_message" 
                  rows="4" 
                  class="w-full rounded-2xl border-2 border-slate-100 bg-slate-50/50 px-6 py-4 text-base font-medium transition-all focus:border-indigo-500 focus:bg-white focus:ring-8 focus:ring-indigo-500/5" 
                  placeholder="Nhập nội dung phản hồi của bạn..."
                ></textarea>

                <!-- Status Selector for Handler -->
                <div v-if="detailTarget.can_handler_reply" class="flex flex-col gap-2">
                  <label class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Kết quả và trạng thái tiếp theo</label>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button 
                      type="button" 
                      @click="replyForm.conversation_status = 'resolved'"
                      :class="replyForm.conversation_status === 'resolved' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-4 ring-indigo-500/10' : 'border-slate-100 bg-white text-slate-600 hover:border-slate-200'"
                      class="flex items-center gap-3 rounded-2xl border-2 px-5 py-4 transition-all"
                    >
                      <div class="size-5 rounded-full border-2 flex items-center justify-center" :class="replyForm.conversation_status === 'resolved' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                        <div v-if="replyForm.conversation_status === 'resolved'" class="size-2 rounded-full bg-white"></div>
                      </div>
                      <div class="text-left leading-tight">
                        <div class="font-black text-sm">Đã giải quyết</div>
                        <div class="text-[10px] font-bold opacity-70">Phản hồi sẽ được đóng lại và lưu trữ.</div>
                      </div>
                    </button>

                    <button 
                      type="button" 
                      @click="replyForm.conversation_status = 'waiting_sender'"
                      :class="replyForm.conversation_status === 'waiting_sender' ? 'border-amber-600 bg-amber-50 text-amber-700 ring-4 ring-amber-500/10' : 'border-slate-100 bg-white text-slate-600 hover:border-slate-200'"
                      class="flex items-center gap-3 rounded-2xl border-2 px-5 py-4 transition-all"
                    >
                      <div class="size-5 rounded-full border-2 flex items-center justify-center" :class="replyForm.conversation_status === 'waiting_sender' ? 'border-amber-600 bg-amber-600' : 'border-slate-300'">
                        <div v-if="replyForm.conversation_status === 'waiting_sender'" class="size-2 rounded-full bg-white"></div>
                      </div>
                      <div class="text-left leading-tight">
                        <div class="font-black text-sm">Cần thêm thông tin</div>
                        <div class="text-[10px] font-bold opacity-70">Người gửi cần trả lời thêm.</div>
                      </div>
                    </button>
                  </div>
                </div>
              </div>

              <div class="mt-8 flex justify-end">
                <button 
                  :disabled="replyForm.processing || !replyForm.reply_message.trim()" 
                  @click="submitReply" 
                  class="rounded-2xl bg-indigo-600 px-10 py-4 text-sm font-black text-white transition-all hover:bg-indigo-700 hover:shadow-2xl hover:shadow-indigo-500/20 active:scale-95 disabled:opacity-50"
                >
                  {{ replyForm.processing ? 'Đang gửi...' : 'Gửi phản hồi' }}
                </button>
              </div>
            </div>
            
            <div v-else-if="detailTarget.action_state === 'resolved' || detailTarget.action_state === 'closed'" class="mt-8 rounded-2xl bg-slate-100 p-8 text-center border-2 border-dashed border-slate-200">
              <div class="text-sm font-black text-slate-500 uppercase tracking-widest">Hội thoại đã kết thúc</div>
              <div class="mt-1 text-xs font-bold text-slate-400">Bạn không thể gửi thêm phản hồi cho yêu cầu này.</div>
            </div>
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex items-center justify-between border-t border-slate-100 bg-white px-8 py-5">
          <button @click="closeDetail" class="text-sm font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Đóng cửa sổ</button>
          <div class="flex items-center gap-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
            <span>ID: #{{ detailTarget.id }}</span>
          </div>
        </div>
      </template>
    </CustomModal>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import CustomModal from '@/components/modals/CustomModal.vue'

const props = defineProps({
  sent: { type: Array, default: () => [] },
  inbox: { type: Array, default: () => [] },
  mailLogs: { type: Array, default: () => [] },
  canReply: { type: Boolean, default: false },
  canSubmitReply: { type: Boolean, default: false },
  filters: { type: Object, default: () => ({}) },
  statusOptions: { type: Array, default: () => [] },
  processingOptions: { type: Array, default: () => [] },
  receiverOptions: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
})

const form = useForm({
  receiver_position_id: props.receiverOptions?.[0]?.value || '',
  subject: '',
  message: '',
})

const filterForm = useForm({
  status: props.filters?.status || '',
  processing_state: props.filters?.processing_state || '',
})

const replyForm = useForm({
  reply_message: '',
  conversation_status: 'resolved',
  status: 'read',
})

const detailTarget = ref(null)

const activeSent = computed(() => props.sent.filter(item => item.action_state !== 'resolved' && item.action_state !== 'closed'))
const activeInbox = computed(() => props.inbox.filter(item => item.action_state !== 'resolved' && item.action_state !== 'closed'))

const feedbackHistory = computed(() => {
  const sentHistory = props.sent.filter(item => item.action_state === 'resolved' || item.action_state === 'closed').map(item => ({ ...item, is_sender: true }))
  const inboxHistory = props.inbox.filter(item => item.action_state === 'resolved' || item.action_state === 'closed').map(item => ({ ...item, is_sender: false }))
  
  return [...sentHistory, ...inboxHistory].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const showInboxSection = Boolean(props.canReply && props.inbox.length > 0)

const submitFeedback = () => {
  form.post(route('feedbacks.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('subject', 'message')
      toast.success('Đã gửi phản hồi thành công.')
    },
    onError: () => toast.error('Không thể gửi phản hồi. Vui lòng kiểm tra lại.'),
  })
}

const markRead = (item) => {
  router.post(route('feedbacks.read', item.id), {}, {
    preserveScroll: true,
  })
}

const openReply = (item) => {
  openDetail(item)
}

const openDetail = (item) => {
  detailTarget.value = item
  replyForm.reset()
  replyForm.status = 'read'
  replyForm.conversation_status = 'resolved'
}

const closeDetail = () => {
  detailTarget.value = null
  replyForm.reset()
}

const submitReply = () => {
  if (!detailTarget.value) return
  if (!replyForm.reply_message.trim()) {
    toast.warning('Vui lòng nhập nội dung phản hồi.')
    return
  }

  replyForm.post(route('feedbacks.reply', detailTarget.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Find the updated item in props and update detailTarget
      toast.success('Đã gửi trả lời.')
      
      // Close modal if resolved
      if (replyForm.conversation_status === 'resolved') {
        closeDetail()
      } else {
        // Keep open but clear message
        replyForm.reset('reply_message')
        // Refresh local target by finding it in props after next tick/reload
        // Inertia will reload props, so detailTarget will still point to the old object
        // But since we reload the page, the modal will likely close or we need to re-find it
        closeDetail() 
      }
    },
    onError: () => toast.error('Không thể gửi trả lời.'),
  })
}

const applyFilters = () => {
  router.get(route('feedbacks.index'), {
    status: filterForm.status || undefined,
    processing_state: filterForm.processing_state || undefined,
  }, {
    preserveScroll: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterForm.status = ''
  filterForm.processing_state = ''
  applyFilters()
}

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatPosition = (position) => {
  if (!position) return '-'
  return `${position.name} (Cấp ${position.authority_level})`
}

const isReplyingAsHandler = (item) => Boolean(item?.sender)

const conversationStateLabel = (state) => {
  if (state === 'resolved') return '\u0110\u00e3 gi\u1ea3i quy\u1ebft'
  if (state === 'closed') return '\u0110\u00e3 \u0111\u00f3ng'
  return ''
}

const feedbackFlowTone = (state) => {
  if (state === 'resolved') {
    return 'bg-emerald-50 text-emerald-700'
  }

  if (state === 'waiting_sender') {
    return 'bg-amber-50 text-amber-700'
  }

  if (state === 'closed') {
    return 'bg-slate-100 text-slate-600'
  }

  return 'bg-slate-100 text-slate-700'
}

const feedbackResponseTone = (state) => {
  return state === 'processed'
    ? 'bg-blue-50 text-blue-700'
    : 'bg-rose-50 text-rose-700'
}

const combinedThread = (item) => {
  const thread = []
  
  if (item.reply_history) {
    item.reply_history.forEach(r => thread.push({ ...r, type: 'reply', unique_id: `reply-${r.id}` }))
  }
  
  if (item.escalation_history) {
    item.escalation_history.forEach(e => thread.push({ ...e, type: 'escalation', created_at: e.escalated_at, unique_id: `esc-${e.id}` }))
  }
  
  return thread.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
}
</script>

<style scoped>
.slim-scrollbar::-webkit-scrollbar {
  height: 4px;
}
.slim-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.slim-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.slim-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
