<template>
  <Head :title="title" />

  <AdminLayout>
    <PageBreadcrumb :title="title" :items="[{ text: 'Dự án', link: null }, { text: title, link: null }]" />

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tìm kiếm dự án</label>
            <input
              v-model="localFilters.search"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              placeholder="Nhập tên hoặc mô tả dự án"
              @keyup.enter="applyFilter"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Trạng thái</label>
            <select
              v-model="localFilters.status"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option value="">Tất cả trạng thái</option>
              <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nhân sự tham gia</label>
            <select
              v-model="localFilters.employee_profile_id"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option value="">Tất cả nhân sự</option>
              <option v-for="option in employeeOptions" :key="option.id" :value="String(option.id)">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Số dòng/trang</label>
            <select
              v-model="localFilters.per_page"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option value="10">10 dòng</option>
              <option value="25">25 dòng</option>
              <option value="50">50 dòng</option>
              <option value="100">100 dòng</option>
            </select>
          </div>
        </div>

        <div class="flex flex-wrap gap-3">
          <button
            type="button"
            class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="resetFilter"
          >
            Xóa lọc
          </button>
          <button
            type="button"
            class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
            @click="applyFilter"
          >
            Tìm kiếm
          </button>
          <button
            v-if="canManageProjects"
            type="button"
            class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
            @click="openCreateModal"
          >
            Thêm dự án
          </button>
        </div>
      </div>

      <p class="mt-4 text-sm text-gray-500">
        {{ scope === 'mine' ? 'Chỉ hiển thị dự án bạn đang tham gia.' : 'Danh sách dự án toàn hệ thống.' }}
      </p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tên dự án</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Tiến độ</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Ngày bắt đầu</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thành viên</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Khóa</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="project in projects" :key="project.id" class="align-top">
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="font-semibold text-gray-900">{{ project.name }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ project.description || 'Chưa có mô tả.' }}</div>
              </td>
              <td class="px-4 py-4 text-center text-sm text-gray-700">
                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                  {{ project.status_label || '-' }}
                </span>
              </td>
              <td class="px-4 py-4 text-center text-sm text-gray-700">
                <div class="mx-auto w-32 rounded-full bg-gray-100">
                  <div
                    class="rounded-full bg-emerald-500 px-2 py-1 text-center text-xs font-semibold text-black"
                    :style="{ width: `${project.progress_percent > 0 ? Math.max(8, project.progress_percent) : 0}%` }"
                  >
                    {{ project.progress_percent || 0 }}%
                  </div>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                  {{ project.task_summary?.completed || 0 }}/{{ project.task_summary?.total || 0 }} hoàn thành
                </div>
                <div v-if="project.is_delayed" class="mt-1 text-xs font-semibold text-rose-600">
                  Chậm tiến độ
                </div>
              </td>
              <td class="px-4 py-4 text-center text-sm text-gray-700">{{ formatDate(project.start_date) }}</td>
              <td class="px-4 py-4 text-center text-sm text-gray-700">
                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                  {{ project.active_members_count || 0 }}
                </span>
              </td>
              <td class="px-4 py-4 text-center text-sm text-gray-700">
                <span
                  :class="project.is_locked ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'"
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                >
                  {{ project.is_locked ? 'Đã khóa' : 'Đang mở' }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="flex justify-center gap-2">
                  <button
                    type="button"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    @click="openDetailModal(project)"
                  >
                    Xem
                  </button>
                  <button
                    v-if="canManageProjects"
                    type="button"
                    class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="project.is_locked"
                    @click="openEditModal(project)"
                  >
                    Sửa
                  </button>
                  <button
                    v-if="canManageProjects"
                    type="button"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                    :class="project.is_locked ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-500 hover:bg-amber-600'"
                    @click="toggleLock(project)"
                  >
                    {{ project.is_locked ? 'Mở khóa' : 'Khóa' }}
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!projects.length">
              <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">
                Chưa có dự án phù hợp.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="paginationMeta.last_page > 1" class="border-t border-gray-200 p-4">
        <Pagination :meta="paginationMeta" @page-change="goToPage" />
      </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <h3 class="mb-3 text-base font-semibold text-gray-900">Danh sách dự án theo từng nhân sự</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhân sự</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Số dự án</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Danh sách dự án</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="item in employeeProjectOverview" :key="item.employee_profile_id">
              <td class="px-4 py-3 text-sm text-gray-700">{{ item.employee_code }} - {{ item.employee_name }}</td>
              <td class="px-4 py-3 text-center text-sm text-gray-700">{{ item.project_count }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="projectItem in item.projects"
                    :key="`${item.employee_profile_id}-${projectItem.project_id}`"
                    class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700"
                  >
                    {{ projectItem.project_name }} ({{ projectItem.role_name || '-' }})
                  </span>
                </div>
              </td>
            </tr>
            <tr v-if="!employeeProjectOverview.length">
              <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có dữ liệu phân bổ nhân sự dự án.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal :show="isFormModalOpen" @close="closeFormModal">
      <div class="p-6">
        <h2 class="mb-5 text-lg font-semibold text-gray-900">
          {{ isEditing ? 'Cập nhật dự án' : 'Tạo dự án mới' }}
        </h2>

        <form class="space-y-4" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700">Tên dự án</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                placeholder="Nhập tên dự án"
              />
              <div v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</div>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
              <input
                v-model="form.start_date"
                type="date"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              />
              <div v-if="form.errors.start_date" class="mt-1 text-sm text-rose-600">{{ form.errors.start_date }}</div>
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Trạng thái</label>
            <select
              v-model="form.status"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <div v-if="form.errors.status" class="mt-1 text-sm text-rose-600">{{ form.errors.status }}</div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Mô tả</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              placeholder="Nhập mô tả dự án"
            ></textarea>
            <div v-if="form.errors.description" class="mt-1 text-sm text-rose-600">{{ form.errors.description }}</div>
          </div>

          <div>
            <div class="mb-2 flex items-center justify-between">
              <label class="block text-sm font-medium text-gray-700">Danh sách nhân sự tham gia</label>
              <button
                v-if="canManageMembers"
                type="button"
                class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                @click="addMemberRow"
              >
                Thêm nhân sự
              </button>
            </div>

            <div class="space-y-2">
              <div
                v-for="(member, index) in form.members"
                :key="`member-${index}`"
                class="grid grid-cols-1 gap-2 rounded-xl border border-gray-200 p-3 md:grid-cols-[minmax(0,1fr)_180px_170px_auto]"
              >
                <select
                  v-model="member.employee_profile_id"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  :disabled="!canManageMembers"
                >
                  <option value="">Chọn nhân sự</option>
                  <option v-for="option in employeeOptions" :key="option.id" :value="option.id">
                    {{ option.label }}
                  </option>
                </select>

                <select
                  v-model="member.role_name"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  :disabled="!canManageMembers"
                >
                  <option value="">Chọn vai trò</option>
                  <option v-for="option in formRoleOptions" :key="`form-role-${option.value}`" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>

                <input
                  v-model="member.joined_at"
                  type="date"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  :disabled="!canManageMembers"
                />

                <button
                  v-if="canAddProjectMember"
                  type="button"
                  class="rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50"
                  @click="removeMemberRow(index)"
                >
                  Xóa
                </button>
              </div>

              <div v-if="!form.members.length" class="rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
                Chưa có nhân sự tham gia.
              </div>
            </div>

            <div v-if="memberErrors.length" class="mt-2 space-y-1">
              <p v-for="message in memberErrors" :key="message" class="text-sm text-rose-600">{{ message }}</p>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <button
              type="button"
              class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
              @click="closeFormModal"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing"
            >
              {{ isEditing ? 'Cập nhật' : 'Tạo mới' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <Modal :show="isDetailModalOpen" max-width="7xl" content-class="overflow-hidden" @close="isDetailModalOpen = false">
      <div v-if="selectedProject" class="max-h-[90vh] overflow-y-auto p-6 lg:p-7">
        <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Chi tiết dự án</h2>
            <p class="mt-1 text-sm text-gray-500">{{ selectedProject.name }}</p>
          </div>
          <button
            type="button"
            class="self-start rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:self-auto"
            @click="isDetailModalOpen = false"
          >
            Đóng
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="rounded-xl border border-gray-200 p-4">
            <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin chung</div>
            <div class="space-y-2 text-sm text-gray-700">
              <div><span class="font-medium text-gray-900">Tên dự án:</span> {{ selectedProject.name }}</div>
              <div><span class="font-medium text-gray-900">Trạng thái:</span> {{ selectedProject.status_label || '-' }}</div>
              <div><span class="font-medium text-gray-900">Ngày bắt đầu:</span> {{ formatDate(selectedProject.start_date) }}</div>
              <div><span class="font-medium text-gray-900">Tình trạng khóa:</span> {{ selectedProject.is_locked ? 'Đã khóa' : 'Đang mở' }}</div>
            </div>
          </div>

          <div class="rounded-xl border border-gray-200 p-4 lg:col-span-2">
            <div class="mb-3 text-sm font-semibold text-gray-900">Mô tả</div>
            <div class="text-sm text-gray-700">{{ selectedProject.description || 'Chưa có mô tả cho dự án này.' }}</div>
          </div>

          <div class="rounded-xl border border-gray-200 p-3 lg:col-span-3">
            <div class="flex flex-wrap gap-2">
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'members' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'members'"
              >
                Nhân sự
              </button>
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'implementation' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'implementation'"
              >
                Triển khai
              </button>
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'attachments' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'attachments'"
              >
                Tệp đính kèm
              </button>
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'history' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'history'"
              >
                Lịch sử
              </button>
            </div>
          </div>

          <div v-if="activeDetailTab === 'members'" class="rounded-xl border border-gray-200 p-4 lg:col-span-3">
            <div class="mb-3 flex items-center justify-between">
              <div class="text-sm font-semibold text-gray-900">Nhân sự theo từng dự án</div>
              <div class="flex items-center gap-2">
                <button
                  v-if="canAddProjectMember"
                  type="button"
                  class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                  @click="addMemberToProject"
                >
                  Thêm nhân sự vào dự án
                </button>
              </div>
            </div>

            <div v-if="canManageProjectRoles" class="mb-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Quản lý vai trò dự án</div>
              <div class="grid grid-cols-1 gap-2 md:grid-cols-[minmax(0,1fr)_auto]">
                <input
                  v-model="newRole.name"
                  type="text"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  placeholder="Nhập tên vai trò mới"
                />
                <button
                  type="button"
                  class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                  @click="addRoleToProject"
                >
                  Thêm vai trò
                </button>
              </div>
              <div class="mt-3 flex flex-wrap gap-2">
                <span
                  v-for="role in selectedProject.roles || []"
                  :key="`project-role-${role.id}`"
                  class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700"
                >
                  {{ role.name }}
                  <button
                    type="button"
                    class="text-rose-600 hover:text-rose-700"
                    title="Xóa vai trò"
                    @click="removeRoleFromProject(role)"
                  >
                    x
                  </button>
                </span>
              </div>
            </div>

            <div v-if="canManageProjectRoles" class="mb-3">
              <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <div class="mb-3">
                  <div class="text-sm font-semibold text-gray-800">Phân quyền theo vai trò</div>
                  <div class="text-xs text-gray-500">Nhấn vào vai trò để mở hoặc đóng chi tiết phân quyền.</div>
                </div>
                <div class="flex flex-col gap-3">
                  <div
                    v-for="role in selectedProject.roles || []"
                    :key="`project-role-permissions-${role.id}`"
                    class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                  >
                    <button
                      type="button"
                      class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-left transition hover:bg-gray-100"
                      @click="activeRolePermissionId = (activeRolePermissionId === role.id ? null : role.id)"
                    >
                      <div>
                        <div class="text-sm font-semibold text-gray-800">{{ role.name }}</div>
                        <div class="text-xs text-gray-500">Quyền này chỉ áp dụng trong dự án hiện tại.</div>
                      </div>
                      <svg
                        class="h-5 w-5 text-gray-500 transition-transform"
                        :class="activeRolePermissionId === role.id ? 'rotate-180' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    
                    <div v-show="activeRolePermissionId === role.id" class="border-t border-gray-200 p-4">
                      <div class="mb-3 flex justify-end">
                        <button
                          type="button"
                          class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50"
                          @click="updateRolePermissions(role)"
                        >
                          Lưu quyền
                        </button>
                      </div>
                      <div class="grid grid-cols-1 gap-3">
                        <div
                          v-for="group in projectRolePermissionGroups"
                          :key="`role-permission-group-${role.id}-${group.key}`"
                          class="rounded-lg border border-gray-200 bg-gray-50 p-3"
                        >
                          <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">{{ group.label }}</div>
                          <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            <label
                              v-for="permission in group.permissions"
                              :key="`role-permission-${role.id}-${permission.value}`"
                              class="flex cursor-pointer gap-2 rounded-lg border border-gray-200 bg-white p-2 text-xs text-gray-700 transition hover:border-indigo-300"
                            >
                              <input
                                v-model="rolePermissionDrafts[role.id]"
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                :value="permission.value"
                              />
                              <span>
                                <span class="block font-semibold text-gray-800">{{ permission.label }}</span>
                                <span class="block text-gray-500">{{ permission.description }}</span>
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="canAddProjectMember" class="mb-3 grid grid-cols-1 gap-2 rounded-xl border border-gray-200 p-3 md:grid-cols-[minmax(0,1fr)_200px_170px]">
              <select
                v-model="newMember.employee_profile_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
              >
                <option value="">Chọn nhân sự</option>
                <option v-for="option in employeeOptions" :key="option.id" :value="String(option.id)">
                  {{ option.label }}
                </option>
              </select>
              <select
                v-model="newMember.role_name"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
              >
                <option value="">Chọn vai trò</option>
                <option v-for="option in selectedProjectRoleOptions" :key="`new-role-${option.value}`" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
              <input
                v-model="newMember.joined_at"
                type="date"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
              />
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhân sự</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Vai trò</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Vị trí</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ngày tham gia</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="member in selectedProject.members || []" :key="member.id">
                    <td class="px-3 py-2 text-sm text-gray-700">{{ member.employee_code }} - {{ member.employee_name }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <template v-if="canUpdateProjectMemberRole">
                        <select
                          v-model="memberRoleDrafts[member.id]"
                          class="w-full rounded-lg border border-gray-300 px-2 py-1 text-sm outline-none focus:border-blue-500"
                        >
                          <option value="">Chọn vai trò</option>
                          <option v-for="option in selectedProjectRoleOptions" :key="`draft-role-${member.id}-${option.value}`" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </template>
                      <template v-else>
                        {{ member.role_name || '-' }}
                      </template>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ member.position_name || '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ formatDate(member.joined_at) }}</td>
                    <td class="px-3 py-2 text-center text-sm text-gray-700">
                      <div v-if="canUpdateProjectMemberRole || canRemoveProjectMember" class="flex items-center justify-center gap-2">
                        <button
                          v-if="canUpdateProjectMemberRole"
                          type="button"
                          class="rounded-lg border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                          @click="updateMemberRole(member)"
                        >
                          Cập nhật vai trò
                        </button>
                        <button
                          v-if="canRemoveProjectMember"
                          type="button"
                          class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                          @click="removeMemberFromProject(member)"
                        >
                          Loại khỏi dự án
                        </button>
                      </div>
                      <span v-else>-</span>
                    </td>
                  </tr>
                  <tr v-if="!(selectedProject.members || []).length">
                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">Chưa có thành viên dự án.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="activeDetailTab === 'implementation'" class="rounded-xl border border-gray-200 p-4 lg:col-span-3">
            <div class="mb-3 text-sm font-semibold text-gray-900">Chi tiết triển khai dự án</div>

            <div class="mb-3 grid grid-cols-2 gap-2 md:grid-cols-4">
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Tiến độ</div>
                <div class="text-sm font-semibold text-gray-900">{{ selectedProject.progress_percent || 0 }}%</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Tổng đầu việc</div>
                <div class="text-sm font-semibold text-gray-900">{{ selectedProject.task_summary?.total || 0 }}</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Đã hoàn thành</div>
                <div class="text-sm font-semibold text-emerald-700">{{ selectedProject.task_summary?.completed || 0 }}</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Chậm tiến độ</div>
                <div class="text-sm font-semibold" :class="selectedProject.is_delayed ? 'text-rose-700' : 'text-gray-900'">
                  {{ selectedProject.task_summary?.delayed || 0 }}
                </div>
              </div>
            </div>

            <div v-if="selectedProject.delay_warning" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700">
              {{ selectedProject.delay_warning }}
            </div>

            <div v-if="canCreateImplementationDetail || editingImplementationId" class="mb-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">
                {{ editingImplementationId ? 'Cập nhật đầu việc' : 'Thêm đầu việc mới' }}
              </div>
              <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-4">
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Nội dung công việc</label>
                  <input
                    v-model="implementationForm.content"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    placeholder="Nhập nội dung công việc"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Nhân sự thực hiện</label>
                  <select
                    v-model="implementationForm.assigned_to"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  >
                    <option value="">Không giao cụ thể</option>
                    <option v-for="option in selectedProjectMemberOptions" :key="`impl-employee-${option.id}`" :value="option.id">
                      {{ option.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Ngày thực hiện</label>
                  <input
                    v-model="implementationForm.execution_date"
                    type="date"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    :disabled="!canEditImplementationSchedule"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Số ngày thực hiện</label>
                  <input
                    v-model="implementationForm.duration_days"
                    type="number"
                    min="1"
                    max="365"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    :disabled="!canEditImplementationSchedule"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Trạng thái đầu việc</label>
                  <select
                    v-model="implementationForm.detail_status"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    @change="syncImplementationFormProgress"
                  >
                    <option v-for="option in implementationStatusOptions" :key="`impl-status-${option.value}`" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Tiến độ (%)</label>
                  <input
                    v-model="implementationForm.progress_percent"
                    type="number"
                    min="0"
                    max="100"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    @input="syncImplementationFormProgress"
                  />
                </div>
                <div class="flex gap-2 xl:col-span-2">
                  <button
                    type="button"
                    class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                    @click="saveImplementationDetail"
                  >
                    {{ editingImplementationId ? 'Lưu' : 'Thêm' }}
                  </button>
                  <button
                    v-if="editingImplementationId"
                    type="button"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    @click="resetImplementationForm"
                  >
                    Hủy
                  </button>
                </div>
              </div>
              <p v-if="!canEditImplementationSchedule" class="mt-2 text-xs text-amber-600">
                Chỉ admin mới được thay đổi ngày thực hiện và số ngày thực hiện.
              </p>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nội dung</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhân sự</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thực hiện</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Dự kiến xong</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tiến độ</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tệp đính kèm</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="detail in selectedImplementationDetails" :key="`impl-detail-${detail.id}`" class="align-top">
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <div class="font-medium text-gray-900">{{ detail.content }}</div>
                      <div class="hidden">
                        Số ngày: {{ detail.duration_days }} |
                        Thực tế xong: {{ formatDate(detail.actual_end_date) }}
                      </div>
                      <div class="mt-1 space-y-0.5 text-xs text-gray-500">
                        <div>Thời lượng: {{ detail.duration_days }} ngày</div>
                        <div>
                          Thực tế xong:
                          <span :class="detail.actual_end_date ? 'text-emerald-700' : 'text-gray-500'">
                            {{ detail.actual_end_date ? formatDate(detail.actual_end_date) : 'Chưa hoàn thành' }}
                          </span>
                        </div>
                        <div v-if="detail.is_delayed" class="font-semibold text-rose-600">
                          Quá hạn {{ detail.delay_days || 1 }} ngày
                        </div>
                      </div>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <div v-if="detail.assigned_name" class="space-y-0.5">
                        <div class="font-semibold text-gray-900">{{ detail.assigned_name }}</div>
                        <div class="text-xs text-gray-500">{{ detail.assigned_code || 'Chưa có mã nhân sự' }}</div>
                      </div>
                      <span v-else class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">
                        Chưa phân công
                      </span>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ formatDate(detail.execution_date) }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ formatDate(detail.expected_end_date) }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <template v-if="detail.can_update_status">
                        <input
                          v-model="implementationDrafts[detail.id].progress_percent"
                          type="number"
                          min="0"
                          max="100"
                          class="w-20 rounded-lg border border-gray-300 px-2 py-1 text-sm outline-none focus:border-blue-500"
                          @input="syncImplementationDraftProgress(detail)"
                        />
                      </template>
                      <template v-else>
                        {{ detail.progress_percent }}%
                      </template>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <template v-if="detail.can_update_status">
                        <select
                          v-model="implementationDrafts[detail.id].detail_status"
                          class="rounded-lg border border-gray-300 px-2 py-1 text-sm outline-none focus:border-blue-500"
                          @change="syncImplementationDraftProgress(detail)"
                        >
                          <option v-for="option in implementationStatusOptions" :key="`impl-row-status-${detail.id}-${option.value}`" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </template>
                      <template v-else>
                        {{ detail.detail_status_label }}
                      </template>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <div class="space-y-2">
                        <div v-if="(detail.attachments || []).length" class="space-y-1">
                          <div
                            v-for="attachment in detail.attachments"
                            :key="`detail-attachment-${detail.id}-${attachment.id}`"
                            class="flex flex-wrap items-center gap-2 rounded-lg bg-gray-50 px-2 py-1"
                          >
                            <a :href="attachment.download_url" class="font-medium text-blue-700 hover:text-blue-800">
                              {{ attachment.original_name }}
                            </a>
                            <span class="text-xs text-gray-500">{{ attachment.size_label }}</span>
                            <button
                              v-if="attachment.can_delete"
                              type="button"
                              class="text-xs font-semibold text-rose-600 hover:text-rose-700"
                              @click="removeAttachment(attachment)"
                            >
                              Xóa
                            </button>
                          </div>
                        </div>
                        <div v-else class="text-xs text-gray-500">Chưa có tệp.</div>
                        <label
                          v-if="detail.can_upload_attachment"
                          class="inline-flex cursor-pointer items-center rounded-lg border border-indigo-200 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50"
                        >
                          Tải tệp
                          <input class="hidden" type="file" multiple :accept="acceptedAttachmentTypes" @change="uploadDetailAttachments(detail, $event)" />
                        </label>
                      </div>
                    </td>
                    <td class="px-3 py-2 text-center text-sm text-gray-700">
                      <div class="flex flex-wrap items-center justify-center gap-2">
                        <button
                          v-if="detail.can_update_status"
                          type="button"
                          class="rounded-lg border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                          @click="updateImplementationStatus(detail)"
                        >
                          Cập nhật
                        </button>
                        <button
                          v-if="detail.can_update_detail"
                          type="button"
                          class="rounded-lg border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                          :disabled="detail.is_locked"
                          @click="editImplementationDetail(detail)"
                        >
                          Sửa
                        </button>
                        <button
                          v-if="detail.can_delete_detail"
                          type="button"
                          class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                          @click="removeImplementationDetail(detail)"
                        >
                          Xóa
                        </button>
                        <button
                          v-if="detail.can_toggle_lock"
                          type="button"
                          class="rounded-lg border border-amber-200 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                          @click="toggleImplementationLock(detail)"
                        >
                          {{ detail.is_locked ? 'Mở khóa' : 'Khóa' }}
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!selectedImplementationDetails.length">
                    <td colspan="8" class="px-3 py-4 text-center text-sm text-gray-500">
                      Chưa có đầu việc triển khai.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-for="detail in selectedImplementationDetails" :key="`impl-comment-${detail.id}`" class="mt-3 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
              <div class="mb-2 flex items-center justify-between gap-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">
                  Thảo luận - #{{ detail.id }}
                </div>
                <span class="rounded-full bg-white px-2 py-1 text-[11px] font-semibold text-slate-600">
                  {{ (detail.comments || []).length }} bình luận
                </span>
              </div>

              <div class="space-y-2">
                <div
                  v-for="comment in detail.comments || []"
                  :key="`detail-comment-${detail.id}-${comment.id}`"
                  class="rounded-lg border border-slate-200 bg-white px-3 py-2"
                >
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                      <div class="text-sm font-semibold text-slate-800">{{ comment.author_name || 'Thành viên dự án' }}</div>
                      <div class="mt-1 whitespace-pre-line break-words text-sm text-slate-700">{{ comment.content }}</div>
                      <div class="mt-1 text-xs text-slate-500">{{ formatDateTime(comment.created_at) }}</div>
                    </div>
                    <button
                      v-if="comment.can_delete"
                      type="button"
                      class="shrink-0 text-xs font-semibold text-rose-600 hover:text-rose-700"
                      @click="removeImplementationComment(detail, comment)"
                    >
                      Xóa
                    </button>
                  </div>
                </div>

                <div v-if="!(detail.comments || []).length" class="rounded-lg border border-dashed border-slate-300 px-3 py-4 text-sm text-slate-500">
                  Chưa có bình luận nào cho đầu việc này.
                </div>
              </div>

              <div class="mt-3">
                <template v-if="detail.can_comment">
                  <textarea
                    v-model="commentDrafts[detail.id]"
                    rows="3"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-indigo-500"
                    placeholder="Nhập nội dung trao đổi trên đầu việc này"
                  ></textarea>
                  <div class="mt-2 flex justify-end">
                    <button
                      type="button"
                      class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                      @click="submitImplementationComment(detail)"
                    >
                      Gửi bình luận
                    </button>
                  </div>
                </template>
                <div v-else class="rounded-lg border border-dashed border-slate-300 px-3 py-4 text-sm text-slate-500">
                  Bạn chỉ có quyền xem bình luận ở đầu việc này.
                </div>
              </div>
            </div>

            <div v-for="detail in selectedImplementationDetails" :key="`impl-log-${detail.id}`" class="mt-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">
                Lịch sử cập nhật - #{{ detail.id }}
              </div>
              <div class="space-y-1 text-xs text-gray-600">
                <div v-for="log in detail.logs || []" :key="`detail-log-${log.id}`">
                  {{ log.updated_at }} - {{ log.updated_by_name || 'Hệ thống' }}:
                  {{ log.field_label }} ({{ log.old_value || '-' }} -> {{ log.new_value || '-' }})
                </div>
                <div v-if="!(detail.logs || []).length">Chưa có lịch sử cập nhật.</div>
              </div>
            </div>
          </div>

          <div v-if="activeDetailTab === 'attachments'" class="rounded-xl border border-gray-200 p-4 lg:col-span-3">
            <div class="mb-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
              <div>
                <div class="text-sm font-semibold text-gray-900">Tệp đính kèm của dự án</div>
                <div class="mt-1 text-xs text-gray-500">Tải tài liệu, hình ảnh hoặc hồ sơ liên quan trực tiếp đến dự án.</div>
              </div>
              <label
                v-if="canUploadProjectAttachments && !selectedProject.is_locked"
                class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
              >
                Tải tệp lên
                <input class="hidden" type="file" multiple :accept="acceptedAttachmentTypes" @change="uploadProjectAttachments" />
              </label>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tên tệp</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Dung lượng</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Người tải lên</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thời điểm</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="attachment in selectedProject.attachments || []" :key="`project-attachment-${attachment.id}`">
                    <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ attachment.original_name }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ attachment.size_label || '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ attachment.uploaded_by_name || 'Hệ thống' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ formatDateTime(attachment.created_at) }}</td>
                    <td class="px-3 py-2 text-center text-sm text-gray-700">
                      <div class="flex flex-wrap items-center justify-center gap-2">
                        <a
                          :href="attachment.download_url"
                          class="rounded-lg border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                        >
                          Tải xuống
                        </a>
                        <button
                          v-if="attachment.can_delete"
                          type="button"
                          class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                          @click="removeAttachment(attachment)"
                        >
                          Xóa
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!(selectedProject.attachments || []).length">
                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">Chưa có tệp đính kèm cho dự án này.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="activeDetailTab === 'history'" class="rounded-xl border border-gray-200 p-4 lg:col-span-3">
            <div class="mb-3 text-sm font-semibold text-gray-900">Theo dõi trạng thái dự án theo thời gian</div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thời điểm</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tiến độ</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Người cập nhật</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ghi chú</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="history in selectedProject.status_histories || []" :key="history.id">
                    <td class="px-3 py-2 text-sm text-gray-700">{{ formatDateTime(history.changed_at) }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ history.old_progress }}% -> {{ history.new_progress }}%</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ history.changed_by_name || '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ history.note || '-' }}</td>
                  </tr>
                  <tr v-if="!(selectedProject.status_histories || []).length">
                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">Chưa có lịch sử thay đổi trạng thái.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </Modal>
    <ActionDialog ref="actionDialogRef" />
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Modal from '@/components/ui/Modal.vue'
import Pagination from '@/components/tables/Pagination.vue'
import ActionDialog from '@/components/ui/ActionDialog.vue'
import { useActionDialog } from '@/composables/useActionDialog'

const props = defineProps({
  projects: { type: Array, default: () => [] },
  pagination: { type: Object, default: () => ({}) },
  scope: { type: String, default: 'all' },
  title: { type: String, default: 'Danh sách dự án' },
  filters: { type: Object, default: () => ({}) },
  status_options: { type: Array, default: () => [] },
  employee_options: { type: Array, default: () => [] },
  project_role_options: { type: Array, default: () => [] },
  project_role_permission_options: { type: Array, default: () => [] },
  implementation_status_options: { type: Array, default: () => [] },
  employee_project_overview: { type: Array, default: () => [] },
  can_manage_projects: { type: Boolean, default: false },
  can_manage_members: { type: Boolean, default: false },
  can_manage_project_roles: { type: Boolean, default: false },
  can_manage_implementation_details: { type: Boolean, default: false },
  can_upload_project_attachments: { type: Boolean, default: false },
  can_edit_implementation_schedule: { type: Boolean, default: false },
})
const { actionDialogRef, openConfirm } = useActionDialog()

const isFormModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const selectedProject = ref(null)
const activeDetailTab = ref('members')
const memberRoleDrafts = ref({})
const rolePermissionDrafts = ref({})
const implementationDrafts = ref({})
const commentDrafts = ref({})
const editingImplementationId = ref(null)
const activeRolePermissionId = ref(null)

const newMember = reactive({
  employee_profile_id: '',
  role_name: '',
  joined_at: '',
})

const newRole = reactive({
  name: '',
})

const implementationForm = reactive({
  content: '',
  assigned_to: '',
  execution_date: '',
  duration_days: 1,
  detail_status: 'planned',
  progress_percent: 0,
})

const localFilters = reactive({
  search: props.filters?.search ?? '',
  status: props.filters?.status ?? '',
  employee_profile_id: props.filters?.employee_profile_id ? String(props.filters.employee_profile_id) : '',
  per_page: props.filters?.per_page ? String(props.filters.per_page) : '10',
})

const form = useForm({
  name: '',
  start_date: '',
  status: 'planning',
  description: '',
  members: [],
})

const statusOptions = computed(() => props.status_options || [])
const employeeOptions = computed(() => props.employee_options || [])
const projectRoleOptions = computed(() => props.project_role_options || [])
const projectRolePermissionGroups = computed(() => props.project_role_permission_options || [])
const implementationStatusOptions = computed(() => props.implementation_status_options || [])
const employeeProjectOverview = computed(() => props.employee_project_overview || [])
const canManageProjects = computed(() => props.can_manage_projects)
const canManageMembers = computed(() => props.can_manage_members || props.can_manage_projects || Boolean(selectedProject.value?.can_manage_members))
const canAddProjectMember = computed(() => props.can_manage_members || props.can_manage_projects || Boolean(selectedProject.value?.can_add_member))
const canUpdateProjectMemberRole = computed(() => props.can_manage_members || props.can_manage_projects || Boolean(selectedProject.value?.can_update_member_role))
const canRemoveProjectMember = computed(() => props.can_manage_members || props.can_manage_projects || Boolean(selectedProject.value?.can_remove_member))
const canManageProjectRoles = computed(() => props.can_manage_project_roles || props.can_manage_projects)
const canManageImplementationDetails = computed(() => props.can_manage_implementation_details || props.can_manage_projects || Boolean(selectedProject.value?.can_manage_implementation_details))
const canCreateImplementationDetail = computed(() => props.can_manage_implementation_details || props.can_manage_projects || Boolean(selectedProject.value?.can_create_implementation_detail))
const canUpdateImplementationDetail = computed(() => props.can_manage_implementation_details || props.can_manage_projects || Boolean(selectedProject.value?.can_update_implementation_detail))
const canUploadProjectAttachments = computed(() => props.can_upload_project_attachments || props.can_manage_projects || Boolean(selectedProject.value?.can_upload_project_attachments))
const canEditImplementationSchedule = computed(() => props.can_edit_implementation_schedule || Boolean(selectedProject.value?.can_edit_implementation_schedule))
const acceptedAttachmentTypes = '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.ppt,.pptx,.zip,.rar'
const formRoleOptions = computed(() => projectRoleOptions.value)
const selectedProjectRoleOptions = computed(() => selectedProject.value?.role_options || projectRoleOptions.value)
const selectedImplementationDetails = computed(() => selectedProject.value?.implementation_details || [])
const paginationMeta = computed(() => ({
  current_page: Number(props.pagination?.current_page || 1),
  last_page: Number(props.pagination?.last_page || 1),
  per_page: Number(props.pagination?.per_page || localFilters.per_page || 10),
  total: Number(props.pagination?.total || 0),
  from: props.pagination?.from ?? 0,
  to: props.pagination?.to ?? 0,
}))
const selectedProjectMemberOptions = computed(() => {
  return (selectedProject.value?.members || []).map((member) => ({
    id: String(member.employee_profile_id),
    label: `${member.employee_code || ''} - ${member.employee_name || 'Nhân sự'}`.trim(),
  }))
})

const memberErrors = computed(() => {
  return Object.entries(form.errors)
    .filter(([key]) => key.startsWith('members'))
    .map(([, value]) => value)
})

function buildImplementationDrafts(project) {
  return Object.fromEntries((project?.implementation_details || []).map((detail) => [detail.id, {
    detail_status: detail.detail_status || 'planned',
    progress_percent: detail.progress_percent ?? 0,
  }]))
}

function buildRolePermissionDrafts(project) {
  return Object.fromEntries((project?.roles || []).map((role) => [role.id, [...(role.permissions || [])]]))
}

function buildCommentDrafts(project, currentDrafts = {}) {
  return Object.fromEntries((project?.implementation_details || []).map((detail) => [detail.id, currentDrafts?.[detail.id] || '']))
}

function normalizeProgressForStatus(progress, status) {
  const numericProgress = Math.max(0, Math.min(100, Number(progress || 0)))

  if (status === 'planned' || status === 'cancelled') {
    return 0
  }

  if (status === 'completed') {
    return 100
  }

  if (status === 'in_progress') {
    return Math.max(1, Math.min(99, numericProgress))
  }

  return numericProgress
}

function syncImplementationFormProgress() {
  implementationForm.progress_percent = normalizeProgressForStatus(
    implementationForm.progress_percent,
    implementationForm.detail_status
  )
}

function syncImplementationDraftProgress(detail) {
  const draft = implementationDrafts.value[detail.id]
  if (!draft) return

  draft.progress_percent = normalizeProgressForStatus(draft.progress_percent, draft.detail_status)
}

function syncSelectedProjectState(project, currentCommentDrafts = commentDrafts.value) {
  selectedProject.value = project
  memberRoleDrafts.value = Object.fromEntries((project?.members || []).map((member) => [member.id, member.role_name || '']))
  rolePermissionDrafts.value = buildRolePermissionDrafts(project)
  implementationDrafts.value = buildImplementationDrafts(project)
  commentDrafts.value = buildCommentDrafts(project, currentCommentDrafts)
}

function syncSelectedProjectFromPage(page, options = {}) {
  const projectId = selectedProject.value?.id
  if (!projectId) return

  const projects = page?.props?.projects || props.projects || []
  const nextProject = projects.find((project) => Number(project.id) === Number(projectId))
  if (!nextProject) return

  syncSelectedProjectState(nextProject, options.keepCommentDrafts ? commentDrafts.value : {})
}

function routeName() {
  return props.scope === 'mine' ? 'projects.mine' : 'projects.index'
}

function applyFilter() {
  goToPage(1)
}

function goToPage(page = 1) {
  router.get(route(routeName()), {
    search: localFilters.search || undefined,
    status: localFilters.status || undefined,
    employee_profile_id: localFilters.employee_profile_id || undefined,
    per_page: localFilters.per_page || undefined,
    page,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function resetFilter() {
  localFilters.search = ''
  localFilters.status = ''
  localFilters.employee_profile_id = ''
  localFilters.per_page = '10'
  applyFilter()
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  selectedProject.value = null
  form.reset()
  form.clearErrors()
  form.status = statusOptions.value?.[0]?.value || 'planning'
  form.members = []
  isFormModalOpen.value = true
}

function openEditModal(project) {
  isEditing.value = true
  editingId.value = project.id
  form.clearErrors()
  form.name = project.name || ''
  form.start_date = project.start_date || ''
  form.status = project.status || (statusOptions.value?.[0]?.value || 'planning')
  form.description = project.description || ''
  form.members = (project.members || []).map((member) => ({
    employee_profile_id: member.employee_profile_id || '',
    role_name: member.role_name || '',
    joined_at: member.joined_at || '',
  }))
  isFormModalOpen.value = true
}

function openDetailModal(project) {
  activeDetailTab.value = 'members'
  syncSelectedProjectState(project, {})
  newMember.employee_profile_id = ''
  newMember.role_name = ''
  newMember.joined_at = ''
  newRole.name = ''
  resetImplementationForm()
  isDetailModalOpen.value = true
}

function closeFormModal() {
  isFormModalOpen.value = false
  form.clearErrors()
}

function addMemberRow() {
  form.members.push({
    employee_profile_id: '',
    role_name: '',
    joined_at: '',
  })
}

function removeMemberRow(index) {
  form.members.splice(index, 1)
}

function submitForm() {
  const payload = {
    name: form.name,
    start_date: form.start_date,
    status: form.status,
    description: form.description,
    members: form.members,
  }

  if (isEditing.value && editingId.value) {
    form.transform(() => payload).put(route('projects.update', editingId.value), submitOptions('Đã cập nhật dự án thành công.'))
    return
  }

  form.transform(() => payload).post(route('projects.store'), submitOptions('Đã tạo dự án mới thành công.'))
}

function submitOptions(successMessage) {
  return {
    preserveScroll: true,
    onSuccess: () => {
      isFormModalOpen.value = false
      form.reset()
      toast.success(successMessage)
    },
    onError: () => {
      toast.error('Không thể lưu dự án. Vui lòng kiểm tra dữ liệu.')
    },
  }
}

async function toggleLock(project) {
  const action = project.is_locked ? 'mở khóa' : 'khóa'
  const confirmed = await openConfirm({
    title: project.is_locked ? 'Mở khóa dự án' : 'Khóa dự án',
    message: `Bạn có chắc muốn ${action} dự án "${project.name}"?`,
    okText: 'Xác nhận',
    cancelText: 'Đóng',
    variant: project.is_locked ? 'warning' : 'danger',
    eyebrow: 'Dự án',
  })
  if (!confirmed) {
    return
  }

  router.put(route('projects.toggle-lock', project.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(project.is_locked ? 'Đã mở khóa dự án.' : 'Đã khóa dự án.')
    },
    onError: () => {
      toast.error(project.is_locked ? 'Không thể mở khóa dự án.' : 'Không thể khóa dự án.')
    },
  })
}

function addMemberToProject() {
  if (!selectedProject.value) return
  if (!newMember.employee_profile_id) {
    toast.error('Vui lòng chọn nhân sự.')
    return
  }
  if (!newMember.role_name) {
    toast.error('Vui lòng chọn vai trò.')
    return
  }

  router.post(route('projects.members.store', selectedProject.value.id), {
    employee_profile_id: newMember.employee_profile_id || null,
    role_name: newMember.role_name,
    joined_at: newMember.joined_at || null,
  }, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã thêm nhân sự vào dự án.')
      newMember.employee_profile_id = ''
      newMember.role_name = ''
      newMember.joined_at = ''
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(
        errors?.employee_profile_id ||
        errors?.role_name ||
        errors?.project ||
        errors?.member ||
        'Không thể thêm nhân sự vào dự án.'
      )
    },
  })
}

function addRoleToProject() {
  if (!selectedProject.value) return

  const roleName = (newRole.name || '').trim()
  if (!roleName) {
    toast.error('Vui lòng nhập tên vai trò.')
    return
  }

  router.post(route('projects.roles.store', selectedProject.value.id), {
    name: roleName,
  }, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã thêm vai trò dự án.')
      newRole.name = ''
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(
        errors?.name ||
        errors?.role ||
        errors?.project ||
        'Không thể thêm vai trò dự án.'
      )
    },
  })
}

async function removeRoleFromProject(role) {
  if (!selectedProject.value) return
  if (!role?.id) return
  const confirmed = await openConfirm({
    title: 'Xóa vai trò dự án',
    message: `Bạn có chắc muốn xóa vai trò "${role.name}"?`,
    okText: 'Xóa vai trò',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Dự án',
  })
  if (!confirmed) return

  router.delete(route('projects.roles.destroy', [selectedProject.value.id, role.id]), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Đã xóa vai trò dự án.')
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
    },
    onError: () => {
      toast.error('Không thể xóa vai trò dự án.')
    },
  })
}

