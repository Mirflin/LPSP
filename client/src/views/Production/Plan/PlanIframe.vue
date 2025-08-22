<template>
  <div>
    <div v-for="(pdf, index) in pdfUrls" :key="index" class="pdf-container" style="margin-bottom: 20px;">
      <iframe :src="pdf" width="100%" height="600px" style="border:none;"></iframe>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, toRef } from 'vue'
import Button from '@/components/ui/Button.vue'

const props = defineProps(['pdfFiles','loading'])

const pdfUrls = ref([])

onMounted(() => {
  pdfUrls.value = props.pdfFiles.map(file => {
    if (file.startsWith('data:application/pdf;base64,')) {
      return file
    } else if (/^[A-Za-z0-9+/=]+$/.test(file)) {
      return `data:application/pdf;base64,${file}`
    } else {
      return file
    }
  })
  printPdfs()
})

const printPdfs = () => {
  const printWindow = window.open('', '_blank')
  pdfUrls.value.forEach(url => {
    printWindow.document.write(`
      <iframe src="${url}" style="width:100%; height:100vh; border:none;"></iframe>
      <div style="page-break-after: always;"></div>
    `)
  })
  printWindow.document.close()
  printWindow.focus()
  printWindow.print()
}
</script>
