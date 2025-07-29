import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';

import axios from 'axios';

export const useClientsStore = defineStore('clients', () => {
    const clients = ref()

    const fetchClients = async () => {
        try {
            const response = await axios.get('/api/clients');
            clients.length = 0;
            clients.value = response.data;
            return null
        } catch (error) {
            return error
        }
    }

    return{
        clients,
        fetchClients,
    }
})
