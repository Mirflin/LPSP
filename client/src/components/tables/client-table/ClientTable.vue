<script setup> 

import { computed, defineProps, ref, toRaw, reactive, watch } from 'vue'
import Vue3Datatable from '@bhplugin/vue3-datatable';
import '@bhplugin/vue3-datatable/dist/style.css';
import { useClientsStore } from '@/storage/clients'

const clientsStore = useClientsStore()

const isOpen = ref(false)
const loading = ref(true);
const total_rows = ref(clientsStore.clients ? clientsStore.clients.length : 0);
const rows = ref(clientsStore.clients)

const params = reactive({ current_page: 1, pagesize: 10, sort_column: 'id', sort_direction: 'asc', search: '' })
const cols = ref([
  { field: 'id', title: 'ID', isUnique: true, hide: false },
  { field: 'name', title: 'Name', hide: false },
  { field: 'email', title: 'Email', hide: false },
  { field: 'phone', title: 'Phone', hide: false },
  { field: 'address', title: 'Address', hide: true },
  { field: 'contact_person', title: 'Contact person', hide: true },
  { field: 'delivery_address', title: 'Delivery address', hide: true },
  { field: 'registration_nr', title: 'Registration number', hide: false },
  { field: 'pvn_nr', title: 'PVN number', hide: false },
  { field: 'bank', title: 'Bank', hide: true },
  { field: 'iban', title: 'Iban', hide: false },
  { field: 'payment_term', title: 'Payment term', hide: false },
])

const changeServer = (data) => {
    params.current_page = data.current_page;
    params.pagesize = data.pagesize;
    params.sort_column = data.sort_column;
    params.sort_direction = data.sort_direction;
    console.log('changeServer', data);
    emit.change_params(params);
};

</script>

<template>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
          <div class="mb-5 relative flex justify-between">
            <button type="button" class="btn gap-2 flex items-center p-2 border-1" @click="isOpen = !isOpen">
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
              <input
                type="text"
                v-model="params.search"
                placeholder="Search client..."
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-5 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[430px]"
              />

            <ul v-if="isOpen" class="absolute left-0 mt-13 p-2.5 min-w-[150px] bg-white rounded shadow-md space-y-1 z-20">
              <li v-for="col in cols" :key="col.field">
                  <label class="flex items-center gap-2 w-full cursor-pointer text-gray-600 hover:text-black">
                      <input type="checkbox" class="form-checkbox" :checked="!col.hide" @change="col.hide = !$event.target.checked" />
                      <span>{{ col.title }}</span>
                  </label>
              </li>
            </ul>
          </div>

        <vue3-datatable
            :rows="rows"
            :columns="cols"
            :loading="!loading"
            :totalRows="total_rows"
            :pagination="true"
            :search="params.search"
            :sortable="true"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            height="700px"
            @change="changeServer"
        >
        </vue3-datatable>
    </div>
</template>

<style scoped>

</style>