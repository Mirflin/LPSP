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
import shadTable from '@/components/tables/shadTable.vue'
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

const production = useProductionStore()
const loading = ref(false)
const alert_type = ref(null)
const alert_message = ref(null)
const clients = useClientsStore()
const row = ref()


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
  row.value.materials = production.materials.map((material) => {
    
  })
  console.log(row.value)
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
      <img src=""></img>
      <Modal v-if="row">
        <Tabs default-value="account" class="w-[400px]">
          <TabsList class="grid w-full grid-cols-2">
            <TabsTrigger value="account">
              Account
            </TabsTrigger>
            <TabsTrigger value="password">
              Password
            </TabsTrigger>
          </TabsList>
          <TabsContent value="account">
            <Card>
              <CardHeader>
                <CardTitle>Account</CardTitle>
                <CardDescription>
                  Make changes to your account here. Click save when you're done.
                </CardDescription>
              </CardHeader>
              <CardContent class="space-y-2">
                <div class="space-y-1">
                  <Label for="name">Name</Label>
                  <Input id="name" default-value="Pedro Duarte" />
                </div>
                <div class="space-y-1">
                  <Label for="username">Username</Label>
                  <Input id="username" default-value="@peduarte" />
                </div>
              </CardContent>
              <CardFooter>
                <Button>Save changes</Button>
              </CardFooter>
            </Card>
          </TabsContent>
          <TabsContent value="password">
            <Card>
              <CardHeader>
                <CardTitle>Password</CardTitle>
                <CardDescription>
                  Change your password here. After saving, you'll be logged out.
                </CardDescription>
              </CardHeader>
              <CardContent class="space-y-2">
                <div class="space-y-1">
                  <Label for="current">Current password</Label>
                  <Input id="current" type="password" />
                </div>
                <div class="space-y-1">
                  <Label for="new">New password</Label>
                  <Input id="new" type="password" />
                </div>
              </CardContent>
              <CardFooter>
                <Button>Save password</Button>
              </CardFooter>
            </Card>
          </TabsContent>
        </Tabs>
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