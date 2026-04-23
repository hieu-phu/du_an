<template>
  <Head title="Cong cua toi" />

  <AdminLayout>
    <PageBreadcrumb title="Cong cua toi" :items="[{ text: 'Cham cong', link: null }, { text: 'Cong cua toi', link: null }]" />

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
          <div class="text-sm font-semibold text-gray-900">Bo loc ky cham cong</div>
          <div class="mt-1 text-sm text-gray-500">Chuyen nhanh qua tung thang hoac chon thang, nam cu the de xem du lieu.</div>
        </div>
        <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
          Dang xem: {{ currentPeriodLabel }}
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_auto]">
        <div class="max-w-xl">
          <InputDate
            v-model="filterForm.period"
            label="Chon thang nam"
            placeholder="Chon thang nam"
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
            Thang nay
          </button>
        </div>
      </div>
    </div>

    <div v-if="!isAttendancePeriodClosed" class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Goi y xu ly nhanh</h3>
          <p class="mt-1 text-sm text-gray-500">He thong tu nhan dien ngay can xu ly va dien san form cho ban.</p>
        </div>
        <div class="text-xs font-medium text-gray-500">Ban cung co the bam "Tao don" ngay trong bang cong ben duoi.</div>
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
            Tao don tu dong
          </button>
        </div>
      </div>

      <div v-else class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        Khong co ngay nao can xu ly gap trong ky hien tai.
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Gui don lien quan cham cong</h3>
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
            Bo goi y va tu nhap lai
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Loai don</label>
          <select v-model="requestForm.request_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chon loai don</option>
            <option v-for="type in request_types" :key="type.value" :value="type.value">{{ requestTypeLabel(type.value) }}</option>
          </select>
          <p class="mt-1 text-xs text-gray-500">Chon "Xin di muon / ve som" neu can giai trinh vi pham gio cong.</p>
          <p v-if="requestForm.errors.request_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.request_type }}</p>
        </div>

        <InputDate
          v-if="showSingleDate"
          v-model="requestForm.request_date"
          :label="singleDateLabel"
          placeholder="Chon ngay"
          :error="requestForm.errors.request_date"
          :config="singleDatePickerConfig"
        >
          <template v-if="singleDateHint" #helper>{{ singleDateHint }}</template>
        </InputDate>

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.from_date"
          :label="fromDateLabel"
          placeholder="Chon tu ngay"
          :error="requestForm.errors.from_date"
          :config="datePickerConfig"
        >
          <template v-if="dateRangeHint" #helper>{{ dateRangeHint }}</template>
        </InputDate>

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.to_date"
          :label="toDateLabel"
          placeholder="Chon den ngay"
          :error="requestForm.errors.to_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Loai nghi</label>
          <select v-model="requestForm.leave_type_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chon loai nghi</option>
            <option v-for="type in leave_types" :key="type.id" :value="type.id">
              {{ type.name }} - {{ type.is_paid ? 'co luong' : 'khong luong' }}
            </option>
          </select>
          <p v-if="selectedLeaveType" class="mt-1 text-xs text-gray-500">
            {{ selectedLeaveType.deducts_balance ? `Con lai: ${formatWorkUnits(selectedLeaveAvailableDays)} ngay` : 'Loai nghi nay khong tru quy phep.' }}
          </p>
          <p v-if="requestForm.errors.leave_type_id || requestForm.errors.leave_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_type_id || requestForm.errors.leave_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Thoi luong nghi</label>
          <select v-model="requestForm.leave_duration_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="full_day">Ca ngay</option>
            <option value="half_day">Nua ngay</option>
            <option value="hourly">Theo gio</option>
          </select>
          <p v-if="requestForm.errors.leave_duration_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_duration_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave' && requestForm.leave_duration_type === 'hourly'">
          <label class="mb-2 block text-sm font-medium text-gray-700">So gio nghi</label>
          <input v-model.number="requestForm.leave_hours" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="number" min="0.5" max="24" step="0.5">
          <p v-if="requestForm.errors.leave_hours" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_hours }}</p>
        </div>

        <div v-if="showLeaveAttachmentField">
          <label class="mb-2 block text-sm font-medium text-gray-700">Minh chung</label>
          <input ref="attachmentInput" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" @change="setAttachment">
          <p v-if="selectedLeaveType?.requires_attachment" class="mt-1 text-xs text-amber-700">Loai nghi nay bat buoc co minh chung.</p>
          <p v-if="requestForm.errors.attachment" class="mt-1 text-sm text-red-500">{{ requestForm.errors.attachment }}</p>
        </div>

        <div v-if="requestForm.request_type === 'late_early'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Vi pham can giai trinh</label>
          <select v-model="requestForm.requested_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="late">Di muon</option>
            <option value="early_leave">Ve som</option>
          </select>
          <p v-if="selectedSmartContext?.requestType === 'late_early'" class="mt-1 text-xs text-gray-500">
            Da dien theo vi pham chinh, ban co the doi neu can.
          </p>
          <p v-if="requestForm.errors.requested_status" class="mt-1 text-sm text-red-500">{{ requestForm.errors.requested_status }}</p>
        </div>

        <div v-if="requestForm.request_type === 'business_trip'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Dia diem cong tac</label>
          <input
            v-model="requestForm.business_trip_location"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
            type="text"
            placeholder="Nhap dia diem cong tac"
          >
          <p v-if="requestForm.errors.business_trip_location" class="mt-1 text-sm text-red-500">{{ requestForm.errors.business_trip_location }}</p>
        </div>

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.from_time"
          :label="fromTimeLabel"
          placeholder="Chon gio"
          :error="requestForm.errors.from_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.to_time"
          :label="toTimeLabel"
          placeholder="Chon gio"
          :error="requestForm.errors.to_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="requestForm.request_type === 'make_up'"
          v-model="requestForm.make_up_related_leave_date"
          label="Ngay nghi can bu"
          placeholder="Chon ngay nghi can bu"
          :error="requestForm.errors.make_up_related_leave_date"
          :config="makeUpRelatedDatePickerConfig"
        >
          <template #helper>
            Chi cho chon ngay co trang thai nghi phep, nghi khong luong hoac thieu cong. So gio lam bu khong duoc vuot phan cong thieu cua ngay nay.
          </template>
        </InputDate>

        <div v-if="requestForm.request_type === 'make_up' && selectedMakeUpQuota" class="md:col-span-2 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-900">
          <div class="font-semibold">So gio thieu cua ngay can bu</div>
          <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-3">
            <div>
              <span class="text-amber-700">Tong thieu:</span>
              <strong class="ml-1">{{ formatMinutes(selectedMakeUpQuota.missing_minutes) }}</strong>
            </div>
            <div>
              <span class="text-amber-700">Da duoc dang ky lam bu:</span>
              <strong class="ml-1">{{ formatMinutes(selectedMakeUpQuota.allocated_minutes) }}</strong>
            </div>
            <div>
              <span class="text-amber-700">Con lai:</span>
              <strong class="ml-1">{{ formatMinutes(selectedMakeUpQuota.remaining_minutes) }}</strong>
            </div>
          </div>
        </div>

        <div v-if="requestForm.request_type === 'make_up'" class="md:col-span-2 rounded-lg border border-amber-100 bg-amber-50 p-4 text-sm text-amber-900">
          <div class="font-semibold">Quy tac lam bu</div>
          <p class="mt-2">
            Lam bu dung de bu cong thieu cua mot ngay nghi da chon. Don nay khong duoc tinh la tang ca va khong phat sinh tien OT.
          </p>
          <p class="mt-1">
            He thong se chan neu so gio lam bu vuot qua phan cong thieu con lai cua ngay nghi can bu.
          </p>
        </div>

        <InputDate
          v-if="requestForm.request_type === 'overtime'"
          v-model="requestForm.request_date"
          label="Ngay tang ca"
          placeholder="Chon ngay"
          :error="requestForm.errors.request_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'overtime'" class="md:col-span-2 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
          <div class="font-semibold">Khung tang ca theo danh muc cham cong</div>
          <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-3">
            <div>
              <span class="text-blue-600">Ca ap dung:</span>
              <strong class="ml-1">{{ overtimeCatalog.shift_name || '-' }}</strong>
            </div>
            <div>
              <span class="text-blue-600">Thoi gian:</span>
              <strong class="ml-1">{{ overtimeWindowLabel }}</strong>
            </div>
            <div>
              <span class="text-blue-600">So gio tang ca:</span>
              <strong class="ml-1">{{ formatMinutes(overtimeCatalog.requested_minutes) }}</strong>
            </div>
          </div>
          <p class="mt-2">
            Tang ca la thoi gian lam viec ngoai khung hanh chinh duoc cau hinh OT. Tang ca khong dung de bu cho ngay nghi thieu cong.
          </p>
          <p v-if="!overtimeCatalog.start_time || !overtimeCatalog.end_time" class="mt-2 text-red-600">
            Ca lam hien tai chua cau hinh khung tang ca. Vui long lien he HR cap nhat danh muc cham cong.
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
          {{ requestForm.processing ? 'Dang gui...' : 'Gui don' }}
        </button>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bang cong ca nhan</h3>
      </div>

      <DataTable
        :columns="columns"
        :data="records"
        :actions="recordActions"
        :row-class="attendanceRowClass"
        paginate
        :default-per-page="10"
        empty-message="Chua co du lieu cham cong."
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
            <span class="truncate">{{ item.request_presence_label || 'Xem don' }}</span>
          </button>
          <span v-else class="inline-flex max-w-[170px] rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
            <span class="truncate">Khong co don</span>
          </span>
        </template>
      </DataTable>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Don cham cong da gui</h3>
        <p class="mt-1 text-sm text-gray-500">Theo doi cac don ban da gui va trang thai duyet hien tai.</p>
      </div>

      <DataTable
        :columns="requestColumns"
        :data="recent_requests"
        :actions="requestActions"
        :row-class="requestRowClass"
        paginate
        :default-per-page="5"
        :per-page-options="[5, 10, 20]"
        empty-message="Ban chua gui don cham cong nao."
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
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Chi tiet don da gui</h3>
        <div class="grid grid-cols-1 gap-3 text-sm text-gray-700 md:grid-cols-2">
          <div><span class="font-medium text-gray-900">Loai don:</span> {{ selectedSubmittedRequest.request_type_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Trang thai:</span> {{ selectedSubmittedRequest.status_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Ngay ap dung:</span> {{ formatDate(selectedSubmittedRequest.request_date) }}</div>
          <div><span class="font-medium text-gray-900">Khoang thoi gian:</span> {{ selectedSubmittedRequest.period || '-' }}</div>
          <div><span class="font-medium text-gray-900">Gui luc:</span> {{ formatDateTime(selectedSubmittedRequest.submitted_at) }}</div>
          <div><span class="font-medium text-gray-900">Nguoi duyet:</span> {{ selectedSubmittedRequest.reviewed_by_name || '-' }}</div>
          <div><span class="font-medium text-gray-900">Duyet luc:</span> {{ formatDateTime(selectedSubmittedRequest.reviewed_at) }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Ly do:</span> {{ selectedSubmittedRequest.reason || '-' }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Ghi chu duyet:</span> {{ selectedSubmittedRequest.review_note || '-' }}</div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'attendance'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thong tin don nghi/cham cong</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Tu ngay:</span> {{ formatDate(selectedSubmittedRequest.from_date) }}</div>
            <div><span class="font-medium text-gray-900">Den ngay:</span> {{ formatDate(selectedSubmittedRequest.to_date) }}</div>
            <div><span class="font-medium text-gray-900">Tu gio:</span> {{ selectedSubmittedRequest.from_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Den gio:</span> {{ selectedSubmittedRequest.to_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Loai nghi:</span> {{ selectedSubmittedRequest.leave_type_name || selectedSubmittedRequest.leave_type || '-' }}</div>
            <div><span class="font-medium text-gray-900">Thoi luong:</span> {{ leaveDurationLabel(selectedSubmittedRequest) }}</div>
            <div v-if="selectedSubmittedRequest.business_trip_location">
              <span class="font-medium text-gray-900">Dia diem cong tac:</span> {{ selectedSubmittedRequest.business_trip_location }}
            </div>
            <div v-if="selectedSubmittedRequest.make_up_related_leave_date">
              <span class="font-medium text-gray-900">Ngay nghi can bu:</span> {{ formatDate(selectedSubmittedRequest.make_up_related_leave_date) }}
            </div>
            <div v-if="selectedSubmittedRequest.leave_days !== null && selectedSubmittedRequest.leave_days !== undefined">
              <span class="font-medium text-gray-900">So ngay nghi:</span> {{ formatWorkUnits(selectedSubmittedRequest.leave_days) }} ngay
            </div>
            <div v-if="selectedSubmittedRequest.requested_status">
              <span class="font-medium text-gray-900">Trang thai de nghi:</span> {{ selectedSubmittedRequest.requested_status }}
            </div>
          </div>

          <div v-if="selectedSubmittedRequest.request_type === 'leave'" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
            <div class="font-medium text-amber-900">Minh chung</div>
            <div v-if="selectedSubmittedRequest.attachment_url" class="mt-1">
              <a
                :href="selectedSubmittedRequest.attachment_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-blue-700 underline underline-offset-2"
              >
                Xem tep dinh kem
              </a>
            </div>
            <div v-else-if="selectedSubmittedRequest.leave_type_requires_attachment" class="mt-1 text-sm text-rose-700">
              Loai nghi nay yeu cau minh chung nhung don hien khong co tep dinh kem.
            </div>
            <div v-else class="mt-1 text-sm text-gray-500">
              Loai nghi nay khong yeu cau minh chung.
            </div>
          </div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'overtime'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thong tin tang ca</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Bat dau:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_start_at) }}</div>
            <div><span class="font-medium text-gray-900">Ket thuc:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_end_at) }}</div>
            <div><span class="font-medium text-gray-900">Phut de nghi:</span> {{ formatMinutes(selectedSubmittedRequest.requested_minutes) }}</div>
            <div><span class="font-medium text-gray-900">Phut duyet:</span> {{ formatMinutes(selectedSubmittedRequest.approved_minutes) }}</div>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button
            v-if="canCancelSubmittedRequest(selectedSubmittedRequest)"
            type="button"
            class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 disabled:opacity-60"
            @click="cancelSubmittedRequest(selectedSubmittedRequest)"
          >
            Huy don
          </button>
          <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700" @click="closeSubmittedRequestDetail">
            Dong
          </button>
        </div>
      </div>
    </Modal>
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
  { label: 'Ngay cong', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'Gio lam', key: 'worked_minutes', align: 'text-center' },
  { label: 'Di muon', key: 'late_minutes', align: 'text-center' },
  { label: 'Ve som', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Tang ca', key: 'overtime_minutes', align: 'text-center' },
  { label: 'Ket qua cong', key: 'attendance_status', align: 'text-center' },
  { label: 'Trang thai ngay', key: 'day_status', align: 'text-center' },
  { label: 'Duyet', key: 'approval_status', align: 'text-center' },
  { label: 'Don lien quan', key: 'request_presence_label', align: 'text-center' },
]

const requestColumns = [
  { label: 'Loai don', key: 'request_type_label' },
  { label: 'Ngay ap dung', key: 'request_date' },
  { label: 'Khoang thoi gian', key: 'period' },
  { label: 'Trang thai', key: 'status_label', align: 'text-center' },
  { label: 'Nguoi duyet', key: 'reviewed_by_name' },
  { label: 'Ghi chu duyet', key: 'review_note' },
  { label: 'Gui luc', key: 'submitted_at' },
]

const requestActions = [
  {
    label: 'Chi tiet',
    buttonProps: {
      title: 'Xem chi tiet don da gui',
      class: 'border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800',
    },
    onClick: (item) => openSubmittedRequestDetail(item),
  },
  {
    label: 'Huy',
    buttonProps: {
      title: 'Huy don dang cho duyet',
      class: 'border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 shadow-sm hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800',
    },
    hidden: (item) => !canCancelSubmittedRequest(item),
    onClick: (item) => cancelSubmittedRequest(item),
  },
]

const recordActions = [
  {
    label: 'Tao don',
    buttonProps: {
      title: 'Tao don tu dong cho dong cong nay',
      class: 'border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800',
    },
    hidden: (item) => !suggestedRequestTypeForRecord(item),
    onClick: (item) => applySmartRequest(item, suggestedRequestTypeForRecord(item)),
  },
]

const summaryCards = computed(() => [
  { label: 'Ngay cong hop le', value: formatWorkUnits(props.summary.approved_work_units ?? props.summary.total_work_units ?? 0) },
  {
    label: 'Cong can bo sung / xac minh',
    value: props.summary.action_required_records ?? props.summary.pending_records ?? 0,
    hint: verificationHint.value,
  },
  { label: 'Don cua toi cho duyet', value: props.summary.pending_request_records ?? 0 },
  { label: 'Gio lam thuc te da duyet', value: formatMinutes(props.summary.approved_actual_worked_minutes ?? props.summary.approved_worked_minutes ?? 0) },
])

const verificationHint = computed(() => {
  const missingCheck = props.summary.needs_verification_missing_check_records ?? props.summary.missing_check_records ?? 0
  const missingAttendance = props.summary.needs_verification_missing_attendance_records ?? props.summary.missing_attendance_records ?? 0
  const timeViolation = props.summary.needs_verification_time_violation_records ?? 0

  return `Thieu check: ${missingCheck}, vang: ${missingAttendance}, gio cong: ${timeViolation}`
})

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Thang ${filterForm.month} / ${filterForm.year}`

  return new Intl.DateTimeFormat('vi-VN', {
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const formError = computed(() => requestForm.errors.error || page.props.errors?.error || '')
const overtimeCatalog = computed(() => props.overtime_catalog || {})
const isAttendancePeriodClosed = computed(() => Boolean(props.month_lock?.is_locked || props.payroll_period?.is_locked))
const selectedLeaveType = computed(() => (props.leave_types || []).find((type) => Number(type.id) === Number(requestForm.leave_type_id)) || null)
const selectedLeaveBalance = computed(() => (props.leave_balances || []).find((balance) => Number(balance.leave_type_id) === Number(requestForm.leave_type_id)) || null)
const selectedLeaveAvailableDays = computed(() => selectedLeaveBalance.value?.available_days ?? selectedLeaveType.value?.annual_quota ?? 0)
const showLeaveAttachmentField = computed(() => requestForm.request_type === 'leave' && Boolean(selectedLeaveType.value?.requires_attachment))
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
  if (!overtimeCatalog.value.start_time || !overtimeCatalog.value.end_time) return 'Chua cau hinh'
  return `${overtimeCatalog.value.start_time} - ${overtimeCatalog.value.end_time}`
})
const showSingleDate = computed(() => ['forgot_check', 'late_early', 'make_up'].includes(requestForm.request_type))
const showDateRange = computed(() => ['leave', 'business_trip'].includes(requestForm.request_type))
const showTimeRange = computed(() => ['forgot_check', 'late_early', 'business_trip', 'make_up'].includes(requestForm.request_type))
const singleDateLabel = computed(() => {
  if (requestForm.request_type === 'forgot_check') return 'Ngay quen cham cong'
  if (requestForm.request_type === 'late_early') return 'Ngay vi pham'
  if (requestForm.request_type === 'make_up') return 'Ngay lam bu'
  return 'Ngay ap dung'
})
const fromDateLabel = computed(() => requestForm.request_type === 'business_trip' ? 'Ngay bat dau cong tac' : 'Ngay bat dau nghi')
const toDateLabel = computed(() => requestForm.request_type === 'business_trip' ? 'Ngay ket thuc cong tac' : 'Ngay ket thuc nghi')
const fromTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Bat dau lam bu'
  if (requestForm.request_type === 'forgot_check') return 'Gio check-in neu quen'
  if (requestForm.request_type === 'business_trip') return 'Bat dau cong tac'
  if (requestForm.request_type === 'late_early') return requestForm.requested_status === 'early_leave' ? 'Bat dau ve som' : 'Bat dau di muon'
  return 'Tu gio'
})
const toTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Ket thuc lam bu'
  if (requestForm.request_type === 'forgot_check') return 'Gio check-out neu quen'
  if (requestForm.request_type === 'business_trip') return 'Ket thuc cong tac'
  if (requestForm.request_type === 'late_early') return requestForm.requested_status === 'early_leave' ? 'Ket thuc ve som' : 'Ket thuc di muon'
  return 'Den gio'
})
const singleDateHint = computed(() => {
  if (requestForm.request_type === 'forgot_check') return 'Chi ap dung cho ngay da xay ra'
  return ''
})
const dateRangeHint = computed(() => {
  if (requestForm.request_type === 'leave') return 'Co the dang ky cho hom nay hoac ngay toi'
  return ''
})
const reasonLabel = computed(() => {
  if (requestForm.request_type === 'business_trip') return 'Noi dung cong tac'
  if (requestForm.request_type === 'overtime') return 'Ly do tang ca'
  return 'Ly do'
})
const reasonPlaceholder = computed(() => {
  if (requestForm.request_type === 'business_trip') return 'Nhap noi dung cong tac'
  if (requestForm.request_type === 'overtime') return 'Nhap ly do dang ky tang ca'
  return 'Nhap ly do chi tiet'
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
  leave: 'Xin nghi phep',
  late_early: 'Xin di muon / ve som',
  forgot_check: 'Xin quen cham cong',
  business_trip: 'Xin cong tac',
  make_up: 'Xin lam bu',
  overtime: 'Dang ky tang ca',
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

function cancelSubmittedRequest(item) {
  if (!canCancelSubmittedRequest(item)) return

  const confirmed = window.confirm('Huy don dang cho duyet nay?')
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

  return parts.join(' - ') || 'Xem don lien quan'
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
  if (minutes <= 0) return '0 phut'
  const hours = Math.floor(minutes / 60)
  const remainMinutes = minutes % 60
  if (hours <= 0) return `${remainMinutes} phut`
  if (remainMinutes === 0) return `${hours} gio`
  return `${hours} gio ${remainMinutes} phut`
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
    return hasMissingCheck(item) ? 'Ngay nay dang thieu check, nen tao don quen cham cong' : 'Tao don quen cham cong'
  }

  if (requestType === 'late_early') {
    return item?.violation_status === 'early_leave' ? 'Ngay nay bi ve som, nen tao don giai trinh' : 'Ngay nay co vi pham gio, nen tao don giai trinh'
  }

  if (requestType === 'make_up') {
    return 'Ngay nay dang thieu cong, co the dung lam bu'
  }

  return 'Co goi y de xu ly nhanh'
}

function smartRecommendationDescription(item, requestType) {
  if (requestType === 'forgot_check') {
    return item?.missing_check_in
      ? `Thieu check-in ngay ${formatDate(item?.work_date)}. Form se dien ngay vi pham va gio vao ca du kien.`
      : `Thieu check-out ngay ${formatDate(item?.work_date)}. Form se dien ngay vi pham va gio ra ca du kien.`
  }

  if (requestType === 'late_early') {
    if (item?.violation_status === 'early_leave') {
      return `Ve som ${formatMinutes(item?.early_leave_minutes)} ngay ${formatDate(item?.work_date)}. Form se dien san ngay va khung gio can giai trinh.`
    }

    if (item?.violation_status === 'late_early') {
      return `Co ca di muon ${formatMinutes(item?.late_minutes)} va ve som ${formatMinutes(item?.early_leave_minutes)} ngay ${formatDate(item?.work_date)}. Form se chon vi pham chinh truoc.`
    }

    return `Di muon ${formatMinutes(item?.late_minutes)} ngay ${formatDate(item?.work_date)}. Form se dien san ngay va khung gio can giai trinh.`
  }

  if (requestType === 'make_up') {
    const quota = makeUpQuotaMap.value[item?.work_date]
    const remainingLabel = quota ? formatMinutes(quota.remaining_minutes) : null
    return `Ngay nay moi dat ${formatWorkUnits(item?.work_unit)} cong.${remainingLabel ? ` Con thieu ${remainingLabel}.` : ''} Form se gan san ngay can bu de ban dang ky lam bu nhanh.`
  }

  return 'He thong de xuat don phu hop nhat voi ngay cong nay.'
}

function smartRecommendationDetails(item, requestType) {
  const details = [
    `Ngay: ${formatDate(item?.work_date)}`,
  ]

  if (item?.shift_name) {
    details.push(`Ca: ${item.shift_name}`)
  }

  if (item?.shift_start_time || item?.shift_end_time) {
    details.push(`Gio ca: ${[item?.shift_start_time, item?.shift_end_time].filter(Boolean).join(' - ')}`)
  }

  if (requestType === 'forgot_check') {
    details.push(item?.missing_check_in ? 'Thieu check-in' : 'Thieu check-out')
    const suggestedTime = item?.missing_check_in ? suggestedCheckInTime(item) : suggestedCheckOutTime(item)
    if (suggestedTime) details.push(`Gio de xuat: ${suggestedTime}`)
  }

  if (requestType === 'late_early') {
    const status = preferredLateEarlyStatus(item)
    const range = suggestedLateEarlyRange(item, status)

    if (Number(item?.late_minutes || 0) > 0) {
      details.push(`Di muon: ${formatMinutes(item.late_minutes)}`)
    }

    if (Number(item?.early_leave_minutes || 0) > 0) {
      details.push(`Ve som: ${formatMinutes(item.early_leave_minutes)}`)
    }

    if (range.from && range.to) {
      details.push(`Khung gio: ${range.from} - ${range.to}`)
    }
  }

  if (requestType === 'make_up') {
    const quota = makeUpQuotaMap.value[item?.work_date]
    if (quota) {
      details.push(`Con thieu: ${formatMinutes(quota.remaining_minutes)}`)
    }
  }

  return details
}

function buildDefaultReason(item, requestType) {
  const dateText = formatDate(item?.work_date)

  if (requestType === 'forgot_check') {
    return item?.missing_check_in
      ? `Giai trinh bo sung check-in ngay ${dateText}.`
      : `Giai trinh bo sung check-out ngay ${dateText}.`
  }

  if (requestType === 'late_early') {
    if (preferredLateEarlyStatus(item) === 'early_leave') {
      return `Giai trinh ve som ngay ${dateText}, ve som ${formatMinutes(item?.early_leave_minutes)}.`
    }

    return `Giai trinh di muon ngay ${dateText}, di muon ${formatMinutes(item?.late_minutes)}.`
  }

  if (requestType === 'make_up') {
    return `Dang ky lam bu cho ngay ${dateText}.`
  }

  if (requestType === 'overtime') {
    return `Dang ky tang ca cho ngay ${dateText}.`
  }

  if (requestType === 'leave') {
    return `Dang ky nghi cho ngay ${dateText}.`
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
  if (item.leave_duration_type === 'full_day') return 'Ca ngay'
  if (item.leave_duration_type === 'half_day') return 'Nua ngay'
  if (item.leave_duration_type === 'hourly') {
    return `Theo gio${item.leave_hours ? ` (${formatWorkUnits(item.leave_hours)} gio)` : ''}`
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
    return 'Nghi theo phan ca'
  }

  if (item?.day_status === 'holiday_paid') {
    return 'Le co luong'
  }

  if (hasMissingCheck(item)) {
    return 'Chua tinh cong'
  }

  if (item?.approval_status === 'rejected') {
    return 'Khong duyet cong'
  }

  if (resolvedUnit === 1) {
    return 'Du cong'
  }

  if (resolvedUnit === 0.5) {
    return 'Nua cong'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'Khong cong'
  }

  const labels = {
    on_time: 'Dung gio',
    late: 'Tre',
    absent: 'Vang',
  }
  return labels[item?.attendance_status] || '-'
}

function formatDayStatus(item) {
  const value = typeof item === 'string' ? item : item?.day_status

  if (value === 'unpaid_leave' && item?.violation_status === 'missing_attendance') {
    return 'Vang mat'
  }

  const labels = {
    present: 'Di lam',
    late: 'Di muon',
    early_leave: 'Ve som',
    leave: 'Nghi phep',
    unpaid_leave: 'Nghi khong luong',
    holiday_paid: 'Le co luong',
    day_off: 'Nghi theo phan ca',
    business_trip: 'Cong tac',
    missing_check_in: 'Thieu check in',
    missing_check_out: 'Thieu check out',
    absent: 'Vang mat',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  if (value === 'needs_verification') {
    return 'Can xac minh'
  }

  const labels = {
    pending: 'Cho duyet',
    not_required: 'Khong can duyet',
    approved: 'Da duyet',
    rejected: 'Tu choi',
    cancelled: 'Da huy',
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
