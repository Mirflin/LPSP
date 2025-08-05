<script setup>
import AdminLayout from '../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ClientTable from '@/components/tables/client-table/ClientTable.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import { useClientsStore } from '@/storage/clients'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import moment from 'moment'
import { useAuthStore } from '../../storage/auth'
import '@/Echo.js'

const clientsStore = useClientsStore()
const loading = ref(true)
const clients = reactive({})
let ws = null
const rows = ref(clientsStore.clients ? toRaw(clientsStore.clients) : [])
const createModal = ref(false)
const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  contact_person: '',
  delivery_address: '',
  registration_nr: '',
  pvn_nr: '',
  bank: '',
  iban: '',
  payment_term: ''
})
const alert_type = ref(null)
const alert_message = ref(null)
const auth = useAuthStore()

watch(() => clientsStore.clients, (newClients) => {
  rows.value = toRaw(newClients)
}, { immediate: true, deep: true })

const cols = [
  { field: 'id', title: 'ID', isUnique: true, hide: true },
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
  { field: 'updated_at', title: 'Updated at', hide: true },
  { field: 'created_at', title: 'Created at', hide: true },
]

onMounted(() => {
  fetch()
  
  let channel = window.Echo.private('clients')
  channel.listen(".newclient", async (data) => {
      alert_type.value = "info"
      alert_message.value = "Client update: "+data.client.name
      fetch()
      setTimeout(() => {
          alert_message.value = null
          alert_type.value = null
      }, 2000)
  })
})
onUnmounted(() => {
  window.Echo.leave('clients')
})

const fetch = async () => {
  const response = await clientsStore.fetchClients()
  if(response) {
    console.error('Error fetching clients:', response)
  }
  loading.value = false
}

const updating = ref(false)

const createClient = async () => {
  if(form.name && form.address && form.delivery_address && form.payment_term && form.payment_term >= 0){
    const response = null
    if(updating.value){
      const response = await clientsStore.updateClient(form)
    }else{
      const response = await clientsStore.createClient(form)
    }
    if(!response) {
      createModal.value = false
      alert_type.value = "success"
      alert_message.value = "Client added/updated successfully!"
      fetch()
      setTimeout(() => {
          alert_message.value = null
          alert_type.value = null
      }, 2000)
    } else{
      alert_type.value = "error"
      alert_message.value = "Failed to create client"
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

const updateForm = async(client) => {
  updating.value = true
  if(client){
    form.id = client.id
    form.address = client.address
    form.bank = client.bank
    form.contact_person = client.contact_person
    form.delivery_address = client.delivery_address
    form.email = client.email
    form.iban = client.iban
    form.name = client.name
    form.payment_term = client.payment_term
    form.phone = client.phone
    form.pvn_nr = client.pvn_nr
    form.registration_nr = client.registration_nr
    createModal.value = true
  }else{
    alert_type.value = "info"
    alert_message.value = "Please pick a client"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 2000)
  }
}

const row_delete = async (rows) => {
  const response = await clientsStore.deleteClients(rows)
  if(!response) {
    alert_type.value = "success"
    alert_message.value = "Client(s) deleted successfully!"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 2000)
  } else{
    alert_type.value = "error"
    alert_message.value = "Failed to delete client(s)"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 3000)
  }
}


const currentPageTitle = 'Clients'
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
                Create new client
              </h4>
            </div>
            <form class="flex flex-col" @submit="saveProfile" @submit.prevent>
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
                        Contact person
                      </label>
                      <input
                        type="text"
                        v-model="form.contact_person"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                    </div>

                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Phone
                      </label>
                      <input
                        type="text"
                        v-model="form.phone"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                    </div>

                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Email
                      </label>
                      <input
                        type="text"
                        v-model="form.email"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                    </div>
                    <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Registration Number
                        </label>
                        <input
                          type="text"
                          v-model="form.registration_nr"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>
                  </div>
                  <div class="mt-6">
                    <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                      Address Information
                    </h5>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Address <span class="text-error-500">*</span>
                        </label>
                        <input
                          type="text"
                          required
                          v-model="form.address"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>

                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Delivery Address <span class="text-error-500">*</span>
                        </label>
                        <input
                          type="text"
                          required
                          v-model="form.delivery_address"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>
                  </div>
                  <div class="mt-6">
                    <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                      Bank Information
                    </h5>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Bank
                        </label>
                        <input
                          type="text"
                          v-model="form.bank"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>

                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Payment Term <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model="form.payment_term"
                            type="number"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                          />

                        </div>
                      </div>
                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          PVN Number
                        </label>
                        <input
                          type="text"
                          v-model="form.pvn_nr"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>
                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Iban
                        </label>
                        <input
                          type="text"
                          v-model="form.iban"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>
                  </div>
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
                  @click="createClient"
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

      <ClientTable @delete="row_delete" @create="createModal = true" @update="updateForm" @refresh="fetch" :cols="cols" :rows="rows" v-if="!loading" />
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