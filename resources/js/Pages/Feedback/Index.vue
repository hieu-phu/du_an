<template>
  <AdminLayout title="Phản hồi nội bộ">
    <PageBreadcrumb title="Phản hồi nội bộ" :items="[{ text: 'Phản hồi nội bộ', link: null }]" />

    <!-- Summary Stats -->
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
      <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="absolute -right-4 -top-4 size-24 rounded-full bg-slate-50 opacity-50"></div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Đã gửi</div>
        <div class="mt-2 text-3xl font-black text-slate-900">{{ summary.sent_total || 0 }}</div>
      </div>
      <div class="relative overflow-hidden rounded-2xl border border-amber-100 bg-linear-to-br from-amber-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-amber-700">Đã gửi chưa xử lý</div>
        <div class="mt-2 text-3xl font-black text-amber-600">{{ summary.sent_unprocessed || 0 }}</div>
      </div>
      <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-linear-to-br from-emerald-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Đã gửi đã xử lý</div>
        <div class="mt-2 text-3xl font-black text-emerald-600">{{ summary.sent_processed || 0 }}</div>
      </div>
      
      <div v-if="canReply" class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Hộp thư</div>
        <div class="mt-2 text-3xl font-black text-slate-900">{{ summary.inbox_total || 0 }}</div>
      </div>
      <div v-if="canReply" class="relative overflow-hidden rounded-2xl border border-orange-100 bg-linear-to-br from-orange-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold uppercase tracking-wider text-orange-700">Hộp thư chưa xử lý</div>
        <div class="mt-2 text-3xl font-black text-orange-600">{{ summary.inbox_unprocessed || 0 }}</div>
      </div>
      <div v-if="canReply" class="relative overflow-hidden rounded-2xl border border-teal-100 bg-linear-to-br from-teal-50 to-white p-5 shadow-sm transition-all hover:shadow-md">
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
          <label class="text-sm font-semibold text-slate-700 ml-1">Trạng thái xử lý</label>
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

    <!-- Create Feedback Form -->
    <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
      <!-- Sent List -->
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-2">
          <div class="size-2 rounded-full bg-blue-500"></div>
          <h3 class="text-lg font-bold text-slate-900">Phản hồi đã gửi</h3>
        </div>
        <div class="space-y-4">
          <div v-for="item in sent" :key="`sent-${item.id}`" class="group relative rounded-xl border border-slate-200 bg-white p-5 transition-all hover:border-blue-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1">
                <div class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ item.subject }}</div>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                  <span class="font-medium text-slate-700">Tới: {{ item.receiver_group_label }}</span>
                  <span class="size-1 rounded-full bg-slate-300"></span>
                  <span>{{ formatDateTime(item.created_at) }}</span>
                </div>
              </div>
              <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ item.status_label }}</span>
            </div>
            <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm leading-relaxed text-slate-700 italic border-l-4 border-slate-200">
              "{{ item.message }}"
            </div>
            
            <div class="mt-4 flex flex-wrap items-center gap-3">
              <button @click="openReply(item)" class="flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-bold text-blue-700 transition-colors hover:bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" />
                </svg>
                Nhắn tiếp
              </button>
              <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">Xử lý: {{ item.processing_state_label }}</span>
              <span v-if="item.escalation_count" class="rounded-lg bg-indigo-100 px-3 py-1.5 text-[10px] font-bold text-indigo-700">
                Leo thang: {{ item.escalation_count }} lần
              </span>
            </div>

            <!-- Reply History -->
            <div v-if="item.reply_history?.length" class="mt-5 space-y-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
              <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-800">Lịch sử phản hồi</div>
              <div v-for="history in item.reply_history" :key="`sent-history-${history.id}`" class="relative rounded-lg border border-emerald-100 bg-white p-3 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                  <span class="text-xs font-bold text-emerald-800">{{ history.replier?.name || '-' }}</span>
                  <span class="text-[10px] text-emerald-600">{{ formatDateTime(history.created_at) }}</span>
                </div>
                <div class="mt-1 text-sm text-emerald-900 leading-relaxed">{{ history.message }}</div>
              </div>
            </div>

            <!-- Escalation History -->
            <div v-if="item.escalation_history?.length" class="mt-4 space-y-3 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
              <div class="text-[10px] font-bold uppercase tracking-widest text-indigo-800">Lịch sử leo thang</div>
              <div v-for="history in item.escalation_history" :key="`sent-escalation-${history.id}`" class="rounded-lg border border-indigo-100 bg-white p-3 text-xs shadow-xs">
                <div class="flex items-center justify-between text-indigo-800">
                  <span class="font-bold text-xs">Lần {{ history.escalation_count }}</span>
                  <span class="text-[10px]">{{ formatDateTime(history.escalated_at) }}</span>
                </div>
                <div class="mt-2 flex items-center gap-2 text-indigo-900 font-medium whitespace-nowrap overflow-x-auto pb-1 slim-scrollbar">
                  <span class="rounded bg-indigo-50 px-1.5 py-0.5">{{ formatPosition(history.from_position) }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                  <span class="rounded bg-indigo-50 px-1.5 py-0.5">{{ formatPosition(history.to_position) }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-if="!sent.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 py-12 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8" /></svg>
            <p class="text-sm">Chưa có phản hồi nào được gửi.</p>
          </div>
        </div>
      </div>

      <!-- Inbox List -->
      <div v-if="canReply" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-2">
          <div class="size-2 rounded-full bg-amber-500"></div>
          <h3 class="text-lg font-bold text-slate-900">Hộp thư cần xử lý</h3>
        </div>
        <div class="space-y-4">
          <div v-for="item in inbox" :key="`inbox-${item.id}`" class="group relative rounded-xl border border-slate-200 bg-white p-5 transition-all hover:border-amber-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1">
                <div class="font-bold text-slate-900 group-hover:text-amber-600 transition-colors">{{ item.subject }}</div>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                  <span class="font-medium text-slate-700">Người gửi: {{ item.sender?.name || '-' }}</span>
                  <span class="size-1 rounded-full bg-slate-300"></span>
                  <span>{{ formatDateTime(item.created_at) }}</span>
                </div>
              </div>
              <span class="rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700">{{ item.status_label }}</span>
            </div>
            <div class="mt-4 rounded-lg bg-orange-50/50 p-4 text-sm leading-relaxed text-slate-700 border-l-4 border-amber-200">
              {{ item.message }}
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
              <button @click="markRead(item)" class="rounded-lg border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50">Đánh dấu đã đọc</button>
              <button v-if="canSubmitReply" @click="openReply(item)" class="flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-1.5 text-xs font-bold text-white transition-all hover:bg-amber-700 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>
                Trả lời
              </button>
              <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">Xử lý: {{ item.processing_state_label }}</span>
              <span v-if="item.escalation_count" class="rounded-lg bg-indigo-100 px-3 py-1.5 text-[10px] font-bold text-indigo-700">
                Leo thang: {{ item.escalation_count }} lần
              </span>
            </div>

            <!-- Reply History -->
            <div v-if="item.reply_history?.length" class="mt-5 space-y-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
              <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-800">Lịch sử trả lời</div>
              <div v-for="history in item.reply_history" :key="`inbox-history-${history.id}`" class="relative rounded-lg border border-emerald-100 bg-white p-3 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                  <span class="text-xs font-bold text-emerald-800">{{ history.replier?.name || '-' }}</span>
                  <span class="text-[10px] text-emerald-600">{{ formatDateTime(history.created_at) }}</span>
                </div>
                <div class="mt-1 text-sm text-emerald-900 leading-relaxed">{{ history.message }}</div>
              </div>
            </div>

            <!-- Escalation History -->
            <div v-if="item.escalation_history?.length" class="mt-4 space-y-3 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
              <div class="text-[10px] font-bold uppercase tracking-widest text-indigo-800">Lịch sử leo thang</div>
              <div v-for="history in item.escalation_history" :key="`inbox-escalation-${history.id}`" class="rounded-lg border border-indigo-100 bg-white p-3 text-xs shadow-xs">
                <div class="flex items-center justify-between text-indigo-800">
                  <span class="font-bold text-xs">Lần {{ history.escalation_count }}</span>
                  <span class="text-[10px]">{{ formatDateTime(history.escalated_at) }}</span>
                </div>
                <div class="mt-2 flex items-center gap-2 text-indigo-900 font-medium whitespace-nowrap overflow-x-auto pb-1 slim-scrollbar">
                  <span class="rounded bg-indigo-50 px-1.5 py-0.5">{{ formatPosition(history.from_position) }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                  <span class="rounded bg-indigo-50 px-1.5 py-0.5">{{ formatPosition(history.to_position) }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-if="!inbox.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 py-12 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8" /></svg>
            <p class="text-sm">Không có phản hồi nào cần xử lý.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Mail Logs -->
    <div v-if="canReply" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm overflow-hidden">
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

    <!-- Reply Modal -->
    <CustomModal
      v-if="replyTarget"
      :title="replyTarget?.sender ? 'Trả lời phản hồi' : 'Nhắn tiếp phản hồi'"
      @close="closeReply"
      :custom_class="['relative', 'w-full', 'max-w-[720px]', 'rounded-2xl', 'bg-white', 'shadow-2xl', 'overflow-hidden']"
    >
      <template #body>
        <div class="p-6">
          <div class="mb-6 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-700">
            <div class="flex items-baseline gap-2">
              <span class="font-bold text-slate-900 w-20">Tiêu đề:</span>
              <span class="text-indigo-700 font-medium">{{ replyTarget.subject }}</span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
              <span class="font-bold text-slate-900 w-20">{{ replyTarget.sender ? 'Người gửi:' : 'Gửi tới:' }}</span>
              <span>{{ replyTarget.sender?.name || replyTarget.receiver_group_label || '-' }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-200">
              <span class="font-bold text-slate-900 block mb-1">Nội dung gốc:</span>
              <p class="italic text-slate-600">"{{ replyTarget.message }}"</p>
            </div>
          </div>
          
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-slate-800 ml-1">Nội dung phản hồi của bạn</label>
            <textarea v-model="replyForm.reply_message" rows="6" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10" placeholder="Nhập nội dung trả lời tại đây..."></textarea>
          </div>

          <div class="mt-8 flex items-center justify-end gap-3">
            <button @click="closeReply" class="rounded-xl border border-slate-200 bg-white px-8 py-2.5 text-sm font-bold text-slate-600 transition-all hover:bg-slate-50 active:scale-95">Hủy bỏ</button>
            <button :disabled="replyForm.processing" @click="submitReply" class="rounded-xl bg-slate-900 px-8 py-2.5 text-sm font-bold text-white transition-all hover:bg-slate-800 hover:shadow-xl active:scale-95 disabled:opacity-50">
              {{ replyForm.processing ? 'Đang gửi...' : 'Gửi phản hồi' }}
            </button>
          </div>
        </div>
      </template>
    </CustomModal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
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

const replyTarget = ref(null)
const replyForm = useForm({
  reply_message: '',
  status: 'read',
})
const filterForm = useForm({
  status: props.filters?.status || '',
  processing_state: props.filters?.processing_state || '',
})

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
  replyTarget.value = item
  replyForm.reset()
}

const closeReply = () => {
  replyTarget.value = null
  replyForm.reset()
}

const submitReply = () => {
  if (!replyTarget.value) return
  if (!replyForm.reply_message.trim()) {
    toast.warning('Vui lòng nhập nội dung phản hồi.')
    return
  }

  replyForm.post(route('feedbacks.reply', replyTarget.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeReply()
      toast.success('Đã gửi trả lời.')
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
