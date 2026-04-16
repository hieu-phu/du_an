<template>
  <Head :title="title" />

  <AdminLayout>
    <PageBreadcrumb :title="title" :items="[{ text: 'Du an', link: null }, { text: title, link: null }]" />

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-3">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tim kiem du an</label>
            <input
              v-model="localFilters.search"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              placeholder="Nhap ten hoac mo ta du an"
              @keyup.enter="applyFilter"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Trang thai</label>
            <select
              v-model="localFilters.status"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option value="">Tat ca trang thai</option>
              <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nhan su tham gia</label>
            <select
              v-model="localFilters.employee_profile_id"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            >
              <option value="">Tat ca nhan su</option>
              <option v-for="option in employeeOptions" :key="option.id" :value="String(option.id)">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex flex-wrap gap-3">
          <button
            type="button"
            class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="resetFilter"
          >
            Xoa loc
          </button>
          <button
            type="button"
            class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
            @click="applyFilter"
          >
            Tim kiem
          </button>
          <button
            v-if="canManageProjects"
            type="button"
            class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
            @click="openCreateModal"
          >
            Them du an
          </button>
        </div>
      </div>

      <p class="mt-4 text-sm text-gray-500">
        {{ scope === 'mine' ? 'Chi hien thi du an ban dang tham gia.' : 'Danh sach du an toan he thong.' }}
      </p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ten du an</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Trang thai</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Tien do</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Ngay bat dau</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thanh vien</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Khoa</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tac</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="project in projects" :key="project.id" class="align-top">
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="font-semibold text-gray-900">{{ project.name }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ project.description || 'Chua co mo ta.' }}</div>
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
                  {{ project.task_summary?.completed || 0 }}/{{ project.task_summary?.total || 0 }} hoan thanh
                </div>
                <div v-if="project.is_delayed" class="mt-1 text-xs font-semibold text-rose-600">
                  Cham tien do
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
                  {{ project.is_locked ? 'Da khoa' : 'Dang mo' }}
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
                    Sua
                  </button>
                  <button
                    v-if="canManageProjects"
                    type="button"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                    :class="project.is_locked ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-500 hover:bg-amber-600'"
                    @click="toggleLock(project)"
                  >
                    {{ project.is_locked ? 'Mo khoa' : 'Khoa' }}
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!projects.length">
              <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">
                Chua co du an phu hop.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <h3 class="mb-3 text-base font-semibold text-gray-900">Danh sach du an theo tung nhan su</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhan su</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">So du an</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Danh sach du an</th>
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
              <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">Chua co du lieu phan bo nhan su du an.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal :show="isFormModalOpen" @close="closeFormModal">
      <div class="p-6">
        <h2 class="mb-5 text-lg font-semibold text-gray-900">
          {{ isEditing ? 'Cap nhat du an' : 'Tao du an moi' }}
        </h2>

        <form class="space-y-4" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700">Ten du an</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                placeholder="Nhap ten du an"
              />
              <div v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</div>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700">Ngay bat dau</label>
              <input
                v-model="form.start_date"
                type="date"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              />
              <div v-if="form.errors.start_date" class="mt-1 text-sm text-rose-600">{{ form.errors.start_date }}</div>
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Trang thai</label>
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
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Mo ta</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
              placeholder="Nhap mo ta du an"
            ></textarea>
            <div v-if="form.errors.description" class="mt-1 text-sm text-rose-600">{{ form.errors.description }}</div>
          </div>

          <div>
            <div class="mb-2 flex items-center justify-between">
              <label class="block text-sm font-medium text-gray-700">Danh sach nhan su tham gia</label>
              <button
                v-if="canManageMembers"
                type="button"
                class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                @click="addMemberRow"
              >
                Them nhan su
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
                  <option value="">Chon nhan su</option>
                  <option v-for="option in employeeOptions" :key="option.id" :value="option.id">
                    {{ option.label }}
                  </option>
                </select>

                <select
                  v-model="member.role_name"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  :disabled="!canManageMembers"
                >
                  <option value="">Chon vai tro</option>
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
                  v-if="canManageMembers"
                  type="button"
                  class="rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50"
                  @click="removeMemberRow(index)"
                >
                  Xoa
                </button>
              </div>

              <div v-if="!form.members.length" class="rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
                Chua co nhan su tham gia.
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
              Huy
            </button>
            <button
              type="submit"
              class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing"
            >
              {{ isEditing ? 'Cap nhat' : 'Tao moi' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <Modal :show="isDetailModalOpen" @close="isDetailModalOpen = false">
      <div v-if="selectedProject" class="max-h-[82vh] overflow-y-auto p-6">
        <h2 class="mb-5 text-lg font-semibold text-gray-900">Chi tiet du an</h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="rounded-xl border border-gray-200 p-4">
            <div class="mb-3 text-sm font-semibold text-gray-900">Thong tin chung</div>
            <div class="space-y-2 text-sm text-gray-700">
              <div><span class="font-medium text-gray-900">Ten du an:</span> {{ selectedProject.name }}</div>
              <div><span class="font-medium text-gray-900">Trang thai:</span> {{ selectedProject.status_label || '-' }}</div>
              <div><span class="font-medium text-gray-900">Ngay bat dau:</span> {{ formatDate(selectedProject.start_date) }}</div>
              <div><span class="font-medium text-gray-900">Tinh trang khoa:</span> {{ selectedProject.is_locked ? 'Da khoa' : 'Dang mo' }}</div>
            </div>
          </div>

          <div class="rounded-xl border border-gray-200 p-4">
            <div class="mb-3 text-sm font-semibold text-gray-900">Mo ta</div>
            <div class="text-sm text-gray-700">{{ selectedProject.description || 'Chua co mo ta cho du an nay.' }}</div>
          </div>

          <div class="rounded-xl border border-gray-200 p-3 md:col-span-2">
            <div class="flex flex-wrap gap-2">
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'members' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'members'"
              >
                Nhan su
              </button>
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'implementation' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'implementation'"
              >
                Trien khai
              </button>
              <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                :class="activeDetailTab === 'history' ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                @click="activeDetailTab = 'history'"
              >
                Lich su
              </button>
            </div>
          </div>

          <div v-if="activeDetailTab === 'members'" class="rounded-xl border border-gray-200 p-4 md:col-span-2">
            <div class="mb-3 flex items-center justify-between">
              <div class="text-sm font-semibold text-gray-900">Nhan su theo tung du an</div>
              <div class="flex items-center gap-2">
                <button
                  v-if="canManageMembers"
                  type="button"
                  class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                  @click="addMemberToProject"
                >
                  Them nhan su vao du an
                </button>
              </div>
            </div>

            <div v-if="canManageProjectRoles" class="mb-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">Quan ly vai tro du an</div>
              <div class="grid grid-cols-1 gap-2 md:grid-cols-[minmax(0,1fr)_auto]">
                <input
                  v-model="newRole.name"
                  type="text"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  placeholder="Nhap ten vai tro moi"
                />
                <button
                  type="button"
                  class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                  @click="addRoleToProject"
                >
                  Them vai tro
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
                    title="Xoa vai tro"
                    @click="removeRoleFromProject(role)"
                  >
                    x
                  </button>
                </span>
              </div>
            </div>

            <div v-if="canManageMembers" class="mb-3 grid grid-cols-1 gap-2 rounded-xl border border-gray-200 p-3 md:grid-cols-[minmax(0,1fr)_200px_170px]">
              <select
                v-model="newMember.employee_profile_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
              >
                <option value="">Chon nhan su</option>
                <option v-for="option in employeeOptions" :key="option.id" :value="String(option.id)">
                  {{ option.label }}
                </option>
              </select>
              <select
                v-model="newMember.role_name"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
              >
                <option value="">Chon vai tro</option>
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
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhan su</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Vai tro</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Vi tri</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ngay tham gia</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tac</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="member in selectedProject.members || []" :key="member.id">
                    <td class="px-3 py-2 text-sm text-gray-700">{{ member.employee_code }} - {{ member.employee_name }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <template v-if="canManageMembers">
                        <select
                          v-model="memberRoleDrafts[member.id]"
                          class="w-full rounded-lg border border-gray-300 px-2 py-1 text-sm outline-none focus:border-blue-500"
                        >
                          <option value="">Chon vai tro</option>
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
                      <div v-if="canManageMembers" class="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          class="rounded-lg border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                          @click="updateMemberRole(member)"
                        >
                          Cap nhat vai tro
                        </button>
                        <button
                          type="button"
                          class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                          @click="removeMemberFromProject(member)"
                        >
                          Loai khoi du an
                        </button>
                      </div>
                      <span v-else>-</span>
                    </td>
                  </tr>
                  <tr v-if="!(selectedProject.members || []).length">
                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">Chua co thanh vien du an.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="activeDetailTab === 'implementation'" class="rounded-xl border border-gray-200 p-4 md:col-span-2">
            <div class="mb-3 text-sm font-semibold text-gray-900">Chi tiet trien khai du an</div>

            <div class="mb-3 grid grid-cols-2 gap-2 md:grid-cols-4">
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Tien do</div>
                <div class="text-sm font-semibold text-gray-900">{{ selectedProject.progress_percent || 0 }}%</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Tong dau viec</div>
                <div class="text-sm font-semibold text-gray-900">{{ selectedProject.task_summary?.total || 0 }}</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Da hoan thanh</div>
                <div class="text-sm font-semibold text-emerald-700">{{ selectedProject.task_summary?.completed || 0 }}</div>
              </div>
              <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-[11px] uppercase tracking-wide text-gray-500">Cham tien do</div>
                <div class="text-sm font-semibold" :class="selectedProject.is_delayed ? 'text-rose-700' : 'text-gray-900'">
                  {{ selectedProject.task_summary?.delayed || 0 }}
                </div>
              </div>
            </div>

            <div v-if="selectedProject.delay_warning" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700">
              {{ selectedProject.delay_warning }}
            </div>

            <div v-if="canManageImplementationDetails" class="mb-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">
                {{ editingImplementationId ? 'Cap nhat dau viec' : 'Them dau viec moi' }}
              </div>
              <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-4">
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Noi dung cong viec</label>
                  <input
                    v-model="implementationForm.content"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    placeholder="Nhap noi dung cong viec"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Nhan su thuc hien</label>
                  <select
                    v-model="implementationForm.assigned_to"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  >
                    <option value="">Khong giao cu the</option>
                    <option v-for="option in selectedProjectMemberOptions" :key="`impl-employee-${option.id}`" :value="option.id">
                      {{ option.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Ngay thuc hien</label>
                  <input
                    v-model="implementationForm.execution_date"
                    type="date"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                    :disabled="!canEditImplementationSchedule"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">So ngay thuc hien</label>
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
                  <label class="mb-1 block text-xs font-medium text-gray-600">Trang thai dau viec</label>
                  <select
                    v-model="implementationForm.detail_status"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  >
                    <option v-for="option in implementationStatusOptions" :key="`impl-status-${option.value}`" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-600">Tien do (%)</label>
                  <input
                    v-model="implementationForm.progress_percent"
                    type="number"
                    min="0"
                    max="100"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                  />
                </div>
                <div class="flex gap-2 xl:col-span-2">
                  <button
                    type="button"
                    class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                    @click="saveImplementationDetail"
                  >
                    {{ editingImplementationId ? 'Luu' : 'Them' }}
                  </button>
                  <button
                    v-if="editingImplementationId"
                    type="button"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    @click="resetImplementationForm"
                  >
                    Huy
                  </button>
                </div>
              </div>
              <p v-if="!canEditImplementationSchedule" class="mt-2 text-xs text-amber-600">
                Chi admin moi duoc thay doi ngay thuc hien va so ngay thuc hien.
              </p>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Noi dung</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nhan su</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thuc hien</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Du kien xong</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tien do</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Trang thai</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tac</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="detail in selectedImplementationDetails" :key="`impl-detail-${detail.id}`" class="align-top">
                    <td class="px-3 py-2 text-sm text-gray-700">
                      <div class="font-medium text-gray-900">{{ detail.content }}</div>
                      <div class="mt-1 text-xs text-gray-500">
                        So ngay: {{ detail.duration_days }} |
                        Thuc te xong: {{ formatDate(detail.actual_end_date) }}
                      </div>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ detail.assigned_code }} - {{ detail.assigned_name || '-' }}</td>
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
                    <td class="px-3 py-2 text-center text-sm text-gray-700">
                      <div class="flex flex-wrap items-center justify-center gap-2">
                        <button
                          v-if="detail.can_update_status"
                          type="button"
                          class="rounded-lg border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                          @click="updateImplementationStatus(detail)"
                        >
                          Cap nhat
                        </button>
                        <button
                          v-if="canManageImplementationDetails"
                          type="button"
                          class="rounded-lg border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                          :disabled="detail.is_locked"
                          @click="editImplementationDetail(detail)"
                        >
                          Sua
                        </button>
                        <button
                          v-if="canManageImplementationDetails"
                          type="button"
                          class="rounded-lg border border-amber-200 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                          @click="toggleImplementationLock(detail)"
                        >
                          {{ detail.is_locked ? 'Mo khoa' : 'Khoa' }}
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!selectedImplementationDetails.length">
                    <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">
                      Chua co dau viec trien khai.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-for="detail in selectedImplementationDetails" :key="`impl-log-${detail.id}`" class="mt-3 rounded-xl border border-gray-200 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">
                Lich su cap nhat - #{{ detail.id }}
              </div>
              <div class="space-y-1 text-xs text-gray-600">
                <div v-for="log in detail.logs || []" :key="`detail-log-${log.id}`">
                  {{ log.updated_at }} - {{ log.updated_by_name || 'He thong' }}:
                  {{ log.field_label }} ({{ log.old_value || '-' }} -> {{ log.new_value || '-' }})
                </div>
                <div v-if="!(detail.logs || []).length">Chua co lich su cap nhat.</div>
              </div>
            </div>
          </div>

          <div v-if="activeDetailTab === 'history'" class="rounded-xl border border-gray-200 p-4 md:col-span-2">
            <div class="mb-3 text-sm font-semibold text-gray-900">Theo doi trang thai du an theo thoi gian</div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thoi diem</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tien do</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Nguoi cap nhat</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ghi chu</th>
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
                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">Chua co lich su thay doi trang thai.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
  projects: { type: Array, default: () => [] },
  scope: { type: String, default: 'all' },
  title: { type: String, default: 'Danh sach du an' },
  filters: { type: Object, default: () => ({}) },
  status_options: { type: Array, default: () => [] },
  employee_options: { type: Array, default: () => [] },
  project_role_options: { type: Array, default: () => [] },
  implementation_status_options: { type: Array, default: () => [] },
  employee_project_overview: { type: Array, default: () => [] },
  can_manage_projects: { type: Boolean, default: false },
  can_manage_members: { type: Boolean, default: false },
  can_manage_project_roles: { type: Boolean, default: false },
  can_manage_implementation_details: { type: Boolean, default: false },
  can_edit_implementation_schedule: { type: Boolean, default: false },
})

const isFormModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const selectedProject = ref(null)
const activeDetailTab = ref('members')
const memberRoleDrafts = ref({})
const implementationDrafts = ref({})
const editingImplementationId = ref(null)

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
const implementationStatusOptions = computed(() => props.implementation_status_options || [])
const employeeProjectOverview = computed(() => props.employee_project_overview || [])
const canManageProjects = computed(() => props.can_manage_projects)
const canManageMembers = computed(() => props.can_manage_members || props.can_manage_projects)
const canManageProjectRoles = computed(() => props.can_manage_project_roles || props.can_manage_projects)
const canManageImplementationDetails = computed(() => props.can_manage_implementation_details || props.can_manage_projects)
const canEditImplementationSchedule = computed(() => props.can_edit_implementation_schedule)
const formRoleOptions = computed(() => projectRoleOptions.value)
const selectedProjectRoleOptions = computed(() => selectedProject.value?.role_options || projectRoleOptions.value)
const selectedImplementationDetails = computed(() => selectedProject.value?.implementation_details || [])
const selectedProjectMemberOptions = computed(() => {
  return (selectedProject.value?.members || []).map((member) => ({
    id: String(member.employee_profile_id),
    label: `${member.employee_code || ''} - ${member.employee_name || 'Nhan su'}`.trim(),
  }))
})

const memberErrors = computed(() => {
  return Object.entries(form.errors)
    .filter(([key]) => key.startsWith('members'))
    .map(([, value]) => value)
})

function routeName() {
  return props.scope === 'mine' ? 'projects.mine' : 'projects.index'
}

function applyFilter() {
  router.get(route(routeName()), {
    search: localFilters.search || undefined,
    status: localFilters.status || undefined,
    employee_profile_id: localFilters.employee_profile_id || undefined,
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
  selectedProject.value = project
  activeDetailTab.value = 'members'
  memberRoleDrafts.value = Object.fromEntries((project.members || []).map((member) => [member.id, member.role_name || '']))
  implementationDrafts.value = Object.fromEntries((project.implementation_details || []).map((detail) => [detail.id, {
    detail_status: detail.detail_status || 'planned',
    progress_percent: detail.progress_percent ?? 0,
  }]))
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
    form.transform(() => payload).put(route('projects.update', editingId.value), submitOptions('Da cap nhat du an thanh cong.'))
    return
  }

  form.transform(() => payload).post(route('projects.store'), submitOptions('Da tao du an moi thanh cong.'))
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
      toast.error('Khong the luu du an. Vui long kiem tra du lieu.')
    },
  }
}

