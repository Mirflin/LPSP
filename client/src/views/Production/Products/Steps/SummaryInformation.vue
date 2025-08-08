<script setup>
import {ref, computed} from 'vue'
import {useProductCreate} from '@/storage/product_create'
import FileIcons from "file-icons-vue"

const props = defineProps(['step', 'stepInfo'])
const emit = defineEmits(['update', 'cancel', 'next', 'back'])
const productStore = useProductCreate()
const loading = ref(false)
console.log(productStore.product)

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
        <div v-if="!loading" class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="flex justify-between">
                <div class="mb-5 text-2xl">
                    Summary
                </div>
            </div>

            <div class="h-185 flex-col">
                <div class="flex flex-row flex-nowrap w-full h-full flex-wrap">
                    <div class="flex-col flex gap-5 flex-1 text-lg p-5 min-w-200">
                        <h2 class="text-2xl mb-5">General</h2>
                        <div class="flex flex-row gap-5 justify-between">
                            <p>Drawing number </p>  {{ productStore.product.drawing_nr }}
                        </div>
                        <div class="flex flex-row gap-5 justify-between">
                            <p>Part number </p>  {{ productStore.product.part_nr }}
                        </div>
                        <div class="flex flex-row gap-5 justify-between">
                            <p>Revision </p>  {{ productStore.product.revision }}
                        </div>
                        <div class="flex flex-row gap-5 justify-between text-wrap">
                            <p>Description </p>  <textarea disabled class="w-1/2 h-40 border-1 p-2 rounded-md" style="resize: none;">{{ productStore.product.description }}</textarea>
                        </div>
                        <div class="flex flex-row gap-5 justify-between text-wrap">
                            <p>Additional information </p>  <textarea disabled class="w-1/2 h-40 border-1 p-2 rounded-md" style="resize: none;">{{ productStore.product.additional_info }}</textarea>
                        </div>
                        <div class="flex flex-row gap-5 justify-between">
                            <p>Weight </p>  {{ productStore.product.weight }}
                        </div>
                        <div class="flex flex-row gap-5 justify-between">
                            <p>Child products </p>  <div class="h-20 overflow-auto"> <p v-for="child in productStore.product.childs">{{child.children_product.drawing_nr}} </p> </div>
                        </div>
                    </div>
                    <div class="flex-1 p-5">
                        <h2 class="text-2xl mb-5">Files</h2>
                        <div class="h-42 p-3 overflow-auto border-1 rounded-md">
                            <div class="flex gap-5 items-center" v-for="fileObj in productStore.product.files">
                                <FileIcons 
                                    :name="fileObj.name" :width="50" :height="50" 
                                    :isFolder="false" :isMulti="false" :isLink="false" 
                                    :itemStyle="{display: 'flex', alignItems: 'center'}"  
                                />
                                <p class="flex gal-5 flex-col"><div class="">{{ fileObj.drawing ? 'Drawing' : ''}} {{ fileObj.bom ? 'BOM' : ''}} {{ fileObj.bom || fileObj.drawing ? '' : 'Other'}}</div>{{ fileObj.file.upload.filename }}</p>
                            </div>
                        </div>
                        <div class="h-100 mt-2">
                            <h2 class="text-2xl mb-5">Processes</h2>
                            <div class="h-full p-3 overflow-auto border-1 rounded-md">
                                <div class="flex gap-5 flex-col" v-for="proc in productStore.product.processes">
                                    <div class="mb-5 text-lg flex justify-between gap-5 w-full">
                                        <p class="w-2">#{{ proc.id }}</p>
                                        <div class="items-start justify-start">
                                            <p>Process: {{proc.process.name}}</p>
                                            <p>Sub-process: {{proc.subprocess ? proc.subprocess : 'no'}}</p>
                                        </div>
                                        <div class="flex flex-col">
                                            <p>Price: {{ proc.price }}€</p>
                                            <p>Additional price: {{ proc.additional_price }}€</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-lg">Total price: {{ total_price }}€</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-col gap-5 items-center justify-center w-full">
                <div class="mt-5 flex justify-between">
                    <Button @click="emit('back')">Back</Button>
                    <div class="text-xl">
                        {{ props.stepInfo[props.step] }}
                    </div>
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