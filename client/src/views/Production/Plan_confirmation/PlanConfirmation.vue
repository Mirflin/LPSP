<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {useClientsStore} from '@/storage/clients.js'
import { useCredsStore } from '@/storage/creds.js'
import Multiselect from 'vue-multiselect'
import { VPdfViewer, useLicense } from "@vue-pdf-viewer/viewer";

const creds = useCredsStore()
const clients = useClientsStore()

const selectedClient = ref([])

const rowLoading = ref(false)

const totalPrice = ref(0)

const rows = ref([])
const loading = ref(false)

const actual_rows = ref([])

useLicense('eyJkYXRhIjoiZXlKMElqb2labkpsWlMxMGNtbGhiQ0lzSW1GMmRTSTZNVGMxTnpjeU1UVTVPU3dpWkcwaU9pSXhPVEl1TVRZNExqZzRMalE0T2pVeE56TWlMQ0p1SWpvaVltSTNNMlJrT1dWbVlqYzFOell5TUNJc0ltVjRjQ0k2TVRjMU56Y3lNVFU1T1N3aVpHMTBJam9pYzNCbFkybG1hV01pTENKd0lqb2lkbWxsZDJWeUluMD0iLCJzaWduYXR1cmUiOiJwc1gxTGlkbnl2S1NCbW9CUWJoL0l2S2NkMExUOW1BWkh6SG5aQ1FkWkVQRnpaN1lqbmtkVzRrVFhGdUVzbEJIUnBRTDVrTmRqcndsVW43V1lTODlvWG4yekFhSjR1bnBMelpRSUpobUJaNEl1eE0wek53QUI0c2gydCsvR2FEQmF4UU1tZVZSOHpmTmhEYmlGeC9kRmRnMWxHZGVacDRVN1F4YnFJZWFHSjhtTE1vWlNZT1NvdTFzN2dQZ28yN2FMYy9qcXpZRmtKNjFhSXlTUUlFRE9KVVdMUXJRcE56VHBGSXRjNTdHcENYWW9hOElKR1RPaWYzRkZheCsxR0VUTjVyM2pzODlyNE9pZEhYdlJ0ZXJVWUNrcGpUTituMHVmQW1RamNrMkVEVHZLRGI1aXZVam40MDA4QlVxeWY2cjREazRka3o0RlI2Um5RR2lrd1V2dmc9PSJ9')

const fetchData = async() => {
    setTimeout( async() => {
        rowLoading.value = true
        const response = await axios.get('/api/plan-confirmation', {
            params: {
                client_id: selectedClient.value.id
            }
        });
        totalPrice.value = response.data.total
        rows.value = response.data.data
        rowLoading.value = false
    }, 500)
}

onMounted(async() => {
    loading.value = true
    await clients.fetchClients()
    await creds.fetchCreds()
    loading.value = false
})

const pdf = ref('')

const print2 = async() => {
    const response = await axios.post('/api/plan-confirmation/print', {
        client_id: selectedClient.value.id
    }, {
        responseType: 'blob'
    });
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    pdf.value = url
}

