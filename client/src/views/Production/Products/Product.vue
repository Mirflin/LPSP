<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import moment from 'moment'
import { useProductionStore } from '@/storage/production'
import { useAuthStore } from '@/storage/auth'
import '@/Echo.js'
import ProductsTable from './ProductsTable.vue'

const production = useProductionStore()
const loading = ref(true)
const rows = ref()
const alert_type = ref(null)
const alert_message = ref(null)

watch(() => production.products, (newProducts) => {
  rows.value = toRaw(newProducts)
}, { immediate: true, deep: true })

const cols = [
  { field: 'id', title: 'ID', isUnique: true, hide: true },
  { field: 'drawing_nr', title: 'Drawing number', hide: false },
  { field: 'part_nr', title: 'Part number', hide: false },
  { field: 'revision', title: 'Revision', hide: false },
  { field: 'description', title: 'Description', hide: false },
  { field: 'additional_info', title: 'Additional info', hide: false },
  { field: 'weight', title: 'Weight', hide: true },
  { field: 'client_id', title: 'Client', hide: false },
  { field: 'updated_at', title: 'Updated at', hide: true },
  { field: 'created_at', title: 'Created at', hide: true },
]

onMounted(() => {
  fetch()
  rows.value = production.products
  let channel = window.Echo.private('products')
  channel.listen(".newProduct", async (data) => {
      alert_type.value = "info"
      alert_message.value = "Product update: "+data.product.drawing_nr
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
  const response = await production.fetch()
  if(response) {
    console.error('Error fetching products:', response)
  }
  loading.value = false
}

const row_delete = async (rows) => {
  const response = await production.deleteProducts(rows)
  if(!response) {
    alert_type.value = "success"
    alert_message.value = "Product(s) deleted successfully!"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 2000)
  } else{
    alert_type.value = "error"
    alert_message.value = "Failed to delete product(s)"
    setTimeout(() => {
        alert_message.value = null
        alert_type.value = null
    }, 3000)
  }
}

const currentPageTitle = 'Products'
</script>

<template>
    <AdminLayout>
      <TimedAlert
        :type="alert_type"
        :message="alert_message"
      />
      <PageBreadcrumb :pageTitle="currentPageTitle" />

      <ProductsTable @delete="row_delete" @refresh="fetch" :cols="cols" :rows="rows" v-if="!loading" />
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