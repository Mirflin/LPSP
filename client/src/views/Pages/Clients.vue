<script setup>
import AdminLayout from '../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ClientTable from '@/components/tables/client-table/ClientTable.vue'
import { onMounted, reactive, ref } from 'vue'
import { useClientsStore } from '@/storage/clients'

const clientsStore = useClientsStore()
const loading = ref(true)
const clients = reactive({})

onMounted(async () => {
  fetch()
})

const fetch = async () => {
  try{
    await clientsStore.fetchClients()
    console.log('Clients fetched successfully:', clientsStore.clients)
  } catch (error) {
    console.error('Error fetching clients:', error)
  } finally {
    loading.value = false
  }
}

const currentPageTitle = 'Clients'
</script>

<template>
    <AdminLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        
        <ClientTable v-if="!loading" />

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