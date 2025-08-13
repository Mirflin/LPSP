<script setup>
import {useProductionStore} from "@/storage/production.js"
import { onMounted, reactive ,ref, toRaw, computed} from 'vue'
import Multiselect from 'vue-multiselect'
import ProcessesInput from "./ProcessesInput.vue"
import PlusIcon from "@/icons/PlusIcon.vue"
import { MinusIcon } from "lucide-vue-next"
import Separator from "@/components/ui/separator/Separator.vue"
import {useProductCreate} from "@/storage/product_create.js"

const props = defineProps(['step', 'stepInfo', 'form', 'back'])
const emit = defineEmits(['update', 'cancel', 'next'])
const loading = ref(false)
const production = useProductionStore()
const productStore = useProductCreate()

const options = ref([])
const process_counter = ref(productStore.product.processes.length)
const alert_message = ref(null)
const alert_type = ref(null)


onMounted(async() => {
    loading.value = true
    options.value = production.materials
    loading.value = false
})

const newProccess = () => {
    process_counter.value += 1
    productStore.product.processes.push({'id': process_counter.value, 'process': '', 'subprocess': ''})
}

const deleteProcess = () => {
    process_counter.value -= 1
    productStore.product.processes.pop()
}

const next = () => {
    let pass = true
    if( productStore.product.processes.length < 1 || Object.is(productStore.product.processes, null) || productStore.product.processes == null){
        alert_message.value = "Please choose minimum 1 process"
        alert_type.value = "error"
        pass = false
        setTimeout(() => {
            alert_message.value = ""
            alert_type.value = ""
        }, 2500)
    }
    for(const prod of productStore.product.processes){
        console.log(prod)
        if(prod.process == ''){
            alert_message.value = "Please fill selected processes"
            alert_type.value = "error"
            pass = false
            setTimeout(() => {
                alert_message.value = ""
                alert_type.value = ""
            }, 2500)
            break
        }
    }
    if(pass){
        console.log(productStore.product)
        emit('next')
    }
}

const back = () => {
    emit('back')
}

const total_price = computed(() => {
    let result = 0
    productStore.product.processes.forEach(proc => {
        result = result + (proc.price + proc.additional_price)
    })
    return result
})

</script>

<template>
    <div>
        <TimedAlert
            :type="alert_type"
            :message="alert_message"
        />
        <div v-if="!loading" class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="flex justify-between">
                <div class="mb-5 text-2xl">
                    Processes
                </div>
                <div class="flex items-start gap-5">
                    <MinusIcon @click="deleteProcess" class="w-9 h-auto  text-red-500 hover:text-red-700 transition cursor-pointer"></MinusIcon>
                    <PlusIcon :disable="process_counter.value >= 10" @click="newProccess" class="w-9 h-auto text-green-500 hover:text-green-700 transition cursor-pointer" />
                </div>
            </div>
            <div class="h-185 flex-col overflow-auto">
                <div v-for="process in productStore.product.processes">
                    <ProcessesInput :process_list="production.processes" v-model="productStore.product.processes" :process="process" />
                    <Separator class="bg-gray-500" />
                </div>

            </div>
            <div class="flex-col gap-5 items-center justify-center w-full">
                <div class="mt-5 flex justify-between">
                    <Button @click="back">Back</Button>
                    <div class="text-xl">
                        {{ props.stepInfo[props.step] }}
                    </div>
                    <Button @click="next" :disabled="props.stepInfo[props.step+1] == null || process_counter < 1">Next</Button>
                </div>
            </div>
        </div>
        <div 
            v-else
            class="flex justify-center items-center"
        >
            <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
                <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
                </div>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>