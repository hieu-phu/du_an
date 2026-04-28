<template>
  <Head title="Danh mục chấm công" />

  <AdminLayout>
    <PageBreadcrumb title="Danh mục chấm công" :items="[{ text: 'Chấm công', link: null }, { text: 'Danh mục', link: null }]" />

    <div class="space-y-6">
      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Ca làm việc</h3>
          <p class="text-sm text-gray-500">Khai báo giờ vào, giờ ra, nghỉ giữa ca, phút chuẩn, ngưỡng nửa công và quy tắc tính muộn sớm/tăng ca.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitShift">
          <div v-if="editingShiftId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Đang chỉnh sửa ca làm <strong>{{ shiftForm.shift_name || `#${editingShiftId}` }}</strong>.
          </div>

          <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
            <div class="mb-4">
              <h4 class="text-sm font-semibold text-gray-900">Cấu hình ca làm việc</h4>
              <p class="mt-1 text-sm text-gray-500">Phần này dùng để khai báo giờ hành chính, nghỉ giữa ca và quy tắc tính công.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Tên ca" :error="shiftForm.errors.shift_name">
                <input v-model.trim="shiftForm.shift_name" class="form-input" placeholder="Ca hành chính">
              </Field>
              <InputDate v-model="shiftForm.start_time" label="Giờ bắt đầu" placeholder="Chọn giờ" :config="timePickerConfig" :error="shiftForm.errors.start_time" />
              <InputDate v-model="shiftForm.end_time" label="Giờ kết thúc" placeholder="Chọn giờ" :config="timePickerConfig" :error="shiftForm.errors.end_time" />
              <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
                Mã ca được tạo tự động khi lưu.
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <InputDate v-model="shiftForm.break_start_time" label="Bắt đầu nghỉ giữa ca" placeholder="Không nghỉ" :config="timePickerConfig" :error="shiftForm.errors.break_start_time" />
              <InputDate v-model="shiftForm.break_end_time" label="Kết thúc nghỉ giữa ca" placeholder="Không nghỉ" :config="timePickerConfig" :error="shiftForm.errors.break_end_time" />
              <Field label="Phút chuẩn" :error="shiftForm.errors.standard_minutes">
                <input v-model.number="shiftForm.standard_minutes" class="form-input bg-gray-100 text-gray-700" type="number" min="1" readonly>
                <p class="text-xs text-gray-500">Tự tính bằng thời lượng ca trừ nghỉ giữa ca.</p>
              </Field>
              <Field label="Ngưỡng nửa công" :error="shiftForm.errors.half_day_minutes">
                <input v-model.number="shiftForm.half_day_minutes" class="form-input bg-gray-100 text-gray-700" type="number" min="1" readonly>
                <p class="text-xs text-gray-500">Tự tính bằng 50% phút chuẩn.</p>
              </Field>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Dung sai chung" :error="shiftForm.errors.grace_minutes">
                <input v-model.number="shiftForm.grace_minutes" class="form-input" type="number" min="0" max="180">
              </Field>
              <Field label="Nghỉ giao ca (phút)" :error="shiftForm.errors.handover_break_minutes">
                <input v-model.number="shiftForm.handover_break_minutes" class="form-input" type="number" min="0" max="240">
              </Field>
              <div class="md:col-span-2 rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
                Số phút này dùng để ghi nhận quy tắc giao ca, không bị trừ khỏi phút chuẩn và thời gian tính công.
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <div class="grid grid-cols-2 gap-3 md:col-start-4">
                <ToggleBox v-model="shiftForm.allows_overtime" label="Tính tăng ca" />
                <ToggleBox v-model="shiftForm.is_overnight" label="Ca qua đêm" />
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-4">
            <div class="mb-4">
              <h4 class="text-sm font-semibold text-blue-900">Quy tắc tăng ca</h4>
              <p class="mt-1 text-sm text-blue-800">Phần này là cấu hình riêng cho tăng ca của ca làm. Có thể bỏ trống nếu ca không sử dụng OT có quy tắc riêng.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <InputDate v-model="shiftForm.overtime_start_time" label="Giờ bắt đầu tăng ca" placeholder="Không cấu hình" :config="timePickerConfig" :error="shiftForm.errors.overtime_start_time" />
              <InputDate v-model="shiftForm.overtime_end_time" label="Giờ kết thúc tăng ca" placeholder="Không cấu hình" :config="timePickerConfig" :error="shiftForm.errors.overtime_end_time" />
              <Field label="Tiền tăng ca / giờ" :error="shiftForm.errors.overtime_hourly_rate">
                <input v-model.number="shiftForm.overtime_hourly_rate" class="form-input" type="number" min="0" step="1000" placeholder="Ví dụ: 50000">
              </Field>
            </div>
          </div>

          <Field label="Ghi chú" :error="shiftForm.errors.description">
            <textarea v-model.trim="shiftForm.description" class="form-input min-h-[74px]" placeholder="Quy định riêng của ca làm nếu có"></textarea>
          </Field>

          <div class="rounded-lg border p-4 text-sm" :class="shiftPreviewError ? 'border-red-200 bg-red-50 text-red-700' : 'border-blue-100 bg-blue-50 text-blue-900'">
            <div class="font-semibold">Tóm tắt ca sau khi lưu</div>
            <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-4">
              <div>Tổng thời gian từ đầu ca đến cuối ca: <strong>{{ formatMinutes(shiftDurationMinutes) }}</strong></div>
              <div>Thời gian nghỉ giữa ca: <strong>{{ formatMinutes(breakMinutes) }}</strong></div>
              <div>Thời lượng ca sau khi trừ nghỉ: <strong>{{ formatMinutes(netShiftMinutes) }}</strong></div>
              <div>Thời gian tính công theo phút chuẩn: <strong>{{ formatMinutes(standardMinutes) }}</strong></div>
              <div>Nghỉ giao ca: <strong>{{ formatMinutes(Number(shiftForm.handover_break_minutes || 0)) }}</strong></div>
              <div>Khung tăng ca: <strong>{{ overtimePreviewLabel }}</strong></div>
              <div>Trạng thái kiểm tra dữ liệu: <strong>{{ shiftPreviewError || 'Hợp lệ để lưu' }}</strong></div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingShiftId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelShiftEdit"
            >
              Hủy sửa
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="shiftForm.processing || !!shiftPreviewError">
              {{ shiftForm.processing ? 'Đang lưu...' : (editingShiftId ? 'Lưu ca làm' : 'Thêm ca') }}
            </button>
          </div>
        </form>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Mã / Tên ca</th>
              <th class="p-2">Khung giờ</th>
              <th class="p-2">Nghỉ giữa ca</th>
              <th class="p-2">Ngưỡng công</th>
              <th class="p-2">Grace</th>
              <th class="p-2">Trạng thái</th>
              <th class="p-2">Tác vụ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in workShifts" :key="item.id" class="border-t">
              <td class="p-2">
                <div class="font-semibold text-gray-900">{{ item.shift_name }}</div>
              </td>
              <td class="p-2">{{ item.start_time }} - {{ item.end_time }}<span v-if="item.is_overnight"> (+1)</span></td>
              <td class="p-2">{{ item.break_start_time && item.break_end_time ? `${item.break_start_time} - ${item.break_end_time}` : 'Không có' }}</td>
              <td class="p-2">
                <div>{{ item.standard_minutes }}p / nửa công {{ item.half_day_minutes }}p</div>
                <div class="text-xs text-gray-500">Nghỉ giao ca {{ item.handover_break_minutes || 0 }}p</div>
              </td>
              <td class="p-2">
                <div>Dung sai chung {{ item.grace_minutes }}p</div>
                <div class="text-xs text-gray-500">
                  OT: {{ item.overtime_start_time && item.overtime_end_time ? `${item.overtime_start_time} - ${item.overtime_end_time}` : 'Không cấu hình' }}
                  <span v-if="item.overtime_hourly_rate"> | {{ formatMoney(item.overtime_hourly_rate) }}/giờ</span>
                </div>
              </td>
              <td class="p-2">
                <StatusBadge :active="item.is_active" />
              </td>
              <td class="p-2 space-x-2">
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="editShift(item)">Chỉnh sửa</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="startQuickAssign(item)">Gán nhân viên</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="toggleShift(item.id)">
                  {{ item.is_active ? 'Ngừng dùng' : 'Kích hoạt' }}
                </button>
              </td>
            </tr>
          </tbody>
        </TableShell>

        <div v-if="quickAssignShift" class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-base font-semibold text-blue-900">Gán nhân viên vào ca làm</h4>
              <p class="mt-1 text-sm text-blue-800">
                Đang chọn ca <strong>{{ quickAssignShift.shift_name }}</strong>. Bạn có thể tạo phân ca cho nhân viên ngay tại đây.
              </p>
            </div>
            <button class="rounded-lg border border-blue-300 bg-white px-3 py-1.5 text-sm font-medium text-blue-800" type="button" @click="cancelQuickAssign">
              Đóng
            </button>
          </div>

          <form class="mt-4 space-y-4" @submit.prevent="submitQuickAssignment">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Nhân viên" :error="assignmentForm.errors.employee_profile_id">
                <select v-model="assignmentForm.employee_profile_id" class="form-input">
                  <option :value="null">Chọn nhân viên</option>
                  <option v-for="item in employeeOptions" :key="item.id" :value="item.id">{{ item.label }}</option>
                </select>
              </Field>
              <Field label="Ca làm">
                <input class="form-input bg-gray-50" :value="quickAssignShift.shift_name" disabled>
              </Field>
              <InputDate v-model="assignmentForm.effective_from" label="Từ ngày" placeholder="Chọn ngày" :config="datePickerConfig" :error="assignmentForm.errors.effective_from" />
              <InputDate v-model="assignmentForm.effective_to" label="Đến ngày" placeholder="Bỏ trống nếu chưa kết thúc" :config="datePickerConfig" :error="assignmentForm.errors.effective_to" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_2fr]">
              <Field label="Ngày trong tuần" :error="assignmentForm.errors.weekdays">
                <div class="flex flex-wrap gap-2">
                  <label v-for="day in weekdays" :key="`quick-${day.value}`" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm">
                    <input v-model="assignmentForm.weekdays" type="checkbox" :value="day.value">
                    {{ day.label }}
                  </label>
                </div>
              </Field>
              <Field label="Ghi chú" :error="assignmentForm.errors.note">
                <input v-model.trim="assignmentForm.note" class="form-input" placeholder="Ghi chú phân ca nếu cần">
              </Field>
            </div>

            <div class="flex justify-end gap-3">
              <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700" type="button" @click="cancelQuickAssign">
                Hủy
              </button>
              <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="assignmentForm.processing">
                {{ assignmentForm.processing ? 'Đang lưu...' : 'Gán vào ca' }}
              </button>
            </div>
          </form>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Ngày lễ</h3>
          <p class="text-sm text-gray-500">Khai báo ngày nghỉ áp dụng toàn công ty, phân loại ngày nghỉ và xác định có tính lương hay lặp lại hàng năm.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitHoliday">
          <div v-if="editingHolidayId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Đang chỉnh sửa ngày lễ <strong>{{ holidayForm.holiday_name || `#${editingHolidayId}` }}</strong>.
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <InputDate v-model="holidayForm.holiday_date" label="Ngày lễ" placeholder="Chọn ngày" :config="datePickerConfig" :error="holidayForm.errors.holiday_date" />
            <Field label="Tên ngày lễ" :error="holidayForm.errors.holiday_name">
              <input v-model.trim="holidayForm.holiday_name" class="form-input" placeholder="Tên ngày lễ">
            </Field>
            <Field label="Loại ngày" :error="holidayForm.errors.holiday_type">
              <select v-model="holidayForm.holiday_type" class="form-input">
                <option value="public">Lễ nhà nước</option>
                <option value="company">Ngày nghỉ công ty</option>
                <option value="compensatory">Nghỉ bù</option>
                <option value="special">Đặc biệt</option>
              </select>
            </Field>
            <div class="grid grid-cols-2 gap-3">
              <ToggleBox v-model="holidayForm.is_paid_leave" label="Có lương" />
              <ToggleBox v-model="holidayForm.is_recurring" label="Lặp lại hàng năm" />
            </div>
          </div>

          <Field label="Ghi chú" :error="holidayForm.errors.note">
            <input v-model.trim="holidayForm.note" class="form-input" placeholder="Ghi chú nếu có">
          </Field>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingHolidayId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelHolidayEdit"
            >
              Hủy sửa
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="holidayForm.processing">
              {{ holidayForm.processing ? 'Đang lưu...' : (editingHolidayId ? 'Lưu ngày lễ' : 'Thêm ngày lễ') }}
            </button>
          </div>
        </form>

        <div class="mt-8 flex items-center justify-between border-b border-gray-200 pb-4">
          <h4 class="text-md font-semibold text-gray-900">Danh sách ngày lễ</h4>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Xem năm:</span>
            <select v-model="selectedHolidayYear" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm focus:border-blue-500 focus:outline-none">
              <option v-for="year in holidayYears" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
        </div>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Ngày</th>
              <th class="p-2">Tên</th>
              <th class="p-2">Loại</th>
              <th class="p-2">Tính lương</th>
              <th class="p-2">Lặp lại</th>
              <th class="p-2">Tác vụ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredHolidays" :key="item.id" class="border-t hover:bg-gray-50 transition-colors">
              <td class="p-2">{{ formatDate(item.holiday_date) }}</td>
              <td class="p-2 font-medium">
                <div class="flex items-center gap-2">
                  {{ item.holiday_name }}
                  <span v-if="item.is_system" class="inline-flex items-center rounded bg-purple-50 px-1.5 py-0.5 text-[10px] font-medium text-purple-600 border border-purple-100">
                    Hệ thống
                  </span>
                </div>
              </td>
              <td class="p-2">{{ holidayTypeLabel(item.holiday_type) }}</td>
              <td class="p-2">
                <span :class="item.is_paid_leave ? 'text-green-600' : 'text-orange-600'">
                  {{ item.is_paid_leave ? 'Có lương' : 'Không lương' }}
                </span>
              </td>
              <td class="p-2">
                <span v-if="item.is_system || item.is_recurring" class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">
                  Hàng năm
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="p-2 space-x-2">
                <template v-if="!item.is_system">
                  <button class="rounded border border-gray-300 bg-white px-3 py-1 text-sm transition-colors hover:bg-gray-50" type="button" @click="editHoliday(item)">Sửa</button>
                  <button class="rounded border border-red-200 bg-white px-3 py-1 text-sm text-red-600 transition-colors hover:bg-red-50" type="button" @click="deleteHoliday(item.id)">Xóa</button>
                </template>
                <span v-else class="text-xs text-gray-400 italic px-3">Cố định</span>
              </td>
            </tr>
            <tr v-if="filteredHolidays.length === 0">
              <td colspan="6" class="p-8 text-center text-gray-500">
                Không có ngày lễ nào trong năm {{ selectedHolidayYear }}
              </td>
            </tr>
          </tbody>
        </TableShell>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Phân ca</h3>
          <p class="text-sm text-gray-500">Gán ca cho cá nhân, phòng ban hoặc toàn công ty trong một khoảng thời gian cụ thể, có thể giới hạn theo ngày trong tuần.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitAssignment">
          <div v-if="editingAssignmentId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Đang chỉnh sửa phân ca <strong>#{{ editingAssignmentId }}</strong>.
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
            <Field label="Áp dụng cho" :error="assignmentForm.errors.target_type">
              <select v-model="assignmentForm.target_type" class="form-input">
                <option value="company">Toàn công ty</option>
                <option value="employee">Nhân viên</option>
                <option value="department">Phòng ban</option>
              </select>
            </Field>
            <Field v-if="assignmentForm.target_type === 'employee'" label="Nhân viên" :error="assignmentForm.errors.employee_profile_id">
              <select v-model="assignmentForm.employee_profile_id" class="form-input">
                <option :value="null">Chọn nhân viên</option>
                <option v-for="item in employeeOptions" :key="item.id" :value="item.id">{{ item.label }}</option>
              </select>
            </Field>
            <Field v-else-if="assignmentForm.target_type === 'department'" label="Phòng ban" :error="assignmentForm.errors.department_id">
              <select v-model="assignmentForm.department_id" class="form-input">
                <option :value="null">Chọn phòng ban</option>
                <option v-for="item in departmentOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
              </select>
            </Field>
            <Field v-else label="Phạm vi">
              <input class="form-input bg-gray-50" value="Toàn công ty" disabled>
            </Field>
            <Field label="Ca làm" :error="assignmentForm.errors.work_shift_id">
              <select v-model="assignmentForm.work_shift_id" class="form-input">
                <option :value="null">Chọn ca làm</option>
                <option v-for="item in activeWorkShifts" :key="item.id" :value="item.id">{{ item.shift_name }}</option>
              </select>
            </Field>
            <InputDate v-model="assignmentForm.effective_from" label="Từ ngày" placeholder="Chọn ngày" :config="datePickerConfig" :error="assignmentForm.errors.effective_from" />
            <InputDate v-model="assignmentForm.effective_to" label="Đến ngày" placeholder="Bỏ trống nếu chưa kết thúc" :config="datePickerConfig" :error="assignmentForm.errors.effective_to" />
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_2fr]">
            <Field label="Ngày trong tuần" :error="assignmentForm.errors.weekdays">
              <div class="flex flex-wrap gap-2">
                <label v-for="day in weekdays" :key="day.value" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm">
                  <input v-model="assignmentForm.weekdays" type="checkbox" :value="day.value">
                  {{ day.label }}
                </label>
              </div>
            </Field>
            <Field label="Ghi chú" :error="assignmentForm.errors.note">
              <input v-model.trim="assignmentForm.note" class="form-input" placeholder="Lý do/ghi chú phân ca">
            </Field>
          </div>

          <div v-if="assignmentForm.errors.employee_profile_id || assignmentForm.errors.department_id" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ assignmentForm.errors.employee_profile_id || assignmentForm.errors.department_id }}
          </div>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingAssignmentId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelAssignmentEdit"
            >
              Hủy sửa
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="assignmentForm.processing">
              {{ assignmentForm.processing ? 'Đang lưu...' : (editingAssignmentId ? 'Lưu phân ca' : 'Thêm phân ca') }}
            </button>
          </div>
        </form>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Đối tượng</th>
              <th class="p-2">Ca</th>
              <th class="p-2">Hiệu lực</th>
              <th class="p-2">Ngày áp dụng</th>
              <th class="p-2">Trạng thái</th>
              <th class="p-2">Tác vụ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in assignments" :key="item.id" class="border-t">
              <td class="p-2">
                <div class="font-semibold text-gray-900">{{ assignmentTargetName(item) }}</div>
                <div class="text-xs text-gray-500">{{ assignmentTargetLabel(item.target_type) }}</div>
              </td>
              <td class="p-2">{{ item.work_shift_name }}</td>
              <td class="p-2">{{ formatDate(item.effective_from) }} - {{ item.effective_to ? formatDate(item.effective_to) : 'Không giới hạn' }}</td>
              <td class="p-2">{{ weekdayLabels(item.weekdays) }}</td>
              <td class="p-2"><StatusBadge :active="item.is_active" /></td>
              <td class="p-2 space-x-2">
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="editAssignment(item)">Chỉnh sửa</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="toggleAssignment(item.id)">
                  {{ item.is_active ? 'Ngừng dùng' : 'Kích hoạt' }}
                </button>
              </td>
            </tr>
          </tbody>
        </TableShell>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
  workShifts: { type: Array, default: () => [] },
  holidays: { type: Array, default: () => [] },
  assignments: { type: Array, default: () => [] },
  employeeOptions: { type: Array, default: () => [] },
  departmentOptions: { type: Array, default: () => [] },
})

