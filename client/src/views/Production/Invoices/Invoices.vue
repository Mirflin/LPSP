<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue';
import {useClientsStore} from '@/storage/clients.js'
import { useCredsStore } from '@/storage/creds.js'
import Multiselect from 'vue-multiselect'
import { onMounted, ref, watch, computed } from 'vue'
import axios from 'axios';
import moment from 'moment'
import Input from '@/components/ui/input/Input.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { toWords } from 'number-to-words'

const creds = useCredsStore()
const clients = useClientsStore()

const selectedClient = ref([])
const selectedPlan = ref()

const totalPrice = ref(0)
const rows = ref([])

const loading = ref(false)
const rowLoading = ref(false)
const mainloading = ref(false)

const planSearch = ref([])

const options = ref({
    terms_delivery: '',
    country_of_origin: 'Latvia',
    delivery_with: '',
    description: '',
    nr: '0',
    dispathch: moment().format('DD/MM/YYYY'),
    payment_term: ''
})

const info = ref([])

const totals = computed(() => {
  let discountEur = 0
  let sumWithoutVAT = 0
  let vatSum = 0
  let sumWithVAT = 0

  info.value.forEach(row => {
    const rowTotal = row.qty * row.price
    const rowDiscount = rowTotal * (row.discount / 100)
    const rowAfterDiscount = rowTotal - rowDiscount
    const rowVAT = rowAfterDiscount * (row.VAT / 100)
    const rowFinal = rowAfterDiscount + rowVAT

    discountEur += rowDiscount
    sumWithoutVAT += rowAfterDiscount
    vatSum += rowVAT
    sumWithVAT += rowFinal
  })

  return {
    discountEur,
    sumWithoutVAT,
    vatSum,
    sumWithVAT
  }
})

const updateRows = async() => {
    info.value = []
    info.value.push({
        dep: 'MET',
        order: selectedPlan.value.po_nr,
        item: selectedPlan.value.product.drawing_nr,
        qty: selectedPlan.value.planed,
        price: selectedPlan.value.price,
        discount: 0,
        VAT: 21,
        total: selectedPlan.value.price * selectedPlan.value.planed
    })

    selectedPlan.value.subplan.forEach(sub => {
        info.value.push({
            dep: 'MET',
            order: selectedPlan.value.po_nr,
            item: sub.product.drawing_nr,
            qty: selectedPlan.value.planed*sub.product.count,
            price: sub.cost,
            discount: 0,
            VAT: 21,
            total: selectedPlan.value.planed*sub.product.count*sub.cost
        })
    });

    info.value.push({
        dep: '009',
        order: 'cost - packing',
        item: 'Euro pallet',
        qty: 0,
        price: 0,
        discount: 0,
        VAT: 0,
        total: 0
    })

    info.value.push({
        dep: '009',
        order: 'cost - packing',
        item: 'Euro pallet frame',
        qty: 0,
        price: 0,
        discount: 0,
        VAT: 0,
        total: 0
    })

    info.value.push({
        dep: '009',
        order: 'Delivery cost',
        item: 'Euro pallet',
        qty: 0,
        price: 0,
        discount: 0,
        VAT: 0,
        total: 0
    })
}

const calcTotal = (row) => {
  const rowTotal = row.qty * row.price
  const rowDiscount = rowTotal * (row.discount / 100)
  const rowAfterDiscount = rowTotal - rowDiscount
  const rowVAT = rowAfterDiscount * (row.VAT / 100)
  return rowAfterDiscount + rowVAT
}

