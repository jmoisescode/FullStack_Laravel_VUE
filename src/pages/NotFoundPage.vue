<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Unauthorized Access
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          You don't have permission to access this page.
        </p>
      </div>
      <div>
        <router-link 
          to="/" 
          class="text-blue-600 hover:text-blue-500"
        >
          Return to Home
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup> 
</script>

<style scoped>
@media (max-width: 480px) {
  .max-w-md {
    padding: 1.5rem !important;
  }
  svg {
    width: 56px !important;
    height: 56px !important;
  }
}
</style>