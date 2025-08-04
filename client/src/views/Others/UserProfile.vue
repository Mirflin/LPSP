<template>
  <admin-layout>
    <TimedAlert
      :type="alert_type"
      :message="alert_message"
    />
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      v-if="!loading"
      class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6"
    >
      <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
      <profile-card @fetch_profile="fetchProfile()" />
      <personal-info-card />
      <address-card />
    </div>
    <div 
      v-else
      class="flex justify-center items-center"
    >
      <div
        class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full"
      >
        <div
          class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full"
        ></div>
      </div>
    </div>
  </admin-layout>
</template>

<script setup>
import AdminLayout from '../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { onMounted, ref } from 'vue'
import TimedAlert from '@/components/TimedAlert.vue'
import ProfileCard from '../../components/profile/ProfileCard.vue'
import PersonalInfoCard from '../../components/profile/PersonalInfoCard.vue'
import AddressCard from '../../components/profile/AddressCard.vue'
import { useAuthStore } from '../../storage/auth'
import axios from 'axios'

const auth = useAuthStore()
const profile = ref({})
const loading = ref(false)
const alert_message = ref()
const alert_type = ref()

const permissionLevels = {}

const fetchProfile = async () => {
  loading.value = true
  try {
    profile.value = (await axios.post('/api/profile', {'user_id': auth.user.id})).data
    auth.profile = profile.value
    profile.value.permission = auth.profile.permission
  } catch (error) {
    alert_message.value = "Click 'Edit' and complete your profile"
    alert_type.value = "info"
    setTimeout(() => {
      alert_message.value = null
      alert_type.value = null
    },3000)
    console.error('Error fetching profile:', error)
  }
  loading.value = false
}

onMounted(async () => {
  fetchProfile()
})


const currentPageTitle = ref('User Profile')
</script>