function toggleLock(project) {
  const action = project.is_locked ? 'mo khoa' : 'khoa'
  if (!window.confirm(`Ban co chac muon ${action} du an "${project.name}"?`)) {
    return
  }

  router.put(route('projects.toggle-lock', project.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(project.is_locked ? 'Da mo khoa du an.' : 'Da khoa du an.')
    },
    onError: () => {
      toast.error(project.is_locked ? 'Khong the mo khoa du an.' : 'Khong the khoa du an.')
    },
  })
}

function addMemberToProject() {
  if (!selectedProject.value) return
  if (!newMember.employee_profile_id) {
    toast.error('Vui long chon nhan su.')
    return
  }
  if (!newMember.role_name) {
    toast.error('Vui long chon vai tro.')
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
      toast.success('Da them nhan su vao du an.')
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
        'Khong the them nhan su vao du an.'
      )
    },
  })
}

function addRoleToProject() {
  if (!selectedProject.value) return

  const roleName = (newRole.name || '').trim()
  if (!roleName) {
    toast.error('Vui long nhap ten vai tro.')
    return
  }

  router.post(route('projects.roles.store', selectedProject.value.id), {
    name: roleName,
  }, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da them vai tro du an.')
      newRole.name = ''
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(
        errors?.name ||
        errors?.role ||
        errors?.project ||
        'Khong the them vai tro du an.'
      )
    },
  })
}