const currentYear = new Date().getFullYear()
const selectedHolidayYear = ref(currentYear)
const holidayYears = computed(() => {
  const years = new Set([currentYear, currentYear + 1])
  props.holidays.forEach(h => years.add(new Date(h.holiday_date).getFullYear()))
  // Ensure years from 2025 to 2030 are selectable for planning
  for (let y = 2025; y <= 2030; y++) years.add(y)
  return Array.from(years).sort((a, b) => b - a)
})

const fixedHolidaysConfig = [
  { monthDay: '01-01', name: 'Tết Dương lịch' },
  { monthDay: '04-30', name: 'Ngày Giải phóng Miền Nam' },
  { monthDay: '05-01', name: 'Ngày Quốc tế Lao động' },
  { monthDay: '09-02', name: 'Quốc khánh' },
  { monthDay: '09-03', name: 'Quốc khánh (Ngày 2)' },
]

const filteredHolidays = computed(() => {
  const year = selectedHolidayYear.value
  
  // 1. Get database holidays for this year
  const dbHolidays = props.holidays
    .filter(h => new Date(h.holiday_date).getFullYear() === year)
    .map(h => ({ ...h, is_system: false }))

  // 2. Generate fixed holidays for this year
  const generatedFixed = fixedHolidaysConfig.map(fh => {
    const holidayDate = `${year}-${fh.monthDay}`
    // Check if DB already has this date (to avoid duplicates/overrides)
    const exists = dbHolidays.some(dh => dh.holiday_date === holidayDate)
    if (exists) return null
    
    return {
      id: `sys-${fh.monthDay}`,
      holiday_date: holidayDate,
      holiday_name: fh.name,
      holiday_type: 'public',
      is_paid_leave: true,
      is_recurring: true,
      is_system: true,
      note: 'Ngày lễ cố định hệ thống'
    }
  }).filter(Boolean)

  // 3. Combine and sort
  return [...dbHolidays, ...generatedFixed].sort((a, b) => new Date(a.holiday_date) - new Date(b.holiday_date))
})

