<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch, isRef, isReactive, unref } from 'vue'
import moment from 'moment'
import { useProductionStore } from '@/storage/production'
import { useAuthStore } from '@/storage/auth'
import {useClientsStore} from '@/storage/clients.js'
import { Button } from "@/components/ui/button"
import TimedAlert from '@/components/TimedAlert.vue'
import RefreshIcon from '@/icons/RefreshIcon.vue'
import Vue3Datatable from '@bhplugin/vue3-datatable'
import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"
import PlusIcon from '@/icons/PlusIcon.vue'
import {useProductCreate} from '@/storage/product_create.js'
import '@bhplugin/vue3-datatable/dist/style.css'
import axios from 'axios'

const production = useProductionStore()
const loading = ref(false)
const alert_type = ref(null)
const alert_message = ref(null)
const clients = useClientsStore()
const row = ref()
const rows = ref()
const total_rows = ref(0)
const openModalView = ref()
const isOpen = ref(false)

const form = reactive({
  name: '',
  surname: '',
  email: '',
  password: '',
})

const searchInput = ref("")
const productStore = useProductCreate()
let timeout = null

const datatable = ref()
const params = reactive({
  current_page: 1,
  pagesize: 10,
  sort_column: "id",
  sort_direction: "asc",
  search: ""
})

watch(searchInput, (newValue) => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    params.search = newValue
  }, 700)
})

onMounted(async() => {
  production.fetch()
  clients.fetchClients()
  await fetchData()
})

const showCreateModal = ref(false)

const cols = ref([
  { field: 'id', title: 'ID' },
  { field: 'name', title: 'Name', hide: false },
  { field: 'last_name', title: 'Surname', hide: false },
  { field: 'email', title: 'Email', hide: false },
  { field: 'permission', title: 'Role', hide: false },
  { field: 'created_at', title: 'Created at', hide: true },
])

const update_row = (updated_row) => {
  row.value = updated_row
  openModalView.value = true
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await axios.post('/api/users-list', {
      page: params.current_page,
      perPage: params.pagesize,
    });

    rows.value = response.data.users
    total_rows.value = response.data.total
  } finally {
    loading.value = false
  }
}

const permissionOptions = (permission) => {
  if(permission == 1) return "Office staff"
  if(permission == 2) return "Quality assurance staff"
  if(permission == 3) return "Foreman"
}

const changeServer = (data) => {
  params.current_page = data.current_page
  params.pagesize = data.pagesize
  params.sort_column = data.field
  params.sort_direction = data.direction
  fetchData()
}

const exportTable = () => {
    let records = datatable.value.getSelectedRows()
    let columns = datatable.value.getColumnFilters()
    if (!records?.length) {
        records = production.products
    }
    const filename = 'Products_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime();
    const coldelimiter = ','
    const linedelimiter = '\n'
    let result = cols.value
        .filter((d) => !d.hide)
        .map((d) => {
            return d.title
        })
        .join(coldelimiter)
    result += linedelimiter
    records.map((item) => {
        cols.value
            .filter((d) => !d.hide)
            .map((d, index) => {
                if (index > 0) {
                    result += coldelimiter
                }
                const val = d.field.split('.').reduce((acc, part) => acc && acc[part], item)
                result += val
            });
        result += linedelimiter
    });

    if (result === null) return;

        const plainData = records.map(item => {
            delete item['name']
            delete item.client_id
            columns.forEach(colum => {
                if(colum.hide){
                    delete item[colum.field]
                }
                if(colum.title == "Client"){
                    item.client = item.client.name
                }
                if(columns.title == "Children"){
                  delete item.children
                }
                if(columns.title == "Files"){
                  delete item.files
                }
            });
            if(isRef(item)) {
                return toRaw(unref(item))
            }
            if(isReactive(item)) {
                return toRaw(item)
            }
            return item
        })
        let ws = XLSX.utils.json_to_sheet(plainData);
        let wb = XLSX.utils.book_new() 
        console.log(ws)
        XLSX.utils.book_append_sheet(wb, ws, 'Products')

        const COL_WIDTH = 100;
        let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9]
        if(!ws["!cols"]) ws["!cols"] = []

        COL_INDEXES.forEach(COL_INDEX => {
            if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
            ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
        });

        if(!ws["!rows"]) ws["!rows"] = []

        if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
        ws["!rows"][0].hpx = 30;

        ws['!autofilter'] = { ref:"A1:I1" };

        XLSX.writeFile(wb, filename+'.xlsx', {cellStyles: true})
};

const save = async () => {
    loading.value = true
    try {
      const response = await axios.post('/api/users-create', {
        name: form.name,
        surname: form.surname,
        email: form.email,
        password: form.password,
        permission: form.permission,
      });
      showCreateModal.value = false
      fetchData()
    } catch (error) {

    } finally {
      loading.value = false
      fetchData()
      cancel()
    }
}

