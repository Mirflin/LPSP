<script setup>
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {ref, defineModel, watch, computed} from "vue"
import Multiselect from 'vue-multiselect'

const props = defineProps(['process', 'process_list'])
const model = defineModel()
const item = model.value.find(i => i.id === props.process.id)
const subprocess = item.subprocess != '' ? ref(true) : ref(false)

watch(subprocess, () => {
    if(!subprocess.value){
        item.subprocess = ""
    }
}, {deep: true, immediate: true})

</script>

<template>
    <div class="flex-col gap-5 mb-5">
        <div class="mb-8 mt-5">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Select proccess #{{ props.process.id }} <span class="text-error-500">*</span>
            </label>
            <div class="relative">
                <multiselect 
                    id="single-select-search" v-model="item.process" :options="props.process_list" placeholder="Select one" label="name" aria-label="pick a value" :close-on-select="true"
                >
                </multiselect>
            </div>

            <div class="flex gap-5 items-center mt-2 mb-2">
                <checkbox v-model="subprocess"></checkbox>
                <label>Enable sub-process?</label>
            </div>

            <div v-if="subprocess">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Sub-process #{{ props.process.id }}
                </label>
                <div class="relative">
                    <input
                        type="text"
                        v-model="item.subprocess"
                        placeholder=""
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                </div>
            </div>

            <div class="w-full flex-col mb-5 mt-5 flex">
                <div class="flex-1 w-full mt-1">
                    <label>Price: </label>
                    <input
                        v-model="item.price"
                        type="number" min="0.00" max="10000.00" step="0.01"
                        class="mr-5 dark:bg-dark-900 h-9 w-40 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    <label>Additional price: </label>
                    <input
                        v-model="item.additional_price"
                        type="number" min="0.00" max="10000.00" step="0.01"
                        class="dark:bg-dark-900 h-9 w-40 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                </div>
            </div>

        </div>
    </div>
</template>