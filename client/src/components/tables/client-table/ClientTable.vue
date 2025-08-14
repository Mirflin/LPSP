<script setup> 

import { computed, defineProps, ref, toRaw, reactive, watch, watchEffect, isReactive, isRef, unref, onMounted } from 'vue'
import Vue3Datatable from '@bhplugin/vue3-datatable'
import '@bhplugin/vue3-datatable/dist/style.css'
import { useClientsStore } from '@/storage/clients'
import { RefreshIcon, TrashIcon } from '../../../icons'
import PlusIcon from '../../../icons/PlusIcon.vue'
import Modal from '../../profile/Modal.vue'
import moment from 'moment/moment'
import TimedAlert from '../../TimedAlert.vue'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { Archive } from 'lucide-vue-next'
import ArchiveIcon from '@/icons/ArchiveIcon.vue'
import { excelParser } from "@/lib/excel-parser";
import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"

const clientsStore = useClientsStore()
const emit = defineEmits(['refresh', 'create', 'delete','update']);
const props = defineProps(["cols", "rows"])

const alert_type = ref('')
const alert_message = ref('')

const isOpen = ref(false)
const loading = ref(false);
const datatable = ref(null);
const total_rows = ref(clientsStore.clients ? clientsStore.clients.length : 0);
const rows = ref(props.rows ? toRaw(props.rows) : [])
total_rows.value = rows.value.length;

watch(() => clientsStore.clients, (newClients) => {
    rows.value = toRaw(newClients)
    total_rows.value = newClients.length;
}, { immediate: true ,deep:true })

const params = reactive({ current_page: 1, pagesize: 10, sort_column: 'id', sort_direction: 'asc', search: '' })

const cols = ref(props.cols)

const deleteAlert = ref(false)

const changeServer = (data) => {
    params.current_page = data.offset / 10
    params.pagesize = data.limit
    params.sort_column = data.field
    params.sort_direction = data.direction
};

const exportTable = (type) => {
    let records = datatable.value.getSelectedRows()
    let columns = datatable.value.getColumnFilters()
    if (!records?.length) {
        records = rows.value
    }
    const filename = 'Clients_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime();

    if (type === 'csv' || type === 'txt') {
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

        if (type === 'csv') {
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
            console.log(ws)
            XLSX.utils.book_append_sheet(wb, ws, 'Clients')

            const COL_WIDTH = 100;
            let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9, 10,11,12,13]
            if(!ws["!cols"]) ws["!cols"] = []

            COL_INDEXES.forEach(COL_INDEX => {
                if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
                ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
            });

            if(!ws["!rows"]) ws["!rows"] = []

            if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
            ws["!rows"][0].hpx = 30;

            ws['!autofilter'] = { ref:"A1:Q1" };

            XLSX.writeFile(wb, 'clients.xlsx', {cellStyles: true})
        }

        if (type === 'txt') {
            var data = 'data:application/txt;charset=utf-8,' + encodeURIComponent(result);
            var link = document.createElement('a')
            link.setAttribute('href', data);
            link.setAttribute('download', filename + '.txt')
            link.click();
        }
    } else if (type === 'print') {
        let rowhtml = '<p>' + filename + '</p>'
        rowhtml +=
            '<table style="width: 100%; " cellpadding="0" cellcpacing="0"><thead><tr style="color: #515365; background: #eff5ff; -webkit-print-color-adjust: exact; print-color-adjust: exact; "> ';
        cols.value
            .filter((d) => !d.hide)
            .map((d) => {
                rowhtml += '<th>' + d.title + '</th>'
            });
        rowhtml += '</tr></thead>'
        rowhtml += '<tbody>'

        records.map((item) => {
            rowhtml += '<tr>'
            cols.value
                .filter((d) => !d.hide)
                .map((d) => {
                    const val = d.field.split('.').reduce((acc, part) => acc && acc[part], item)
                    rowhtml += '<td>' + (val ? val : '') + '</td>'
                });
            rowhtml += '</tr>'
        });
        rowhtml +=
            '<style>body {font-family:Arial; color:#495057;}p{text-align:center;font-size:18px;font-weight:bold;margin:15px;}table{ border-collapse: collapse; border-spacing: 0; }th,td{font-size:12px;text-align:left;padding: 4px;}th{padding:8px 4px;}tr:nth-child(2n-1){background:#f7f7f7; }</style>';
        rowhtml += '</tbody></table>'
        const winPrint = window.open('', '', 'left=0,top=0,width=1000,height=600,toolbar=0,scrollbars=0,status=0')
        winPrint.document.write('<title>' + filename + '</title>' + rowhtml)
        winPrint.document.close()
        winPrint.focus()
        winPrint.onafterprint = () => {
            winPrint.close()
        };
        winPrint.print()
    } else if(type === "excel"){
         excelParser().exportDataFromJSON(rows, null, null);
    }
}; 