const print = async(type) => {
    const response = await axios.post('/api/plan-confirmation/print', {
        client_id: selectedClient.value.id
    }, {
        responseType: 'blob'
    });

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    if(type === 'print'){
        const iframe = document.getElementById('pdfFrame4');
        iframe.src = url;
        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }else{
        const a = document.createElement("a");
        a.href = url;
        a.download = "PO-confirmation.pdf";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}

</script>

<template>
    <AdminLayout>
        <iframe id="pdfFrame4" style="display: none;"></iframe>

        <div v-if="!loading" class="flex flex-col items-center justify-center">
            <div class="mb-5 flex justify-between w-[210mm]">

                <multiselect 
                    id="single-select-object" v-model="selectedClient" @select="fetchData" deselect-label="Can't remove this value" track-by="name" label="name"
                    placeholder="Select one" :options="clients.clients" :searchable="true" :allow-empty="false" :close-on-select="true"
                    aria-label="pick a value" class="mr-60"
                >
                </multiselect>

                <!--
                <Select @update:model-value="fetchData" v-model="selectedClient">
                    <SelectTrigger>
                    <SelectValue placeholder="Select client" />
                    </SelectTrigger>
                    <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Clients</SelectLabel>
                        <div v-for="client in clients.clients">
                            <SelectItem :value="client">
                                {{client.name}}
                            </SelectItem>
                        </div>
                    </SelectGroup>
                    </SelectContent>
                </Select>
                -->

                <div class="flex gap-5">
                    <Button @click="print2" :disabled="rows.length === 0 ? true : false" class="bg-green-500 hover:bg-green-400">Generate pdf</Button>
                </div>
            </div>

            <div class="h-200 w-full">
                <VPdfViewer
                    v-if="pdf"
                    :src="pdf"
                />
            </div>

            <!--
            <div ref="pdf" class="w-[210mm] h-[297mm] bg-white p-12 border-2 border-black text-sm font-sans">
                <div class="flex items-center">
                    <img src="/images/logo/main-logo.jpg" alt="LPSP Logo" class="h-30" />
                    <h1 class="mt-20 w-full text-center mr-30 text-xl font-semibold">Purchase order confirmation</h1>
                </div>
                <div class="flex justify-between">
                    <div class="mt-5 mb-3 flex gap-20">
                        <div class="flex flex-col gap-1">
                            <p>Supplier</p>
                            <p>Legal Address</p>
                            <p>Bank</p>
                            <p>Export Address</p>
                            <p>Phone</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-50">{{ creds.creds.name ?? '-' }}</p>
                            <p class="w-50">{{ creds.creds.address ?? '-' }}</p>
                            <p class="w-50">{{ creds.creds.bank ?? '-' }}</p>
                            <p class="w-50">{{ creds.creds.export_address ?? '-' }}</p>
                            <p class="w-50">{{ creds.creds.phone ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 mb-3 flex gap-20">
                        <div class="flex flex-col gap-1">
                            <p>TAX No.</p>
                            <p>VAT No.</p>
                            <p>Iban</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-27">{{ creds.creds.registration_nr ?? '-' }}</p>
                            <p class="w-27">{{ creds.creds.vat_nr ?? '-' }}</p>
                            <p class="w-27">{{ creds.creds.iban ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <Separator></Separator>
                <a class="m-3" href="https://drive.google.com/file/d/1ZCXJOuqZk_hw8OhAESjU4BeaxpscIja8/view">LPSP Sales and delivery terms</a>
                <Separator></Separator>
                <div class="flex justify-between mb-3">
                    <div class="mt-5 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>Customer</p>
                            <p>Legal Address</p>
                            <p>Bank</p>
                            <p>Shipping Address</p>
                            <p>Phone</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-50">{{ selectedClient.name ?? '-' }}</p>
                            <p class="w-50">{{ selectedClient.address ?? '-' }}</p>
                            <p class="w-50">{{ selectedClient.bank ?? '-' }}</p>
                            <p class="w-50">{{ selectedClient.delivery_address ?? '-' }}</p>
                            <p class="w-50">{{ selectedClient.phone ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>TAX No.</p>
                            <p>VAT No.</p>
                            <p>Iban</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-30">{{ selectedClient.registration_nr ?? '-' }}</p>
                            <p class="w-30">{{ selectedClient.vat_nr ?? '-' }}</p>
                            <p class="w-30">{{ selectedClient.iban ?? '-'}}</p>
                        </div>
                    </div>
                </div>
                <Separator></Separator>

                <table class="w-full mt-3 border-collapse border border-gray-300 text-[10px] text-center">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                        <th class="border border-gray-300 py-1 px-1">Purchase order No.</th>
                        <th class="border border-gray-300 py-1 px-1 min-w-[120px]">Customer</th>
                        <th class="border border-gray-300 py-1 px-1">Shipping date</th>
                        <th class="border border-gray-300 py-1 px-1 min-w-[200px]">Item description</th>
                        <th class="border border-gray-300 py-1 px-1">Rev</th>
                        <th class="border border-gray-300 py-1 px-1">Amount</th>
                        <th class="border border-gray-300 py-1 px-1">Price(EUR)/Pcs Excl. VAT</th>
                        <th class="border border-gray-300 py-1 px-1">Total</th>
                        </tr>
                    </thead>
                    <tbody v-if="!rowLoading">
                        <tr ref="actual_rows" v-for="row in rows">
                            <td class="border border-gray-300 py-1">{{ row.po_nr ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.customer ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.ship_date ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.desc ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.rev ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.ammount ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.price ?? '-' }}</td>
                            <td class="border border-gray-300 py-1">{{ row.total ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td class="border border-gray-300 py-1 h-5"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1 font-bold">Order sum</td>
                            <td class="border border-gray-300 py-1">{{ totalPrice }}</td>
                        </tr>

                        <tr v-for="i in (24-actual_rows.length)">
                            <td class="border border-gray-300 py-1 h-5"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                            <td class="border border-gray-300 py-1"></td>
                        </tr>
                    </tbody>
                    <div 
                        v-else
                        class="flex justify-center items-center w-full"
                    >
                        <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
                            <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
                            </div>
                        </div>
                    </div>
                </table>
            </div>
        </div>
        <div 
          v-else
          class="flex justify-center items-center"
        >
          <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
              <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
            </div>
        </div>
    -->
    </div>
    </AdminLayout>
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

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>