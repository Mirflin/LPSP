<script setup>
import {ref, toRaw, defineModel, watch} from 'vue'
import FileIcons from "file-icons-vue"
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { DeleteIcon } from 'lucide-vue-next';
import {useProductCreate} from "@/storage/product_create.js"

const props = defineProps(['file_obj'])
const ifDrawing = ref(false)
const ifBOM = ref(false)

const productStore = useProductCreate()

const deleteFile = () => {
    let index = productStore.product.files.findIndex(file => file.id == props.file_obj.id)
    if(index !== -1){
        productStore.product.files.splice(index,1)
    }
}

console.log(props.file_obj)
</script>

<template>
    <div class="flex gap-5 w-full items-center justify-between mt-5 pl-10 border-1 rounded-md p-2 hover:bg-gray-100">
        <div class="flex gap-5 items-center">
            <FileIcons 
                :name="props.file_obj.file.upload.filename" :width="50" :height="50" 
                :isFolder="false" :isMulti="false" :isLink="false" 
                :itemStyle="{display: 'flex', alignItems: 'center'}"  
            />
            <p>{{ props.file_obj.file.upload.filename }}</p>
        </div>
        <div class="flex gap-5">
            <div>
                <Checkbox v-model="props.file_obj.drawing" @click="props.file_obj.bom = false"></Checkbox>
                <label class="ml-2">Drawing?</label>
            </div>
            <div>
                <Checkbox v-model="props.file_obj.bom" @click="props.file_obj.drawing = false"></Checkbox>
                <label class="ml-2">BOM?</label>
            </div>
            <DeleteIcon @click="deleteFile" class="cursor-pointer mr-1"></DeleteIcon>
        </div>
    </div>
</template>
