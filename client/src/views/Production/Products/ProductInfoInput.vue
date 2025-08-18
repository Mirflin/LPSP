<script setup>
import {useProductionStore} from '@/storage/production.js'
import Separator from "@/components/ui/separator/Separator.vue"
import { ChevronDown } from "lucide-vue-next"
import { ChevronUpIcon } from "lucide-vue-next"
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs"

const production = useProductionStore()
const props = defineProps(['prod_process', 'editMode'])

</script>

<template>
    <div class="flex gap-2 text-lg mt-5 items-center">
        <Label class="w-20">#{{ prod_process.place }} </Label>

        <Label class="w-20">Process: </Label>
        <p v-if="!props.editMode" class="flex-1 justify-start">{{ prod_process.process_list.name }}</p>

        <Select v-model="prod_process.id" @update:modelValue="updateProcess(prod_process.id)" v-else required>
            <SelectTrigger class="w-[180px]">
                <SelectValue placeholder="Select a process" />
            </SelectTrigger>
            <SelectContent class="selcontent z-[100000]" positionStrategy="absolute">
                <SelectGroup>
                    <SelectLabel>Processes</SelectLabel>
                    <div v-for="process in production.processes">
                        <SelectItem :value="process.id" class="cursor-pointer">
                            {{ process.name }}
                        </SelectItem>
                    </div>
                </SelectGroup>
            </SelectContent>
        </Select>

        <Label class="w-25">Sub-process: </Label>
        <p v-if="!props.editMode" class="flex-1 justify-start">{{ prod_process.sub_process }}</p>
        <Input class="w-70" v-else v-model="prod_process.sub_process"></Input>
    </div>
    <Separator></Separator>
</template>