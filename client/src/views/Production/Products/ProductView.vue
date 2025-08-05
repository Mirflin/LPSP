<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useRoute } from 'vue-router'
import {reactive, ref, shallowRef, watch} from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { BookUser, Check, CreditCard, Truck } from "lucide-vue-next"
import { Stepper, StepperDescription, StepperIndicator, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from "@/components/ui/stepper"
import { Button } from '@/components/ui/button'
import GeneralInformation from './Steps/GeneralInformation.vue'
import ProcessesInformation from './Steps/ProcessesInformation.vue'
import PriceInformation from './Steps/PriceInformation.vue'
import { useRouter } from 'vue-router'

const route = useRoute()
const product_id = ref(route.params.id)
const step = ref(0)
const router = useRouter()
const componentMap = [
    GeneralInformation,
    ProcessesInformation,
    PriceInformation
]

const selectedComponent = shallowRef(componentMap[step.value])
const stepInfo = ["General info", "Processes", "Price", "Files", "Finalize"]
const form = ref({
    "drawing_nr": "",
    "part_nr": "",
    "revision": "",
    "description": "",
    "additional_info": "",
    "weight": "",
    "materials": "",
    "processes": "",
})

watch(step, () => {
    selectedComponent.value = componentMap[step.value]
})

const increase = () => {
    step.value = step.value + 1
}

const cancel = () => {
    router.push("/product")
}

const update = async(items) => {
    form.value.drawing_nr = items.drawing_nr || form.value.drawing_nr
    form.value. part_nr = items.part_nr || form.value.part_nr
    form.value.revision = items.revision || form.value.revision
    form.value.description = items.description || form.value.description
    form.value.additional_info = items.additional_info || form.value.additional_info
    form.value.weight = items.weight || form.value.weight
    form.value.materials = items.materials || form.value.materials
    form.value.processes = items.processes || form.value.processes
    console.log(form.value)
}

const currentPageTitle = "Product #"+product_id.value
</script>

<template>
    <AdminLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        
        <component :form="form" :stepInfo="stepInfo" :step="step" @update="update" @next="increase" @cancel="cancel" v-bind:is="selectedComponent"></component>

        <div class="flex gap-5 justify-between items-center mt-5 p-1">
            <Button v-if="stepInfo[step+1] == null">Save</Button>
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>