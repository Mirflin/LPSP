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

const props = defineProps(['step', 'stepInfo', 'form', 'back'])
const emit = defineEmits(['update', 'cancel', 'next'])
const loading = ref(false)
const productStore = useProductCreate()

const files = productStore.product.files

const alert_message = ref(null)
const alert_type = ref(null)

const next = () => {
    let pass = true
    if(pass){
        emit('next')
    }
}

const back = () => {
    emit('back')
}

</script>

<template>
    <div>
        <TimedAlert
            :type="alert_type"
            :message="alert_message"
        />
        <div v-if="!loading" class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6" v-auto-animate>
            <div class="flex justify-between">
                <div class="mb-5 text-2xl">
                    Files
                </div>
            </div>

            <div class="w-full flex h-60">
                    <Dropzone class="w-full"></Dropzone>
            </div>

            <h2 class="text-2xl mt-5 h-10">Uploaded files:</h2>
            <div class="h-110 overflow-auto">
                <div v-for="file in files" v-auto-animate>
                    <FilesInput v-model="files" :file_obj="file"></FilesInput>
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