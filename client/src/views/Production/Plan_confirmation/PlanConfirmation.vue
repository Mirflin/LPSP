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

const clients = useClientsStore()

const selectedValue = ref(null)

const rows = ref([])
const loading = ref(false)

const fetchData = async(client_id) => {
    const response = await axios.get('/api/plan-confirmation', {client_id: client_id});
    rows.value = response.data.data
    console.log(rows.value)
}

onMounted(async() => {
    loading.value = true
    await clients.fetchClients()
    loading.value = false
})
</script>

<template>
    <AdminLayout>
        <div v-if="!loading">
            <div class="mb-5">
                <Select @update:model-value="fetchData(selectedValue)" v-model="selectedValue">
                    <SelectTrigger>
                    <SelectValue placeholder="Select client" />
                    </SelectTrigger>
                    <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Clients</SelectLabel>
                        <div v-for="client in clients.clients">
                            <SelectItem :value="client.id">
                                {{client.name}}
                            </SelectItem>
                        </div>
                    </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

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
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                        </div>
                    </div>

                    <div class="mt-5 mb-3 flex gap-20">
                        <div class="flex flex-col gap-1">
                            <p>TAX No.</p>
                            <p>VAT No.</p>
                            <p>Iban</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-27">d</p>
                            <p class="w-27">d</p>
                            <p class="w-27">d</p>
                        </div>
                    </div>
                </div>
                <Separator></Separator>
                <h1 class="m-3">Files</h1>
                <Separator></Separator>
                <div class="flex justify-between">
                    <div class="mt-5 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>Customer</p>
                            <p>Legal Address</p>
                            <p>Bank</p>
                            <p>Shipping Address</p>
                            <p>Phone</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                            <p class="w-50">d</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-17">
                        <div class="flex flex-col gap-1">
                            <p>TAX No.</p>
                            <p>VAT No.</p>
                            <p>Iban</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="w-30">d</p>
                            <p class="w-30">d</p>
                            <p class="w-30">d</p>
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
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 py-1">PO-001</td>
                            <td class="border border-gray-300 py-1">John Doe Manufacturing Ltd.</td>
                            <td class="border border-gray-300 py-1">2025-08-27</td>
                            <td class="border border-gray-300 py-1">High-performance Widget A with extended description</td>
                            <td class="border border-gray-300 py-1">1</td>
                            <td class="border border-gray-300 py-1">10</td>
                            <td class="border border-gray-300 py-1">5.00</td>
                            <td class="border border-gray-300 py-1">50.00</td>
                        </tr>
                    </tbody>
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