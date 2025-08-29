<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue';
import {useClientsStore} from '@/storage/clients.js'
import { useCredsStore } from '@/storage/creds.js'
import Multiselect from 'vue-multiselect'
import { onMounted, ref } from 'vue'
import axios from 'axios';

const creds = useCredsStore()
const clients = useClientsStore()

const selectedClient = ref(null)
const selectedPlan = ref(null)

const totalPrice = ref(0)
const rows = ref([])

const loading = ref(false)
const rowLoading = ref(false)
const mainloading = ref(false)

const planSearch = ref([])

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
    console.log(response.data);
    planSearch.value = response.data
    loading.value = false
    return response.data
}

onMounted( async() => {

    await getPlan('')
    await clients.fetchClients()
})

</script>

<template>
    <AdminLayout>
        <div class="flex gap-5">
            <multiselect 
                v-model="selectedPlan" id="ajax" label="name" track-by="po_nr" placeholder="Type plan nr. to search"
                open-direction="bottom" :options="planSearch" :multiple="false" :searchable="true" :loading="loading"
                :internal-search="false" :clear-on-select="false" :close-on-select="true" :options-limit="10"
                :limit="1" :max-height="600" :show-no-results="false" :hide-selected="true"
                @search-change="getPlan" class="z-[10000] mb-5"
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
                aria-label="pick a client"
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
        </div>

    </AdminLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>