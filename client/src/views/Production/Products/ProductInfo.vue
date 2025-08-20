<script setup>
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
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs"
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import { Button } from "@/components/ui/button"
import { CircleX } from "lucide-vue-next"
import FilesInput from "./Steps/FilesInput.vue"
import ProductFIles from "./ProductFIles.vue"
import ProductChilds from "./ProductChilds.vue"
import {useProductionStore} from '@/storage/production.js'
import Separator from "@/components/ui/separator/Separator.vue"
import { ChevronDown } from "lucide-vue-next"
import { ChevronUpIcon } from "lucide-vue-next"
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import ProductInfoInput from "./ProductInfoInput.vue"

const production = useProductionStore()
const emit = defineEmits(['close','open', 'update'])
const props = defineProps(['product'])
const editMode = ref(false)
const tab = ref('general')
const parrent = ref()

const showBOM = ref(true)
const showDrawings = ref(true)
const showOthers = ref(true)

const drawingRef = ref([])
const bomRef = ref([])
const otherRef = ref([])

const alert_message = ref()
const alert_type = ref()

const loading = ref(false)
const childs_loading = ref(false)

const process_id = ref()

const goTo = async(row) => {
    childs_loading.value = true
    
    const res = await production.getProductById(row.id)
    const newRow = res.data.data

    emit('update', newRow)
    parrent.value = props.product
    tab.value = 'general'

    childs_loading.value = false
}

const updateServer = () => {
    if(editMode.value == true){
        loading.value = true
        const response = production.updateRow(props.product)
        if(response){
            alert_message.value = response.message
            alert_type.value = 'error'

            setTimeout(() => {
                alert_message.value = ''
                alert_type.value = ''
            }, 3000)
        }else{
            alert_message.value = 'Successfully updated product details!'
            alert_type.value = 'success'

            setTimeout(() => {
                alert_message.value = ''
                alert_type.value = ''
            }, 3000)
        }
        editMode.value = false
        loading.value = false
    }else{
        editMode.value = true
    }
}

const updateProcess = (index) => {
  const selectedId = process_id.value
  console.log(props.product.processes[index])
  const found = production.processes.find(p => p.id === selectedId)
  if (found) {
    props.product.processes[index].name = found.name
    props.product.processes[index].process_id = found.id
  }
}

</script>

