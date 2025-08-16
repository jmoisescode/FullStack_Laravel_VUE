<template>
  <NavBar>
    <template #page>
      <div class="w-full overflow-hidden z-1">
        <div class="overflow-auto h-[calc(100% - 112px)] px-5 xl:px-0 py-8">
          <router-view></router-view>
        </div>
      </div>
    </template>
  </NavBar>
</template>

<script setup>
import NavBar from "@/components/NavBar.vue";
</script>

<style lang="scss" scoped></style>
