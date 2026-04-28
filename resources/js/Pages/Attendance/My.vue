<template>
  <Head title="Công của tôi" />

  <AdminLayout>
    <PageBreadcrumb title="Công của tôi" :items="[{ text: 'Chấm công', link: null }, { text: 'Công của tôi', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
        <div v-if="card.hint" class="mt-1 text-xs text-gray-500">{{ card.hint }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="text-sm font-semibold text-gray-900">Bộ lọc kỳ chấm công</div>
          <div class="mt-1 text-sm text-gray-500">Chuyển nhanh qua từng tháng hoặc chọn tháng, năm cụ thể để xem dữ liệu.</div>
        </div>
        <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
          Đang xem: {{ currentPeriodLabel }}
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_auto]">
        <div class="max-w-xl">
          <InputDate
            v-model="filterForm.period"
            label="Chọn tháng năm"
            placeholder="Chọn tháng năm"
            :clearable="false"
            :config="periodPickerConfig"
          />
        </div>

        <div class="flex flex-wrap items-center gap-2 xl:justify-end">
          <button
            type="button"
            class="rounded-2xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="jumpToCurrentPeriod"
          >
            Tháng này
          </button>
        </div>
      </div>
    </div>

    <div v-if="!isAttendancePeriodClosed" class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Gợi ý xử lý nhanh</h3>
          <p class="mt-1 text-sm text-gray-500">Hệ thống tự nhận diện ngày cần xử lý và điền sẵn form cho bạn.</p>
        </div>
        <div class="text-xs font-medium text-gray-500">Bạn cũng có thể bấm "Tạo đơn" ngay trong bảng công bên dưới.</div>
      </div>

      <div v-if="smartRecommendations.length" class="mt-4 grid grid-cols-1 gap-3 xl:grid-cols-3">
        <div
          v-for="item in smartRecommendations"
          :key="`${item.requestType}-${item.record.id || item.record.work_date}`"
          class="rounded-2xl border border-gray-200 bg-gray-50 p-4"
        >
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold text-gray-900">{{ item.title }}</div>
              <div class="mt-1 text-xs text-gray-500">{{ item.dateLabel }}</div>
            </div>
            <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-700">{{ requestTypeLabel(item.requestType) }}</span>
          </div>
          <p class="mt-3 text-sm text-gray-600">{{ item.description }}</p>
          <button
            type="button"
            class="mt-4 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
            @click="applySmartRequest(item.record, item.requestType)"
          >
            Tạo đơn tự động
          </button>
        </div>
      </div>

      <div v-else class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        Không có ngày nào cần xử lý gấp trong kỳ hiện tại.
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Gửi đơn liên quan chấm công</h3>
      </div>

      <div v-if="formError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ formError }}
      </div>

      <div v-if="selectedSmartContext && !isAttendancePeriodClosed" class="mb-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm text-blue-900">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div>
            <div class="font-semibold">{{ selectedSmartContext.title }}</div>
            <div class="mt-1">{{ selectedSmartContext.description }}</div>
            <div v-if="selectedSmartContext.details?.length" class="mt-3 flex flex-wrap gap-2">
              <span
                v-for="detail in selectedSmartContext.details"
                :key="detail"
                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-semibold text-blue-800"
              >
                {{ detail }}
              </span>
            </div>
          </div>
          <button
            type="button"
            class="rounded-xl border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
            @click="clearSmartContext"
          >
            Bỏ gợi ý và tự nhập lại
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Loại đơn</label>
          <select v-model="requestForm.request_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chọn loại đơn</option>
            <option v-for="type in request_types" :key="type.value" :value="type.value">{{ requestTypeLabel(type.value) }}</option>
          </select>
          <p class="mt-1 text-xs text-gray-500">Chọn "Xin đi muộn / về sớm" nếu cần giải trình vi phạm giờ công.</p>
          <p v-if="requestForm.errors.request_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.request_type }}</p>
        </div>

        <InputDate
          v-if="showSingleDate"
          v-model="requestForm.request_date"
          :label="singleDateLabel"
          placeholder="Chọn ngày"
          :error="requestForm.errors.request_date"
          :config="singleDatePickerConfig"
        >
          <template v-if="singleDateHint" #helper>{{ singleDateHint }}</template>
        </InputDate>

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.from_date"
          :label="fromDateLabel"
          placeholder="Chọn từ ngày"
          :error="requestForm.errors.from_date"
          :config="datePickerConfig"
        >
          <template v-if="dateRangeHint" #helper>{{ dateRangeHint }}</template>
        </InputDate>

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.to_date"
          :label="toDateLabel"
          placeholder="Chọn đến ngày"
          :error="requestForm.errors.to_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Lý do nghỉ (Loại nghỉ)</label>
          <select v-model="requestForm.leave_type_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">-- Chọn lý do nghỉ cụ thể --</option>
            <optgroup v-if="paidLeaveTypes.length" label="Nghỉ hưởng lương (Theo quy định)">
              <option v-for="type in paidLeaveTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </optgroup>
            <optgroup v-if="unpaidLeaveTypes.length" label="Nghỉ không hưởng lương">
              <option v-for="type in unpaidLeaveTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </optgroup>
          </select>
          
          <div v-if="selectedLeaveType" class="mt-2 rounded-lg border border-emerald-100 bg-emerald-50 p-3 text-xs text-emerald-800">
            <div class="flex items-start gap-2">
              <span class="mt-0.5 inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
              <div>
                <p class="font-semibold">{{ selectedLeaveType.name }} ({{ selectedLeaveType.is_paid ? 'Có lương' : 'Không lương' }})</p>
                <p class="mt-1 opacity-90">{{ selectedLeaveType.description || 'Nghỉ phép theo quy định của công ty.' }}</p>
                <p v-if="selectedLeaveType.deducts_balance" class="mt-1 font-medium">
                  Hạn mức còn lại của bạn: <span class="text-emerald-700">{{ formatWorkUnits(selectedLeaveAvailableDays) }} ngày</span>
                </p>
              </div>
            </div>
          </div>
          <p v-if="requestForm.errors.leave_type_id || requestForm.errors.leave_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_type_id || requestForm.errors.leave_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Thời lượng nghỉ</label>
          <select v-model="requestForm.leave_duration_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="full_day">Cả ngày</option>
            <option value="half_day">Nửa ngày</option>
            <option value="hourly">Theo giờ</option>
          </select>
          <p v-if="requestForm.errors.leave_duration_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_duration_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave' && requestForm.leave_duration_type === 'hourly'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Số giờ nghỉ</label>
          <input v-model.number="requestForm.leave_hours" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="number" min="0.5" max="24" step="0.5">
          <p v-if="requestForm.errors.leave_hours" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_hours }}</p>
        </div>

        <div v-if="showLeaveAttachmentField">
          <label class="mb-2 block text-sm font-medium text-gray-700">Minh chứng (Bắt buộc)</label>
          <input ref="attachmentInput" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" @change="setAttachment">
          <div class="mt-2 flex items-center gap-2 text-xs text-amber-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ attachmentWarningMessage }}</span>
          </div>
          <p v-if="requestForm.errors.attachment" class="mt-1 text-sm text-red-500">{{ requestForm.errors.attachment }}</p>
        </div>

        <div v-if="requestForm.request_type === 'late_early'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Vi phạm cần giải trình</label>
          <select v-model="requestForm.requested_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="late">Đi muộn</option>
            <option value="early_leave">Về sớm</option>
          </select>
          <p v-if="selectedSmartContext?.requestType === 'late_early'" class="mt-1 text-xs text-gray-500">
            Đã điền theo vi phạm chính, bạn có thể đổi nếu cần.
          </p>
          <p v-if="requestForm.errors.requested_status" class="mt-1 text-sm text-red-500">{{ requestForm.errors.requested_status }}</p>
        </div>

        <div v-if="requestForm.request_type === 'business_trip'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Địa điểm công tác</label>
          <input
            v-model="requestForm.business_trip_location"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
            type="text"
            placeholder="Nhập địa điểm công tác"
          >
          <p v-if="requestForm.errors.business_trip_location" class="mt-1 text-sm text-red-500">{{ requestForm.errors.business_trip_location }}</p>
        </div>

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.from_time"
          :label="fromTimeLabel"
          placeholder="Chọn giờ"
          :error="requestForm.errors.from_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.to_time"
          :label="toTimeLabel"
          placeholder="Chọn giờ"
          :error="requestForm.errors.to_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="requestForm.request_type === 'make_up'"
          v-model="requestForm.make_up_related_leave_date"
          label="Ngày nghỉ cần bù"
          placeholder="Chọn ngày nghỉ cần bù"
          :error="requestForm.errors.make_up_related_leave_date"
          :config="makeUpRelatedDatePickerConfig"
        >
          <template #helper>
            Chỉ cho chọn ngày có trạng thái nghỉ phép, nghỉ không lương hoặc thiếu công. Số giờ làm bù không được vượt phần công thiếu của ngày này.
          </template>
        </InputDate>



        <div v-if="requestForm.request_type === 'make_up'" class="md:col-span-2 rounded-lg border border-amber-100 bg-amber-50 p-4 text-sm text-amber-900">
          <div class="font-semibold">Quy tắc làm bù</div>
          <p class="mt-2">
            Làm bù dùng để bù công thiếu của một ngày nghỉ đã chọn. Đơn này không được tính là tăng ca và không phát sinh tiền OT.
          </p>
          <p class="mt-1">
            Hệ thống sẽ chặn nếu số giờ làm bù vượt quá phần công thiếu còn lại của ngày nghỉ cần bù.
          </p>
        </div>

        <InputDate
          v-if="requestForm.request_type === 'overtime'"
          v-model="requestForm.request_date"
          label="Ngày tăng ca"
          placeholder="Chọn ngày"
          :error="requestForm.errors.request_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'overtime'" class="md:col-span-2 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
          <div class="font-semibold">Quy tắc đăng ký tăng ca</div>
          <p class="mt-2">
            Nhân viên vui lòng tự nhập giờ bắt đầu và giờ kết thúc tăng ca thực tế. Tăng ca là thời gian làm việc ngoài khung hành chính được cấu hình OT.
          </p>
          <p class="mt-1">
            Tăng ca không dùng để bù cho ngày nghỉ thiếu công.
          </p>
          <p v-if="requestForm.errors.start_at || requestForm.errors.end_at" class="mt-2 text-red-600">
            {{ requestForm.errors.start_at || requestForm.errors.end_at }}
          </p>
        </div>
      </div>

      <div class="mt-4">
        <label class="mb-2 block text-sm font-medium text-gray-700">{{ reasonLabel }}</label>
        <textarea v-model="requestForm.reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2" :placeholder="reasonPlaceholder"></textarea>
        <p v-if="requestForm.errors.reason" class="mt-1 text-sm text-red-500">{{ requestForm.errors.reason }}</p>
      </div>

      <div class="mt-4 flex justify-end">
        <button
          type="button"
          class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
          :disabled="requestForm.processing"
          @click="submitAttendanceRequest"
        >
          {{ requestForm.processing ? 'Đang gửi...' : 'Gửi đơn' }}
        </button>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bảng công cá nhân</h3>
      </div>

      <DataTable
        :columns="columns"
        :data="records"
        :actions="recordActions"
        :row-class="attendanceRowClass"
        paginate
        :default-per-page="10"
        empty-message="Chưa có dữ liệu chấm công."
      >
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
        </template>
        <template #cell-check_in_at="{ item }">
          {{ formatDateTime(item.check_in_at) }}
        </template>
        <template #cell-check_out_at="{ item }">
          {{ formatDateTime(item.check_out_at) }}
        </template>
        <template #cell-worked_minutes="{ item }">
          {{ formatMinutes(item.worked_minutes) }}
        </template>
        <template #cell-late_minutes="{ item }">
          {{ shouldShowViolationMinutes(item) ? formatMinutes(item.late_minutes) : '-' }}
        </template>
        <template #cell-early_leave_minutes="{ item }">
          {{ shouldShowViolationMinutes(item) ? formatMinutes(item.early_leave_minutes) : '-' }}
        </template>
        <template #cell-overtime_minutes="{ item }">
          {{ formatMinutes(item.overtime_minutes) }}
        </template>
        <template #cell-attendance_status="{ item }">
          <span :class="statusClass(item)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatAttendanceStatus(item) }}
          </span>
        </template>
        <template #cell-day_status="{ item }">
          <span :class="dayStatusClass(item.day_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatDayStatus(item) }}
          </span>
        </template>
        <template #cell-approval_status="{ item }">
          <span :class="approvalStatusClass(item.display_approval_status || item.approval_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.display_approval_status || item.approval_status) }}
          </span>
        </template>
        <template #cell-request_presence_label="{ item }">
          <button
            v-if="item.request_detail"
            type="button"
            :class="requestPresenceClass(item)"
            :title="requestLinkTitle(item)"
            class="inline-flex max-w-[190px] rounded-full px-3 py-1 text-xs font-semibold transition hover:ring-2 hover:ring-blue-200"
            @click="openLinkedRequestDetail(item)"
          >
            <span class="truncate">{{ item.request_presence_label || 'Xem đơn' }}</span>
          </button>
          <span v-else class="inline-flex max-w-[170px] rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
            <span class="truncate">Không có đơn</span>
          </span>
        </template>
      </DataTable>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Đơn chấm công đã gửi</h3>
        <p class="mt-1 text-sm text-gray-500">Theo dõi các đơn bạn đã gửi và trạng thái duyệt hiện tại.</p>
      </div>

      <DataTable
        :columns="requestColumns"
        :data="recent_requests"
        :actions="requestActions"
        :row-class="requestRowClass"
        paginate
        :default-per-page="5"
        :per-page-options="[5, 10, 20]"
        empty-message="Bạn chưa gửi đơn chấm công nào."
      >
        <template #cell-request_type_label="{ item }">
          {{ item.request_type_label || requestTypeLabel(item.request_type) }}
        </template>
        <template #cell-request_date="{ item }">
          {{ formatDate(item.request_date) }}
        </template>
        <template #cell-status_label="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.status_label }}
          </span>
        </template>
        <template #cell-submitted_at="{ item }">
          {{ formatDateTime(item.submitted_at) }}
        </template>
      </DataTable>
    </div>

    <Modal :show="!!selectedSubmittedRequest" @close="closeSubmittedRequestDetail">
      <div v-if="selectedSubmittedRequest" class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Chi tiết đơn đã gửi</h3>
        <div class="grid grid-cols-1 gap-3 text-sm text-gray-700 md:grid-cols-2">
          <div><span class="font-medium text-gray-900">Loại đơn:</span> {{ selectedSubmittedRequest.request_type_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Trạng thái:</span> {{ selectedSubmittedRequest.status_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Ngày áp dụng:</span> {{ formatDate(selectedSubmittedRequest.request_date) }}</div>
          <div><span class="font-medium text-gray-900">Khoảng thời gian:</span> {{ selectedSubmittedRequest.period || '-' }}</div>
          <div><span class="font-medium text-gray-900">Gửi lúc:</span> {{ formatDateTime(selectedSubmittedRequest.submitted_at) }}</div>
          <div><span class="font-medium text-gray-900">Người duyệt:</span> {{ selectedSubmittedRequest.reviewed_by_name || '-' }}</div>
          <div><span class="font-medium text-gray-900">Duyệt lúc:</span> {{ formatDateTime(selectedSubmittedRequest.reviewed_at) }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Lý do:</span> {{ selectedSubmittedRequest.reason || '-' }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Ghi chú duyệt:</span> {{ selectedSubmittedRequest.review_note || '-' }}</div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'attendance'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thông tin đơn nghỉ/chấm công</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Từ ngày:</span> {{ formatDate(selectedSubmittedRequest.from_date) }}</div>
            <div><span class="font-medium text-gray-900">Đến ngày:</span> {{ formatDate(selectedSubmittedRequest.to_date) }}</div>
            <div><span class="font-medium text-gray-900">Từ giờ:</span> {{ selectedSubmittedRequest.from_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Đến giờ:</span> {{ selectedSubmittedRequest.to_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Loại nghỉ:</span> {{ selectedSubmittedRequest.leave_type_name || selectedSubmittedRequest.leave_type || '-' }}</div>
            <div><span class="font-medium text-gray-900">Thời lượng:</span> {{ leaveDurationLabel(selectedSubmittedRequest) }}</div>
            <div v-if="selectedSubmittedRequest.business_trip_location">
              <span class="font-medium text-gray-900">Địa điểm công tác:</span> {{ selectedSubmittedRequest.business_trip_location }}
            </div>
            <div v-if="selectedSubmittedRequest.make_up_related_leave_date">
              <span class="font-medium text-gray-900">Ngày nghỉ cần bù:</span> {{ formatDate(selectedSubmittedRequest.make_up_related_leave_date) }}
            </div>
            <div v-if="selectedSubmittedRequest.leave_days !== null && selectedSubmittedRequest.leave_days !== undefined">
              <span class="font-medium text-gray-900">Số ngày nghỉ:</span> {{ formatWorkUnits(selectedSubmittedRequest.leave_days) }} ngày
            </div>
            <div v-if="selectedSubmittedRequest.requested_status">
              <span class="font-medium text-gray-900">Trạng thái đề nghị:</span> {{ selectedSubmittedRequest.requested_status }}
            </div>
          </div>

          <div v-if="selectedSubmittedRequest.request_type === 'leave'" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
            <div class="font-medium text-amber-900">Minh chứng</div>
            <div v-if="selectedSubmittedRequest.attachment_url" class="mt-1">
              <a
                :href="selectedSubmittedRequest.attachment_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-blue-700 underline underline-offset-2"
              >
                Xem tệp đính kèm
              </a>
            </div>
            <div v-else-if="selectedSubmittedRequest.leave_type_requires_attachment" class="mt-1 text-sm text-rose-700">
              Loại nghỉ này yêu cầu minh chứng nhưng đơn hiện không có tệp đính kèm.
            </div>
            <div v-else class="mt-1 text-sm text-gray-500">
              Loại nghỉ này không yêu cầu minh chứng.
            </div>
          </div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'overtime'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thông tin tăng ca</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Bắt đầu:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_start_at) }}</div>
            <div><span class="font-medium text-gray-900">Kết thúc:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_end_at) }}</div>
            <div><span class="font-medium text-gray-900">Phút đề nghị:</span> {{ formatMinutes(selectedSubmittedRequest.requested_minutes) }}</div>
            <div><span class="font-medium text-gray-900">Phút duyệt:</span> {{ formatMinutes(selectedSubmittedRequest.approved_minutes) }}</div>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button
            v-if="canCancelSubmittedRequest(selectedSubmittedRequest)"
            type="button"
            class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 disabled:opacity-60"
            @click="cancelSubmittedRequest(selectedSubmittedRequest)"
          >
            Hủy đơn
          </button>
          <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700" @click="closeSubmittedRequestDetail">
            Đóng
          </button>
        </div>
      </div>
    </Modal>
    <ActionDialog ref="actionDialogRef" />
  </AdminLayout>
</template>

<script setup>
import { computed, nextTick, reactive, ref, watch } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import InputDate from '@/components/forms/InputDate.vue'
import Modal from '@/components/ui/Modal.vue'
import ActionDialog from '@/components/ui/ActionDialog.vue'
import { useActionDialog } from '@/composables/useActionDialog'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  request_types: { type: Array, default: () => [] },
  recent_requests: { type: Array, default: () => [] },
  overtime_catalog: { type: Object, default: () => ({}) },
  leave_types: { type: Array, default: () => [] },
  leave_balances: { type: Array, default: () => [] },
  make_up_quota_catalog: { type: Array, default: () => [] },
  month_lock: { type: Object, default: () => ({ is_locked: false }) },
  payroll_period: { type: Object, default: () => ({ is_locked: false }) },
})

const page = usePage()
const { actionDialogRef, openConfirm } = useActionDialog()
const attachmentInput = ref(null)
const selectedSubmittedRequest = ref(null)
const selectedSmartContext = ref(null)
const isApplyingSmartRequest = ref(false)

const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
})

