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
        "materials": "",
        "client": '',
        "files": [],
        "processes": [{'id': 1, 'process': '', 'subprocess': '', 'price': 0, 'additional_price': 0}],
    })

    return{
        product
    }
})
