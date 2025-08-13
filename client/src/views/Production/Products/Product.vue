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

const production = useProductionStore()
const loading = ref(true)
const alert_type = ref(null)
const alert_message = ref(null)
const clients = useClientsStore()
const row = ref()

const cols = ref([
  { field: 'id', header: 'ID' },
  { field: 'drawing_nr', header: 'Drawing number', hide: false },
  { field: 'part_nr', header: 'Part number', hide: false },
  { field: 'revision', header: 'Revision', hide: false },
  { field: 'description', header: 'Description', hide: false },
  { field: 'additional_info', header: 'Additional info', hide: false },
  { field: 'weight', header: 'Weight', hide: true },
  { field: 'client.name', header: 'Client', hide: false },
  { field: 'updated_at', header: 'Updated at', hide: true },
  { field: 'created_at', header: 'Created at', hide: true },
])

onMounted(async () => {
  loading.value = true
  if(!production.products){
    fetch()
    await clients.fetchClients()
  }
  loading.value = false
})

const fetch = async () => {
  const response = await production.fetch()
  if(response) {
    console.error('Error fetching products:', response)
  }
}

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
      <PageBreadcrumb :pageTitle="currentPageTitle" />

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


      <shadTable :cols="cols"></shadTable>
      <!--
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
      -->
    </AdminLayout>
</template>

<style scoped> 

</style>