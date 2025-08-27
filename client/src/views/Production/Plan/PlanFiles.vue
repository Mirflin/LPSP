<script setup>
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Label from '@/components/ui/label/Label.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import {reactive, ref} from 'vue'
import FileIcons from "file-icons-vue"
import Button from '@/components/ui/Button.vue';
import axios from 'axios';
import { PDFDocument } from 'pdf-lib';
import PDFMerger from 'pdf-merger-js';
import PlanIframe from './PlanIframe.vue';

let merger = new PDFMerger()

const options = reactive({
    bom: true,
    drawing: true,
    others: true,
})

const props = defineProps(['selectedProduct'])

const loading = ref(false)
const printing = ref(false)

const mergePDFs = async(pdfFiles) => {
  const mergedPdf = await PDFDocument.create();

  for (const file of pdfFiles) {
    const pdfBytes = Uint8Array.from(atob(file.content), c => c.charCodeAt(0));
    const pdf = await PDFDocument.load(pdfBytes);
    const copiedPages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
    copiedPages.forEach(page => mergedPdf.addPage(page));
  }

  const mergedBytes = await mergedPdf.save();
  return new Blob([mergedBytes], { type: 'application/pdf' });
}

const fileArr = ref([]);

const printFiles = async() => {
    loading.value = true
    const res = await axios.post('api/download/files', {'options': options, 'product': props.selectedProduct}, {
      responseType: 'blob'
    })

    const blob = new Blob([res.data], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);

    const iframe = document.getElementById('pdfFrame2');
    iframe.src = url;

    iframe.onload = () => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    };
}

</script>

<template>
    <iframe id="pdfFrame2" style="width:0; height:0; border:0;"></iframe>
    <!--<PlanIframe v-if="printing" :loading="loading" :pdfFiles="fileArr"></PlanIframe>-->
    <div class="w-full p-5">
        <Separator></Separator>
        <h1 class="mt-5 text-xl">Files to download: </h1>
        <div>
            <h2 class="mt-3">BOM: </h2>
            <div v-for="file in props.selectedProduct.files" class="mt-3 mb-3">
                <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 3 && options.bom">
                    <FileIcons 
                        :name="file.name" :width="50" :height="50" 
                        :isFolder="false" :isMulti="false" :isLink="false" 
                        :itemStyle="{display: 'flex', alignItems: 'center'}"  
                    />
                    <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                </div>
            </div>

            <div v-for="children in props.selectedProduct.children" class="mt-3 mb-3">
                <div v-for="file in children.files">
                    <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 3 && options.bom">
                        <FileIcons 
                            :name="file.name" :width="50" :height="50" 
                            :isFolder="false" :isMulti="false" :isLink="false" 
                            :itemStyle="{display: 'flex', alignItems: 'center'}"  
                        />
                        <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                    </div>
                </div>
            </div>

            <Separator></Separator>
            <h2 class="mt-3">Drawings: </h2>
            <div v-for="file in props.selectedProduct.files" class="mt-3 mb-3">
                <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 2 && options.drawing">
                    <FileIcons 
                        :name="file.name" :width="50" :height="50" 
                        :isFolder="false" :isMulti="false" :isLink="false" 
                        :itemStyle="{display: 'flex', alignItems: 'center'}"  
                    />
                    <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                </div>
            </div>

            <div v-for="children in props.selectedProduct.children" class="mt-3 mb-3">
                <div v-for="file in children.files">
                    <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 2 && options.drawing">
                        <FileIcons 
                            :name="file.name" :width="50" :height="50" 
                            :isFolder="false" :isMulti="false" :isLink="false" 
                            :itemStyle="{display: 'flex', alignItems: 'center'}"  
                        />
                        <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                    </div>
                </div>
            </div>

            <Separator></Separator>
            <h2 class="mt-3">Others: </h2>
            <div v-for="file in props.selectedProduct.files" class="mt-3">
                <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 1 && options.others">
                    <FileIcons 
                        :name="file.name" :width="50" :height="50" 
                        :isFolder="false" :isMulti="false" :isLink="false" 
                        :itemStyle="{display: 'flex', alignItems: 'center'}"  
                    />
                    <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                </div>
            </div>

            <div v-for="children in props.selectedProduct.children" class="mt-3 mb-3">
                <div v-for="file in children.files">
                    <div class="flex gap-5 items-center cursor-pointer" @click="downloadFile" v-if="file.type == 1 && options.others">
                        <FileIcons 
                            :name="file.name" :width="50" :height="50" 
                            :isFolder="false" :isMulti="false" :isLink="false" 
                            :itemStyle="{display: 'flex', alignItems: 'center'}"  
                        />
                        <p>{{ file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed bottom-10 left-10">
        <Button @click="printFiles" class="w-auto h-8 bg-green-500 hover:bg-green-400">Print pdfs</Button>
    </div>
</template>