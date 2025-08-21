<script setup>
import moment from 'moment'
import { computed, ref, defineExpose } from 'vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';

const props = defineProps(['product', 'settings', 'child', 'editMode'])
console.log(props.settings)
const proc_ref = ref([])

props.product.count = 1

const materials = computed(() => {
    let string = ""
    props.product.materials.forEach(material => {
        string = string + material.name + " "
    });
    return string
})

let outsource_counter = 0
props.product.processes.forEach((proc, index) => {
  proc.date_completion = ''
  if(proc.process_list.name == 'Outsourcing' || proc.process_list.name == 'Surface treatment'){
    outsource_counter += 1
  }
});

props.product.processes.forEach((proc, index) => {
  if(proc.process_list.name == 'Outsourcing' || proc.process_list.name == 'Surface treatment'){
    proc.date_completion = (moment(props.settings.po_date, 'DD.MM.YYYY').subtract((7*outsource_counter), 'days')).format('DD.MM.YYYY')
    console.log(proc.date_completion)
    outsource_counter -= 1
  }
})

const hightlight = (process_name) => {
  switch (process_name) {
  case "Sheet Laser":
    return 'bg-blue-300';
  case "Tube Laser":
    return 'bg-yellow-300';
  case "Surface treatment":
    return 'bg-green-300';
  case "Outsourcing":
    return 'bg-green-300';
  case "Welding":
    return 'bg-purple-300';
  default:
    return 'bg-white';
}
}

const pdf = ref(null)

defineExpose({ pdf })
</script>

<template>
  <div v-if="props.child" class="flex gap-2 mb-3">
    <Label>For one product: </Label>
    <Input class="w-30" v-model="props.product.count" type="number"></Input>
  </div>
  <div ref="pdf" class="w-[210mm] h-[297mm] bg-white p-12 border-2 border-black text-sm font-sans">
    <div class="flex justify-between items-start mb-8">
      <div>
        <div class="mb-2">
          <p class="font-bold">Sagatavots:</p>
          <div class="border border-black px-2 py-1 text-center">{{ moment().format('DD.MM.YYYY') }}</div>
        </div>
        <div>
          <p class="font-bold">Pasūtījuma Nr.:</p>
          <div v-if="!props.editMode || props.child" class="border border-black px-2 py-1 text-center w-50">{{ props.settings.po_nr }}</div>
          <Input v-model="props.settings.po_nr" v-if="props.editMode && !props.child" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
        </div>
      </div>
      <div>
        <img src="/images/logo/main-logo.jpg" alt="LPSP Logo" class="h-30" />
      </div>
    </div>

    <div class="mb-8">
      <p class="font-bold mb-1">Klients:</p>
      <div class="border border-black px-2 py-1 text-center w-120"> {{ props.product.client.name }} </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
      <div>
        <p class="font-bold mb-1">Detaljs Nr.</p>
        <div v-if="!props.editMode" class="border border-black px-2 py-1 text-center"> {{ props.product.part_nr }}</div>
        <Input v-model="props.product.part_nr" v-if="props.editMode" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
      </div>
      <div>
        <p class="font-bold mb-1">Nosaukums</p>
        <div v-if="!props.editMode" class="border border-black px-2 py-1 text-center"> {{ props.product.description ? props.product.description : '-'}} </div>
        <Input v-model="props.product.description" v-if="props.editMode" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
      </div>
      <div>
        <p class="font-bold mb-1">Rasējuma Nr.</p>
        <div class="border border-black px-2 py-1 text-center"> {{ props.product.drawing_nr }} </div>
      </div>
      <div>
        <p class="font-bold mb-1">Revīzija</p>
        <div v-if="!props.editMode" class="border border-black px-2 py-1 text-center"> {{ props.product.revision ? props.product.revision : '-' }} </div>
        <Input v-model="props.product.revision" v-if="props.editMode" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
      <div>
        <p class="font-bold mb-1">Izpildīšanas datums</p>
        <div v-if="!props.editMode || props.child" class="border border-black px-2 py-1 text-center h-7">{{ props.settings.po_date }}</div>
        <Input v-model="props.settings.po_date" v-if="props.editMode && !props.child" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
      </div>
      <div>
        <p class="font-bold mb-1">Daudzums</p>
        <div v-if="!props.editMode || props.child" class="border border-black px-2 py-1 text-center">{{ !props.child ? props.settings.count : props.product.count * props.settings.count }}</div>
        <Input v-model="props.settings.count" v-if="props.editMode && !props.child" class="border rounded-none border-blue-500 px-2 py-1 text-center"></Input>
      </div>
    </div>

    <div class="mb-8">
      <p class="font-bold mb-2">Procesi:</p>
      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="border border-black px-2 py-1 text-left"></th>
            <th class="border border-black px-2 py-1 text-left">Process</th>
            <th class="border border-black px-2 py-1 text-left">Subprocess</th>
            <th class="border border-black px-2 py-1 text-left">Kvalitāti pārbaudija / skaitīja</th>
            <th class="border border-black px-2 py-1 text-left">Skaits</th>
          </tr>
        </thead>
        <tbody>
          <tr ref="proc_ref" v-for="process in props.product.processes">
            <td v-if="!editMode" class="border border-black px-2 py-1">{{ process.date_completion }}</td>
            <td v-else class="border border-black px-2 py-1"><Input class="w-24 h-6" v-model="process.date_completion"></Input></td>
            <td class="border border-black px-2 py-1 h-7" :class="hightlight(process.process_list.name)">{{ process.process_list.name }}</td>
            <td class="border border-black px-2 py-1" :class="!process.sub_process ? 'text-center' : ''">{{ process.sub_process ? process.sub_process : '-' }}</td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
          </tr>
          <tr v-for="i in (10 - proc_ref.length)">
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1 h-7"></td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
            <td class="border border-black px-2 py-1"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div>
      <p class="font-bold mb-1">Materiāls:</p>
      <div class="border border-black px-2 py-1 text-center"> {{ materials ? materials : 'nav' }} </div>
      <div v-if="!props.editMode" class="border border-black px-2 py-1 p-1 w-full min-h-30 h-auto whitespace-normal break-words">{{ props.product.additional_info }}</div>
      <textarea rows="5" cols="20" style="columns: 20; column-gap: 20px; white-space: pre-wrap;"  v-if="props.editMode" class="border border-blue-400 px-2 py-1 p-1 w-full min-h-30 h-auto whitespace-normal break-words" :readonly="!editMode" v-model="props.product.additional_info"></textarea>
    </div>
  </div>
</template>

<style scoped>
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