<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import moment from 'moment'
import { useProductionStore } from '@/storage/production'
import { useAuthStore } from '@/storage/auth'
import ProductsTable from './ProductsTable.vue'
import {useClientsStore} from '@/storage/clients.js'
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/components/ui/tabs"
import TimedAlert from '@/components/TimedAlert.vue'
import ProductInfo from './ProductInfo.vue'

const production = useProductionStore()
const loading = ref(false)
const alert_type = ref(null)
const alert_message = ref(null)
const clients = useClientsStore()
const row = ref()
const openModalView = ref()

watch(
  () => production.product_alert,
  (newVal) => {
    console.log(newVal)
    if (newVal) {
      alert_type.value = 'success'
      alert_message.value = 'A new product was added!'
      setTimeout(() => {
        alert_type.value = ''
        alert_message.value = ''
        production.product_alert = false
      }, 3000)
    } else {
      alert_type.value = null
      alert_message.value = null
    }
  }
)

onMounted(async() => {
  loading.value = true
  if(production.products.lenght <= 0){
    fetch()
  }
  loading.value = false
})

const fetch = async() => {
  loading.value = true
  const response = await production.fetch()
  loading.value = false
}

const cols = ref([
  { field: 'id', title: 'ID' },
  { field: 'drawing_nr', title: 'Drawing number', hide: false },
  { field: 'part_nr', title: 'Part number', hide: false },
  { field: 'revision', title: 'Revision', hide: false },
  { field: 'description', title: 'Description', hide: false },
  { field: 'additional_info', title: 'Additional info', hide: false },
  { field: 'weight', title: 'Weight', hide: true },
  { field: 'client', title: 'Client', hide: false },
  { field: 'updated_at', title: 'Updated at', hide: true },
  { field: 'created_at', title: 'Created at', hide: true },
])

const update_row = (updated_row) => {
  row.value = updated_row
  console.log(row.value)
  openModalView.value = true
}

const currentPageTitle = 'Products'
</script>

<template>
    <AdminLayout>
      <TimedAlert
        :type="alert_type"
        :message="alert_message"
      />
      <div v-if="!loading">
      <PageBreadcrumb :pageTitle="currentPageTitle" />

      <Modal v-if="openModalView">
        <template #body>
          <ProductInfo @close="openModalView = false" :product="row"></ProductInfo>
        </template>
      </Modal>


      <!--<shadTable :cols="cols"></shadTable> -->
      </div>
      <ProductsTable @update="update_row" @refresh="fetch" :cols="cols" v-if="!loading" />
      
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