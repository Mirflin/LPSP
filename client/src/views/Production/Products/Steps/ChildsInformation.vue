<script setup>
import {useProductionStore} from "@/storage/production.js"
import { onMounted, reactive ,ref, toRaw, computed} from 'vue'
import Multiselect from 'vue-multiselect'
import ProcessesInput from "./ProcessesInput.vue"
import PlusIcon from "@/icons/PlusIcon.vue"
import { MinusIcon } from "lucide-vue-next"
import Separator from "@/components/ui/separator/Separator.vue"
import {useProductCreate} from "@/storage/product_create.js"
import Dropzone from "@/components/forms/FormElements/Dropzone.vue"
import FilesInput from "./FilesInput.vue"
import ChildsInput from "./ChildsInput.vue"

const production = useProductionStore()
const props = defineProps(['step', 'stepInfo', 'form', 'back'])
const emit = defineEmits(['update', 'cancel', 'next'])
const loading = ref(false)
const productStore = useProductCreate()
const childs_counter = ref(productStore.product.childs.length)
const alert_message = ref(null)
const alert_type = ref(null)

const next = () => {
    let pass = true
    productStore.product.childs.forEach(child => {
        if(child.children_product == '' || Object.is(child.children_product, null) || child.children_product == null){
            console.log("err")
            alert_message.value = 'Please fill all created child labels'
            alert_type.value = 'error'
            pass = false
            setTimeout(() => {
                alert_message.value = ''
                alert_type.value = ''
            }, 3000)
        }
    })
    if(pass){
        emit('next')
    }
}

const back = () => {
    emit('back')
}

const newChild = () => {
    childs_counter.value += 1
    productStore.product.childs.push({'id': childs_counter.value,'children_product': ''})
}
const deleteChild = () => {
    childs_counter.value -= 1
    productStore.product.childs.pop()
}
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
                    Child products
                </div>
                <div class="flex items-start gap-5">
                    <MinusIcon @click="deleteChild" class="w-9 h-auto  text-red-500 hover:text-red-700 transition cursor-pointer"></MinusIcon>
                    <PlusIcon  @click="newChild" class="w-9 h-auto text-green-500 hover:text-green-700 transition cursor-pointer" />
                </div>
            </div>
            <div class="h-185 flex-col overflow-auto">
                <div v-for="child in productStore.product.childs">
                    <ChildsInput v-model="productStore.product.childs" :child="child" :products="production.products"></ChildsInput>
                    <Separator class="bg-gray-500" />
                </div>
            </div>

            <div class="flex-col gap-5 items-center justify-center w-full">
                <div class="mt-5 flex justify-between">
                    <Button @click="back">Back</Button>
                    <div class="text-xl">
                        {{ props.stepInfo[props.step] }}
                    </div>
                    <Button @click="next" :disabled="props.stepInfo[props.step+1] == null">Next</Button>
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