function updateRolePermissions(role) {
  if (!selectedProject.value || !role?.id) return

  router.put(route('projects.roles.update', [selectedProject.value.id, role.id]), {
    permissions: rolePermissionDrafts.value[role.id] || [],
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Đã cập nhật quyền vai trò dự án.')
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
    },
    onError: (errors) => {
      toast.error(errors?.permissions || errors?.project || 'Không thể cập nhật quyền vai trò dự án.')
    },
  })
}

function resetImplementationForm() {
  editingImplementationId.value = null
  implementationForm.content = ''
  implementationForm.assigned_to = ''
  implementationForm.execution_date = ''
  implementationForm.duration_days = 1
  implementationForm.detail_status = implementationStatusOptions.value?.[0]?.value || 'planned'
  implementationForm.progress_percent = 0
}

function editImplementationDetail(detail) {
  editingImplementationId.value = detail.id
  implementationForm.content = detail.content || ''
  implementationForm.assigned_to = detail.assigned_to ? String(detail.assigned_to) : ''
  implementationForm.execution_date = detail.execution_date || ''
  implementationForm.duration_days = detail.duration_days || 1
  implementationForm.detail_status = detail.detail_status || (implementationStatusOptions.value?.[0]?.value || 'planned')
  implementationForm.progress_percent = detail.progress_percent ?? 0
}

