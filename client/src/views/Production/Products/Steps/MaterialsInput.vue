<script setup>
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {ref, defineModel, watch, computed} from "vue"
import Multiselect from 'vue-multiselect'

const props = defineProps(['process'])
const model = defineModel()
const item = model.value.find(i => i.id === props.process.id)

</script>

<template>
    <div class="flex-col gap-5 mb-5">
        <div class="mb-8 mt-5">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Input material #{{ props.process.id }} <span class="text-error-500">*</span>
            </label>
            <div class="relative">
                <input
                    type="text"
                    v-model="item.material"
                    placeholder="Metal, plastic..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
            </div>
        </div>
    </div>
</template>