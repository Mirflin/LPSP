<script setup>
import { CircleX } from 'lucide-vue-next'
import Multiselect from 'vue-multiselect'
import {onMounted, ref, reactive} from 'vue'
import axios from 'axios'
import {
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs"
import { Separator } from '@/components/ui/separator'
import ProductInfo from '../Products/ProductInfo.vue'
import {useClientsStore} from '@/storage/clients.js'
import PlanTemplate from './PlanTemplate.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import moment from 'moment'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'
import PlanFiles from './PlanFiles.vue'
import {useProductionStore} from '@/storage/production.js'

const emit = defineEmits(['close'])
const production = useProductionStore()
const clients = useClientsStore()
const loading = ref(false)
const selectedProduct = ref([])
const products = ref([])

const editMode = ref(false)

const generalOptions = ref({
    po_nr: '',
    po_date: moment().format('DD.MM.YYYY'),
    count: '',
    enableSub: true,
    price_for_one: 0,
    total: 0,
    extra_process: '',
})

const pdf = ref(null)

const displayInfo = ref(false)

const tab = ref("general")

const initLoading = ref(false)

onMounted(async() => {
    initLoading.value = true
    if(clients.clients || clients.clients.length < 1){
        await clients.fetchClients()
    }
    if(products.value || products.value.length < 1){
        await getProduct('')
    }
    initLoading.value = false
})

const getProduct = async(query) => {
    loading.value = true
    products.value = (await axios.post('/api/product-by-name', {'drawing_nr': query})).data.data
    console.log(products.value)
    loading.value = false
}
const clearAll = () => {
    products.value = []
}
const limitText = (count) => {
    return `and ${count} other countries`
}

const pdfLoad = ref(false)

const generatePDF = async() => {
    pdfLoad.value = true
    const response = await axios.post(`/api/plans/download`, 
        { plan: selectedProduct.value, settings: generalOptions.value },
        { responseType: 'blob' }
    )

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    const iframe = document.getElementById('pdfFrame');
    iframe.src = url;
    iframe.onload = () => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    };


    pdfLoad.value = false
}

const save = async() => {
    const data = {
        'po_date': generalOptions.value.po_date,
        'po_nr': generalOptions.value.po_nr,
        'client_id': selectedProduct.value.client.id,
        'product_id': selectedProduct.value.id,
        'price': generalOptions.value.price_for_one,
        'total': generalOptions.value.total,
        'extra_process': generalOptions.value.extra_process
    }

    const response = await production.createPlan(data)
    if(response){
        console.log(response)
    }
    
    emit('close')
}

console.log(selectedProduct.value)
</script>