const Field = (props, { slots }) => h('div', { class: 'space-y-2' }, [
  h('label', { class: 'block text-sm font-medium text-gray-700' }, props.label),
  slots.default?.(),
  props.error ? h('p', { class: 'text-sm text-red-500' }, Array.isArray(props.error) ? props.error[0] : props.error) : null,
])
Field.props = ['label', 'error']

const ToggleBox = (props, { emit }) => h('label', { class: 'flex h-full items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700' }, [
  h('input', {
    checked: props.modelValue,
    type: 'checkbox',
    onChange: (event) => emit('update:modelValue', event.target.checked),
  }),
  props.label,
])
ToggleBox.props = ['modelValue', 'label']
ToggleBox.emits = ['update:modelValue']

const StatusBadge = (props) => h('span', {
  class: [
    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
    props.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600',
  ],
}, props.active ? 'Đang dùng' : 'Ngừng dùng')
StatusBadge.props = ['active']

const TableShell = (props, { slots }) => h('div', { class: ['overflow-auto', props.class] }, [
  h('table', { class: 'min-w-full text-sm' }, slots.default?.()),
])
TableShell.props = ['class']

const datePickerConfig = {
  dateFormat: 'Y-m-d',
  altFormat: 'd/m/Y',
  allowInput: false,
}