const reload = async () => {
    loading.value = true
    emit('refresh')
    setTimeout(() => {
        loading.value = false
    }, 600);
}

const showAlertModal = ref(false)

const showAlert = () => {
    let records = datatable.value.getSelectedRows();
    if(records.length != 0) {
        showAlertModal.value = true
    }else{
        alert_type.value = "error"
        alert_message.value = "You didn't selected any rows to delete"
        setTimeout(() => {
            alert_message.value = null
            alert_type.value = null
        }, 2000)
    }
}

const deleteRecords = async () => {
    loading.value = true
    let records = datatable.value.getSelectedRows();
    if(records.length > 3){
        loading.value = false
        alert_type.value = "error"
        alert_message.value = "Can't delete more than 3 rows in one time"
        setTimeout(() => {
            alert_message.value = null
            alert_type.value = null
        }, 2000)
    }else{
        emit('delete', records)
        setTimeout(() => {
            emit('refresh')
            loading.value = false
        }, 1000);
    }
}

const rowClick = async (e) => {
    emit('update', toRaw(e))
}

</script>

<template>
    <TimedAlert
        :type="alert_type"
        :message="alert_message"
    />
    <AlertDialog v-model:open="showAlertModal">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                This action cannot be undone. This will put client
                account into suspended account folder and if not reverted, never be used.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showAlertModal = false">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteRecords">Continue</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
    <div class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
          <div class="mb-5 relative flex justify-between">
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
                <div class="flex items-center gap-5">
                    <button @click="reload" class="p-2 rounded-md hover:bg-gray-100 transition">
                        <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition" />
                    </button>
                    <input
                        type="text"
                        v-model="params.search"
                        placeholder="Global client search"
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
            <div class="flex justify-between items-center">
              <div class="mb-5 flex items-center gap-3">
                <button type="button" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @click="exportTable('csv')">
                    Excel
                </button>
                <button type="button" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @click="exportTable('txt')">
                    TXT
                </button>
                <button type="button" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @click="exportTable('print')">
                    PRINT
                </button>
              </div>
              <button class="m-4 flex items-center gap-4">
                <TrashIcon @click="showAlert" class="w-9 h-auto text-gray-500 hover:text-gray-700 transition" />

                <PlusIcon @click="$emit('create')" class="w-9 h-auto  text-green-500 hover:text-green-700 transition" />
              </button>
            </div>

        <vue3-datatable
            ref="datatable"
            :rows="rows || []"
            :hasCheckbox="true"
            :columns="cols"
            :loading="loading"
            :totalRows="total_rows"
            :pagination="true"
            :search="params.search"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :columnFilter="true"
            :stickyHeader="true"
            height="450px"
            skin="bh-table-compact "
            :rowClass="'text-sm text-gray-800 dark:text-gray-300 text-center hover:bg-gray-100 hover:cursor-pointer'"
            @rowClick="rowClick"
            @sortChange="changeServer"
        >
          
          <template #email="data">
            <a :href="`mailto:${data.value.email}`" class="text-primary text-blue-600 hover:underline underline" @click.stop>{{ data.value.email }}</a>
          </template>
          <template #created_at="data">
            <span class="text-gray-500">{{ data.value.created_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
          </template>
          <template #updated_at="data">
            <span class="text-gray-500">{{ data.value.updated_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
          </template>
        </vue3-datatable>
    </div>
</template>

<style scoped>

</style>