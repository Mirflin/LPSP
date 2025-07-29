<script setup>
import {onMounted, ref, reactive} from 'vue'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useAuthStore } from '../../storage/auth'
import { useCredsStore } from '../../storage/creds'
import Modal from '../../components/profile/Modal.vue'
import axios from 'axios'
const auth = useAuthStore()
const loading = ref(true)
const credsStore = useCredsStore()
const form = reactive({})
const isProfileInfoModal = ref(false)
const response = ref()

onMounted(async () => {
  loading.value = true
  try {
    await credsStore.fetchCreds()
  } catch (error) {
    console.error('Error fetching data:', error)
  }
  if(credsStore.creds) {
    form.name = credsStore.creds.name || ''
    form.address = credsStore.creds.address || ''
    form.vat_nr = credsStore.creds.vat_nr || ''
    form.bank = credsStore.creds.bank || ''
    form.iban = credsStore.creds.iban || ''
    form.export_address = credsStore.creds.export_address || ''
    form.phone = credsStore.creds.phone || ''
    form.registration = credsStore.creds.registration_nr || ''
  }
  loading.value = false
})

const saveProfile = async () => {
  loading.value = true
  try {
    response.value = await axios.patch('/api/credentials', form)
    credsStore.creds = form
    isProfileInfoModal.value = false
    await credsStore.fetchCreds()
    setTimeout(() => {
      response.value = null
    }, 3000)
  } catch (error) {
    console.error('Error saving credentials:', error)
  }
  loading.value = false
}

const cancel = () => {
    form.name = credsStore.creds.name || ''
    form.address = credsStore.creds.address || ''
    form.vat_nr = credsStore.creds.vat_nr || ''
    form.bank = credsStore.creds.bank || ''
    form.iban = credsStore.creds.iban || ''
    form.export_address = credsStore.creds.export_address || ''
    form.phone = credsStore.creds.phone || ''
    form.registration = credsStore.creds.registration_nr || ''
}

const currentPageTitle = ref('LPSP credentials')
</script>
<template>
    <admin-layout>
        <div v-auto-animate class="alert_center">
            <Alert
                v-if="response && response.status === 200"
                :variant="response && response.status === 200 ? 'success' : 'error'"
                title="Alert"
                :message="response && response.status === 200 ? 'Credentials updated successfully!' : 'Error updating credentials.'"
                :showLink="false"
            />
        </div>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        <div
            v-if="!loading"
            class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6"
        >
            <button @click="isProfileInfoModal = true" class="edit-button mb-5">
                <svg
                    class="fill-current"
                    width="18"
                    height="18"
                    viewBox="0 0 18 18"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                    fill=""
                    />
                </svg>
                Edit
            </button>
            <div>
                <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
                        Information
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Name</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.name }}</p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Address</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{credsStore.creds.address}}</p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Vat number</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.vat_nr }}</p>
                        </div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Iban</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.iban }}</p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                Bank
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ credsStore.creds.bank }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Export address</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.export_address }}</p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.phone }}</p>
                        </div>

                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Registration number</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ credsStore.creds.registration_nr }}</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div 
            v-else
            class="flex justify-center items-center"
        >
            <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
                <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
                </div>
            </div>
        </div>
        <Modal v-if="isProfileInfoModal && !loading" @close="isProfileInfoModal = false">
        <template #body>
            <div
                class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
            >
                <button
                    @click="isProfileInfoModal = false"
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
                    Edit main LPSP credentials
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
                            Export address <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            required
                            v-model="form.export_address"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div>
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Phone <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            required
                            v-model="form.phone"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>
                        </div>
                    </div>
                    <div class="mt-7">
                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                        Bank
                        </h5>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                        <div class="col-span-2 lg:col-span-1">
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Bank <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            required
                            v-model="form.bank"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div class="col-span-2 lg:col-span-1">
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Registration number <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            v-model="form.registration"
                            required
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div class="col-span-2 lg:col-span-1">
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Vat number <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            v-model="form.vat_nr"
                            required
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        <div class="col-span-2 lg:col-span-1">
                            <label
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                            Iban <span class="text-error-500">*</span>
                            </label>
                            <input
                            type="text"
                            v-model="form.iban"
                            required
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>

                        </div>
                    </div>
            
                    </div>
                    <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                    <button
                        @click="isProfileInfoModal = false; cancel()"
                        type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
                    >
                        Cancel
                    </button>
                    <button
                        @click="saveProfile"
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
    </admin-layout>
</template>

<style scoped>
.alert_center{
  position: fixed;
  height: auto;
  z-index: 10;
  width: 25rem;
  top: 10%;
  left: 50%;
  margin-left: -12.5rem;
}
</style>