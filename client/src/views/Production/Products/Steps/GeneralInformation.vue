<script setup>
import {useProductionStore} from "@/storage/production.js"
import { onMounted, reactive ,ref, toRaw, computed} from 'vue'
import Multiselect from 'vue-multiselect'
import {useProductCreate} from "@/storage/product_create.js"
import {useClientsStore} from "@/storage/clients.js"
import { useRouter } from 'vue-router'

const props = defineProps(['step', 'stepInfo'])
const emit = defineEmits(['update', 'cancel', 'next', 'back'])
const loading = ref(false)
const production = useProductionStore()
const productStore = useProductCreate()
const clients = useClientsStore()
const ifDrawing = ref(false)
const router = useRouter()

let options = []
onMounted(async() => {
    loading.value = true
    if(!production.products){
        router.push('/product')
    }
    options = production.materials
    loading.value = false
})

const alert_message = ref()
const alert_type = ref()

const drawing_check = async() =>{
    ifDrawing.value = false
    production.products.forEach(prod => {
        if(prod.drawing_nr == productStore.product.drawing_nr){
            ifDrawing.value = true
        }
    });
}

const next = () => {
    if((!productStore.product.drawing_nr || !productStore.product.part_nr || !productStore.product.client)){
        alert_message.value = "Please fill all required fields!"
        alert_type.value = "error"
        setTimeout(() => {
            alert_message.value = null
            alert_type.value = null
        }, 3000)
    }else{
        console.log(productStore.product)
        emit('next')
    }
}

</script>

<template>
    <TimedAlert
        :type="alert_type"
        :message="alert_message"
    />
    <div v-if="!loading" class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <div class="mb-5 text-2xl">
            General information
        </div>
        <div class="flex-col gap-5 items-center justify-center w-full h-185">
            <div class="flex-col gap-5 mb-5">
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Drawing number <span class="text-error-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.drawing_nr"
                            @input="drawing_check"
                            placeholder="34-294..."
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        <span
                            class="absolute left-0 top-1/2 flex h-11 w-[46px] -translate-y-1/2 items-center justify-center border-r border-gray-200 dark:border-gray-800"
                        >
                            <svg class="p-2" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Blueprint</title><path d="M21.809 5.524L12.806.179l-.013-.007.078-.045h-.166a1.282 1.282 0 0 0-1.196.043l-.699.403-8.604 4.954a1.285 1.285 0 0 0-.644 1.113v10.718c0 .46.245.884.644 1.113l9.304 5.357c.402.232.898.228 1.297-.009l9.002-5.345c.39-.231.629-.651.629-1.105V6.628c0-.453-.239-.873-.629-1.104zm-19.282.559L11.843.719a.642.642 0 0 1 .636.012l9.002 5.345a.638.638 0 0 1 .207.203l-4.543 2.555-4.498-2.7a.963.963 0 0 0-.968-.014L6.83 8.848 2.287 6.329a.644.644 0 0 1 .24-.246zm14.13 8.293l-4.496-2.492V6.641a.32.32 0 0 1 .155.045l4.341 2.605v5.085zm-4.763-1.906l4.692 2.601-4.431 2.659-4.648-2.615a.317.317 0 0 1-.115-.112l4.502-2.533zm-.064 10.802l-9.304-5.357a.643.643 0 0 1-.322-.557V7.018L6.7 9.51v5.324c0 .348.188.669.491.84l4.811 2.706.157.088v4.887a.637.637 0 0 1-.329-.083z"/></svg>
                        </span>
                    </div>
                    <p v-if="ifDrawing" class="text-red-500" v-auto-animate>This drawing already exists!</p>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Part number <span class="text-error-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.part_nr"
                            placeholder="74-193..."
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        <span
                            class="absolute left-0 top-1/2 flex h-11 w-[46px] -translate-y-1/2 items-center justify-center border-r border-gray-200 dark:border-gray-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" fill="currentColor" class="p-2 bi bi-gear-wide-connected" viewBox="0 0 16 16" id="IconChangeColor"> <path d="M7.068.727c.243-.97 1.62-.97 1.864 0l.071.286a.96.96 0 0 0 1.622.434l.205-.211c.695-.719 1.888-.03 1.613.931l-.08.284a.96.96 0 0 0 1.187 1.187l.283-.081c.96-.275 1.65.918.931 1.613l-.211.205a.96.96 0 0 0 .434 1.622l.286.071c.97.243.97 1.62 0 1.864l-.286.071a.96.96 0 0 0-.434 1.622l.211.205c.719.695.03 1.888-.931 1.613l-.284-.08a.96.96 0 0 0-1.187 1.187l.081.283c.275.96-.918 1.65-1.613.931l-.205-.211a.96.96 0 0 0-1.622.434l-.071.286c-.243.97-1.62.97-1.864 0l-.071-.286a.96.96 0 0 0-1.622-.434l-.205.211c-.695.719-1.888.03-1.613-.931l.08-.284a.96.96 0 0 0-1.186-1.187l-.284.081c-.96.275-1.65-.918-.931-1.613l.211-.205a.96.96 0 0 0-.434-1.622l-.286-.071c-.97-.243-.97-1.62 0-1.864l.286-.071a.96.96 0 0 0 .434-1.622l-.211-.205c-.719-.695-.03-1.888.931-1.613l.284.08a.96.96 0 0 0 1.187-1.186l-.081-.284c-.275-.96.918-1.65 1.613-.931l.205.211a.96.96 0 0 0 1.622-.434l.071-.286zM12.973 8.5H8.25l-2.834 3.779A4.998 4.998 0 0 0 12.973 8.5zm0-1a4.998 4.998 0 0 0-7.557-3.779l2.834 3.78h4.723zM5.048 3.967c-.03.021-.058.043-.087.065l.087-.065zm-.431.355A4.984 4.984 0 0 0 3.002 8c0 1.455.622 2.765 1.615 3.678L7.375 8 4.617 4.322zm.344 7.646.087.065-.087-.065z" id="mainIconPathAttribute"></path> </svg>
                        </span>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Revision
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.revision"
                            placeholder="A,B,C..."
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        <span
                            class="absolute left-0 top-1/2 flex h-11 w-[46px] -translate-y-1/2 items-center justify-center border-r border-gray-200 dark:border-gray-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" fill="currentColor" class="p-2 bi bi-clipboard" viewBox="0 0 16 16" id="IconChangeColor"> <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" id="mainIconPathAttribute"></path> <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" id="mainIconPathAttribute"></path> </svg>
                        </span>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Client <span class="text-error-500">*</span>
                    </label>
                    <div class="relative">
                        <multiselect 
                            id="single-select-search" v-model="productStore.product.client" :options="clients.clients" placeholder="Select one" label="name" aria-label="pick a value" :close-on-select="true"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Description
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.description"
                            placeholder="Description"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                    </div>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Additional information
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.additional_info"
                            placeholder="Additional information"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                    </div>
                </div>
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Weight
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="productStore.product.weight"
                            placeholder="Weight"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                    </div>
                </div>

                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Per product (default 1)
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            v-model="productStore.product.count"
                            placeholder="... per product"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 flex justify-between">
            <div>
                <div class="flex gap-5">
                    <Button @click="emit('back')" disabled>Back</Button>
                </div>
            </div>
            <div class="text-xl">
                {{ props.stepInfo[props.step] }}
            </div>
            <Button @click="next" :disabled="props.stepInfo[props.step+1] == null || ifDrawing">Next</Button>
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
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>