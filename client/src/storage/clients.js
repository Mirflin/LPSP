import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';

import axios from 'axios';

export const useClientsStore = defineStore('clients', () => {
    const clients = ref([])

    const fetchClients = async () => {
        try {
            const response = await axios.get('/api/clients');
            clients.value.length = 0;
            clients.value = response.data;
            return null
        } catch (error) {
            clients.value = []
            return error
        }
    }

    const createClient = async (form) => {
        try { 
            const response = await axios.post('/api/clients', form);
            return null;
        } catch (error) {
            console.log(error);
            return error
        }
    }

    const deleteClients = async (rows) => {
        try {
           for (const row of rows) {
                const response = await axios.delete(`/api/clients/${row.id}`);
           }
           return null
        } catch (error) {
            return error;
        }
    }

    return{
        clients,
        fetchClients,
        createClient,
        deleteClients
    }
})