function removeRoleFromProject(role) {
  if (!selectedProject.value) return
  if (!role?.id) return
  if (!window.confirm(`Ban co chac muon xoa vai tro "${role.name}"?`)) return

  router.delete(route('projects.roles.destroy', [selectedProject.value.id, role.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da xoa vai tro du an.')
      isDetailModalOpen.value = false
    },
    onError: () => {
      toast.error('Khong the xoa vai tro du an.')
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
    toast.error('Vui long nhap noi dung cong viec.')
    return
  }
  if (!implementationForm.execution_date) {
    toast.error('Vui long chon ngay thuc hien.')
    return
  }
  if (!implementationForm.duration_days || Number(implementationForm.duration_days) < 1) {
    toast.error('So ngay thuc hien phai lon hon 0.')
    return
  }

  const payload = {
    content: implementationForm.content.trim(),
    assigned_to: implementationForm.assigned_to || null,
    execution_date: implementationForm.execution_date,
    duration_days: Number(implementationForm.duration_days),
    detail_status: implementationForm.detail_status,
    progress_percent: Number(implementationForm.progress_percent || 0),
  }

  const onSuccess = () => {
    toast.success(editingImplementationId.value ? 'Da cap nhat dau viec.' : 'Da them dau viec.')
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
      'Khong the luu dau viec.'
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
    progress_percent: Number(draft.progress_percent ?? detail.progress_percent ?? 0),
  }, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da cap nhat trang thai dau viec.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.detail_status || errors?.progress_percent || 'Khong the cap nhat trang thai dau viec.')
    },
  })
}

