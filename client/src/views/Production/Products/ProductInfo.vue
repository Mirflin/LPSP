<script setup>
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs"
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch } from 'vue'
import { Button } from "@/components/ui/button"
import { CircleX } from "lucide-vue-next"


const emit = defineEmits(['close'])
const props = defineProps(['product'])
const editMode = ref(false)

</script>

<template>
    <div
        class="no-scrollbar relative w-full max-w-[1200px] h-2/3 overflow-y-auto rounded-3xl bg-white p-2 mr-5 ml-5 dark:bg-gray-900 lg:p-11"
    >
        <Tabs default-value="general" class="w-full">
            <TabsList class="w-1/2 gap-2 h-10">
                <TabsTrigger value="general">
                    General
                </TabsTrigger>
                <TabsTrigger value="client">
                    Client
                </TabsTrigger>
                <TabsTrigger value="materials">
                    Materials
                </TabsTrigger>
                <TabsTrigger value="files">
                    Files
                </TabsTrigger>
                <TabsTrigger value="subproducts">
                    Sub-products
                </TabsTrigger>
            </TabsList>
            <TabsContent value="general" class="flex flex-col gap-5 h-full">
                <div class="flex gap-2 text-lg mt-5 flex">
                    <Label class="w-60">Drawing number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.drawing_nr }}</p>
                    <Input v-else v-model="props.product.drawing_nr"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Part number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.part_nr }}</p>
                    <Input v-else v-model="props.product.part_nr"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Revision number: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.revision }}</p>
                    <Input v-else v-model="props.product.revision"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Weight: </Label>
                    <p class="flex-1 justify-start" v-if="!editMode">{{ props.product.weight }}</p>
                    <Input v-else v-model="props.product.weight"></Input>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Description: </Label>
                    <textarea v-model="props.product.description" class="flex-1 justify-start items-start p-2" :class="editMode ? 'border-1' : ''" :readonly="!editMode" placeholder="-"></textarea>
                </div>
                <div class="flex gap-2 text-lg mt-5 items-start">
                    <Label class="w-60">Additional information: </Label>
                    <textarea v-model="props.product.additional_info" class="flex-1 justify-start p-2" :class="editMode ? 'border-1' : ''" :readonly="!editMode" placeholder="-"></textarea>
                </div>
            </TabsContent>
            <TabsContent value="client" class="flex flex-col gap-5 h-full">
                <div class="flex gap-2 text-lg mt-5 flex">
                    <Label class="w-60">Client: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.name }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Contact person: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.contact_person }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Address: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.address }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Delivery address: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.delivery_address }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Email: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.email }}</p>
                </div>
                <div class="flex gap-2 text-lg mt-5">
                    <Label class="w-60">Phone: </Label>
                    <p class="flex-1 justify-start">{{ props.product.client.phone }}</p>
                </div>
            </TabsContent>
            <TabsContent value="materials">
            Change your password here.
            </TabsContent>
            <TabsContent value="files">
            Change your password here.
            </TabsContent>
            <TabsContent value="subproducts">
            Change your password here.
            </TabsContent>
        </Tabs>
        <div @click="emit('close')" class="absolute right-10 top-10 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
            <CircleX size={10}></CircleX>
        </div>
        <div @click="editMode = !editMode" class="absolute bottom-10 left-10 text-red-500 w-10 h-10 cursor-pointer hover:text-red-400">
            <Button class="" :class="editMode ? 'bg-green-400 hover:bg-red-200' : 'bg-red-400 hover:bg-green-200'">{{editMode ? 'Save changes' : 'Edit mode'}}</Button>
        </div>
        <div class="absolute bottom-10 right-10">
            {{ props.product.id ?? '-' }}
        </div>
    </div>
</template>