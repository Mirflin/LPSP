import { defineStore } from "pinia";
import {ref, computed, reactive, watch} from 'vue';

import axios from 'axios';

export const useProductCreate = defineStore('product_create', () => {
    const product = ref({
        "drawing_nr": "",
        "part_nr": "",
        "revision": "",
        "description": "",
        "additional_info": "",
        "weight": "",
        "materials": [{'id': 1, 'material': ''}],
        "client": '',
        "files": [],
        "childs": [],
        "processes": [{'id': 1, 'process': '', 'subprocess': ''}],
    })

    const reset = () => {
        product.value.drawing_nr = ''
        product.value.part_nr = ''
        product.value.revision = ''
        product.value.description = ''
        product.value.additional_info = ''
        product.value.weight = ''
        product.value.materials = []
        product.value.client = ''
        product.value.files = []
        product.value.childs = []
        product.value.processes = [{'id': 1, 'process': '', 'subprocess': ''}]
    }

    const save = async() => {
        product.value.files.forEach(file => {
            delete file.file
        });
        try{
            const response = await axios.post('/api/product-create', product.value)
            reset()
            return null
        } catch(error){
            return error
        }
    }

    return{
        save,
        reset,
        product
    }
})
