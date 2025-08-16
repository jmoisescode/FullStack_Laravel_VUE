<script setup>
import { useToast } from "primevue/usetoast";

const toast = useToast();
</script>

<template>
  <div class="w-dvw h-dvh flex flex-col items-center bg-[#F1F4F8]">
    <router-view></router-view>
  <Toast group="app_toast" /><ConfirmPopup></ConfirmPopup>
  </div>
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