const cancel = () => {
  form.name = ''
  form.surname = ''
  form.email = ''
  form.password = ''
  form.permission = ''
}

const currentPageTitle = 'History'
</script>

<template>
    <AdminLayout>
      <TimedAlert
        :type="alert_type"
        :message="alert_message"
      />
      
      <PageBreadcrumb :pageTitle="currentPageTitle" />

      <Modal v-if="openModalView">
        <template #body>
          <ProductInfo @update="update_row" @close="openModalView = false" @open="openModalView = true" :product="row"></ProductInfo>
        </template>
      </Modal>

      <div>
        <div class="mb-5 relative flex justify-between">
          <div class="flex gap-5 items-center">
            <button type="button" class="dark:bg-dark-900 h-11 w-40 flex rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @click="isOpen = !isOpen">
                Column Chooser
                <svg
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="transition"
                    :class="{ 'rotate-180': isOpen }"
                >
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
          </div>
            <div class="flex items-center gap-5">
              <PlusIcon @click="showCreateModal = true" class="text-green-500 w-5 cursor-pointer scale-300 rounded-md hover:bg-gray-100 transition"></PlusIcon>
              <button @click="fetchData; datatable.reset()" class="p-2 rounded-md hover:bg-gray-100 transition">
                <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition" />
              </button>
              <input
                type="text"
                v-model="searchInput  "
                placeholder="Global search"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>

            <ul v-if="isOpen" class="absolute left-0 mt-13 p-2.5 min-w-[150px] bg-white rounded shadow-md space-y-1 z-20 dark:bg-dark-900 h-auto w-40 rounded-lg border border-gray-200 py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 dark:border-gray-800 dark:bg-gray-800 dark:text-white dark:placeholder:text-white/30 dark:focus:border-brand-800">
              <li v-for="col in cols" :key="col.field">
                  <label class="flex items-center gap-2 w-full cursor-pointer">
                      <input type="checkbox" class="form-checkbox" :checked="!col.hide" @change="col.hide = !$event.target.checked" />
                      <span>{{ col.title }}</span>
                  </label>
              </li>
            </ul>
        </div>

        <vue3-datatable
            ref="datatable"
            :rows="rows"
            :columns="cols"
            :totalRows="total_rows"
            :loading="loading"
            :pagination="true"
            :pageSize="params.pageSize"
            skin="bh-table-compact"
            :search="params.search"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :isServerMode="true"
            :rowClass="'text-sm text-gray-800 dark:text-gray-300 text-center hover:bg-gray-100 hover:cursor-pointer dark:hover:bg-blue-900'" 
            @change="changeServer"
        >
            <template #permission="data">
                <span class="text-gray-500">{{ permissionOptions(data.value.permission) }}</span>
            </template>
            <template #user="data">
                {{ data.value.user.last_name }}
            </template>
            <template #created_at="data">
                <span class="text-gray-500">{{ data.value.created_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
            <template #updated_at="data">
                <span class="text-gray-500">{{ data.value.updated_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
        </vue3-datatable>
      </div>

      <Modal v-if="showCreateModal && !loading" @close="showCreateModal = false">
        <template #body>
            <div
                class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
            >
                <div class="px-2 pr-14">
                    <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Create new user
                    </h4>
                </div>
                <form class="flex flex-col" @submit.prevent>
                    <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">
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
                            Surname <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            v-model="form.surname"
                            required
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div>
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Email <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            required
                            v-model="form.email"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div>
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Password <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="password"
                            required
                            v-model="form.password"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>
                        <div>
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Permission <span class="text-error-500">*</span>
                            </label>
                            <Select v-model="form.permission" class="w-full">
                              <SelectTrigger>
                                <SelectValue placeholder="Select a user permissions" />
                              </SelectTrigger>
                              <SelectContent class="z-[99999]">
                                <SelectGroup>
                                  <SelectLabel>Permissions</SelectLabel>
                                    <SelectItem :value="1">
                                      Office staff
                                    </SelectItem>
                                    <SelectItem :value="2">
                                      Quality assurance staff
                                    </SelectItem>
                                  <SelectItem :value="3">
                                      Foreman
                                    </SelectItem>
                                  </SelectGroup>
                              </SelectContent>
                            </Select>
                            </div>
                        </div>
                    </div>
            
                    </div>
                    <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                    <button
                        @click="showCreateModal = false; cancel()"
                        type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
                    >
                        Cancel
                    </button>
                    <button
                        @click="save"
                        :disabled="params.name == '' || params.surname == '' || params.email == '' || params.password == ''"
                        type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
                    >
                        Save Changes
                    </button>
                    </div>
                </form>
            </div>
        </template>
    </Modal>

    </AdminLayout>
</template>