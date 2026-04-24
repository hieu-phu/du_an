<template>
  <Head title="Bang luong cong ty" />

  <AdminLayout>
    <PageBreadcrumb title="Bang luong cong ty" :items="[{ text: 'Luong', link: null }, { text: 'Bang luong cong ty', link: null }]" />

    <div class="space-y-6">
      <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
          <div>
            <div class="text-sm font-semibold text-gray-900">Bo loc bang luong</div>
            <div class="mt-1 text-sm text-gray-500">Loc theo ky luong, nhan su, phong ban va chuc vu.</div>
          </div>
          <div class="inline-flex items-center rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
            {{ currentPeriodLabel }}
          </div>
        </div>

        <div
          class="mt-4 rounded-2xl px-4 py-3 text-sm"
          :class="periodStatus?.is_locked ? 'border border-amber-200 bg-amber-50 text-amber-800' : 'border border-blue-200 bg-blue-50 text-blue-800'"
        >
          <div class="font-semibold">{{ periodStatus?.status_label || 'Nhap / tinh dong' }}</div>
          <div class="mt-1">
            <template v-if="periodStatus?.is_locked">
              Snapshot duoc khoa luc {{ formatDateTime(periodStatus.locked_at) }} boi {{ periodStatus.locked_by_name || 'He thong' }}.
              Dang dung {{ formatNumber(periodStatus.snapshot_count) }} ban ghi snapshot.
            </template>
            <template v-else>
              Ky luong chua khoa. So lieu dang duoc tinh dong theo cham cong va dieu chinh hien tai.
            </template>
          </div>
        </div>

        <div v-if="false" class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
          <div class="font-semibold">Ky luong da duoc chot</div>
          <div class="mt-1">
            Snapshot duoc khoa luc {{ formatDateTime(periodStatus.locked_at) }} bởi {{ periodStatus.locked_by_name || 'He thong' }}.
            Dang dung {{ formatNumber(periodStatus.snapshot_count) }} ban ghi snapshot.
          </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-12">
          <div class="xl:col-span-3">
            <InputDate
              v-model="filterForm.period"
              label="Ky luong"
              placeholder="Chon thang nam"
              :clearable="false"
              :config="periodPickerConfig"
            />
          </div>

          <label class="block xl:col-span-5">
            <span class="mb-2 block text-sm font-medium text-gray-700">Tim nhan su</span>
            <input
              v-model="filterForm.keyword"
              type="text"
              class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              placeholder="VD: EMP-001, Nguyen Van A, Ke toan"
              @keydown.enter.prevent="applyFilters"
            >
          </label>

          <label class="block xl:col-span-2">
            <span class="mb-2 block text-sm font-medium text-gray-700">Phong ban</span>
            <select
              v-model="filterForm.department_id"
              class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              @change="applyFilters"
            >
              <option value="">Tat ca</option>
              <option v-for="item in departments" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
            </select>
          </label>

          <label class="block xl:col-span-2">
            <span class="mb-2 block text-sm font-medium text-gray-700">Chuc vu</span>
            <select
              v-model="filterForm.position_id"
              class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              @change="applyFilters"
            >
              <option value="">Tat ca</option>
              <option v-for="item in positions" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
            </select>
          </label>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
          <button
            type="button"
            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            @click="applyFilters"
          >
            Ap dung
          </button>
          <button
            type="button"
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="jumpToCurrentPeriod"
          >
            Thang nay
          </button>
          <button
            type="button"
            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="resetFilters"
          >
            Dat lai
          </button>
          <button
            type="button"
            class="rounded-xl border border-emerald-300 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"
            @click="exportExcel"
          >
            Xuat Excel
          </button>
          <button
            v-if="permissions.can_manage_payroll && !periodStatus?.is_locked"
            type="button"
            class="rounded-xl border border-amber-300 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50"
            @click="lockPeriod"
          >
            Chot ky luong
          </button>
          <button
            v-if="permissions.can_manage_payroll && periodStatus?.is_locked"
            type="button"
            class="rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50"
            @click="unlockPeriod"
          >
            Mo khoa ky luong
          </button>
          <button
            v-if="permissions.can_manage_payroll"
            type="button"
            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            @click="recalculatePeriod"
          >
            Tinh lai luong
          </button>
        </div>
      </section>

      <section
        v-if="summary.warning_employee_count > 0"
        class="rounded-[24px] border border-amber-200 bg-amber-50 p-6 shadow-theme-sm"
      >
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <div class="text-sm font-semibold text-amber-900">Canh bao du lieu chua hoan tat</div>
            <div class="mt-1 text-sm text-amber-800">
              Co {{ formatNumber(summary.warning_employee_count) }} nhan su dang co canh bao, tong {{ formatNumber(summary.total_warning_count) }} muc can kiem tra.
            </div>
          </div>
          <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">
            Kiem tra cham cong, duyet cong va thong tin luong
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <SummaryCard label="Nhan su trong ky" :value="formatNumber(summary.employee_count)" />
        <SummaryCard label="Thu nhap phat sinh" :value="formatCurrency(summary.total_gross_amount)" />
        <SummaryCard label="Khau tru thieu cong" :value="formatCurrency(summary.total_attendance_deduction_amount)" tone="red" />
        <SummaryCard label="So du sau doi tru" :value="formatCurrency(summary.total_net_amount)" tone="green" />
      </section>

      <section class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 shadow-theme-sm">
        Thu nhap theo cong la tien cua cong da lam va da duyet. "Khau tru thieu cong" la phan thu nhap khong duoc huong do chua du cong chuan trong ky dang tinh.
      </section>

      <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
        <BreakdownItem label="Tong luong co ban" :value="formatCurrency(summary.total_base_salary)" />
        <BreakdownItem label="Tong phu cap" :value="formatCurrency(summary.total_allowance_amount)" />
        <BreakdownItem label="Thu nhap theo cong" :value="formatCurrency(summary.total_base_salary_amount)" />
        <BreakdownItem label="Tien OT" :value="formatCurrency(summary.total_overtime_amount)" />
        <BreakdownItem label="Tien cho duyet" :value="formatCurrency(summary.total_pending_amount)" />
        <BreakdownItem label="Khau tru theo cong tam tinh" :value="formatCurrency(summary.total_attendance_deduction_amount)" />
        <BreakdownItem label="Khau tru khac tam tinh" :value="formatCurrency(summary.total_manual_deduction_amount)" />
        <BreakdownItem label="Tong khau tru tam tinh" :value="formatCurrency(summary.total_deduction_amount)" />
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Danh sach luong nhan su</h3>
            <p class="mt-1 text-sm text-gray-500">Bam vao cot de sap xep. Chon chi tiet de xem bang luong tung nhan su.</p>
          </div>
          <div class="text-sm text-gray-500">Tong dong: {{ formatNumber(rows.total) }}</div>
        </div>

        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="p-2">Nhan su</th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('department')">
                    Phong ban
                    <span>{{ sortIndicator('department') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('base_salary')">
                    Luong co ban
                    <span>{{ sortIndicator('base_salary') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('approved_work_units')">
                    Cong duyet
                    <span>{{ sortIndicator('approved_work_units') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('approved_overtime_minutes')">
                    OT duyet
                    <span>{{ sortIndicator('approved_overtime_minutes') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('overtime_amount')">
                    Tien OT
                    <span>{{ sortIndicator('overtime_amount') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('allowance_amount')">
                    Phu cap
                    <span>{{ sortIndicator('allowance_amount') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('pending_amount')">
                    Cho duyet
                    <span>{{ sortIndicator('pending_amount') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('attendance_deduction_amount')">
                    Khau tru thieu cong
                    <span>{{ sortIndicator('attendance_deduction_amount') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('manual_deduction_amount')">
                    Tru khac
                    <span>{{ sortIndicator('manual_deduction_amount') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('warning_count')">
                    Canh bao
                    <span>{{ sortIndicator('warning_count') }}</span>
                  </button>
                </th>
                <th class="p-2">
                  <button type="button" class="inline-flex items-center gap-1 font-semibold" @click="toggleSort('net_amount')">
                    So du sau doi tru
                    <span>{{ sortIndicator('net_amount') }}</span>
                  </button>
                </th>
                <th class="p-2 text-right">Tac vu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in rows.data" :key="item.employee_profile_id" class="border-t align-top">
                <td class="p-2">
                  <div class="font-semibold text-gray-900">{{ item.name || '-' }}</div>
                  <div class="text-xs text-gray-500">{{ item.employee_code || '-' }}</div>
                  <div class="text-xs text-gray-500">{{ item.position || '-' }}</div>
                </td>
                <td class="p-2">
                  <div>{{ item.department || '-' }}</div>
                  <div class="text-xs text-gray-500">{{ employmentTypeLabel(item.employment_type) }}</div>
                </td>
                <td class="p-2 font-medium text-gray-900">{{ formatCurrency(item.summary.base_salary, item.currency) }}</td>
                <td class="p-2">
                  <div class="font-medium text-gray-900">{{ formatNumber(item.summary.approved_work_units) }} / {{ formatNumber(item.summary.expected_work_days) }}</div>
                  <div class="text-xs text-gray-500">{{ formatCurrency(item.summary.base_salary_amount, item.currency) }}</div>
                </td>
                <td class="p-2">{{ formatMinutes(item.summary.approved_overtime_minutes) }}</td>
                <td class="p-2 font-medium text-gray-900">{{ formatCurrency(item.summary.overtime_amount, item.currency) }}</td>
                <td class="p-2 font-medium text-blue-700">{{ formatCurrency(item.summary.allowance_amount, item.currency) }}</td>
                <td class="p-2">{{ formatCurrency(item.summary.pending_amount, item.currency) }}</td>
                <td class="p-2 text-red-600">{{ formatCurrency(item.summary.attendance_deduction_amount, item.currency) }}</td>
                <td class="p-2 text-red-600">{{ formatCurrency(item.summary.manual_deduction_amount, item.currency) }}</td>
                <td class="p-2">
                  <div v-if="item.summary.warning_count" class="space-y-1">
                    <div class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                      {{ formatNumber(item.summary.warning_count) }} canh bao
                    </div>
                    <div class="text-xs text-amber-700">{{ item.warnings[0] }}</div>
                  </div>
                  <div v-else class="text-xs font-semibold text-emerald-700">Da day du</div>
                </td>
                <td class="p-2 font-semibold text-emerald-700">{{ formatCurrency(item.summary.net_amount, item.currency) }}</td>
                <td class="p-2 text-right">
                  <button
                    type="button"
                    class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50"
                    @click="openDetail(item.employee_profile_id)"
                  >
                    Chi tiet
                  </button>
                </td>
              </tr>
              <tr v-if="!rows.data.length">
                <td class="border-t p-4 text-center text-gray-500" colspan="13">Khong co du lieu luong cho bo loc hien tai.</td>
              </tr>
            </tbody>
            <tfoot v-if="rows.data.length">
              <tr class="border-t bg-gray-50 font-semibold text-gray-900">
                <td class="p-2" colspan="2">Tong trang hien tai</td>
                <td class="p-2">{{ formatCurrency(sumByPage('base_salary')) }}</td>
                <td class="p-2">{{ formatNumber(sumByPage('approved_work_units')) }}</td>
                <td class="p-2">{{ formatMinutes(sumByPage('approved_overtime_minutes')) }}</td>
                <td class="p-2">{{ formatCurrency(sumByPage('overtime_amount')) }}</td>
                <td class="p-2">{{ formatCurrency(sumByPage('allowance_amount')) }}</td>
                <td class="p-2">{{ formatCurrency(sumByPage('pending_amount')) }}</td>
                <td class="p-2 text-red-600">{{ formatCurrency(sumByPage('attendance_deduction_amount')) }}</td>
                <td class="p-2 text-red-600">{{ formatCurrency(sumByPage('manual_deduction_amount')) }}</td>
                <td class="p-2">{{ formatNumber(sumByPage('warning_count')) }}</td>
                <td class="p-2 text-emerald-700">{{ formatCurrency(sumByPage('net_amount')) }}</td>
                <td class="p-2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </section>

      <Pagination :meta="rows" @page-change="handlePageChange" />

      <CustomModal
        v-if="selectedDetail"
        title="Chi tiet bang luong nhan su"
        :custom_class="detailModalClasses"
        @close="closeDetail"
      >
        <template #body>
          <div class="max-h-[80vh] overflow-y-auto px-6 pb-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="rounded-xl border border-gray-200 p-4">
                <div class="mb-3 text-sm font-semibold text-gray-900">Thong tin nhan su</div>
                <div class="space-y-2 text-sm text-gray-600">
                  <div><span class="font-medium text-gray-900">Ma NV:</span> {{ selectedDetail.profile.employee_code || '-' }}</div>
                  <div><span class="font-medium text-gray-900">Ho ten:</span> {{ selectedDetail.profile.name || '-' }}</div>
                  <div><span class="font-medium text-gray-900">Email:</span> {{ selectedDetail.profile.email || '-' }}</div>
                  <div><span class="font-medium text-gray-900">Phong ban:</span> {{ selectedDetail.profile.department || '-' }}</div>
                  <div><span class="font-medium text-gray-900">Chuc vu:</span> {{ selectedDetail.profile.position || '-' }}</div>
                  <div><span class="font-medium text-gray-900">Loai HD:</span> {{ employmentTypeLabel(selectedDetail.profile.employment_type) }}</div>
                </div>
              </div>

              <div class="rounded-xl border border-gray-200 p-4">
                <div class="mb-3 text-sm font-semibold text-gray-900">Tong hop ky luong</div>
                <div class="space-y-2 text-sm text-gray-600">
                  <div><span class="font-medium text-gray-900">Luong co ban:</span> {{ formatCurrency(selectedDetail.summary.base_salary, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Thu nhap theo cong da duyet:</span> {{ formatCurrency(selectedDetail.summary.base_salary_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Tien OT:</span> {{ formatCurrency(selectedDetail.summary.overtime_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Phu cap:</span> {{ formatCurrency(selectedDetail.summary.allowance_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Tong thu nhap phat sinh:</span> {{ formatCurrency(selectedDetail.summary.gross_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Tien cho duyet:</span> {{ formatCurrency(selectedDetail.summary.pending_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Khau tru do thieu cong:</span> {{ formatCurrency(selectedDetail.summary.attendance_deduction_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">Khau tru khac tam tinh:</span> {{ formatCurrency(selectedDetail.summary.manual_deduction_amount, selectedDetail.profile.currency) }}</div>
                  <div><span class="font-medium text-gray-900">So du sau doi tru:</span> <span class="text-emerald-700">{{ formatCurrency(selectedDetail.summary.net_amount, selectedDetail.profile.currency) }}</span></div>
                </div>
              </div>
            </div>

            <div
              v-if="selectedDetail.summary.warnings?.length"
              class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4"
            >
              <div class="mb-3 text-sm font-semibold text-amber-900">Canh bao du lieu</div>
              <ul class="space-y-2 text-sm text-amber-800">
                <li v-for="warning in selectedDetail.summary.warnings" :key="warning">{{ warning }}</li>
              </ul>
            </div>

            <div class="mt-4 rounded-xl border border-gray-200 p-4">
              <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                  <div class="text-sm font-semibold text-gray-900">Phu cap va khau tru</div>
                  <div class="mt-1 text-xs text-gray-500">Quan ly theo tung nhan su, tung ky luong.</div>
                </div>
              </div>

              <div
                v-if="periodStatus?.is_locked"
                class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
              >
                Ky luong da khoa snapshot. Muon sua phu cap hoac khau tru, hay mo khoa ky luong truoc.
              </div>

              <div class="mt-4 overflow-auto">
                <table class="min-w-full text-sm">
                  <thead>
                    <tr class="text-left text-gray-600">
                      <th class="p-2">Loai</th>
                      <th class="p-2">Noi dung</th>
                      <th class="p-2">So tien</th>
                      <th class="p-2">Ghi chu</th>
                      <th v-if="permissions.can_manage_payroll && !periodStatus?.is_locked" class="p-2 text-right">Tac vu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in selectedDetail.adjustments" :key="item.id" class="border-t">
                      <td class="p-2">
                        <span
                          class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                          :class="item.type === 'allowance' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'"
                        >
                          {{ item.type === 'allowance' ? 'Phu cap' : 'Khau tru' }}
                        </span>
                      </td>
                      <td class="p-2 font-medium text-gray-900">{{ item.label }}</td>
                      <td class="p-2">{{ formatCurrency(item.amount, selectedDetail.profile.currency) }}</td>
                      <td class="p-2">{{ item.note || '-' }}</td>
                      <td v-if="permissions.can_manage_payroll && !periodStatus?.is_locked" class="p-2 text-right">
                        <button
                          type="button"
                          class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50"
                          @click="removeAdjustment(item)"
                        >
                          Xoa
                        </button>
                      </td>
                    </tr>
                    <tr v-if="!selectedDetail.adjustments.length">
                      <td class="border-t p-4 text-center text-gray-500" :colspan="permissions.can_manage_payroll && !periodStatus?.is_locked ? 5 : 4">
                        Chua co phu cap/khau tru rieng trong ky.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="permissions.can_manage_payroll && !periodStatus?.is_locked" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-12">
                <label class="block md:col-span-2">
                  <span class="mb-2 block text-sm font-medium text-gray-700">Loai</span>
                  <select v-model="adjustmentForm.type" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="allowance">Phu cap</option>
                    <option value="deduction">Khau tru</option>
                  </select>
                </label>
                <label class="block md:col-span-4">
                  <span class="mb-2 block text-sm font-medium text-gray-700">Noi dung</span>
                  <input v-model="adjustmentForm.label" type="text" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="VD: Phu cap xang xe" />
                </label>
                <label class="block md:col-span-2">
                  <span class="mb-2 block text-sm font-medium text-gray-700">So tien</span>
                  <input v-model="adjustmentForm.amount" type="number" min="0" step="1000" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="0" />
                </label>
                <label class="block md:col-span-4">
                  <span class="mb-2 block text-sm font-medium text-gray-700">Ghi chu</span>
                  <input v-model="adjustmentForm.note" type="text" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Mo ta them neu can" />
                </label>
              </div>

              <div v-if="permissions.can_manage_payroll && !periodStatus?.is_locked" class="mt-3 flex justify-end">
                <button
                  type="button"
                  class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                  @click="saveAdjustment"
                >
                  Luu phu cap / khau tru
                </button>
              </div>
            </div>

            <div class="mt-4 rounded-xl border border-gray-200 p-4">
              <div class="mb-3 text-sm font-semibold text-gray-900">Cong tinh luong</div>
              <div class="overflow-auto">
                <table class="min-w-full text-sm">
                  <thead>
                    <tr class="text-left text-gray-600">
                      <th class="p-2">Ngay</th>
                      <th class="p-2">Check in</th>
                      <th class="p-2">Check out</th>
                      <th class="p-2">Ca</th>
                      <th class="p-2">Cong</th>
                      <th class="p-2">Tien cong</th>
                      <th class="p-2">OT</th>
                      <th class="p-2">Tien OT</th>
                      <th class="p-2">Trang thai</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in selectedDetail.records" :key="item.id" class="border-t">
                      <td class="p-2">{{ formatDate(item.work_date) }}</td>
                      <td class="p-2">{{ item.check_in_at || '-' }}</td>
                      <td class="p-2">{{ item.check_out_at || '-' }}</td>
                      <td class="p-2">
                        <div class="font-medium text-gray-900">{{ item.shift_name || '-' }}</div>
                        <div class="text-xs text-gray-500">{{ item.shift_time_range || '-' }}</div>
                      </td>
                      <td class="p-2">{{ formatNumber(item.work_unit) }}</td>
                      <td class="p-2">{{ formatCurrency(item.payable_amount, selectedDetail.profile.currency) }}</td>
                      <td class="p-2">{{ formatMinutes(item.overtime_minutes) }}</td>
                      <td class="p-2">{{ formatCurrency(item.overtime_amount, selectedDetail.profile.currency) }}</td>
                      <td class="p-2">{{ approvalLabel(item.approval_status) }}</td>
                    </tr>
                    <tr v-if="!selectedDetail.records.length">
                      <td class="border-t p-4 text-center text-gray-500" colspan="9">Khong co du lieu chi tiet trong ky.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </template>
      </CustomModal>
    </div>
    <ActionDialog ref="actionDialogRef" />
  </AdminLayout>
</template>

<script setup>
import { computed, h, reactive, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'
import Pagination from '@/components/tables/Pagination.vue'
import CustomModal from '@/components/modals/CustomModal.vue'
import ActionDialog from '@/components/ui/ActionDialog.vue'
import { useActionDialog } from '@/composables/useActionDialog'

const props = defineProps({
  filters: { type: Object, required: true },
  departments: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] },
  periodStatus: { type: Object, default: null },
  permissions: { type: Object, default: () => ({ can_manage_payroll: false }) },
  summary: { type: Object, required: true },
  rows: { type: Object, required: true },
  selectedDetail: { type: Object, default: null },
})
const { actionDialogRef, openAlert, openConfirm } = useActionDialog()

const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  keyword: props.filters.keyword || '',
  department_id: props.filters.department_id ? String(props.filters.department_id) : '',
  position_id: props.filters.position_id ? String(props.filters.position_id) : '',
  sort_by: props.filters.sort_by || 'employee_code',
  sort_dir: props.filters.sort_dir || 'asc',
  per_page: Number(props.filters.per_page || 10),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
})

const adjustmentForm = reactive({
  type: 'allowance',
  label: '',
  amount: '',
  note: '',
})

let lastAppliedPeriod = `${filterForm.year}-${filterForm.month}`

const detailModalClasses = [
  'relative',
  'w-full',
  'max-w-[1100px]',
  'flex',
  'flex-col',
  'rounded-sm',
  'bg-white',
  'overflow-hidden',
  'max-h-[90vh]',
]

const SummaryCard = (componentProps) => h('div', { class: 'rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm' }, [
  h('div', { class: 'text-sm text-gray-500' }, componentProps.label),
  h('div', { class: ['mt-2 text-2xl font-semibold', componentProps.tone === 'green' ? 'text-emerald-700' : componentProps.tone === 'red' ? 'text-red-600' : 'text-gray-900'] }, componentProps.value),
])
SummaryCard.props = ['label', 'value', 'tone']

const BreakdownItem = (componentProps) => h('div', { class: 'rounded-lg border border-gray-100 bg-gray-50 p-4' }, [
  h('div', { class: 'text-sm text-gray-500' }, componentProps.label),
  h('div', { class: 'mt-1 text-lg font-semibold text-gray-900' }, componentProps.value),
])
BreakdownItem.props = ['label', 'value']

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Thang ${filterForm.month}/${filterForm.year}`

  return new Intl.DateTimeFormat('vi-VN', {
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const periodPickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-01',
  altInput: true,
  altFormat: 'm/Y',
  defaultDate: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
}

watch(
  () => filterForm.period,
  (value) => {
    const date = parsePeriodValue(value)
    if (!date) return

    filterForm.month = date.getMonth() + 1
    filterForm.year = date.getFullYear()
  }
)

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
  () => props.selectedDetail?.profile?.id,
  () => resetAdjustmentForm()
)

function currentQuery(extra = {}) {
  return {
    month: filterForm.month,
    year: filterForm.year,
    keyword: filterForm.keyword || undefined,
    department_id: filterForm.department_id || undefined,
    position_id: filterForm.position_id || undefined,
    sort_by: filterForm.sort_by || undefined,
    sort_dir: filterForm.sort_dir || undefined,
    per_page: filterForm.per_page || undefined,
    ...extra,
  }
}

function applyFilters(extra = {}) {
  router.get(route('salary.company'), currentQuery({
    page: 1,
    employee_profile_id: undefined,
    ...extra,
  }), {
    preserveState: true,
    preserveScroll: true,
  })
}

function handlePageChange(page) {
  router.get(route('salary.company'), currentQuery({
    page,
    employee_profile_id: props.filters.employee_profile_id || undefined,
  }), {
    preserveState: true,
    preserveScroll: true,
  })
}

function toggleSort(column) {
  if (filterForm.sort_by === column) {
    filterForm.sort_dir = filterForm.sort_dir === 'asc' ? 'desc' : 'asc'
  } else {
    filterForm.sort_by = column
    filterForm.sort_dir = 'asc'
  }

  applyFilters()
}

function sortIndicator(column) {
  if (filterForm.sort_by !== column) return '↕'
  return filterForm.sort_dir === 'asc' ? '↑' : '↓'
}

function openDetail(employeeProfileId) {
  router.get(route('salary.company'), currentQuery({
    page: props.rows.current_page,
    employee_profile_id: employeeProfileId,
  }), {
    preserveState: true,
    preserveScroll: true,
  })
}

function closeDetail() {
  router.get(route('salary.company'), currentQuery({
    page: props.rows.current_page,
    employee_profile_id: undefined,
  }), {
    preserveState: true,
    preserveScroll: true,
  })
}

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
}

function resetFilters() {
  filterForm.keyword = ''
  filterForm.department_id = ''
  filterForm.position_id = ''
  filterForm.sort_by = 'employee_code'
  filterForm.sort_dir = 'asc'
  applyFilters()
}

function exportExcel() {
  window.location.href = route('salary.company.export.excel', currentQuery({
    page: undefined,
    employee_profile_id: undefined,
  }))
}

async function lockPeriod() {
  const confirmed = await openConfirm({
    title: 'Chốt kỳ lương',
    message: `Chốt kỳ lương tháng ${String(filterForm.month).padStart(2, '0')}/${filterForm.year}? Hệ thống sẽ lưu snapshot bảng lương hiện tại.`,
    okText: 'Chốt kỳ lương',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Bảng lương',
  })
  if (!confirmed) {
    return
  }

  router.post(route('salary.company.lock'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveScroll: true,
  })
}

async function unlockPeriod() {
  const confirmed = await openConfirm({
    title: 'Mở khóa kỳ lương',
    message: `Mở khóa kỳ lương tháng ${String(filterForm.month).padStart(2, '0')}/${filterForm.year}? Sau khi mở khóa, bảng lương sẽ quay lại chế độ tính động.`,
    okText: 'Mở khóa',
    cancelText: 'Đóng',
    variant: 'warning',
    eyebrow: 'Bảng lương',
  })
  if (!confirmed) {
    return
  }

  router.post(route('salary.company.unlock'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveScroll: true,
  })
}

async function recalculatePeriod() {
  const message = periodStatusLocked()
    ? `Tinh lai snapshot ky luong thang ${String(filterForm.month).padStart(2, '0')}/${filterForm.year}?`
    : `Ky luong ${String(filterForm.month).padStart(2, '0')}/${filterForm.year} chua khoa. Ban van muon tai lai man hinh hien tai?`

  const confirmed = await openConfirm({
    title: 'Xác nhận tính lại',
    message,
    okText: 'Xác nhận',
    cancelText: 'Đóng',
    variant: periodStatusLocked() ? 'warning' : 'primary',
    eyebrow: 'Bảng lương',
  })
  if (!confirmed) {
    return
  }

  if (!periodStatusLocked()) {
    applyFilters()
    return
  }

  router.post(route('salary.company.recalculate'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveScroll: true,
  })
}

async function saveAdjustment() {
  if (!props.selectedDetail?.profile?.id) return
  if (periodStatusLocked()) {
    await openAlert({
      title: 'Kỳ lương đã khóa',
      message: 'Hãy mở khóa kỳ lương trước khi thêm phụ cấp hoặc khấu trừ.',
      okText: 'Đã hiểu',
      variant: 'warning',
      eyebrow: 'Bảng lương',
    })
    return
  }

  if (!adjustmentForm.label.trim() || Number(adjustmentForm.amount) <= 0) {
    await openAlert({
      title: 'Dữ liệu chưa hợp lệ',
      message: 'Vui lòng nhập nội dung và số tiền hợp lệ.',
      okText: 'Đã hiểu',
      variant: 'warning',
      eyebrow: 'Điều chỉnh',
    })
    return
  }

  router.post(route('salary.company.adjustments.store'), {
    employee_profile_id: props.selectedDetail.profile.id,
    month: filterForm.month,
    year: filterForm.year,
    type: adjustmentForm.type,
    label: adjustmentForm.label.trim(),
    amount: Number(adjustmentForm.amount),
    note: adjustmentForm.note.trim() || undefined,
    ...currentQuery({
      page: props.rows.current_page,
      employee_profile_id: props.selectedDetail.profile.id,
    }),
  }, {
    preserveScroll: true,
    onSuccess: () => resetAdjustmentForm(),
  })
}

async function removeAdjustment(item) {
  if (periodStatusLocked()) {
    await openAlert({
      title: 'Kỳ lương đã khóa',
      message: 'Hãy mở khóa kỳ lương trước khi xóa phụ cấp hoặc khấu trừ.',
      okText: 'Đã hiểu',
      variant: 'warning',
      eyebrow: 'Bảng lương',
    })
    return
  }

  const confirmed = await openConfirm({
    title: 'Xóa khoản điều chỉnh',
    message: `Xóa khoản ${item.type === 'allowance' ? 'phụ cấp' : 'khấu trừ'} "${item.label}"?`,
    okText: 'Xóa khoản',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Điều chỉnh',
  })
  if (!confirmed) {
    return
  }

  router.delete(route('salary.company.adjustments.destroy', item.id), {
    data: currentQuery({
      page: props.rows.current_page,
      employee_profile_id: props.selectedDetail?.profile?.id,
    }),
    preserveScroll: true,
  })
}

function resetAdjustmentForm() {
  adjustmentForm.type = 'allowance'
  adjustmentForm.label = ''
  adjustmentForm.amount = ''
  adjustmentForm.note = ''
}

function periodStatusLocked() {
  return Boolean(props.periodStatus?.is_locked)
}

function employmentTypeLabel(type) {
  return {
    official: 'Chinh thuc',
    probation: 'Thu viec',
    intern: 'Thuc tap',
    contractor: 'Hop dong',
  }[type] || type || '-'
}

function approvalLabel(status) {
  return {
    approved: 'Da duyet',
    rejected: 'Tu choi',
    pending: 'Cho duyet',
  }[status] || status || '-'
}

function formatCurrency(value, currency = 'VND') {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency,
    maximumFractionDigits: 0,
  }).format(Number(value || 0))
}

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(Number(value || 0))
}

function formatMinutes(minutes) {
  const value = Number(minutes || 0)
  if (value <= 0) return '0 phut'
  const hours = Math.floor(value / 60)
  const rest = value % 60
  if (!hours) return `${rest} phut`
  return rest ? `${hours} gio ${rest} phut` : `${hours} gio`
}

function formatDate(value) {
  if (!value) return '-'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}

function formatDateTime(value) {
  if (!value) return '-'
  const [date, time] = String(value).split(' ')
  return `${formatDate(date)} ${time || ''}`.trim()
}

function sumByPage(field) {
  return (props.rows.data || []).reduce((total, item) => total + Number(item.summary?.[field] || 0), 0)
}

function buildPeriodValue(year, month) {
  if (!year || !month) return ''
  return `${year}-${String(month).padStart(2, '0')}-01`
}

function parsePeriodValue(value) {
  if (!value) return null
  const parsed = new Date(value)
  return Number.isNaN(parsed.getTime()) ? null : parsed
}
</script>
