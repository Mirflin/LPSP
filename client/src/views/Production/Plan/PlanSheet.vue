<script setup>
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from '@/components/ui/sheet'
import { ref, watch } from 'vue'

const props = defineProps(['row', 'open'])
const emit = defineEmits(['close'])

</script>

<template>
    <div>
        <Sheet v-model:open="props.open" @close="emit('close')">
            <SheetContent>
                <SheetHeader class="mt-10">
                    <SheetTitle>Are you absolutely sure?</SheetTitle>
                    <SheetDescription>
                    This action cannot be undone. This will permanently delete your account
                    and remove your data from our servers.
                    </SheetDescription>
                </SheetHeader>
            </SheetContent>
        </Sheet>
    </div>
</template>