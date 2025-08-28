<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue';
import { ref, reactive, onMounted, watch, isRef, isReactive, unref, toRaw } from "vue"
import Vue3Datatable from "@bhplugin/vue3-datatable"
import { useProductionStore } from "@/storage/production"
import '@bhplugin/vue3-datatable/dist/style.css'
import RefreshIcon from '../../../icons/RefreshIcon.vue';
import { Badge } from '@/components/ui/badge';
import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"
import PlanCreate from './PlanCreate.vue';
import PlusIcon from '@/icons/PlusIcon.vue';
import moment from 'moment'
import PlanSheet from './PlanSheet.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import axios from 'axios';
import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from '@/components/ui/drawer'

const production = useProductionStore()
const datatable = ref(null)
const loading = ref(false)

const sheetOpen = ref(false)

const cols = ref([
  { field: "id", title: "ID" , hide: true},
  { field: "user.last_name", title: "Created by", hide: true },
  { field: "po_nr", title: "PO Number", hide: false },
  { field: "po_date", title: "PO delivery", hide: false },
  { field: "client.name", title: "Client", hide: false },
  { field: "product.drawing_nr", title: "Product", hide: false },
  { field: "produced", title: "Produced", hide: true },
  { field: "sended", title: "Sended", hide: true },
  { field: "price", title: "Price", hide: true },
  { field: "total", title: "Total", hide: false },
  { field: "invoice", title: "Invoice", hide: false },
  { field: "extra_process", title: "Extra process", hide: true },
  { field: "statuss_label", title: "Status", hide: false },
  { field: "outsource_statuss_label", title: "Outsource Status", hide: false },
  { field: "created_at", title: "Created at", hide: true },
  { field: "updated_at", title: "Updated at", hide: true },
])


const filters = ref({})

const params = reactive({
  current_page: 1,
  pagesize: 10,
  sort_column: "id",
  sort_direction: "asc",
  search: "",
})

const completed = ref(false)

const alert_message = ref(null)
const alert_type = ref(null)

const rows = ref([])         
const total_rows = ref(0)   
const searchInput = ref("")
const isOpen = ref(false)

const showCreateModal = ref()

let timeout = null

onMounted(async () => {
    await fetchData()
})

watch(searchInput, (newValue) => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    params.search = newValue
  }, 700)
})

const refreshTimeout = ref(false)

const sheetRow = ref(null)

const fetchData = async () => {
  loading.value = true
  try {
    const response = await production.fetchPlan({
      page: params.current_page,
      perPage: params.pagesize,
      sort: params.sort_column,
      direction: params.sort_direction,
      search: params.search,
      filters: filters.value,
      completed: completed.value
    })

    rows.value = production.plans
    total_rows.value = production.total
  } finally {
    loading.value = false
  }
  refreshTimeout.value = true
  setTimeout(() => {
    refreshTimeout.value = false
  }, 1500)
}

const changeServer = (data) => {
  params.current_page = data.current_page
  params.pagesize = data.pagesize
  params.sort_column = data.field
  params.sort_direction = data.direction
  fetchData()
}

const badgeStyle = (label) => {
  switch(label){
    case "Pending":
      return 'bg-yellow-500'
    case "Aproved":
      return 'bg-orange-400'
    case "In Production":
    case "Waiting Supplier":
      return 'bg-blue-500'
    case "Completed":
    case "Delivered":
      return 'bg-green-500'
    case "Cancelled":
    case "Outsource Cancelled":
      return 'bg-red-500'
    case "Not Outsourced":
      return 'bg-gray-500'
  }
}

const timed = async() => {
  setTimeout(() => {
    fetchData()
  }, 200)
}

