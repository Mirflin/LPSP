import { defineStore } from "pinia";
import {ref, computed, reactive} from 'vue';

import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref()

    const authenticated = ref(false)
    const isAuthResolved = ref(false)

    const login = async (credentials) => {
        await axios.get('/sanctum/csrf-cookie')

        try{
            await axios.post('/login', credentials)
            await attempt()

            return null
        } catch(error){

            return "Invalid Credentials"
        }
    }

    const register = async (credentials) => {
        await axios.get('/sanctum/csrf-cookie')

        try{
            await axios.post('/register', credentials)
            await attempt()
            return null
        } catch(error){
          console.log(error)
            return error
        }
    }

    const logout = async () => {
        try{
            await axios.post('/logout')

            user.value = {}
            authenticated.value = false
            return null

        } catch(error){
            return 'Logout failed'
        }
    }

    const attempt = async () => {
        try{
            const response = await axios.get('/api/user')

            user.value = response.data
            authenticated.value = true
        } catch(error){
            user.value = {}
            authenticated.value = false
        } finally{
            isAuthResolved.value = true
        }
    }

    return{
        user,
        authenticated,
        login,
        register,
        logout,
        attempt,
    }
})
