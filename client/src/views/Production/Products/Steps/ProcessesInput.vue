<script setup>
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {ref, defineModel, watch, computed} from "vue"
import Multiselect from 'vue-multiselect'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const props = defineProps(['process', 'process_list'])
const model = defineModel()
console.log(model.value)
const item = model.value.find(i => i.id === props.process.id)
const subprocess = item.subprocess != '' ? ref(true) : ref(false)

const manual = ref(false)

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

                <checkbox v-if="subprocess" v-model="manual"></checkbox>
                <label v-if="subprocess">Enable manual input?</label>
            </div>

            <div v-if="subprocess">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Sub-process #{{ props.process.id }}
                </label>
                <div class="relative">
                    <input
                        v-if="manual"
                        type="text"
                        v-model="item.subprocess"
                        placeholder=""
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    <Select v-else v-model="item.subprocess" class="w-full mt-2">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a process" />
                        </SelectTrigger>

                        <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Partners</SelectLabel>

                            <SelectItem value="Swedan Partners">
                                Swedan Partners
                            </SelectItem>
                            <SelectItem value="Silkeborg">
                                Silkeborg
                            </SelectItem>
                            <SelectItem value="Roechling">
                                Roechling
                            </SelectItem>
                            <SelectItem value="Kurzemes atslēga FE/ZN">
                                Kurzemes atslēga FE/ZN
                            </SelectItem>
                            <SelectItem value="ICS Steel FE/ZN">
                                ICS Steel FE/ZN
                            </SelectItem>
                            <SelectItem value="GF Powdercoating">
                                GF Powdercoating
                            </SelectItem>
                            <SelectItem value="Railing Service">
                                Railing Service
                            </SelectItem>
                            <SelectItem value="AB Galdnieks">
                                AB Galdnieks
                            </SelectItem>
                            <SelectItem value="HDG - Zincpot Estonia">
                                HDG - Zincpot Estonia
                            </SelectItem>
                            <SelectItem value="Kurz Powder paint">
                                Kurz Powder paint
                            </SelectItem>
                            <SelectItem value="ICS - Electro Polishing">
                                ICS - Electro Polishing
                            </SelectItem>
                        </SelectGroup>
                        <SelectGroup>
                            <SelectLabel>Processes</SelectLabel>
                            <SelectItem value="Leak test">
                                Leak test
                            </SelectItem>
                            <SelectItem value="Preassure test (2bar.)">
                                Pressure test (2bar.)
                            </SelectItem>
                        </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>
    </div>
</template>