const timePickerConfig = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  altFormat: 'H:i',
  time_24hr: true,
  allowInput: false,
}

const weekdays = [
  { value: 1, label: 'T2' },
  { value: 2, label: 'T3' },
  { value: 3, label: 'T4' },
  { value: 4, label: 'T5' },
  { value: 5, label: 'T6' },
  { value: 6, label: 'T7' },
  { value: 7, label: 'CN' },
]

const editingShiftId = ref(null)
const editingHolidayId = ref(null)
const editingAssignmentId = ref(null)
const quickAssignShift = ref(null)

const shiftForm = useForm({
  shift_name: '',
  start_time: '08:00',
  end_time: '17:00',
  break_start_time: '12:00',
  break_end_time: '13:00',
  overtime_start_time: '',
  overtime_end_time: '',
  standard_minutes: 480,
  half_day_minutes: 240,
  handover_break_minutes: 0,
  overtime_hourly_rate: '',
  grace_minutes: 0,
  late_grace_minutes: 0,
  early_leave_grace_minutes: 0,
  allows_overtime: true,
  is_overnight: false,
  is_active: true,
  description: '',
})

const holidayForm = useForm({
  holiday_date: '',
  holiday_name: '',
  holiday_type: 'public',
  is_paid_leave: true,
  is_recurring: false,
  note: '',
})

