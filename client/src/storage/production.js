import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';

import axios from 'axios';

export const useProductionStore = defineStore('production', () => {
    const products = ref()
    const plans = ref()
    const materials = ref()

    const fetch = async () => {
        try {
            const response = await axios.get('/api/production');
            products.value = response.data.products;
            plans.value = response.data.plans;
            materials.value = response.data.materials;
        } catch (error) {
            console.error("Failed to fetch production data:", error);
        }
    }

    return{
        products,
        plans,
        materials,
        fetch,
    }
})
