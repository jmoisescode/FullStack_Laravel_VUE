<template>
    
<div class="bg-white rounded-2xl shadow-xl w-full h-full md:h-auto  md:w-[800px] md:max-w-lg justify-center md:justify-start p-9 flex flex-col items-center">
      <!-- Logo -->
      <div class="flex flex-col items-center"> 
          <img
            src="@/assets/logo.png"
            alt="Versatech Logo"
            class="w-20 p-1  ml-[-20px]"
          />
      </div>
      <!-- Login Form -->
      <form @submit.prevent="login()" class="w-full">
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-[#223a5f] mb-1">Email</label>
          <InputText
            id="email"
            v-model="form.email"
            type="email"
            required
            class="transition ease-in-out duration-600  w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4d7cff] bg-[#f7f8fa] text-[#223a5f] transition"
            autocomplete="email"
          />
          
        </div>
        <div class="mb-4 flex flex-col ">
          <label for="password" class="block text-sm font-medium text-[#223a5f] mb-1">Password</label>
          <Password 
            id="password"
            v-model="form.password" 
            inputClass=" w-full"
            type="password" 
            required 
             :feedback="false"
            toggleMask
            placeholder="Enter your password"  
          />
        </div>
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center text-sm text-[#223a5f] cursor-pointer">
            <input type="checkbox" v-model="rememberMe" class="transition ease-in-out duration-600 accent-[#2d4e7c] mr-2" />
            Remember me
          </label>
        
        </div>
        <button
          type="submit" 
          
            :disabled="isloading"
           :class="{ 'opacity-75 cursor-not-allowed': isloading }"
          class="w-full py-2 rounded-lg transition ease-in-out duration-300 bg-gradient-to-r from-[#223a5f] to-[#2d4e7c] text-white font-semibold text-lg shadow hover:from-[#2d4e7c] hover:to-[#2d4e7c] hover:shadow-xl hover:scale-102"
        >
          Sign In
        </button>
        <div class="my-4 text-center text-sm text-gray-500">
          Don't have an account? 
          <a @click="router.push('/signup')" class="text-blue-700 hover:underline font-medium">Sign up</a>
        </div>
 
      </form> 
    </div>
</template>

<script setup> 
import { ref, onMounted ,reactive } from 'vue'  
import router from '@/router' 
import { useToast } from "primevue/usetoast";
import { useAuthStore } from '@/stores/auth';
import { Password } from 'primevue'; 
 

const toast = useToast()
const auth = useAuthStore();
const email = ref('') 
const password = ref('')
const rememberMe = ref(false) 
const isloading = ref(false);
 
const form = reactive({
  email: '',
  password: '' 
});
const errors = ref({})
const validateForm = async () => {
  try {
    // await loginSchema.validate(form.value, { abortEarly: false })
    return true
  } catch (validationErrors) {
    errors.value = {} 
    validationErrors.inner.forEach(error => {
      errors.value[error.path] = error.message
    })
    return false
  }
}

const login = async () => { 
 isloading.value = true;
  try {
    const isValid = await validateForm()
    if (!isValid) return

    const res = await auth.login(form.email, form.password)

          toast.add({ severity: 'success', summary: 'Sucess', detail: 'Login Success!', life: 3000 ,
              group: "app_toast",});
       if (rememberMe.value) {
        localStorage.setItem('remember_email', form.email)
      } else {
        localStorage.removeItem('remember_email')
      } 
       
        router.replace('/');  
 
   
  } catch (error) {  

          toast.add({ severity: 'error', summary: 'Error logging in', detail: error.message, life: 3000 ,
              group: "app_toast",});
  } finally { 
    isloading.value = false;
  }
}; 

onMounted(async() => { 
  // Load saved email if "Remember me" was checked
  const savedEmail = localStorage.getItem('remember_email')
  if (savedEmail) {
    form.email = savedEmail
    rememberMe.value = true
  }
})

</script>

<style   scoped>

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