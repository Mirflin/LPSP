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
            return error
        }
    }


    const deleteProducts = async (rows) => {
        try {
            for (const row of rows) {
                const response = await axios.delete(`/api/products/${row.id}`)
            }
            return null
        } catch(error) {
            return error
        }
    }

    const createMaterial = async (form) => {
        try {
            const response = await axios.post('/api/material-create', form)
            return null
        } catch(error) {
            return error
        }
    }

    const updateMaterial = async (form) => {
        try {
            const response = await axios.patch('/api/material-update', form)
            return null
        } catch(error) {
            return error
        }
    }

    const deleteMaterials = async (rows) => {
        try {
            for (const row of rows) {
                const response = await axios.delete(`/api/material/${row.id}`)
            }
            return null
        } catch(error) {
            return error
        }
    }

    return{
        products,
        plans,
        materials,
        createMaterial,
        updateMaterial,
        deleteMaterials,
        fetch,
    }
})