function saveImplementationDetail() {
  if (!selectedProject.value) return
  if (!implementationForm.content.trim()) {
    toast.error('Vui lòng nhập nội dung công việc.')
    return
  }
  if (!implementationForm.execution_date) {
    toast.error('Vui lòng chọn ngày thực hiện.')
    return
  }
  if (!implementationForm.duration_days || Number(implementationForm.duration_days) < 1) {
    toast.error('Số ngày thực hiện phải lớn hơn 0.')
    return
  }

  const payload = {
    content: implementationForm.content.trim(),
    assigned_to: implementationForm.assigned_to || null,
    execution_date: implementationForm.execution_date,
    duration_days: Number(implementationForm.duration_days),
    detail_status: implementationForm.detail_status,
    progress_percent: normalizeProgressForStatus(implementationForm.progress_percent, implementationForm.detail_status),
  }

  const onSuccess = () => {
    toast.success(editingImplementationId.value ? 'Đã cập nhật đầu việc.' : 'Đã thêm đầu việc.')
    resetImplementationForm()
    isDetailModalOpen.value = false
  }

  const onError = (errors) => {
    toast.error(
      errors?.content ||
      errors?.assigned_to ||
      errors?.execution_date ||
      errors?.duration_days ||
      errors?.detail ||
      errors?.project ||
      'Không thể lưu đầu việc.'
    )
  }

  if (editingImplementationId.value) {
    router.put(
      route('projects.implementation-details.update', [selectedProject.value.id, editingImplementationId.value]),
      payload,
      { preserveState: false, preserveScroll: true, onSuccess, onError }
    )
    return
  }

  router.post(
    route('projects.implementation-details.store', selectedProject.value.id),
    payload,
    { preserveState: false, preserveScroll: true, onSuccess, onError }
  )
}