const assignmentForm = useForm({
  target_type: 'company',
  employee_profile_id: null,
  department_id: null,
  work_shift_id: null,
  effective_from: '',
  effective_to: '',
  weekdays: [1, 2, 3, 4, 5],
  note: '',
  is_active: true,
})

const activeWorkShifts = computed(() => props.workShifts.filter((shift) => shift.is_active))

const shiftDurationMinutes = computed(() => timeRangeMinutes(shiftForm.start_time, shiftForm.end_time, shiftForm.is_overnight))
const breakMinutes = computed(() => {
  if (!shiftForm.break_start_time || !shiftForm.break_end_time) return 0
  return timeRangeMinutes(shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)
})
const netShiftMinutes = computed(() => Math.max(0, shiftDurationMinutes.value - breakMinutes.value))
const standardMinutes = computed(() => Number(shiftForm.standard_minutes || 0))
const overtimeGapMinutes = computed(() => {
  if (!shiftForm.overtime_start_time || !shiftForm.end_time) return 0
  const shiftStartMinutes = timeToMinutes(shiftForm.start_time)
  let shiftEndMinutes = timeToMinutes(shiftForm.end_time)
  let overtimeStartMinutes = timeToMinutes(shiftForm.overtime_start_time)

  if (shiftForm.is_overnight) {
    if (shiftEndMinutes <= shiftStartMinutes) shiftEndMinutes += 1440
    if (overtimeStartMinutes < shiftStartMinutes) overtimeStartMinutes += 1440
  }

  return overtimeStartMinutes - shiftEndMinutes
})
const overtimePreviewLabel = computed(() => {
  if (!shiftForm.overtime_start_time || !shiftForm.overtime_end_time) return 'Chưa cấu hình'
  return `${shiftForm.overtime_start_time} - ${shiftForm.overtime_end_time}`
})
const shiftPreviewError = computed(() => {
  if (shiftDurationMinutes.value <= 0) return 'Giờ kết thúc phải sau giờ bắt đầu'
  if ((shiftForm.break_start_time && !shiftForm.break_end_time) || (!shiftForm.break_start_time && shiftForm.break_end_time)) return 'Cần nhập đủ giờ nghỉ'
  if ((shiftForm.overtime_start_time && !shiftForm.overtime_end_time) || (!shiftForm.overtime_start_time && shiftForm.overtime_end_time)) return 'Cần nhập đủ giờ tăng ca'
  if (breakMinutes.value < 0 || breakMinutes.value >= shiftDurationMinutes.value || !rangeInsideShift(shiftForm.start_time, shiftForm.end_time, shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)) return 'Giờ nghỉ không hợp lệ'
  if (Number(shiftForm.handover_break_minutes || 0) < 0) return 'Nghỉ giao ca không hợp lệ'
  if (!shiftForm.allows_overtime && (shiftForm.overtime_start_time || shiftForm.overtime_end_time || shiftForm.overtime_hourly_rate)) return 'Đang tắt tính tăng ca nhưng vẫn còn cấu hình OT'
  if (standardMinutes.value !== netShiftMinutes.value) return `Phút chuẩn phải bằng ${netShiftMinutes.value} phút`
  if (Number(shiftForm.half_day_minutes) > Number(shiftForm.standard_minutes)) return 'Ngưỡng nửa công vượt phút chuẩn'
  if (shiftForm.overtime_start_time && shiftForm.overtime_end_time && overtimeGapMinutes.value > 0) return `Không được để khoảng hở ${shiftForm.end_time}-${shiftForm.overtime_start_time}`
  if (shiftForm.overtime_start_time && shiftForm.overtime_end_time && overtimeGapMinutes.value < 0) return 'Giờ bắt đầu tăng ca phải nối tiếp ngay sau giờ kết thúc ca'
  return ''
})

