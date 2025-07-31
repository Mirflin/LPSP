<script setup>
import Alert from './ui/Alert.vue'
const props = defineProps(['type', 'message'])

</script>

<template>
    <div v-auto-animate>
        <Alert
            class="alert_center"
            v-if="props.message"
            title="Alert"
            :variant="props.type"
            :message="props.message"
        />
    </div>
</template>

<style scoped>
.alert_center{
  position: fixed;
  height: auto;
  z-index: 199999;
  width: 25rem;
  top: 10%;
  left: 50%;
  margin-left: -12.5rem;
}
</style>