let lastAppliedPeriod = `${filterForm.year}-${filterForm.month}`

const requestForm = useForm({
  request_type: '',
  request_date: '',
  from_date: '',
  to_date: '',
  from_time: '',
  to_time: '',
  leave_type_id: '',
  leave_type: 'paid',
  leave_duration_type: 'full_day',
  leave_hours: '',
  attachment: null,
  requested_status: 'late',
  start_at: '',
  end_at: '',
  business_trip_location: '',
  make_up_related_leave_date: '',
  reason: '',
})

const columns = [
  { label: 'Ngày công', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'Giờ làm', key: 'worked_minutes', align: 'text-center' },
  { label: 'Đi muộn', key: 'late_minutes', align: 'text-center' },
  { label: 'Về sớm', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Tăng ca', key: 'overtime_minutes', align: 'text-center' },
  { label: 'Kết quả công', key: 'attendance_status', align: 'text-center' },
  { label: 'Trạng thái ngày', key: 'day_status', align: 'text-center' },
  { label: 'Duyệt', key: 'approval_status', align: 'text-center' },
  { label: 'Đơn liên quan', key: 'request_presence_label', align: 'text-center' },
]

const requestColumns = [
  { label: 'Loại đơn', key: 'request_type_label' },
  { label: 'Ngày áp dụng', key: 'request_date' },
  { label: 'Khoảng thời gian', key: 'period' },
  { label: 'Trạng thái', key: 'status_label', align: 'text-center' },
  { label: 'Người duyệt', key: 'reviewed_by_name' },
  { label: 'Ghi chú duyệt', key: 'review_note' },
  { label: 'Gửi lúc', key: 'submitted_at' },
]

const requestActions = [
  {
    label: 'Chi tiết',
    buttonProps: {
      title: 'Xem chi tiết đơn đã gửi',
      class: 'border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800',
    },
    onClick: (item) => openSubmittedRequestDetail(item),
  },
  {
    label: 'Hủy',
    buttonProps: {
      title: 'Hủy đơn đang chờ duyệt',
      class: 'border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 shadow-sm hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800',
    },
    hidden: (item) => !canCancelSubmittedRequest(item),
    onClick: (item) => cancelSubmittedRequest(item),
  },
]

const recordActions = [
  {
    label: 'Tạo đơn',
    buttonProps: {
      title: 'Tạo đơn tự động cho dòng công này',
      class: 'border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800',
    },
    hidden: (item) => !suggestedRequestTypeForRecord(item),
    onClick: (item) => applySmartRequest(item, suggestedRequestTypeForRecord(item)),
  },
]

const summaryCards = computed(() => [
  { label: 'Ngày công hợp lệ', value: formatWorkUnits(props.summary.approved_work_units ?? props.summary.total_work_units ?? 0) },
  {
    label: 'Công cần bổ sung / xác minh',
    value: props.summary.action_required_records ?? props.summary.pending_records ?? 0,
    hint: verificationHint.value,
  },
  { label: 'Đơn của tôi chờ duyệt', value: props.summary.pending_request_records ?? 0 },
  { label: 'Giờ làm thực tế đã duyệt', value: formatMinutes(props.summary.approved_actual_worked_minutes ?? props.summary.approved_worked_minutes ?? 0) },
])

const verificationHint = computed(() => {
  const missingCheck = props.summary.needs_verification_missing_check_records ?? props.summary.missing_check_records ?? 0
  const missingAttendance = props.summary.needs_verification_missing_attendance_records ?? props.summary.missing_attendance_records ?? 0
  const timeViolation = props.summary.needs_verification_time_violation_records ?? 0

  return `Thiếu check: ${missingCheck}, vắng: ${missingAttendance}, giờ công: ${timeViolation}`
})

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Tháng ${filterForm.month} / ${filterForm.year}`

  return new Intl.DateTimeFormat('vi-VN', {
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const formError = computed(() => requestForm.errors.error || page.props.errors?.error || '')
const overtimeCatalog = computed(() => props.overtime_catalog || {})
const isAttendancePeriodClosed = computed(() => Boolean(props.month_lock?.is_locked || props.payroll_period?.is_locked))
const selectedLeaveType = computed(() => (props.leave_types || []).find((type) => Number(type.id) === Number(requestForm.leave_type_id)) || null)
const paidLeaveTypes = computed(() => (props.leave_types || []).filter((type) => type.is_paid))
const unpaidLeaveTypes = computed(() => (props.leave_types || []).filter((type) => !type.is_paid))

const selectedLeaveBalance = computed(() => (props.leave_balances || []).find((balance) => Number(balance.leave_type_id) === Number(requestForm.leave_type_id)) || null)
const selectedLeaveAvailableDays = computed(() => selectedLeaveBalance.value?.available_days ?? selectedLeaveType.value?.annual_quota ?? 0)

const estimatedLeaveDays = computed(() => {
  if (requestForm.request_type !== 'leave' || !requestForm.from_date || !requestForm.to_date) return 0
  if (requestForm.leave_duration_type === 'half_day') return 0.5
  if (requestForm.leave_duration_type === 'hourly') return (Number(requestForm.leave_hours) || 0) / 8

  const start = new Date(requestForm.from_date)
  const end = new Date(requestForm.to_date)
  if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) return 0

  const diffTime = Math.abs(end - start)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  return diffDays
})

const showLeaveAttachmentField = computed(() => {
  if (requestForm.request_type !== 'leave' || !selectedLeaveType.value) return false
  
  if (selectedLeaveType.value.requires_attachment) return true
  
  const isSickLeave = ['SICK', 'NGHI_OM'].includes(selectedLeaveType.value.code)
  if (isSickLeave && estimatedLeaveDays.value >= 3) return true
  
  return false
})

const attachmentWarningMessage = computed(() => {
  if (!selectedLeaveType.value) return ''
  const isSickLeave = ['SICK', 'NGHI_OM'].includes(selectedLeaveType.value.code)
  if (isSickLeave && estimatedLeaveDays.value >= 3) {
    return 'Nghỉ bệnh từ 3 ngày trở lên yêu cầu giấy xác nhận của bác sĩ.'
  }
  return 'Loại nghỉ này yêu cầu tải lên minh chứng.'
})
const makeUpEligibleDates = computed(() => {
  const dates = (props.make_up_quota_catalog || [])
    .map((item) => item.date)
    .filter(Boolean)

  return [...new Set(dates)]
})
const makeUpQuotaMap = computed(() => Object.fromEntries((props.make_up_quota_catalog || []).map((item) => [item.date, item])))
const selectedMakeUpQuota = computed(() => {
  const date = requestForm.make_up_related_leave_date
  if (!date) return null
  return makeUpQuotaMap.value[date] || null
})
const makeUpRelatedDatePickerConfig = computed(() => ({
  ...datePickerConfig,
  enable: makeUpEligibleDates.value.length > 0 ? makeUpEligibleDates.value : [() => false],
}))
const smartRecommendations = computed(() => {
  if (isAttendancePeriodClosed.value) return []

  const usedKeys = new Set()

  return (props.records || [])
    .map((record) => {
      const requestType = suggestedRequestTypeForRecord(record)
      if (!requestType) return null

      const key = `${requestType}-${record.work_date}`
      if (usedKeys.has(key)) return null
      usedKeys.add(key)

      return {
        record,
        requestType,
        title: smartRecommendationTitle(record, requestType),
        description: smartRecommendationDescription(record, requestType),
        dateLabel: formatDate(record.work_date),
      }
    })
    .filter(Boolean)
    .slice(0, 3)
})
const overtimeWindowLabel = computed(() => {
  if (!overtimeCatalog.value.start_time || !overtimeCatalog.value.end_time) return 'Chưa cấu hình'
  return `${overtimeCatalog.value.start_time} - ${overtimeCatalog.value.end_time}`
})
const showSingleDate = computed(() => ['forgot_check', 'late_early', 'make_up'].includes(requestForm.request_type))
const showDateRange = computed(() => ['leave', 'business_trip'].includes(requestForm.request_type))
const showTimeRange = computed(() => ['forgot_check', 'late_early', 'business_trip', 'make_up', 'overtime'].includes(requestForm.request_type))
const singleDateLabel = computed(() => {
  if (requestForm.request_type === 'forgot_check') return 'Ngày quên chấm công'
  if (requestForm.request_type === 'late_early') return 'Ngày vi phạm'
  if (requestForm.request_type === 'make_up') return 'Ngày làm bù'
  return 'Ngày áp dụng'
})
const fromDateLabel = computed(() => requestForm.request_type === 'business_trip' ? 'Ngày bắt đầu công tác' : 'Ngày bắt đầu nghỉ')
const toDateLabel = computed(() => requestForm.request_type === 'business_trip' ? 'Ngày kết thúc công tác' : 'Ngày kết thúc nghỉ')
const fromTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Bắt đầu làm bù'
  if (requestForm.request_type === 'forgot_check') return 'Giờ check-in nếu quên'
  if (requestForm.request_type === 'business_trip') return 'Bắt đầu công tác'
  if (requestForm.request_type === 'overtime') return 'Bắt đầu tăng ca'
  if (requestForm.request_type === 'late_early') return requestForm.requested_status === 'early_leave' ? 'Bắt đầu về sớm' : 'Bắt đầu đi muộn'
  return 'Từ giờ'
})
const toTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Kết thúc làm bù'
  if (requestForm.request_type === 'forgot_check') return 'Giờ check-out nếu quên'
  if (requestForm.request_type === 'business_trip') return 'Kết thúc công tác'
  if (requestForm.request_type === 'overtime') return 'Kết thúc tăng ca'
  if (requestForm.request_type === 'late_early') return requestForm.requested_status === 'early_leave' ? 'Kết thúc về sớm' : 'Kết thúc đi muộn'
  return 'Đến giờ'
})
const singleDateHint = computed(() => {
  if (requestForm.request_type === 'forgot_check') return 'Chỉ áp dụng cho ngày đã xảy ra'
  return ''
})
const dateRangeHint = computed(() => {
  if (requestForm.request_type === 'leave') return 'Có thể đăng ký cho hôm nay hoặc ngày tới'
  return ''
})
const reasonLabel = computed(() => {
  if (requestForm.request_type === 'business_trip') return 'Nội dung công tác'
  if (requestForm.request_type === 'overtime') return 'Lý do tăng ca'
  return 'Lý do'
})
const reasonPlaceholder = computed(() => {
  if (requestForm.request_type === 'business_trip') return 'Nhập nội dung công tác'
  if (requestForm.request_type === 'overtime') return 'Nhập lý do đăng ký tăng ca'
  return 'Nhập lý do chi tiết'
})

const datePickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
}

const todayDate = new Date().toISOString().slice(0, 10)

const singleDatePickerConfig = computed(() => {
  if (['forgot_check', 'late_early'].includes(requestForm.request_type)) {
    return {
      ...datePickerConfig,
      maxDate: todayDate,
    }
  }

  return {
    ...datePickerConfig,
    minDate: todayDate,
  }
})

const periodPickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-01',
  altInput: true,
  altFormat: 'm/Y',
  defaultDate: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
}

const timePickerConfig = {
  allowInput: false,
  enableTime: true,
  noCalendar: true,
  time_24hr: true,
  minuteIncrement: 5,
  dateFormat: 'H:i',
  altInput: true,
  altFormat: 'H:i',
}

const dateTimePickerConfig = {
  allowInput: false,
  enableTime: true,
  time_24hr: true,
  minuteIncrement: 5,
  dateFormat: 'Y-m-d H:i',
  altInput: true,
  altFormat: 'd/m/Y H:i',
}

const REQUEST_TYPE_LABELS = {
  leave: 'Xin nghỉ phép',
  late_early: 'Xin đi muộn / về sớm',
  forgot_check: 'Xin quên chấm công',
  business_trip: 'Xin công tác',
  make_up: 'Xin làm bù',
  overtime: 'Đăng ký tăng ca',
}

watch(() => requestForm.request_type, (type) => {
  requestForm.clearErrors()
  requestForm.request_date = ''
  requestForm.from_date = ''
  requestForm.to_date = ''
  requestForm.from_time = ''
  requestForm.to_time = ''
  requestForm.leave_type_id = ''
  requestForm.leave_duration_type = 'full_day'
  requestForm.leave_hours = ''
  requestForm.attachment = null
  if (attachmentInput.value) {
    attachmentInput.value.value = ''
  }
  requestForm.start_at = ''
  requestForm.end_at = ''
  requestForm.business_trip_location = ''
  requestForm.make_up_related_leave_date = ''

  if (type !== 'leave') {
    requestForm.leave_type = 'paid'
  }

  if (type !== 'late_early') {
    requestForm.requested_status = 'late'
  }

  if (type === 'overtime') {
    requestForm.request_date = overtimeCatalog.value.work_date || new Date().toISOString().slice(0, 10)
  }

  if (!isApplyingSmartRequest.value) {
    selectedSmartContext.value = null
  }
})

watch(() => requestForm.leave_type_id, () => {
  requestForm.attachment = null
  if (attachmentInput.value) {
    attachmentInput.value.value = ''
  }
})

watch(isAttendancePeriodClosed, (closed) => {
  if (closed) {
    selectedSmartContext.value = null
  }
})

watch(
  () => [Number(filterForm.month), Number(filterForm.year)],
  ([month, year]) => {
    if (!month || !year) return

    const nextPeriod = `${year}-${month}`
    if (nextPeriod === lastAppliedPeriod) return

    lastAppliedPeriod = nextPeriod
    applyFilters()
  }
)

watch(
  () => filterForm.period,
  (value) => {
    const date = parsePeriodValue(value)
    if (!date) return

    const nextMonth = date.getMonth() + 1
    const nextYear = date.getFullYear()

    if (nextMonth === Number(filterForm.month) && nextYear === Number(filterForm.year)) {
      return
    }

    filterForm.month = nextMonth
    filterForm.year = nextYear
  }
)

function requestTypeLabel(value) {
  return REQUEST_TYPE_LABELS[value] || value || '-'
}

function applyFilters() {
  router.get(route('attendance.mine'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function shiftPeriod(direction) {
  const currentMonth = Number(filterForm.month)
  const currentYear = Number(filterForm.year)

  if (!currentMonth || !currentYear) return

  const period = new Date(currentYear, currentMonth - 1 + direction, 1)
  filterForm.month = period.getMonth() + 1
  filterForm.year = period.getFullYear()
}

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
}

function buildPeriodValue(year, month) {
  if (!year || !month) return ''
  return `${year}-${String(month).padStart(2, '0')}-01`
}

function parsePeriodValue(value) {
  if (!value) return null
  const date = value instanceof Date ? value : new Date(value)
  return Number.isNaN(date.getTime()) ? null : date
}

function submitAttendanceRequest() {
  if (requestForm.request_type === 'overtime' && requestForm.request_date && requestForm.from_time && requestForm.to_time) {
    requestForm.start_at = `${requestForm.request_date} ${requestForm.from_time}`
    requestForm.end_at = `${requestForm.request_date} ${requestForm.to_time}`
  }

  if (requestForm.request_type === 'leave' && selectedLeaveType.value) {
    requestForm.leave_type = selectedLeaveType.value.is_paid ? 'paid' : 'unpaid'
  }

  requestForm.post(route('attendance.requests.store'), {
    preserveScroll: true,
    onSuccess: () => {
      requestForm.reset()
      selectedSmartContext.value = null
    },
  })
}

function setAttachment(event) {
  requestForm.attachment = event.target.files?.[0] || null
}

function openSubmittedRequestDetail(item) {
  selectedSubmittedRequest.value = item
}

function openLinkedRequestDetail(item) {
  if (!item?.request_detail) return
  selectedSubmittedRequest.value = item.request_detail
}

function closeSubmittedRequestDetail() {
  selectedSubmittedRequest.value = null
}

function canCancelSubmittedRequest(item) {
  return item?.status === 'pending' && !!item?.approval_request_id
}

async function cancelSubmittedRequest(item) {
  if (!canCancelSubmittedRequest(item)) return

  const confirmed = await openConfirm({
    title: 'Hủy đơn chấm công',
    message: 'Bạn có chắc muốn hủy đơn đang chờ duyệt này?',
    okText: 'Xác nhận hủy',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Xác nhận',
  })
  if (!confirmed) return

  router.delete(route('attendance.requests.destroy', item.approval_request_id), {
    preserveScroll: true,
    onSuccess: () => {
      if (selectedSubmittedRequest.value?.approval_request_id === item.approval_request_id) {
        selectedSubmittedRequest.value = null
      }
    },
  })
}

async function applySmartRequest(record, requestType) {
  if (!record || !requestType || isAttendancePeriodClosed.value) return

  isApplyingSmartRequest.value = true
  requestForm.request_type = requestType
  requestForm.clearErrors()
  selectedSmartContext.value = {
    title: smartRecommendationTitle(record, requestType),
    description: smartRecommendationDescription(record, requestType),
    details: smartRecommendationDetails(record, requestType),
    requestType,
  }

  await nextTick()

  if (requestType === 'forgot_check') {
    requestForm.request_date = record.work_date || ''
    requestForm.from_time = record.missing_check_in ? suggestedCheckInTime(record) : ''
    requestForm.to_time = record.missing_check_out ? suggestedCheckOutTime(record) : ''
    requestForm.reason = buildDefaultReason(record, requestType)
  }

  if (requestType === 'late_early') {
    const status = preferredLateEarlyStatus(record)
    const range = suggestedLateEarlyRange(record, status)

    requestForm.request_date = record.work_date || ''
    requestForm.requested_status = status
    requestForm.from_time = range.from
    requestForm.to_time = range.to
    requestForm.reason = buildDefaultReason(record, requestType)
  }

  if (requestType === 'make_up') {
    requestForm.request_date = todayDate
    requestForm.make_up_related_leave_date = record.work_date || ''
    requestForm.reason = buildDefaultReason(record, requestType)
  }

  if (requestType === 'overtime') {
    requestForm.request_date = overtimeCatalog.value.work_date || todayDate
    requestForm.reason = buildDefaultReason(record, requestType)
  }

  if (requestType === 'leave') {
    requestForm.from_date = record.work_date || ''
    requestForm.to_date = record.work_date || ''
    requestForm.reason = buildDefaultReason(record, requestType)
  }

  window.scrollTo({ top: 0, behavior: 'smooth' })
  isApplyingSmartRequest.value = false
}

function clearSmartContext() {
  selectedSmartContext.value = null
  requestForm.reset()
  if (attachmentInput.value) {
    attachmentInput.value.value = ''
  }
}

function requestLinkTitle(item) {
  const parts = [
    item?.request_detail?.request_type_label || item?.request_presence_label,
    item?.request_status_label,
    item?.request_reason,
  ].filter(Boolean)

  return parts.join(' - ') || 'Xem đơn liên quan'
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function formatMinutes(value) {
  const minutes = Number(value) || 0
  if (minutes <= 0) return '0 phút'
  const hours = Math.floor(minutes / 60)
  const remainMinutes = minutes % 60
  if (hours <= 0) return `${remainMinutes} phút`
  if (remainMinutes === 0) return `${hours} giờ`
  return `${hours} giờ ${remainMinutes} phút`
}

function suggestedRequestTypeForRecord(item) {
  if (isAttendancePeriodClosed.value || !item || hasExistingResolvedRequest(item)) return null

  if (hasMissingCheck(item)) {
    return 'forgot_check'
  }

  if (['late', 'early_leave', 'late_early'].includes(item?.violation_status)) {
    return 'late_early'
  }

  if (makeUpEligibleDates.value.includes(item?.work_date) && Number(item?.work_unit ?? 0) < 1) {
    return 'make_up'
  }

  return null
}

function hasExistingResolvedRequest(item) {
  return item?.request_type === suggestedRequestTypeForRecordByIssue(item) && ['pending', 'approved'].includes(item?.request_status)
}

function suggestedRequestTypeForRecordByIssue(item) {
  if (hasMissingCheck(item)) return 'forgot_check'
  if (['late', 'early_leave', 'late_early'].includes(item?.violation_status)) return 'late_early'
  if (makeUpEligibleDates.value.includes(item?.work_date) && Number(item?.work_unit ?? 0) < 1) return 'make_up'
  return null
}

function preferredLateEarlyStatus(item) {
  if (item?.violation_status === 'early_leave') return 'early_leave'
  if (item?.violation_status === 'late_early') {
    return Number(item?.early_leave_minutes ?? 0) > Number(item?.late_minutes ?? 0) ? 'early_leave' : 'late'
  }
  return 'late'
}

function smartRecommendationTitle(item, requestType) {
  if (requestType === 'forgot_check') {
    return hasMissingCheck(item) ? 'Ngày này đang thiếu check, nên tạo đơn quên chấm công' : 'Tạo đơn quên chấm công'
  }

  if (requestType === 'late_early') {
    return item?.violation_status === 'early_leave' ? 'Ngày này bị về sớm, nên tạo đơn giải trình' : 'Ngày này có vi phạm giờ, nên tạo đơn giải trình'
  }

  if (requestType === 'make_up') {
    return 'Ngày này đang thiếu công, có thể dùng làm bù'
  }

  return 'Có gợi ý để xử lý nhanh'
}

function smartRecommendationDescription(item, requestType) {
  if (requestType === 'forgot_check') {
    return item?.missing_check_in
      ? `Thiếu check-in ngày ${formatDate(item?.work_date)}. Form sẽ điền ngày vi phạm và giờ vào ca dự kiến.`
      : `Thiếu check-out ngày ${formatDate(item?.work_date)}. Form sẽ điền ngày vi phạm và giờ ra ca dự kiến.`
  }

  if (requestType === 'late_early') {
    if (item?.violation_status === 'early_leave') {
      return `Về sớm ${formatMinutes(item?.early_leave_minutes)} ngày ${formatDate(item?.work_date)}. Form sẽ điền sẵn ngày và khung giờ cần giải trình.`
    }

    if (item?.violation_status === 'late_early') {
      return `Có cả đi muộn ${formatMinutes(item?.late_minutes)} và về sớm ${formatMinutes(item?.early_leave_minutes)} ngày ${formatDate(item?.work_date)}. Form sẽ chọn vi phạm chính trước.`
    }

    return `Đi muộn ${formatMinutes(item?.late_minutes)} ngày ${formatDate(item?.work_date)}. Form sẽ điền sẵn ngày và khung giờ cần giải trình.`
  }

  if (requestType === 'make_up') {
    const quota = makeUpQuotaMap.value[item?.work_date]
    const remainingLabel = quota ? formatMinutes(quota.remaining_minutes) : null
    return `Ngày này mới đạt ${formatWorkUnits(item?.work_unit)} công.${remainingLabel ? ` Còn thiếu ${remainingLabel}.` : ''} Form sẽ gán sẵn ngày cần bù để bạn đăng ký làm bù nhanh.`
  }

  return 'Hệ thống đề xuất đơn phù hợp nhất với ngày công này.'
}

function smartRecommendationDetails(item, requestType) {
  const details = [
    `Ngày: ${formatDate(item?.work_date)}`,
  ]

  if (item?.shift_name) {
    details.push(`Ca: ${item.shift_name}`)
  }

  if (item?.shift_start_time || item?.shift_end_time) {
    details.push(`Giờ ca: ${[item?.shift_start_time, item?.shift_end_time].filter(Boolean).join(' - ')}`)
  }

  if (requestType === 'forgot_check') {
    details.push(item?.missing_check_in ? 'Thiếu check-in' : 'Thiếu check-out')
    const suggestedTime = item?.missing_check_in ? suggestedCheckInTime(item) : suggestedCheckOutTime(item)
    if (suggestedTime) details.push(`Giờ đề xuất: ${suggestedTime}`)
  }

  if (requestType === 'late_early') {
    const status = preferredLateEarlyStatus(item)
    const range = suggestedLateEarlyRange(item, status)

    if (Number(item?.late_minutes || 0) > 0) {
      details.push(`Đi muộn: ${formatMinutes(item.late_minutes)}`)
    }

    if (Number(item?.early_leave_minutes || 0) > 0) {
      details.push(`Về sớm: ${formatMinutes(item.early_leave_minutes)}`)
    }

    if (range.from && range.to) {
      details.push(`Khung giờ: ${range.from} - ${range.to}`)
    }
  }

  if (requestType === 'make_up') {
    const quota = makeUpQuotaMap.value[item?.work_date]
    if (quota) {
      details.push(`Còn thiếu: ${formatMinutes(quota.remaining_minutes)}`)
    }
  }

  return details
}

function buildDefaultReason(item, requestType) {
  const dateText = formatDate(item?.work_date)

  if (requestType === 'forgot_check') {
    return item?.missing_check_in
      ? `Giải trình bổ sung check-in ngày ${dateText}.`
      : `Giải trình bổ sung check-out ngày ${dateText}.`
  }

  if (requestType === 'late_early') {
    if (preferredLateEarlyStatus(item) === 'early_leave') {
      return `Giải trình về sớm ngày ${dateText}, về sớm ${formatMinutes(item?.early_leave_minutes)}.`
    }

    return `Giải trình đi muộn ngày ${dateText}, đi muộn ${formatMinutes(item?.late_minutes)}.`
  }

  if (requestType === 'make_up') {
    return `Đăng ký làm bù cho ngày ${dateText}.`
  }

  if (requestType === 'overtime') {
    return `Đăng ký tăng ca cho ngày ${dateText}.`
  }

  if (requestType === 'leave') {
    return `Đăng ký nghỉ cho ngày ${dateText}.`
  }

  return ''
}

function suggestedLateEarlyRange(item, status) {
  if (status === 'early_leave') {
    const from = timeFromDateTime(item?.check_out_at) || subtractMinutesFromTime(item?.shift_end_time, Number(item?.early_leave_minutes || 0))
    const to = item?.shift_end_time || addMinutesToTime(from, Number(item?.early_leave_minutes || 0))

    return normalizeSuggestedRange(from, to)
  }

  const to = timeFromDateTime(item?.check_in_at) || addMinutesToTime(item?.shift_start_time, Number(item?.late_minutes || 0))
  const from = item?.shift_start_time || subtractMinutesFromTime(to, Number(item?.late_minutes || 0))

  return normalizeSuggestedRange(from, to)
}

function suggestedCheckInTime(item) {
  return item?.shift_start_time || timeFromDateTime(item?.check_in_at) || ''
}

function suggestedCheckOutTime(item) {
  return item?.shift_end_time || timeFromDateTime(item?.check_out_at) || ''
}

function normalizeSuggestedRange(from, to) {
  if (!from || !to || from === to) {
    return { from: from || '', to: to || '' }
  }

  return { from, to }
}

function timeFromDateTime(value) {
  if (!value) return ''
  const text = String(value)
  const match = text.match(/(\d{2}:\d{2})/)
  return match?.[1] || ''
}

function addMinutesToTime(value, minutes) {
  return shiftTime(value, Math.abs(Number(minutes || 0)))
}

function subtractMinutesFromTime(value, minutes) {
  return shiftTime(value, -Math.abs(Number(minutes || 0)))
}

function shiftTime(value, minutes) {
  if (!value) return ''
  const [hour, minute] = String(value).split(':').map((part) => Number(part))
  if (!Number.isFinite(hour) || !Number.isFinite(minute)) return ''

  const date = new Date(2000, 0, 1, hour, minute + Number(minutes || 0), 0)
  return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`
}