function updateImplementationStatus(detail) {
  if (!selectedProject.value) return
  const draft = implementationDrafts.value[detail.id] || {}
  router.put(route('projects.implementation-details.status', [selectedProject.value.id, detail.id]), {
    detail_status: draft.detail_status || detail.detail_status,
    progress_percent: normalizeProgressForStatus(
      draft.progress_percent ?? detail.progress_percent ?? 0,
      draft.detail_status || detail.detail_status
    ),
  }, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã cập nhật trạng thái đầu việc.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.detail_status || errors?.progress_percent || 'Không thể cập nhật trạng thái đầu việc.')
    },
  })
}

function toggleImplementationLock(detail) {
  if (!selectedProject.value) return
  router.put(route('projects.implementation-details.toggle-lock', [selectedProject.value.id, detail.id]), {}, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success(detail.is_locked ? 'Đã mở khóa đầu việc.' : 'Đã khóa đầu việc.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.project || 'Không thể thay đổi khóa đầu việc.')
    },
  })
}

async function removeImplementationDetail(detail) {
  if (!selectedProject.value) return
  const confirmed = await openConfirm({
    title: 'Xóa đầu việc',
    message: 'Bạn có chắc muốn xóa đầu việc này?',
    okText: 'Xóa đầu việc',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Triển khai',
  })
  if (!confirmed) return

  router.delete(route('projects.implementation-details.destroy', [selectedProject.value.id, detail.id]), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Đã xóa đầu việc.')
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.project || 'Không thể xóa đầu việc.')
    },
  })
}

