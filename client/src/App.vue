<template>
  <ThemeProvider>
    <Suspense>
      <SidebarProvider>
        <RouterView />
      </SidebarProvider>
    </Suspense>
  </ThemeProvider>
</template>

<script setup>
import ThemeProvider from './components/layout/ThemeProvider.vue'
import SidebarProvider from './components/layout/SidebarProvider.vue'
</script>
