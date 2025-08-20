<script setup>
import { CircleX } from 'lucide-vue-next'
import Multiselect from 'vue-multiselect'
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs"
import { Separator } from '@/components/ui/separator'
import ProductInfo from '../Products/ProductInfo.vue'
import {useClientsStore} from '@/storage/clients.js'
import PlanTemplate from './PlanTemplate.vue'

const emit = defineEmits(['close'])

const clients = useClientsStore()
const loading = ref(false)
const selectedProduct = ref([])
const products = ref([])

const displayInfo = ref(false)

const tab = ref("product")

onMounted(async() => {
    loading.value = true
    if(clients.clients || clients.clients.length < 1){
        await clients.fetchClients()
    }
    if(products.value || products.value.length < 1){
        await getProduct('')
    }
    loading.value = false
})

const getProduct = async(query) => {
    loading.value = true
    products.value = (await axios.post('/api/product-by-name', {'drawing_nr': query})).data.data
    displayInfo.value = true
    loading.value = false
}
const clearAll = () => {
    products.value = []
}
const limitText = (count) => {
    return `and ${count} other countries`
}

setInterval(() => {
    console.log(selectedProduct)
},5000)

</script>

<template>
    <div
        class="relative w-full h-full pt-20 overflow-y-auto rounded-3xl bg-white p-2 dark:bg-gray-900"
    >
        <Tabs v-model="tab" class="w-full p-3" v-auto-animate>
            <TabsList class="w-full gap-2 h-10">
                <TabsTrigger value="product">
                    Product
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo" value="client">
                    Client
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo" value="preview">
                    Preview
                </TabsTrigger>
                <TabsTrigger value="options">
                    Summary
                </TabsTrigger>
            </TabsList>
            <div v-if="!loading">
                <TabsContent value="product">
                    <multiselect v-model="selectedProduct" id="ajax" label="name" track-by="drawing_nr" placeholder="Type to search"
                        open-direction="bottom" :options="products" :multiple="false" :searchable="true" :loading="loading"
                        :internal-search="false" :clear-on-select="false" :close-on-select="true" :options-limit="10"
                        :limit="1" :limit-text="limitText" :max-height="600" :show-no-results="false" :hide-selected="true"
                        @search-change="getProduct" class="z-[10000] mb-5"
                    >
                        <template #singleLabel="props">
                            <strong>
                                {{ props.option.drawing_nr }}
                            </strong>
                        </template>
                        <template #option="props">
                            {{ props.option.drawing_nr }}
                        </template>
                    </multiselect>
                    <Separator></Separator>
                    
                    <div class="mt-5" v-if="displayInfo">
                        <div class="flex gap-2 text-lg mt-5 flex">
                            <Label class="w-60">Drawing number: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.drawing_nr }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Part number: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.part_nr }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Revision: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.revision }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Weight: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.weight }}</p>
                        </div>

                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Process count: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.processes ? selectedProduct.processes.length : 0 }}</p>
                        </div>

                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Files count: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.files ? selectedProduct.files.length : 0 }}</p>
                        </div>

                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Sub-products count: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.children ? selectedProduct.children.length : 0 }}</p>
                        </div>

                        <div class="flex gap-2 text-lg mt-5 items-start">
                            <Label class="w-60">Description: </Label>
                            <textarea v-model="selectedProduct.description" class="flex-1 justify-start items-start p-2 border-1" :readonly="true" placeholder="-"></textarea>
                        </div>
                        <div class="flex gap-2 text-lg mt-5 items-start">
                            <Label class="w-60">Additional information: </Label>
                            <textarea v-model="selectedProduct.additional_info" class="flex-1 justify-start p-2 border-1" :readonly="true" placeholder="-"></textarea>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="client">
                    <multiselect v-model="selectedProduct.client" id="ajax" label="name" track-by="name" placeholder="Type to search"
                        open-direction="bottom" :options="clients.clients" :multiple="false" :searchable="true" :loading="loading"
                        :clear-on-select="false" :close-on-select="true"
                        :limit="1" :max-height="600" :hide-selected="true" class="z-[10000] mb-5"
                    >
                        <template #singleLabel="props">
                            <strong>
                                {{ props.option.name }}
                            </strong>
                        </template>
                        <template #option="props">
                            {{ props.option.name }}
                        </template>
                    </multiselect>

                    <Separator></Separator>
                    <div v-if="displayInfo">
                        <div class="flex gap-2 text-lg mt-5 flex">
                            <Label class="w-60">Client: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.name }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Registration number: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.registration_nr }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Contact person: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.contact_person }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Address: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.address }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Delivery address: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.delivery_address }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Email: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.email }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Phone: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.phone }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Bank: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.bank }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Iban: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.iban }}</p>
                        </div>
                        <div class="flex gap-2 text-lg mt-5">
                            <Label class="w-60">Payment term: </Label>
                            <p class="flex-1 justify-start">{{ selectedProduct.client.payment_term }}</p>
                        </div>
                    </div>
                </TabsContent>

                <TabsContent value="preview" class="flex justify-center mt-5">
                    <PlanTemplate :product="selectedProduct"></PlanTemplate>
                </TabsContent>
            </div>

            <div
                v-else
                class="flex-col gap-4 w-full flex items-center justify-center scale-50 mb-15"
            >
                <div
                    class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full"
                >
                    <div
                        class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full"
                    >
                    </div>
                </div>
            </div>
        </Tabs>
    </div>

    <div @click="emit('close')" class="fixed right-5 top-5 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
        <CircleX size="30"></CircleX>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>