const exportTable = () => {
    let records = datatable.value.getSelectedRows()
    let columns = datatable.value.getColumnFilters()
    if (!records?.length) {
        records = production.plans
    }
    const filename = 'Plan_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime();
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
        columns.forEach(colum => {
            if(colum.hide){
                delete item[colum.field]
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
    XLSX.utils.book_append_sheet(wb, ws, 'Plan')

    const COL_WIDTH = 100;
    let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9, 10, 11, 12,13 ,14,15]
    if(!ws["!cols"]) ws["!cols"] = []

    COL_INDEXES.forEach(COL_INDEX => {
        if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
        ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
    });

    if(!ws["!rows"]) ws["!rows"] = []

    if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
    ws["!rows"][0].hpx = 30;

    ws['!autofilter'] = { ref:"A1:Q1" };

    XLSX.writeFile(wb, filename+'.xlsx', {cellStyles: true})
};

const setRow = (data) => {
  sheetRow.value = data
  sheetOpen.value = true
}

const updateStatus = async(data) => {
  const response = await axios.post('/api/plan-status', {id: data.value.id, status: data.value.statuss_label, outstatus: data.value.outsource_statuss_label})
}

const updateSubStatus = async(data) => {
  const response = await axios.post('/api/subplan-status', {id: data.plan_id, subId: data.id, status: data.statuss_label, outsource_status: data.outsource_statuss_label, produced: data.produced})
}

const updateProduced = async() => {
  const response = await axios.patch('/api/plan-produced', {id: sheetRow.value.id, produced: sheetRow.value.produced, sended: sheetRow.value.sended})
  console.log(response)
}

const download_print_plan = async(po_nr, type) => {
  const response = await axios.post('/api/plan-download', {plan_nr: po_nr}, {
    responseType: "blob",
    withCredentials: true
  })

  if(response.status == 200){
    if(type == 'download'){
      const fileURL = window.URL.createObjectURL(response.data);
      const a = document.createElement("a");
      a.href = fileURL;
      a.download = 'plan-'+po_nr+'.pdf';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);

      window.URL.revokeObjectURL(fileURL);
    }else{
      const blob = new Blob([response.data], { type: 'application/pdf' })
      const url = window.URL.createObjectURL(blob)

      const iframe = document.getElementById('pdfFrame3');
      iframe.src = url;
      iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
      };
    }
  }else{
    alert_message.value = 'Plan not found!'
    alert_type.value = 'error'
    setTimeout(() => {
      alert_message.value = null
      alert_type.value = null
    }, 2000)
  }
}

</script>

