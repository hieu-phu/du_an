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
              <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
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

                <input
                  v-model="member.role_name"
                  type="text"
                  class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  placeholder="Vai tro"
                  :disabled="!canManageMembers"
                />

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
      <div v-if="selectedProject" class="p-6">
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

          <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
            <div class="mb-3 flex items-center justify-between">
              <div class="text-sm font-semibold text-gray-900">Nhan su theo tung du an</div>
              <button
                v-if="canManageMembers"
                type="button"
                class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                @click="addMemberToProject"
              >
                Them nhan su vao du an
              </button>
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
              <input
                v-model="newMember.role_name"
                type="text"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                placeholder="Vai tro"
              />
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
                        <input
                          v-model="memberRoleDrafts[member.id]"
                          type="text"
                          class="w-full rounded-lg border border-gray-300 px-2 py-1 text-sm outline-none focus:border-blue-500"
                        />
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

          <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
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
  employee_project_overview: { type: Array, default: () => [] },
  can_manage_projects: { type: Boolean, default: false },
  can_manage_members: { type: Boolean, default: false },
})

const isFormModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const selectedProject = ref(null)
const memberRoleDrafts = ref({})

const newMember = reactive({
  employee_profile_id: '',
  role_name: '',
  joined_at: '',
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
const employeeProjectOverview = computed(() => props.employee_project_overview || [])
const canManageProjects = computed(() => props.can_manage_projects)
const canManageMembers = computed(() => props.can_manage_members || props.can_manage_projects)

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
  memberRoleDrafts.value = Object.fromEntries((project.members || []).map((member) => [member.id, member.role_name || '']))
  newMember.employee_profile_id = ''
  newMember.role_name = ''
  newMember.joined_at = ''
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

  router.post(route('projects.members.store', selectedProject.value.id), {
    employee_profile_id: newMember.employee_profile_id || null,
    role_name: newMember.role_name,
    joined_at: newMember.joined_at || null,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Da them nhan su vao du an.')
      newMember.employee_profile_id = ''
      newMember.role_name = ''
      newMember.joined_at = ''
      isDetailModalOpen.value = false
    },
    onError: () => {
      toast.error('Khong the them nhan su vao du an.')
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
