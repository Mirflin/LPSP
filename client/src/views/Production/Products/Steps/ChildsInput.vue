<script setup>
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {ref, defineModel, watch, computed} from "vue"
import Multiselect from 'vue-multiselect'

const props = defineProps(['child', 'products'])
const model = defineModel()
const item = model.value.find(i => i.id === props.child.id)
const subprocess = item.subprocess != '' ? ref(true) : ref(false)
console.log(props.products)
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
                Select child #{{ item.id }} <span class="text-error-500">*</span>
            </label>
            <div class="relative">
                <multiselect 
                    id="single-select-search" 
                    v-model="item.children_product"
                    :options="props.products" placeholder="Select one" track-by="drawing_nr" :option-height="60"
                    label="name" aria-label="pick a product" :close-on-select="true" :searchable="true"
                >
                <template #singleLabel="props">
                    <span class="option__title">
                        {{ props.option.drawing_nr }}
                    </span>
                </template>

                <template #option="props">
                    <div class="option__desc">
                        <span class="option__title">
                            {{ props.option.drawing_nr }}
                        </span>
                    </div>
                </template>
                </multiselect>
            </div>

        </div>
    </div>
</template>