<template>
    <TimedAlert
        :type="alert_type"
        :message="alert_message"
    />
    <div
        class="relative w-full max-w-[1200px] h-2/3 overflow-y-auto rounded-3xl bg-white p-2 mr-5 ml-5 dark:bg-gray-900 lg:p-11"
    >
        <Tabs v-model="tab" class="w-full p-3" v-auto-animate>

            <TabsList class="w-full gap-2 h-10">
                <TabsTrigger value="general">
                    General
                </TabsTrigger>
                <TabsTrigger value="client">
                    Client
                </TabsTrigger>
                <TabsTrigger value="materials">
                    Materials
                </TabsTrigger>
                <TabsTrigger value="processes">
                    Processes
                </TabsTrigger>
                <TabsTrigger value="files">
                    Files
                </TabsTrigger>
                <TabsTrigger value="subproducts">
                    Sub-products
                </TabsTrigger>
            </TabsList>

            <TabsContent value="general" class="flex flex-col gap-5 h-full">
                <div class="flex gap-2 text-lg mt-5 flex">
                    <Label class="w-60">Drawing number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.drawing_nr }}</p>
                    <Input v-else v-model="props.product.drawing_nr"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Part number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.part_nr }}</p>
                    <Input v-else v-model="props.product.part_nr"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Revision number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.revision }}</p>
                    <Input v-else v-model="props.product.revision"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Weight: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.weight }}</p>
                    <Input v-else v-model="props.product.weight"></Input>
                </div>
                <Separator />
                <div class="flex gap-2 text-lg mt-5 items-start">
                    <Label class="w-60">Description: </Label>
                    <textarea v-model="props.product.description" class="flex-1 justify-start items-start p-2" :class="editMode ? 'border-1' : ''" :readonly="!editMode" placeholder="-"></textarea>
                </div>
                <div class="flex gap-2 text-lg mt-5 items-start">
                    <Label class="w-60">Additional information: </Label>
                    <textarea v-model="props.product.additional_info" class="flex-1 justify-start p-2" :class="editMode ? 'border-1' : ''" :readonly="!editMode" placeholder="-"></textarea>
                </div>
            </TabsContent>

            <TabsContent value="client" class="flex flex-col gap-5 h-full wrap">
                <div class="flex gap-2 text-lg mt-5 flex">
                    <Label class="w-60">Client: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.name }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Registration number: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.registration_nr }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Contact person: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.contact_person }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Address: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.address }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Delivery address: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.delivery_address }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Email: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.email }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Phone: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.phone }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Bank: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.bank }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Iban: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.iban }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Payment term: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.payment_term }}</p>
                </div>
            </TabsContent>

            <TabsContent value="materials">
                <div class="overflow-auto max-h-150">
                    <div v-for="material in props.product.materials" class="flex flex-col gap-5 wrap">
                        <div class="flex gap-2 text-lg mt-5 items-center">
                            <Label class="w-20">Id: </Label>
                            <p class="flex-1 justify-start mr-5">{{ material.id }}</p>
                            <Label class="w-20">Material: </Label>
                            <p v-if="!editMode" class="flex-1 justify-start">{{ material.name }}</p>
                            <Input v-else v-model="material.name"></Input>
                        </div>
                        <Separator></Separator>
                    </div>
                </div>
            </TabsContent>

            <TabsContent value="processes">
                <div class="overflow-auto h-150">
                    <div v-for="(process , index) in props.product.processes" class="flex flex-col gap-5 wrap">
                        <ProductInfoInput :prod_process="process" :editMode="editMode"></ProductInfoInput>
                    </div>
                </div>
            </TabsContent>

            <TabsContent value="files">
                <p class="flex justify-center items-center mt-10" v-if="props.product.files.length < 1">No files uploaded</p>

                <div class="overflow-auto max-h-150" v-else>
                    <div @click="showDrawings = !showDrawings" class="flex gap-2 justify-start w-auto cursor-pointer mt-10 items-center" v-auto-animate>
                        <h2 class="text-lg">Drawings</h2>
                        <ChevronDown v-if="showDrawings"></ChevronDown>
                        <ChevronUpIcon v-else></ChevronUpIcon>
                    </div>
                    <div v-if="showDrawings">
                        <div v-for="file in props.product.files" v-if="showDrawings">
                            <ProductFIles ref="drawingRef" v-if="file.type == 2" :editMode="editMode" :file_obj="file" :files_list="props.product.files"></ProductFIles>
                        </div>
                        <p v-if="drawingRef.length < 1">No drawing files</p>
                    </div>
                    <Separator></Separator>

                    <div @click="showBOM = !showBOM" class="flex gap-2 justify-start w-auto cursor-pointer mt-10 items-center" v-auto-animate>
                        <h2 class="text-lg">BOM</h2>
                        <ChevronDown v-if="showBOM"></ChevronDown>
                        <ChevronUpIcon v-else></ChevronUpIcon>
                    </div>
                    <div v-if="showBOM">
                        <div v-for="file in props.product.files" v-if="showBOM">
                            <ProductFIles ref="bomRef" v-if="file.type == 1" :editMode="editMode" :file_obj="file" :files_list="props.product.files"></ProductFIles>
                        </div>
                        <p v-if="bomRef.length < 1">No BOM files</p>
                    </div>
                    <Separator></Separator>

                    <div @click="showOthers = !showOthers" class="flex gap-2 justify-start w-auto cursor-pointer mt-10 items-center" v-auto-animate>
                        <h2 class="text-lg">Other</h2>
                        <ChevronDown v-if="showOthers"></ChevronDown>
                        <ChevronUpIcon v-else></ChevronUpIcon>
                    </div>
                    <div v-if="showOthers">
                        <div v-for="file in props.product.files">
                            <ProductFIles ref="otherRef" v-if="file.type == 3" :editMode="editMode" :file_obj="file" :files_list="props.product.files"></ProductFIles>
                        </div>
                        <p v-if="otherRef.length < 1">No other files</p>
                    </div>
                </div>
            </TabsContent>

            <TabsContent value="subproducts">
                <div v-if="!childs_loading">
                    <p class="flex justify-center items-center mt-10" v-if="!props.product.children || props.product.children.length < 1">No childs selected</p>
                    <div class="overflow-auto h-150" v-else>
                        <div v-for="child in props.product.children">
                            <ProductChilds @goto="goTo" :child="child"></ProductChilds>
                        </div>
                    </div>
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
            </TabsContent>

        </Tabs>
        <div @click="emit('close'); editMode = false" class="fixed right-10 top-10 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
            <CircleX size="40"></CircleX>
        </div>
        <div class="fixed bottom-10 left-10 text-red-500 w-10 h-10 hover:text-red-400">
            <Button disabled v-if="!loading" @click="updateServer" class="cursor-pointer" :class="editMode ? 'bg-green-400 hover:bg-green-200' : 'bg-red-400 hover:bg-red-200'">{{editMode ? 'Save changes' : 'Edit mode'}}</Button>
            <Button v-else  :class="editMode ? 'bg-green-400 hover:bg-green-200' : 'bg-red-400 hover:bg-red-200'">Loading...</Button>
        </div>
        <div @click="editMode = false; emit('update', parrent); parrent = null; tab = 'general'" v-if="parrent" class="fixed bottom-10 left-50 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
            <Button class="bg-blue-400 hover:bg-blue-300">Go back to parrent</Button>
        </div>
        <div class="fixed bottom-10 right-10">
            {{ props.product.id ?? '-' }}
        </div>
    </div>
</template>

<style scoped>
.selcontent{
    z-index: 999999;
}
</style>