const save = async() => {
    const response = await axios.post('/api/invoice-create', {
        'info': info.value, 'options': options.value, 'client_id': selectedClient.value.id, 'plan_id': selectedPlan.value.id,
        'discountEur': totals.value.discountEur, 'sumWithoutVAT': totals.value.sumWithoutVAT, 'vatSum': totals.value.vatSum, 'sumWithVAT': totals.value.sumWithVAT,
        'words': toWords(totals.value.sumWithVAT)

    }, {
        responseType: 'blob'
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    const iframe = document.getElementById('pdfFrame5');
    iframe.src = url;
    iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    };
}

const download = async() => {
    const response = await axios.post('/api/invoice-create', {
        'info': info.value, 'options': options.value, 'client_id': selectedClient.value.id, 'plan_id': selectedPlan.value.id,
        'discountEur': totals.value.discountEur, 'sumWithoutVAT': totals.value.sumWithoutVAT, 'vatSum': totals.value.vatSum, 'sumWithVAT': totals.value.sumWithVAT,
        'words': toWords(totals.value.sumWithVAT)

    }, {
        responseType: 'blob'
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    const a = document.createElement("a");
    a.href = url;
    a.download = "invoice-"+selectedPlan.value.po_nr+".pdf";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

const fetchData = async() => {
    setTimeout( async() => {
        rowLoading.value = true
        const response = await axios.get('/api/invoice-get', {
            params: {
                client_id: selectedClient.value.id,
                po_nr: selectedPlan.value.po_nr
            }
        });
        console.log(response.data);
        /*
        totalPrice.value = response.data.total
        rows.value = response.data.data
        rowLoading.value = false
        */
    }, 500)
}

const getPlan = async (query) => {
    loading.value = true
    const response = await axios.get('/api/plan-search', {
        params: {
            search: query
        }
    });
    planSearch.value = response.data
    loading.value = false
    return response.data
}

onMounted( async() => {
    await getPlan('')
    await clients.fetchClients()
})

watch(info, (newVal) => {
  newVal.forEach(row => {
    row.total = calcTotal(row)
  })
}, { deep: true })

const payment = () => {
    options.value.payment_term =
  "After " +
  (selectedClient.value.payment_term ?? '-') +
  " days, " +
  moment().add(selectedClient.value.payment_term ?? 0, 'days').format('DD/MM/YYYY')

}

</script>

<template>
    <AdminLayout>
        <iframe id="pdfFrame5" style="display: none;"></iframe>
        <div class="flex gap-5">
            <multiselect 
                v-model="selectedPlan" id="ajax" label="name" track-by="po_nr" placeholder="Type plan nr. to search"
                open-direction="bottom" :options="planSearch" :multiple="false" :searchable="true" :loading="loading"
                :internal-search="false" :clear-on-select="false" :close-on-select="true"
                :limit="1" :max-height="600" :show-no-results="false" :hide-selected="true"
                @search-change="getPlan" @select="updateRows" class="z-[10000] mb-5"
            >
                <template #singleLabel="props">
                    <strong>
                        {{ props.option.po_nr }}
                    </strong>
                </template>

                <template #option="props">
                    <p class="w-full h-full">
                        {{ props.option.po_nr }}
                    </p>
                </template>
            </multiselect>

            <multiselect 
                id="single-select-object" v-model="selectedClient" deselect-label="Can't remove this client" track-by="name" label="name"
                placeholder="Select client" :options="clients.clients" :searchable="true" :allow-empty="false"
                aria-label="pick a client" @select="payment"
            >
                <template #singleLabel="props">
                    <strong>
                        {{ props.option.name }}
                    </strong>
                </template>

                <template #option="props">
                    <p class="w-full h-full">
                        {{ props.option.name }}
                    </p>
                </template>
            </multiselect>
            <div class="flex gap-1 items-center">
                <Label class="w-20">Invoice nr. </Label>
                <Input class="w-70" v-model="options.nr"></Input>
            </div>
        </div>

        <div class="flex w-full justify-center">
            <div ref="pdf" class="w-[210mm] h-[297mm] bg-white p-12 border-2 border-black text-sm font-sans">
                <div class="flex items-center">
                    <img src="/images/logo/main-logo.jpg" alt="LPSP Logo" class="h-30" />
                    <h1 class="mt-20 w-full text-center mr-30 text-xl font-semibold">Invoice No. LPSP-{{ moment().format('YYYY') }}  {{ options.nr }}</h1>
                </div>
                <div class="flex justify-between">
                    <div class="mt-5 mb-1 flex gap-20">
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
                <div class="flex justify-between mb-1">
                    <div class="mt-1 flex gap-17">
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

                    <div class="mt-1 flex gap-17">
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

                <div class="flex justify-between mb-1">
                    <div class="mt-1 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>Payment terms</p>
                            <p>Terms delivery</p>
                            <p>Description</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-50">{{ options.payment_term }}</p>

                            <select v-model="options.terms_delivery" class="w-50">
                                <option value="FCA LV-3414 According to Incoterms 2020">FCA LV-3414 According to Incoterms 2020</option>
                                <option value="DAP according to Incoterms 2020">DAP according to Incoterms 2020</option>
                            </select>

                            <Input v-model="options.description" class="w-50 h-5"></Input>
                        </div>
                    </div>

                    <div class="mt-1 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>Dispatch date</p>
                            <p>Delivery with</p>
                            <p>Country of origin</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-30">{{ moment().format('DD/MM/YYYY') }}</p>

                            <select v-model="options.delivery_with" class="w-30">
                                <option value="IK Alberts Freimanis">IK Alberts Freimanis</option>
                                <option value="IZ Group SIA">IZ Group SIA</option>
                                <option value="ADS Batic">ADS Batic</option>
                                <option value="DSV">DSV</option>
                                <option value="Venipack">Venipack</option>
                                <option value="LPSP Transport">LPSP Transport</option>
                                <option value="Customer transport">Customer transport</option>
                                <option value="DHL">DHL</option>
                                <option value="FedEx">FedEx</option>
                            </select>

                            <Input v-model="options.country_of_origin" class="w-30 h-5"></Input>
                        </div>
                    </div>
                </div>

                <Separator></Separator>
                
                <div class="mt-3">
                    <h1 class="w-full text-left">Justification</h1>
                    <table class="w-full text-[11px]">
                        <thead>
                            <tr class="text-center">
                                <th class="border border-black w-15 p-1">Department</th>
                                <th class="border border-black w-20 p-1">Purchase order</th>
                                <th class="border border-black w-15 p-1">Unit</th>
                                <th class="border border-black w-20 p-1">Item</th>
                                <th class="border border-black w-15 p-1">QTY</th>
                                <th class="border border-black w-15 p-1">Price Eur/pcs</th>
                                <th class="border border-black w-15 p-1">Discount (%)</th>
                                <th class="border border-black w-15 p-1">VAT (%)</th>
                                <th class="border border-black w-15 p-1">Total</th>
                            </tr>
                        </thead>
                        <tbody v-for="elem in info" class="text-center border-1">
                            <tr v-if="elem.dep == '009'" class="text-center border-1">
                                <td class="border-1 h-5">{{ elem.dep }}</td>
                                <td class="border-1">{{ elem.order }}</td>
                                <td class="border-1">pcs.</td>
                                <td class="border-1">{{ elem.item }}</td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.price"></Input></td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.qty"></Input></td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.discount"></Input></td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.VAT"></Input></td>
                                <td class="border-1"> {{ calcTotal(elem).toFixed(2) }}</td>
                            </tr>
                            <tr v-else class="text-center border-1">
                                <td class="border-1 h-5">{{ elem.dep }}</td>
                                <td class="border-1">{{ elem.order }}</td>
                                <td class="border-1">pcs.</td>
                                <td class="border-1">{{ elem.item }}</td>
                                <td class="border-1">{{ elem.qty }}</td>
                                <td class="border-1">{{ elem.price }}</td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.discount"></Input></td>
                                <td class="border-1"><Input type="number" class="w-full h-full" v-model="elem.VAT"></Input></td>
                                <td class="border-1"> {{ calcTotal(elem).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 w-full flex justify-end flex-col items-end gap-1">
                    <p>Discount (EUR): {{ totals.discountEur }} </p>
                    <p>Untaxed Amount (EUR): {{ totals.sumWithoutVAT }}</p>
                    <p>VAT (EUR): {{ totals.vatSum }}</p>
                    <p>Total (EUR): {{ totals.sumWithVAT }}</p>
                </div>

                <div class="mt-2">
                    <Separator></Separator>
                    <a class="m-3" href="https://drive.google.com/file/d/1ZCXJOuqZk_hw8OhAESjU4BeaxpscIja8/view">LPSP Sales and delivery terms</a>
                    <Separator></Separator>

                    <p class="mt-2">Total ammount in Words: {{ toWords(totals.sumWithVAT) }}</p>
                    <p class="mt-2">Salses person: Lauris Puteklis</p>

                    <Separator class="mb-2"></Separator>
                    <p class="text-center w-50 mb-1">{{ moment().format('DD/MM/YYYY') }}</p>
                    <Separator></Separator>
                    <p class="w-100">The invoice is produced electronically and valid without signaturein accordance with Article 11 part 7 of the Law "About Accounting".</p>
                </div>

            </div>
        </div>
        <div class="fixed bottom-10">
            <Button @click="save" :disabled="!selectedPlan && !selectedClient && options.nr == 0" class="bg-green-500 hover:bg-green-300 w-25">Print</Button>
        </div>

        <div class="fixed bottom-10 ml-30">
            <Button @click="download" :disabled="!selectedPlan && !selectedClient && options.nr == 0" class="bg-blue-500 hover:bg-bue-300 w-25">Download</Button>
        </div>
    </AdminLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>