watch(() => assignmentForm.target_type, (type) => {
  if (type === 'employee') {
    assignmentForm.department_id = null
  } else if (type === 'department') {
    assignmentForm.employee_profile_id = null
  } else {
    assignmentForm.employee_profile_id = null
    assignmentForm.department_id = null
  }
})

watch(() => shiftForm.grace_minutes, (value) => {
  const normalized = Number(value || 0)
  shiftForm.late_grace_minutes = normalized
  shiftForm.early_leave_grace_minutes = normalized
})

watch(
  () => [
    shiftForm.start_time,
    shiftForm.end_time,
    shiftForm.break_start_time,
    shiftForm.break_end_time,
    shiftForm.is_overnight,
  ],
  () => syncShiftWorkThresholds(),
  { immediate: true }
)

function syncShiftWorkThresholds() {
  if (!canAutoCalculateShiftThresholds()) return

  const minutes = netShiftMinutes.value
  shiftForm.standard_minutes = minutes
  shiftForm.half_day_minutes = Math.ceil(minutes / 2)
}

function canAutoCalculateShiftThresholds() {
  const hasBreakPair = Boolean(shiftForm.break_start_time) === Boolean(shiftForm.break_end_time)
  return shiftDurationMinutes.value > 0
    && hasBreakPair
    && breakMinutes.value >= 0
    && rangeInsideShift(shiftForm.start_time, shiftForm.end_time, shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)
    && netShiftMinutes.value > 0
}

