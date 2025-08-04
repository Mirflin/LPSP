<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useRoute } from 'vue-router'
import {ref, shallowRef} from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { BookUser, Check, CreditCard, Truck } from "lucide-vue-next"
import { Stepper, StepperDescription, StepperIndicator, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from "@/components/ui/stepper"
import { Button } from '@/components/ui/button'
import GeneralInformation from './Steps/GeneralInformation.vue'
const route = useRoute()
const product_id = ref(route.params.id)
const step = ref(0)

const componentMap = [
    GeneralInformation
]

const selectedComponent = shallowRef(GeneralInformation)

const increase = () => {
    step.value = step.value + 1
}

const decrease = () => {
    step.value = step.value - 1
}

const currentPageTitle = "Product #"+product_id.value
</script>

<template>
    <AdminLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        
        <component :step="step" @back="decrease" @next="increase" v-bind:is="selectedComponent"></component>
    </AdminLayout>
</template>

<style scoped>

</style>