function formatWorkUnits(value) {
  const units = Number(value) || 0
  return Number.isInteger(units) ? `${units}` : units.toFixed(1)
}

function leaveDurationLabel(item) {
  if (!item?.leave_duration_type) return '-'
  if (item.leave_duration_type === 'full_day') return 'Cả ngày'
  if (item.leave_duration_type === 'half_day') return 'Nửa ngày'
  if (item.leave_duration_type === 'hourly') {
    return `Theo giờ${item.leave_hours ? ` (${formatWorkUnits(item.leave_hours)} giờ)` : ''}`
  }

  return item.leave_duration_type
}

function hasMissingCheck(item) {
  return item?.day_status === 'missing_check_in' || item?.day_status === 'missing_check_out' || item?.missing_check_in || item?.missing_check_out
}

function shouldShowViolationMinutes(item) {
  return item?.approval_status !== 'rejected' && !hasMissingCheck(item)
}

function formatAttendanceStatus(item) {
  const resolvedUnit = Number(item?.work_unit)

  if (item?.day_status === 'day_off') {
    return 'Nghỉ theo phân ca'
  }

  if (item?.day_status === 'holiday_paid') {
    return 'Lễ có lương'
  }

  if (hasMissingCheck(item)) {
    return 'Chưa tính công'
  }

  if (item?.approval_status === 'rejected') {
    return 'Không duyệt công'
  }

  if (resolvedUnit === 1) {
    return 'Đủ công'
  }

  if (resolvedUnit === 0.5) {
    return 'Nửa công'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'Không công'
  }

  const labels = {
    on_time: 'Đúng giờ',
    late: 'Trễ',
    absent: 'Vắng',
  }
  return labels[item?.attendance_status] || '-'
}