<template>
    <iframe id="pdfFrame" style="display: none;"></iframe>
    <div
        class="relative w-full h-full pt-20 overflow-y-auto rounded-3xl bg-white p-2 dark:bg-gray-900"
    >
        <Tabs v-model="tab" class="w-full p-3" v-auto-animate>
            <TabsList class="w-full gap-2 h-10">
                <TabsTrigger value="general">
                    General
                </TabsTrigger>
                <TabsTrigger value="product">
                    Product
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo" value="client">
                    Client
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo || !generalOptions.po_date || !generalOptions.po_nr || !generalOptions.count " value="preview">
                    Pdf
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo || !generalOptions.po_date || !generalOptions.po_nr || !generalOptions.count " value="files">
                    Files
                </TabsTrigger>
                <TabsTrigger :disabled="!displayInfo || !generalOptions.po_date || !generalOptions.po_nr || (!generalOptions.count && !generalOptions.count < 1)" value="summary">
                    Summary
                </TabsTrigger>
            </TabsList>

            <div v-if="!initLoading">
                <TabsContent value="general" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-2">
                        <Label>PO number: <span class="text-error-500">*</span></Label>
                        <Input v-model="generalOptions.po_nr" placeholder="12345..."></Input>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label>Product count: <span class="text-error-500">*</span></Label>
                        <Input v-model="generalOptions.count" placeholder="10, 20, 30..."></Input>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label>PO term: <span class="text-error-500">*</span></Label>
                        <VueDatePicker :hide-navigation="['time']" v-model="generalOptions.po_date" model-type="dd.MM.yyyy" :enable-time="false"></VueDatePicker>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label>Extra process: </Label>
                        <Input v-model="generalOptions.extra_process"></Input>
                    </div>
                </TabsContent>

                <TabsContent value="product">
                    <multiselect v-model="selectedProduct" id="ajax" label="name" track-by="drawing_nr" placeholder="Type to search"
                        open-direction="bottom" :options="products" :multiple="false" :searchable="true" :loading="loading"
                        :internal-search="false" :clear-on-select="false" :close-on-select="true" :options-limit="10"
                        :limit="1" :max-height="600" :show-no-results="false" :hide-selected="true"
                        @search-change="getProduct" class="z-[10000] mb-5"
                    >
                        <template #singleLabel="props">
                            <strong>
                                {{ props.option.drawing_nr }}
                            </strong>
                        </template>
                        <template #option="props">
                            <p class="w-full h-full" @click="displayInfo = true">
                                {{ props.option.drawing_nr }}
                            </p>
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

                <TabsContent value="preview" class="flex flex-col items-center justify-center mt-5">
                    <div class="flex justify-start hidden">
                        <div class="flex gap-5 w-full">
                            <Label>Enable subproducts?</Label>
                            <Checkbox v-model="generalOptions.enableSub"></Checkbox>
                        </div>
                    </div>

                    <div class="flex flex-col gap-5">
                        <p class="mb-2">#1</p>
                        <PlanTemplate ref="pdf" :editMode="editMode" :child="false" :settings="generalOptions" :product="selectedProduct"></PlanTemplate>
                        <div v-if="generalOptions.enableSub" v-for="(child, index) in selectedProduct.children">
                            <Separator class="mt-5 mb-10"></Separator>
                            <p class="mb-2"># {{ index+2 }}</p>
                            <PlanTemplate ref="pdf" :editMode="editMode" :child="true" :settings="generalOptions" :product="child"></PlanTemplate>
                        </div>
                    </div>
                </TabsContent>

                <TabsContent value="files" class="flex justify-center mt-5">
                    <PlanFiles :selectedProduct="selectedProduct"></PlanFiles>
                </TabsContent>

                <TabsContent value="summary" class="flex flex-row flex-wrap gap-5 justify-center mt-5">
                    <div class="w-1/2">
                        <Label class="mb-3">Price for one</Label>
                        <Input type="number" v-model="generalOptions.price_for_one" @input="generalOptions.total = generalOptions.price_for_one * generalOptions.count" min="0"></Input>
                    </div>
                    <Separator></Separator>
                    <div class="w-1/2">
                        {{ generalOptions.price_for_one ? generalOptions.price_for_one : 0 }} * {{ generalOptions.count }} (product count)
                        <p class="mb-2"></p>
                        <Separator></Separator>
                        <Label class="mb-3 mt-3">Total</Label>
                        <p>{{ generalOptions.total }} €</p>
                    </div>
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

    <div v-if="tab == 'preview'" class="fixed left-10 bottom-10">
        <Button @click="editMode = !editMode" :class="editMode ? 'bg-green-500 hover:bg-green-300' : 'bg-red-500 hover:bg-red-300'">Edit mode</Button>
    </div>

    <div class="fixed right-10 bottom-10">
        <Button :disabled="!displayInfo || !generalOptions.po_date || !generalOptions.price_for_one || !generalOptions.po_nr || (!generalOptions.count && !generalOptions.count < 1)" @click="save" class="bg-blue-500 hover:bg-blue-300">Finish</Button>
    </div>

    <div v-if="tab == 'preview'" class="fixed left-40 bottom-9 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
        <Button @click="generatePDF()" class="bg-green-500 hover:bg-green-300">{{ !pdfLoad ? "Print Pdf" : 'Loading...'}}</Button>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>