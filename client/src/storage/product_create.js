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
        "materials": [],
        "client": '',
        "files": [],
        "childs": [],
        "processes": [{'id': 1, 'process': '', 'subprocess': '', 'price': 0, 'additional_price': 0}],
    })

    const save = async() => {
        product.value.files.forEach(file => {
            delete file.file
            console.log(file)
        });
        console.log(product.value)
        try{
            const response = await axios.post('/api/product-create', product.value)
            return null
        } catch(error){
            return error
        }
    }

    function appendFormData(formData, data, parentKey = '') {
        if (data === null || data === undefined) return;

        if (Array.isArray(data)) {
            data.forEach((item, index) => {
            appendFormData(formData, item, `${parentKey}[${index}]`);
            });
        } else if (typeof data === 'object' && !(data instanceof File)) {
            Object.keys(data).forEach(key => {
            const value = data[key];
            const newKey = parentKey ? `${parentKey}[${key}]` : key;
            appendFormData(formData, value, newKey);
            });
        } else {
            formData.append(parentKey, data);
        }
        }

    return{
        save,
        product
    }
})
