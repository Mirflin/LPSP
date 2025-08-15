<script setup>
import {ref} from 'vue'
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

const props = defineProps(['child'])
const emit = defineEmits(['goto'])

</script>

<template>
    <div @click="emit('goto', props.child)" class="flex gap-5 w-full items-center justify-between mt-5 pl-10 border-1 rounded-md p-2 hover:bg-gray-100 cursor-pointer">
        <div class="flex gap-8 items-center justify-center">
            <Label>Id:</Label>
            {{ props.child.id }}
            <Label>Drawing number:</Label>
            {{ props.child.drawing_nr }}
            <Label>Part number:</Label>
            {{ props.child.drawing_nr }}
            <Label>Revision:</Label>
            {{ props.child.revision }}
        </div>
    </div>
</template>