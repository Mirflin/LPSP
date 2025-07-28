<template>
  <router-link to="/" class="lg:hidden">
    <img class="h-15 dark:hidden" src="/images/logo/main-logo.jpg" alt="Logo" />
    <img class="hidden h-15 dark:block" src="/images/logo/main-logo.jpg" alt="Logo" />
  </router-link>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