function resetShiftForm() {
  shiftForm.reset()
  shiftForm.start_time = '08:00'
  shiftForm.end_time = '17:00'
  shiftForm.break_start_time = '12:00'
  shiftForm.break_end_time = '13:00'
  shiftForm.overtime_start_time = ''
  shiftForm.overtime_end_time = ''
  shiftForm.standard_minutes = 480
  shiftForm.half_day_minutes = 240
  shiftForm.handover_break_minutes = 0
  shiftForm.overtime_hourly_rate = ''
  shiftForm.grace_minutes = 0
  shiftForm.late_grace_minutes = 0
  shiftForm.early_leave_grace_minutes = 0
  shiftForm.allows_overtime = true
  shiftForm.is_overnight = false
  shiftForm.is_active = true
  shiftForm.clearErrors()
  editingShiftId.value = null
}

function resetHolidayForm() {
  holidayForm.reset()
  holidayForm.holiday_type = 'public'
  holidayForm.is_paid_leave = true
  holidayForm.is_recurring = false
  holidayForm.clearErrors()
  editingHolidayId.value = null
}

function resetAssignmentForm() {
  assignmentForm.reset()
  assignmentForm.target_type = 'company'
  assignmentForm.employee_profile_id = null
  assignmentForm.department_id = null
  assignmentForm.work_shift_id = null
  assignmentForm.weekdays = [1, 2, 3, 4, 5]
  assignmentForm.is_active = true
  assignmentForm.clearErrors()
  editingAssignmentId.value = null
}

const submitShift = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetShiftForm(),
  }
  return editingShiftId.value
    ? shiftForm.put(route('attendance.catalogs.work-shifts.update', editingShiftId.value), options)
    : shiftForm.post(route('attendance.catalogs.work-shifts.store'), options)
}

const submitHoliday = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetHolidayForm(),
  }
  return editingHolidayId.value
    ? holidayForm.put(route('attendance.catalogs.holidays.update', editingHolidayId.value), options)
    : holidayForm.post(route('attendance.catalogs.holidays.store'), options)
}

const submitAssignment = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetAssignmentForm(),
  }
  return editingAssignmentId.value
    ? assignmentForm.put(route('attendance.catalogs.assignments.update', editingAssignmentId.value), options)
    : assignmentForm.post(route('attendance.catalogs.assignments.store'), options)
}

const submitQuickAssignment = () => {
  assignmentForm.target_type = 'employee'
  assignmentForm.department_id = null
  const options = {
    preserveScroll: true,
    onSuccess: () => {
      resetAssignmentForm()
      quickAssignShift.value = null
    },
  }
  return assignmentForm.post(route('attendance.catalogs.assignments.store'), options)
}

function editShift(item) {
  editingShiftId.value = item.id
  shiftForm.shift_name = item.shift_name || ''
  shiftForm.start_time = item.start_time || '08:00'
  shiftForm.end_time = item.end_time || '17:00'
  shiftForm.break_start_time = item.break_start_time || ''
  shiftForm.break_end_time = item.break_end_time || ''
  shiftForm.overtime_start_time = item.overtime_start_time || ''
  shiftForm.overtime_end_time = item.overtime_end_time || ''
  shiftForm.standard_minutes = Number(item.standard_minutes || 480)
  shiftForm.half_day_minutes = Number(item.half_day_minutes || 240)
  shiftForm.handover_break_minutes = Number(item.handover_break_minutes || 0)
  shiftForm.overtime_hourly_rate = item.overtime_hourly_rate ?? ''
  const unifiedGrace = Number(item.grace_minutes || item.late_grace_minutes || item.early_leave_grace_minutes || 0)
  shiftForm.grace_minutes = unifiedGrace
  shiftForm.late_grace_minutes = unifiedGrace
  shiftForm.early_leave_grace_minutes = unifiedGrace
  shiftForm.allows_overtime = !!item.allows_overtime
  shiftForm.is_overnight = !!item.is_overnight
  shiftForm.is_active = !!item.is_active
  shiftForm.description = item.description || ''
  shiftForm.clearErrors()
}

function editHoliday(item) {
  editingHolidayId.value = item.id
  holidayForm.holiday_date = item.holiday_date || ''
  holidayForm.holiday_name = item.holiday_name || ''
  holidayForm.holiday_type = item.holiday_type || 'public'
  holidayForm.is_paid_leave = !!item.is_paid_leave
  holidayForm.is_recurring = !!item.is_recurring
  holidayForm.note = item.note || ''
  holidayForm.clearErrors()
}

