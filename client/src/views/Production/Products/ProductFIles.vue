<script setup>
import {ref, toRaw, defineModel, watch} from 'vue'
import FileIcons from "file-icons-vue"
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { DeleteIcon } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps(['file_obj', 'files_list', 'editMode'])
const ifDrawing = ref(props.file_obj.type == 2 ? true : false)
const ifBOM = ref(props.file_obj.type == 1 ? true : false)
const ifOther = ref(props.file_obj.type == 3 ? true : false)

const deleteFile = () => {
    let index = props.files_list.findIndex(file => file.id == props.file_obj.id)
    if(index !== -1){
        props.files_list.splice(index,1)
    }
}

const downloadFile = async () => {
    try {
        const url = `http://192.168.88.48:8000/api/download/${encodeURIComponent(props.file_obj.path)}`;

        const response = await axios.get(url, {
            responseType: "blob",
            withCredentials: true
        });

        const fileURL = window.URL.createObjectURL(response.data);
        const a = document.createElement("a");
        a.href = fileURL;
        a.download = props.file_obj.name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        window.URL.revokeObjectURL(fileURL);
    } catch (error) {
        console.error("Download failed:", error);
    }
};

</script>

<template>
    <div class="flex gap-5 w-full items-center justify-between mt-5 pl-10 border-1 rounded-md p-2 hover:bg-gray-100">
        <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile">
            <FileIcons 
                :name="props.file_obj.name" :width="50" :height="50" 
                :isFolder="false" :isMulti="false" :isLink="false" 
                :itemStyle="{display: 'flex', alignItems: 'center'}"  
            />
            <p>{{ props.file_obj.name.length > 40 ? props.file_obj.name.slice(0,40)+'...' : props.file_obj.name}}</p>
        </div>
        <div v-if="props.editMode" class="flex gap-5">
            <div>
                <Checkbox v-model="ifDrawing" @click="props.file_obj.type = 2; ifBOM = false; ifOther = false"></Checkbox>
                <label class="ml-2">Drawing?</label>
            </div>
            <div>
                <Checkbox v-model="ifBOM" @click="props.file_obj.type = 1; ifDrawing = false; ifOther = false"></Checkbox>
                <label class="ml-2">BOM?</label>
            </div>
            <div>
                <Checkbox v-model="ifOther" @click="props.file_obj.type = 3; ifDrawing = false; ifBOM = false"></Checkbox>
                <label class="ml-2">Other?</label>
            </div>
            <DeleteIcon @click="deleteFile" class="cursor-pointer mr-1"></DeleteIcon>
        </div>
    </div>
</template>
