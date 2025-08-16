<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 flex flex-col items-center">
      <!-- Icon -->
      <div class="mb-6 flex flex-col items-center">
         <svg version="1.0" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 564.000000 564.000000"
         width="60"
            preserveAspectRatio="xMidYMid meet">
 
                <circle cx="260" cy="260" r="230" stroke="#4d7cff" stroke-width="20" fill="none"/>
                 <line x1="320" y1="320" x2="180" y2="180" stroke="#4d7cff" stroke-width="20" stroke-linecap="round"/>
                <line x1="180" y1="320" x2="320" y2="180" stroke="#4d7cff" stroke-width="20" stroke-linecap="round"/>
            </svg>
       
        <div class="text-center mt-2">
          <span class="block font-bold text-2xl text-[#223a5f]">404 Not Found</span> 
        </div>
      </div>
      <!-- Message -->
      <div class="w-full text-center mb-6">
        <p class="text-[#223a5f] text-lg font-semibold mb-2">Oops! The page you are looking for does not exist.</p>
        <p class="text-gray-400 text-sm">It might have been moved or deleted. Please check the URL or go back to the homepage.</p>
      </div>
      <!-- Button -->
      <button
        @click="goHome"
        class="w-80 py-2 rounded-lg bg-blue-400 text-white font-semibold text-lg shadow hover:bg-blue-500 hover:cursor-pointer transition"
      >
        Go to Homepage
      </button>
      <!-- Footer --> 
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
function goHome() {
  router.push('/')
}
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