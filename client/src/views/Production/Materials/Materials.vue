<script setup>
import AdminLayout from '../../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import MaterialTable from './MaterialTable.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import moment from 'moment'
import { useAuthStore } from '../../../storage/auth'
import {useProductionStore} from '../../../storage/production'

const production = useProductionStore()
const loading = ref(true)
const rows = ref()
const createModal = ref(false)
const form = reactive({
  name: '',
  description: '',
})
const alert_type = ref(null)
const alert_message = ref(null)
const auth = useAuthStore()

const cols = [
  { field: 'id', title: 'ID', isUnique: true, hide: false },
  { field: 'name', title: 'Name', hide: false },
  {field: 'description', title: 'Description', hide: false},
  { field: 'updated_at', title: 'Updated at', hide: true },
  { field: 'created_at', title: 'Created at', hide: true },
]

const fetch = async () => {
  if(!production.materials){
    const response = await production.fetch()
    if(response) {
      console.error('Error fetching production:', response)
    }
  }
  loading.value = false
}

onMounted(() => {
    fetch()
    rows.value = production.materials
    console.log(rows.value)
})

const updating = ref(false)

const createMaterial = async () => {
  if(form.name){
    const response = null
    if(updating.value){
      const response = await production.updateMaterial(form)
    }else{
      const response = await production.createMaterial(form)
    }
    if(!response) {
      createModal.value = false
      alert_type.value = "success"
      alert_message.value = "Material added/updated successfully!"
      fetch()
      setTimeout(() => {
          alert_message.value = null
          alert_type.value = null
      }, 2000)
    } else{
      alert_type.value = "error"
      alert_message.value = "Failed to create/update material"
      setTimeout(() => {
          alert_message.value = null
          alert_type.value = null
      }, 3000)
    }
  }else{
    alert_type.value = "error"
    alert_message.value = "Please provide data to all required fields!"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 3000)
  }
  updating.value = false
}

const updateForm = async(material) => {
  updating.value = true
  if(material){
    form.id = material.id
    form.name = material.name
    createModal.value = true
  }else{
    alert_type.value = "info"
    alert_message.value = "Please pick a material"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 2000)
  }
}

const row_delete = async (rows) => {
  const response = await production.deleteMaterials(rows)
  if(!response) {
    alert_type.value = "success"
    alert_message.value = "Material(s) deleted successfully!"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 2000)
  } else{
    alert_type.value = "error"
    alert_message.value = "Failed to delete material(s)"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 3000)
  }
}


const currentPageTitle = 'Materials'
</script>

<template>
    <AdminLayout>
      <TimedAlert
        :type="alert_type"
        :message="alert_message"
      />
      <PageBreadcrumb :pageTitle="currentPageTitle" />

      <Modal v-if="createModal" @close="createModal = false">
        <template #body>
          <div
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
          >
            <!-- close btn -->
            <button
              @click="createModal = false"
              class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300"
            >
              <svg
                class="fill-current"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                  fill=""
                />
              </svg>
            </button>
            <div class="px-2 pr-14">
              <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Create new material
              </h4>
            </div>
            <form class="flex flex-col" @submit="saveProfile" @submit.prevent>
              <div class="custom-scrollbar h-auto overflow-y-auto p-2">
                <div>
                  <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                    General Information
                  </h5>
                  <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Name <span class="text-error-500">*</span>
                      </label>
                      <input
                        type="text"
                        v-model="form.name"
                        required
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                    </div>

                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Description
                      </label>
                      <input
                        type="text"
                        v-model="form.description"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                    </div>
                </div>
              </div>
              </div>
              <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                <button
                  @click="createModal = false"
                  type="button"
                  class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
                >
                  Cancel
                </button>
                <button
                  @click="createMaterial"
                  type="submit"
                  class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
                >
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </template>
      </Modal>

      <MaterialTable @delete="row_delete" @create="createModal = true" @update="updateForm" @refresh="fetch" :cols="cols" :rows="rows" v-if="!loading" />
      <div 
          v-else
          class="flex justify-center items-center"
      >
          <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
              <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
              </div>
          </div>
      </div>
    </AdminLayout>
</template>

<style scoped> 

</style>