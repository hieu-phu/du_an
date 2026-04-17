<template>
  <Head title="Cau hinh website" />

  <AdminLayout>
    <PageBreadcrumb title="Cau hinh website" :items="[{ text: 'Cau hinh', link: null }, { text: 'Website', link: null }]" />

    <form class="space-y-6" @submit.prevent="submit">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Thong tin co ban</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Ten he thong</label>
            <input v-model="form.site_name" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="text">
            <p v-if="form.errors.site_name" class="mt-1 text-sm text-red-600">{{ form.errors.site_name }}</p>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Logo</label>
            <input class="w-full rounded-lg border border-gray-300 px-3 py-2" type="file" accept="image/*" @change="onLogoChange">
            <div v-if="setting.logo_path" class="mt-2 text-xs text-gray-500">
              Dang co logo: <span class="font-medium">{{ setting.logo_path }}</span>
            </div>
            <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600">
              <input v-model="form.remove_logo" type="checkbox">
              Xoa logo hien tai
            </label>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Favicon</label>
            <input class="w-full rounded-lg border border-gray-300 px-3 py-2" type="file" accept="image/*" @change="onFaviconChange">
            <div v-if="setting.favicon_path" class="mt-2 text-xs text-gray-500">
              Dang co favicon: <span class="font-medium">{{ setting.favicon_path }}</span>
            </div>
            <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600">
              <input v-model="form.remove_favicon" type="checkbox">
              Xoa favicon hien tai
            </label>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Noi dung giao dien</h3>
        <div class="mt-4 grid grid-cols-1 gap-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Header content</label>
            <textarea v-model="form.header_content" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Footer content</label>
            <textarea v-model="form.footer_content" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white disabled:opacity-60" type="submit">
          {{ form.processing ? 'Dang luu...' : 'Luu cau hinh' }}
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  setting: { type: Object, required: true },
})

const form = useForm({
  site_name: props.setting.site_name || '',
  header_content: props.setting.header_content || '',
  footer_content: props.setting.footer_content || '',
  logo: null,
  favicon: null,
  remove_logo: false,
  remove_favicon: false,
})

const onLogoChange = (event) => {
  form.logo = event.target.files?.[0] || null
}

const onFaviconChange = (event) => {
  form.favicon = event.target.files?.[0] || null
}

const submit = () => {
  form.transform((data) => ({ ...data, _method: 'PUT' })).post(route('settings.update'), {
    forceFormData: true,
    onSuccess: () => {
      form.logo = null
      form.favicon = null
      form.remove_logo = false
      form.remove_favicon = false
    },
  })
}
</script>