function toggleImplementationLock(detail) {
  if (!selectedProject.value) return
  router.put(route('projects.implementation-details.toggle-lock', [selectedProject.value.id, detail.id]), {}, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success(detail.is_locked ? 'Da mo khoa dau viec.' : 'Da khoa dau viec.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.project || 'Khong the thay doi khoa dau viec.')
    },
  })
}

function removeImplementationDetail(detail) {
  if (!selectedProject.value) return
  if (!window.confirm('Ban co chac muon xoa dau viec nay?')) return

  router.delete(route('projects.implementation-details.destroy', [selectedProject.value.id, detail.id]), {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da xoa dau viec.')
      isDetailModalOpen.value = false
    },
    onError: (errors) => {
      toast.error(errors?.detail || errors?.project || 'Khong the xoa dau viec.')
    },
  })
}

function updateMemberRole(member) {
  if (!selectedProject.value) return

  router.put(route('projects.members.update', [selectedProject.value.id, member.id]), {
    role_name: memberRoleDrafts.value[member.id] || member.role_name || '',
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da cap nhat vai tro nhan su.')
      isDetailModalOpen.value = false
    },
    onError: () => {
      toast.error('Khong the cap nhat vai tro nhan su.')
    },
  })
}

function removeMemberFromProject(member) {
  if (!selectedProject.value) return
  if (!window.confirm('Ban co chac muon loai nhan su nay khoi du an?')) return

  router.delete(route('projects.members.destroy', [selectedProject.value.id, member.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da loai nhan su khoi du an.')
      isDetailModalOpen.value = false
    },
    onError: () => {
      toast.error('Khong the loai nhan su khoi du an.')
    },
  })
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}
</script>