function uploadProjectAttachments(event) {
  if (!selectedProject.value) return
  const files = Array.from(event.target.files || [])
  event.target.value = ''
  if (!files.length) return

  const formData = new FormData()
  files.forEach((file) => formData.append('files[]', file))

  router.post(route('projects.attachments.store', selectedProject.value.id), formData, {
    forceFormData: true,
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã tải tệp đính kèm lên dự án.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(firstAttachmentError(errors) || 'Không thể tải tệp đính kèm lên dự án.')
    },
  })
}

function uploadDetailAttachments(detail, event) {
  if (!selectedProject.value) return
  const files = Array.from(event.target.files || [])
  event.target.value = ''
  if (!files.length) return

  const formData = new FormData()
  files.forEach((file) => formData.append('files[]', file))

  router.post(route('projects.implementation-details.attachments.store', [selectedProject.value.id, detail.id]), formData, {
    forceFormData: true,
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã tải tệp đính kèm lên đầu việc.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(firstAttachmentError(errors) || 'Không thể tải tệp đính kèm lên đầu việc.')
    },
  })
}

function submitImplementationComment(detail) {
  if (!selectedProject.value) return

  const content = (commentDrafts.value?.[detail.id] || '').trim()
  if (!content) {
    toast.error('Vui lòng nhập nội dung bình luận.')
    return
  }

  router.post(route('projects.implementation-details.comments.store', [selectedProject.value.id, detail.id]), {
    content,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      commentDrafts.value = {
        ...commentDrafts.value,
        [detail.id]: '',
      }
      syncSelectedProjectFromPage(page)
      toast.success('Đã gửi bình luận cho đầu việc.')
    },
    onError: (errors) => {
      toast.error(errors?.content || errors?.comment || 'Không thể gửi bình luận cho đầu việc.')
    },
  })
}

