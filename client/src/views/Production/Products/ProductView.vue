<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useRoute } from 'vue-router'
import {onMounted, reactive, ref, shallowRef, watch} from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { BookUser, Check, CreditCard, Truck } from "lucide-vue-next"
import { Stepper, StepperDescription, StepperIndicator, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from "@/components/ui/stepper"
import { Button } from '@/components/ui/button'
import GeneralInformation from './Steps/GeneralInformation.vue'
import ProcessesInformation from './Steps/ProcessesInformation.vue'
import FilesInformation from './Steps/FilesInformation.vue'
import ChildsInformation from './Steps/ChildsInformation.vue'
import SummaryInformation from './Steps/SummaryInformation.vue'
import { useRouter } from 'vue-router'
import {useProductionStore} from "@/storage/production.js"
import {useClientsStore} from "@/storage/clients.js"
import axios from 'axios'
import {useProductCreate} from '@/storage/product_create.js'
import MaterialsInformation from './Steps/MaterialsInformation.vue'

const production = useProductionStore()
const clients = useClientsStore()
const productStore = useProductCreate()

const route = useRoute()
const product_id = ref(route.params.id)
const step = ref(0)
const router = useRouter()
const componentMap = [
    GeneralInformation,
    ProcessesInformation,
    MaterialsInformation,
    FilesInformation,
    ChildsInformation,
    SummaryInformation
]

const selectedComponent = shallowRef(componentMap[step.value])
const stepInfo = ["General info", "Processes", "Materials", "Files", "Sub-products", "Summary"]
const loading = ref(false)
const alert_message = ref()
const alert_type = ref()

watch(step, () => {
    selectedComponent.value = componentMap[step.value]
})

onMounted(async() => {
    if(!clients.clients || !production.materials){
        clients.fetchClients()
        production.fetch()
    }
})

const increase = () => {
    step.value = step.value + 1
}

const back = () => {
    step.value = step.value - 1
}

const cancel = () => {
    router.push("/product")
}

const save = async() => {
    loading.value = true
    const response = await productStore.save()
    if(!response){
        router.push('/product')
    }else{
        alert_message.value = response
        alert_message.value = "error"
        setTimeout(() => {
            alert_message.value = ''
            alert_type.value = ''
        }, 3000);
    }
}

</script>

<template>
    <AdminLayout>
        <TimedAlert
            :type="alert_type"
            :message="alert_message"
        />
        <component v-if="!loading" :stepInfo="stepInfo" :step="step" @next="increase" @back="back" @cancel="cancel" v-bind:is="selectedComponent"></component>
        <div 
            v-else
            class="flex justify-center items-center"
        >
            <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
                <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
                </div>
            </div>
        </div>

        <div class="flex gap-5 justify-end items-center mt-5 p-1">
            <Button @click="cancel" class="bg-red-600 hover:bg-red-500">Cancel</Button>
            <Button @click="save" :disabled="stepInfo[step+1] != null" class="bg-green-600 hover:bg-green-400">Save</Button>
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>