import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';
import axios from 'axios';
import {createEcho} from '@/Echo.js'

export const useProductionStore = defineStore('production', () => {
    const products = ref([])
    const plans = ref()
    const materials = ref()
    const processes = ref()
    let subscribed = false
    const product_alert = ref(false)
    const materialsRef = ref()
    const echo = createEcho()
    const total = ref(0)

    if(!subscribed){
        let products_channel = echo.private('product')
        products_channel.listen(".newproduct", async (data) => {
            product_alert.value = true
            products.value.push(data.product)
        })
        subscribed = true
    }

    const fetchPlan = async (params) => {
        try {
            const res = await axios.get("/api/plans", { params })
            plans.value = res.data.data
            total.value = res.data.total
        } catch (err) {
            console.error("Failed to fetch plan:", err)
            throw err
      }
    }

    const fetch = async () => {
        try {
            const response = await axios.get('/api/production');
            products.value = response.data.products;
            materials.value = response.data.materials;
            processes.value = response.data.processes
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

    const updateRow = async(row) => {
        try{
            await axios.patch('/api/product-update', row)
            return null
        } catch(error) {
            console.log(error)
            return error
        }
    }

    return{
        products,
        plans,
        total,
        materials,
        processes,
        product_alert,
        createMaterial,
        updateMaterial,
        deleteMaterials,
        deleteProducts,
        fetch,
        fetchPlan,
        updateRow,
    }
}, {
    persist: true
})
