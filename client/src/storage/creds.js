import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';

import axios from 'axios';

export const useCredsStore = defineStore('creds', () => {
    const creds = ref([])

    const fetchCreds = async () => {
        try {
            const response = await axios.get('/api/credentials');
            if(response.data){
                creds.value = response.data;
            }
        } catch (error) {
            creds.value = []
            console.error("Failed to fetch credentials:", error);
        }
    }

    return{
        creds,
        fetchCreds
    }
},{
    persist: true
})
