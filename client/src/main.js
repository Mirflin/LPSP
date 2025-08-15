import './assets/main.css'
// Import Swiper styles
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'

import PrimeVue from 'primevue/config';
import { createApp } from 'vue'
import App from './App.vue'
import router from '@/router/index.js'
import VueApexCharts from 'vue3-apexcharts'
import axios from 'axios'
import {createPinia} from 'pinia'
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'
import Vueform from '@vueform/vueform'
import vueformConfig from './../vueform.config'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import Checkbox from 'primevue/checkbox';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://192.168.88.48:8000";

const app = createApp(App)
const store = createPinia()
store.use(piniaPluginPersistedstate)


app.use(PrimeVue);
app.component('Checkbox', Checkbox);

app.use(store)
app.use(Vueform, vueformConfig)
app.use(autoAnimatePlugin)
app.use(router)
app.use(VueApexCharts)

app.mount('#app')
