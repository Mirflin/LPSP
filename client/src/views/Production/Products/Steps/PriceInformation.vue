<script setup>
import {ref} from "vue"

const props = defineProps(['step', 'stepInfo', 'form'])
const emit = defineEmits(['update', 'cancel', 'next'])
const processes = ref(props.form.processes)
console.log(processes.value)
</script>

<template>
    <div v-if="!loading" class="rounded-2xl border border-gray-200 bg-white h-auto p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <div class="flex justify-between">
            <div class="mb-5 text-2xl">
                Price
            </div>
        </div>
        <div class="flex-col gap-5 items-center justify-center w-full">
            <div class="mt-5 flex justify-between">
                <Button @click="emit('cancel')">Cancel</Button>
                <div class="text-xl">
                    {{ props.stepInfo[props.step] }}
                </div>
                <Button @click="emit('next'); emit('update', process_form)" :disabled="props.stepInfo[props.step+1] == null || process_counter < 1">Next</Button>
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
</template>