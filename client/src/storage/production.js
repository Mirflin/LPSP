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
    const total_products = ref(0)

    if(!subscribed){
        let channel = echo.private('plan');
        channel.listen('.plan_update', (data) => {
            const plan = plans.value.find(pl => pl.id == data.plan.id)

            plan.statuss = data.plan.statuss
            plan.statuss_label = data.plan.statuss_label

            plan.outsource_statuss = data.plan.outsource_statuss
            plan.outsource_statuss_label = data.plan.outsource_statuss_label
        })

        channel.listen('.plan_new', (data) => {
            const plan = plans.value.find(pl => pl.id == data.plan.id)

            if(!plan){
                plans.value.push(data.plan)
            }
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

    const fetchProducts = async (params) => {
        try {
            const res = await axios.get("/api/products", { params })
            products.value = res.data.data
            total_products.value = res.data.total
        } catch (err) {
            console.error("Failed to fetch plan:", err)
            throw err
        }
    }

    const getProductById = async (id) => {
        try {
            const res = await axios.post("/api/product-by-id", {'id': id})
            return res
        } catch (err) {
            console.error("Failed to fetch product:", err)
            throw err
        }
    }

    const fetch = async () => {
        try {
            const response = await axios.get('/api/production');
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

    const createPlan = async(row) => {
        try{
            await axios.post('/api/plan-create', row)
            return null
        } catch(error) {
            console.log(error)
            return error
        }
    }

    return{
        products,
        total_products,
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
        getProductById,
        fetchProducts,
        updateRow,
        createPlan
    }
}, {
    persist: true
})