async function removeImplementationComment(detail, comment) {
  if (!selectedProject.value) return

  const confirmed = await openConfirm({
    title: 'Xóa bình luận',
    message: 'Bạn có chắc muốn xóa bình luận này?',
    okText: 'Xóa bình luận',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Thảo luận',
  })
  if (!confirmed) return

  router.delete(route('projects.implementation-details.comments.destroy', [selectedProject.value.id, detail.id, comment.id]), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
      toast.success('Đã xóa bình luận.')
    },
    onError: (errors) => {
      toast.error(errors?.comment || 'Không thể xóa bình luận.')
    },
  })
}

async function removeAttachment(attachment) {
  if (!selectedProject.value) return
  const confirmed = await openConfirm({
    title: 'Xóa tệp đính kèm',
    message: `Bạn có chắc muốn xóa tệp "${attachment.original_name}"?`,
    okText: 'Xóa tệp',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Tệp đính kèm',
  })
  if (!confirmed) return

  router.delete(route('projects.attachments.destroy', [selectedProject.value.id, attachment.id]), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Đã xóa tệp đính kèm.')
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
    },
    onError: (errors) => {
      toast.error(errors?.attachments || 'Không thể xóa tệp đính kèm.')
    },
  })
}

function firstAttachmentError(errors) {
  return errors?.attachments || errors?.files || Object.entries(errors || {}).find(([key]) => key.startsWith('files.'))?.[1]
}

function updateMemberRole(member) {
  if (!selectedProject.value) return

  router.put(route('projects.members.update', [selectedProject.value.id, member.id]), {
    role_name: memberRoleDrafts.value[member.id] || member.role_name || '',
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Đã cập nhật vai trò nhân sự.')
      isDetailModalOpen.value = false
    },
    onError: () => {
      toast.error('Không thể cập nhật vai trò nhân sự.')
    },
  })
}

async function removeMemberFromProject(member) {
  if (!selectedProject.value) return
  const confirmed = await openConfirm({
    title: 'Loại nhân sự khỏi dự án',
    message: 'Bạn có chắc muốn loại nhân sự này khỏi dự án?',
    okText: 'Xác nhận loại',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Dự án',
  })
  if (!confirmed) return

  router.delete(route('projects.members.destroy', [selectedProject.value.id, member.id]), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Đã loại nhân sự khỏi dự án.')
      syncSelectedProjectFromPage(page, { keepCommentDrafts: true })
    },
    onError: () => {
      toast.error('Không thể loại nhân sự khỏi dự án.')
    },
  })
}

function formatDate(value) {
  if (!value) return '-'
  if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
    const [year, month, day] = value.split('-')
    return `${day}/${month}/${year}`
  }

  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}
</script>