<template>
    <AdminLayout>
        <iframe id="pdfFrame3" style="display: none;"></iframe>
        <TimedAlert
          :type="alert_type"
          :message="alert_message"
        />

        <Modal v-if="showCreateModal">
          <template #body>
            <PlanCreate @close="showCreateModal = false"></PlanCreate>
          </template>
        </Modal>

        <div class="mb-5 relative flex justify-between">
          <div class="flex items-center gap-5">
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
            <Button disabled @click="exportTable" class="bg-green-500 hover:bg-green-300">Export to excel</Button>
          </div>

          <div class="flex items-center gap-2">
            <Checkbox :default-value="false" @update:modelValue="timed" v-model="completed"></Checkbox>
            <Label>Completed plans?</Label>
          </div>

          <div class="flex items-center gap-5">
            <PlusIcon @click="showCreateModal = true" class="text-green-500 w-5 cursor-pointer scale-300 rounded-md hover:bg-gray-100 transition"></PlusIcon>
            <button @click="fetchData" :disabled="refreshTimeout" class="p-2 rounded-md hover:bg-gray-100 transition">
              <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition" />
            </button>
            <input
              type="text"
              v-model="searchInput"
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
            :pagination="false"
            skin="bh-table-compact"
            :search="params.search"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :isServerMode="true"
            :stickyHeader="true"
            class="table-test min-h-100"
            :rowClass="'text-sm text-gray-800 dark:text-gray-300 text-center hover:bg-gray-100 hover:cursor-pointer dark:hover:bg-blue-900'"
            @change="changeServer"
            @rowClick="setRow"
        >

            <template #price="data">
              <span>{{ data.value.price }} €</span>
            </template>

            <template #total="data">
              <span>{{ data.value.total }} €</span>
            </template>

            <template #statuss_label="data">
              <Select v-model="data.value.statuss_label" @update:model-value="updateStatus(data)">
                <SelectTrigger>
                  <Badge :class="badgeStyle(data.value.statuss_label)">
                    <SelectValue></SelectValue>
                  </Badge>
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Status</SelectLabel>
                      <SelectItem value="Pending">
                        Pending
                      </SelectItem>
                      <SelectItem value="Aproved">
                        Aproved
                      </SelectItem>
                      <SelectItem value="In Production">
                        In Production
                      </SelectItem>
                      <SelectItem value="Completed">
                        Completed
                      </SelectItem>
                      <SelectItem value="Cancelled">
                        Cancelled
                      </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </template>

            <template #outsource_statuss_label="data">
              <Select v-model="data.value.outsource_statuss_label" @update:model-value="updateStatus(data)">
                <SelectTrigger>
                  <Badge :class="badgeStyle(data.value.outsource_statuss_label)">
                    <SelectValue></SelectValue>
                  </Badge>
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Status</SelectLabel>
                      <SelectItem value="Not Outsourced">
                        Not Outsourced
                      </SelectItem>
                      <SelectItem value="Waiting Supplier">
                        Waiting Supplier
                      </SelectItem>
                      <SelectItem value="Delivered">
                        Delivered
                      </SelectItem>
                      <SelectItem value="Outsource Cancelled">
                        Outsource Cancelled
                      </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </template>
            
            <template #created_at="data">
                <span class="text-gray-500">{{ data.value.created_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
            <template #updated_at="data">
                <span class="text-gray-500">{{ data.value.updated_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
        </vue3-datatable>

        <Drawer v-model:open="sheetOpen">
          <DrawerContent class="h-250">
            <DrawerHeader class="hidden">
              <DrawerTitle>Are you absolutely sure?</DrawerTitle>
              <DrawerDescription>This action cannot be undone.</DrawerDescription>
            </DrawerHeader>
            
            <div class="flex justify-center items-center w-full">
              <div class="p-4 w-250">
                <h1 class="text-xl mb-5">Plan nr. {{ sheetRow.po_nr }}</h1>
                <div class="flex justify-between">

                  <div class="flex gap-5">
                    <Button @click="download_print_plan(sheetRow.po_nr, 'print')" class="bg-green-500 hover:bg-green-400">Print plan</Button>
                    <Button @click="download_print_plan(sheetRow.po_nr, 'download')" class="bg-blue-500 hover:bg-blue-400">Download plan</Button>
                  </div>

                  <div class="flex gap-3">
                    <Label>Produced: </Label>
                    <Input @change="updateProduced" type="number" v-model="sheetRow.produced" class="w-20"></Input>
                  </div>

                  <div class="flex gap-3">
                    <Label>Sended: </Label>
                    <Input @change="updateProduced" type="number" v-model="sheetRow.sended" class="w-20"></Input>
                  </div>

                  <Button disabled class="bg-red-500 hover:bg-red-400">Delete</Button>
                </div>
                <h1 class="mt-5">Subproduction</h1>
                <div class="border-1 h-85 rounded-md p-5 overflow-auto mt-1" v-auto-animate>
                  <div v-for="sub in sheetRow.subplan" class="w-full border-1 h-15 rounded-md flex p-2 justify-between items-center hover:bg-gray-100">
                    {{ sub.product.drawing_nr }}
                    <Label>Produced: </Label>
                    <Input type="number" @change="updateSubStatus(sub)" class="w-20" v-model="sub.produced"></Input>
                    <Label>Status: </Label>
                    <Select v-model="sub.statuss_label" @update:model-value="updateSubStatus(sub)">
                      <SelectTrigger>
                        <Badge class="w-20" :class="badgeStyle(sub.statuss_label)">
                          <SelectValue></SelectValue>
                        </Badge>
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Status</SelectLabel>
                            <SelectItem value="Pending">
                              Pending
                            </SelectItem>
                            <SelectItem value="Aproved">
                              Aproved
                            </SelectItem>
                            <SelectItem value="In Production">
                              In Production
                            </SelectItem>
                            <SelectItem value="Completed">
                              Completed
                            </SelectItem>
                            <SelectItem value="Cancelled">
                              Cancelled
                            </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <Label>Outsource: </Label>
                    <Select v-model="sub.outsource_statuss_label" @update:model-value="updateSubStatus(sub)">
                      <SelectTrigger>
                        <Badge class="w-30" :class="badgeStyle(sub.outsource_statuss_label)">
                          <SelectValue></SelectValue>
                        </Badge>
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Status</SelectLabel>
                            <SelectItem value="Not Outsourced">
                              Not Outsourced
                            </SelectItem>
                            <SelectItem value="Waiting Supplier">
                              Waiting Supplier
                            </SelectItem>
                            <SelectItem value="Delivered">
                              Delivered
                            </SelectItem>
                            <SelectItem value="Outsource Cancelled">
                              Outsource Cancelled
                            </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </div>
            </div>

            <DrawerFooter>
              <DrawerClose class="mb-10">
                <Button variant="outline">
                  Close
                </Button>
              </DrawerClose>
            </DrawerFooter>
          </DrawerContent>
        </Drawer>

    </AdminLayout>
</template>

<style>
.table-test .bh-table-responsive{
    height: auto !important;
    overflow: none;
}
.table-test .bh-table-responsive .bh-table-compact{
  height: auto;
}
</style>