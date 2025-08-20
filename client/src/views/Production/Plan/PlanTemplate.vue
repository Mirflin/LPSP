<script setup>
import moment from 'moment'
import { computed, ref } from 'vue';
const props = defineProps(['product'])

const proc_ref = ref([])

const editMode = ref(false)

const materials = computed(() => {
    let string = ""
    props.product.materials.forEach(material => {
        string = string + material.name + " "
    });
    return string
})

</script>

<template>
  <div class="w-[210mm] h-[297mm] bg-white p-12 border-2 border-black text-sm font-sans">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
      <div>
        <div class="mb-2">
          <p class="font-bold">Sagatavots:</p>
          <div class="border border-black px-2 py-1 text-center">{{ moment().format('DD.MM.YYYY') }}</div>
        </div>
        <div>
          <p class="font-bold">Pasūtījuma Nr.:</p>
          <div class="border border-black px-2 py-1 text-center w-50">29209</div>
        </div>
      </div>
      <div>
        <img src="/images/logo/main-logo.jpg" alt="LPSP Logo" class="h-30" />
      </div>
    </div>

    <!-- Client -->
    <div class="mb-8">
      <p class="font-bold mb-1">Klients:</p>
      <div class="border border-black px-2 py-1 text-center w-120"> {{ props.product.client.name }} </div>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-2 gap-6 mb-8">
      <div>
        <p class="font-bold mb-1">Detaljs Nr.</p>
        <div class="border border-black px-2 py-1 text-center"> {{ props.product.part_nr }}</div>
      </div>
      <div>
        <p class="font-bold mb-1">Nosaukums</p>
        <div class="border border-black px-2 py-1 text-center"> {{ props.product.description ? props.product.description : '-'}} </div>
      </div>
      <div>
        <p class="font-bold mb-1">Rasējuma Nr.</p>
        <div class="border border-black px-2 py-1 text-center"> {{ props.product.drawing_nr }} </div>
      </div>
      <div>
        <p class="font-bold mb-1">Revīzija</p>
        <div class="border border-black px-2 py-1 text-center"> {{ props.product.revision ? props.product.revision : '-' }} </div>
      </div>
    </div>

    <!-- Execution -->
    <div class="grid grid-cols-2 gap-6 mb-8">
      <div>
        <p class="font-bold mb-1">Izpildīšanas datums</p>
        <div v-if="!editMode" class="border border-black px-2 py-1 text-center h-7">04.04.2025</div>
        <Input v-else class="border rounded-none border-black px-2 py-1 text-center"></Input>
      </div>
      <div>
        <p class="font-bold mb-1">Daudzums</p>
        <div v-if="!editMode" class="border border-black px-2 py-1 text-center">92</div>
        <Input v-else class="border rounded-none border-black px-2 py-1 text-center"></Input>
      </div>
    </div>

    <!-- Process -->
    <div class="mb-8">
      <p class="font-bold mb-2">Procesi:</p>
      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="border border-black px-2 py-1 text-left">Process</th>
            <th class="border border-black px-2 py-1 text-left">Sub-process</th>
            <th class="border border-black px-2 py-1 text-left">Kvalitāti pārbaude</th>
            <th class="border border-black px-2 py-1 text-left">Skaits</th>
          </tr>
        </thead>
        <tbody>
          <tr ref="proc_ref" v-for="process in props.product.processes">
            <td class="border border-black px-2 py-1 h-7">{{ process.process_list.name }}</td>
            <td class="border border-black px-2 py-1">{{ process.sub_process ? process.sub_process : '-' }}</td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
          </tr>
          <tr v-for="i in (10 - proc_ref.length)">
            <td class="border border-black px-2 py-1 h-7"></td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Material -->
    <div>
      <p class="font-bold mb-1">Materiāls:</p>
      <div class="border border-black px-2 py-1 text-center"> {{ materials ? materials : 'nav' }} </div>
      <div v-if="!editMode" class="border border-black px-2 py-1 p-1 w-full min-h-30 h-auto whitespace-normal break-words">{{ props.product.additional_info }}</div>
      <textarea v-else class="border border-black px-2 py-1 p-1 w-full min-h-30 h-auto whitespace-normal break-words" :readonly="!editMode" v-model="props.product.additional_info"></textarea>
    </div>
  </div>
  <div class="fixed left-10 bottom-10">
    <Button @click="editMode = !editMode" :class="editMode ? 'bg-green-500 hover:bg-green-300' : 'bg-red-500 hover:bg-red-300'">Edit mode</Button>
  </div>
</template>

<style scoped>
/* Print setup */
@page {
  size: A4;
  margin: 0;
}
@media print {
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    margin: 0;
  }
  .a4-page {
    border: none;
    box-shadow: none;
    page-break-after: always;
  }
}
</style>