function editAssignment(item) {
  editingAssignmentId.value = item.id
  assignmentForm.target_type = item.target_type || 'company'
  assignmentForm.employee_profile_id = item.employee_profile_id ?? null
  assignmentForm.department_id = item.department_id ?? null
  assignmentForm.work_shift_id = item.work_shift_id ?? null
  assignmentForm.effective_from = item.effective_from || ''
  assignmentForm.effective_to = item.effective_to || ''
  assignmentForm.weekdays = Array.isArray(item.weekdays) ? item.weekdays.map(Number) : [1, 2, 3, 4, 5]
  assignmentForm.note = item.note || ''
  assignmentForm.is_active = !!item.is_active
  assignmentForm.clearErrors()
}

const cancelShiftEdit = () => resetShiftForm()
const cancelHolidayEdit = () => resetHolidayForm()
const cancelAssignmentEdit = () => resetAssignmentForm()

function startQuickAssign(item) {
  quickAssignShift.value = item
  assignmentForm.clearErrors()
  assignmentForm.target_type = 'employee'
  assignmentForm.employee_profile_id = null
  assignmentForm.department_id = null
  assignmentForm.work_shift_id = item.id
  assignmentForm.effective_from = ''
  assignmentForm.effective_to = ''
  assignmentForm.weekdays = [1, 2, 3, 4, 5]
  assignmentForm.note = ''
  assignmentForm.is_active = true
}

function cancelQuickAssign() {
  quickAssignShift.value = null
  resetAssignmentForm()
}

const toggleShift = (id) => router.put(route('attendance.catalogs.work-shifts.toggle', id), {}, {
  preserveScroll: true,
  onError: (errors) => {
    toast.error(errors.work_shift || 'Không thể đổi trạng thái ca làm.')
  },
})
const deleteHoliday = (id) => {
  if (confirm('Xóa ngày lễ này?')) {
    router.delete(route('attendance.catalogs.holidays.destroy', id), { preserveScroll: true })
  }
}
const toggleAssignment = (id) => router.put(route('attendance.catalogs.assignments.toggle', id), {}, { preserveScroll: true })

function timeRangeMinutes(start, end, overnight = false) {
  if (!start || !end) return 0
  const startMinutes = timeToMinutes(start)
  let endMinutes = timeToMinutes(end)
  if (overnight && endMinutes <= startMinutes) endMinutes += 1440
  return endMinutes - startMinutes
}

function timeToMinutes(value) {
  const [hour, minute] = String(value).slice(0, 5).split(':').map(Number)
  if (Number.isNaN(hour) || Number.isNaN(minute)) return 0
  return hour * 60 + minute
}

function rangeInsideShift(shiftStart, shiftEnd, rangeStart, rangeEnd, overnight = false) {
  if (!rangeStart && !rangeEnd) return true
  if (!rangeStart || !rangeEnd) return false
  let shiftStartMinutes = timeToMinutes(shiftStart)
  let shiftEndMinutes = timeToMinutes(shiftEnd)
  let rangeStartMinutes = timeToMinutes(rangeStart)
  let rangeEndMinutes = timeToMinutes(rangeEnd)
  if (overnight) {
    if (shiftEndMinutes <= shiftStartMinutes) shiftEndMinutes += 1440
    if (rangeStartMinutes < shiftStartMinutes) rangeStartMinutes += 1440
    if (rangeEndMinutes <= rangeStartMinutes) rangeEndMinutes += 1440
  }
  return rangeStartMinutes >= shiftStartMinutes && rangeEndMinutes <= shiftEndMinutes && rangeEndMinutes > rangeStartMinutes
}

function formatMinutes(minutes) {
  if (!minutes || minutes <= 0) return '0 phút'
  const hours = Math.floor(minutes / 60)
  const rest = minutes % 60
  if (!hours) return `${rest} phút`
  return rest ? `${hours} giờ ${rest} phút` : `${hours} giờ`
}

function formatMoney(value) {
  const amount = Number(value || 0)
  return new Intl.NumberFormat('vi-VN').format(amount)
}

function formatDate(value) {
  if (!value) return '-'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}

function holidayTypeLabel(type) {
  return {
    public: 'Lễ nhà nước',
    company: 'Ngày nghỉ công ty',
    compensatory: 'Nghỉ bù',
    special: 'Đặc biệt',
  }[type] || type || '-'
}

function assignmentTargetLabel(type) {
  return {
    company: 'Toàn công ty',
    employee: 'Nhân viên',
    department: 'Phòng ban',
  }[type] || 'Khác'
}

function assignmentTargetName(item) {
  return item.employee_name || item.department_name || 'Toàn công ty'
}

function weekdayLabels(days) {
  if (!days || !days.length) return 'Tất cả ngày'
  const labels = new Map(weekdays.map((day) => [day.value, day.label]))
  return days.map((day) => labels.get(Number(day))).filter(Boolean).join(', ')
}
</script>

<style scoped>
.form-input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 0.625rem 0.75rem;
  color: #111827;
  outline: none;
}

.form-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgb(37 99 235 / 0.15);
}
</style>