function formatDayStatus(item) {
  const value = typeof item === 'string' ? item : item?.day_status

  if (value === 'unpaid_leave' && item?.violation_status === 'missing_attendance') {
    return 'Vắng mặt'
  }

  const labels = {
    present: 'Đi làm',
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    leave: 'Nghỉ phép',
    unpaid_leave: 'Nghỉ không lương',
    holiday_paid: 'Lễ có lương',
    day_off: 'Nghỉ theo phân ca',
    business_trip: 'Công tác',
    missing_check_in: 'Thiếu check-in',
    missing_check_out: 'Thiếu check-out',
    absent: 'Vắng mặt',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  if (value === 'needs_verification') {
    return 'Cần xác minh'
  }

  const labels = {
    pending: 'Chờ duyệt',
    not_required: 'Không cần duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
    cancelled: 'Đã hủy',
  }
  return labels[value] || '-'
}

function statusClass(item) {
  const resolvedUnit = Number(item?.work_unit)

  if (hasMissingCheck(item)) {
    return 'bg-yellow-50 text-yellow-700'
  }

  if (item?.approval_status === 'rejected') {
    return 'bg-rose-50 text-rose-700'
  }

  if (resolvedUnit === 1) {
    return 'bg-emerald-50 text-emerald-700'
  }

  if (resolvedUnit === 0.5) {
    return 'bg-blue-50 text-blue-700'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'bg-orange-50 text-orange-700'
  }

  return item?.attendance_status === 'late'
    ? 'bg-amber-50 text-amber-700'
    : item?.attendance_status === 'absent'
      ? 'bg-rose-50 text-rose-700'
      : 'bg-emerald-50 text-emerald-700'
}

function dayStatusClass(value) {
  const classes = {
    present: 'bg-emerald-50 text-emerald-700',
    late: 'bg-amber-50 text-amber-700',
    early_leave: 'bg-orange-50 text-orange-700',
    leave: 'bg-sky-50 text-sky-700',
    unpaid_leave: 'bg-rose-50 text-rose-700',
    holiday_paid: 'bg-cyan-50 text-cyan-700',
    day_off: 'bg-slate-50 text-slate-600',
    business_trip: 'bg-violet-50 text-violet-700',
    missing_check_in: 'bg-yellow-50 text-yellow-700',
    missing_check_out: 'bg-yellow-50 text-yellow-700',
    absent: 'bg-slate-100 text-slate-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function approvalStatusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    needs_verification: 'bg-orange-50 text-orange-700',
    not_required: 'bg-slate-50 text-slate-600',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
    cancelled: 'bg-slate-100 text-slate-600',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function requestPresenceClass(item) {
  if (item?.request_status === 'approved') return 'bg-emerald-50 text-emerald-700'
  if (item?.request_status === 'pending') return 'bg-amber-50 text-amber-700'
  if (item?.request_status === 'rejected') return 'bg-rose-50 text-rose-700'
  if (item?.request_status === 'cancelled') return 'bg-slate-100 text-slate-600'
  return 'bg-slate-50 text-slate-700'
}

function attendanceRowClass(item) {
  const displayStatus = item?.display_approval_status || item?.approval_status

  if (displayStatus === 'needs_verification') {
    return 'bg-orange-50/70 hover:bg-orange-50'
  }

  if (displayStatus === 'pending') {
    return 'bg-amber-50/60 hover:bg-amber-50'
  }

  if (item?.request_status === 'pending') {
    return 'bg-blue-50/50 hover:bg-blue-50'
  }

  return ''
}

function requestRowClass(item) {
  if (item?.status === 'pending') {
    return 'bg-amber-50/60 hover:bg-amber-50'
  }

  return ''
}
</script>
