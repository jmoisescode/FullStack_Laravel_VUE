<template>
  <div
    class="bg-white rounded-2xl shadow-xl flex-1  w-full h-full md:h-auto  md:w-[800px] max-w-lg justify-center md:justify-start p-9 "
  >
    <!-- Logo -->
    <div class="flex flex-col items-center">
      <img
        src="@/assets/logo.png"
        alt="Versatech Logo"
        class="w-20 p-1 ml-[-20px]"
      />
    </div>
    <!-- Login Form -->
    <form @submit.prevent="Register()" class="w-full ">
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-[#223a5f] mb-1"
          >Email</label
        >
        <InputText
          id="email"
          v-model="form.email"
          type="email"
          required
          class="transition ease-in-out duration-600 w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4d7cff] bg-[#f7f8fa] text-[#223a5f] transition"
          autocomplete="email"
        />
      </div>
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-[#223a5f] mb-1"
          >Name</label
        >
        <InputText
          id="name"
          v-model="form.name"
          type="text"
          required
          class="transition ease-in-out duration-600 w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4d7cff] bg-[#f7f8fa] text-[#223a5f] transition"
          autocomplete="name"
        />
      </div>
      <div class="mb-4">
        <label for="Role" class="block text-sm font-medium text-[#223a5f] mb-1"
          >User Role</label
        >
        <Select
          v-model="selectedRole"
          :options="RoleList"
          optionLabel="name"
          placeholder="Select a Role"
          @change="form.role = selectedRole.code"
          class="w-full"
          required
        >
          <template #value="slotProps">
            <div v-if="slotProps.value" class="flex items-center">
              <div class="flex-1 font-bold w-40 p-1rounded-md">
                {{ slotProps.value.name }}
              </div>
            </div>
            <span v-else>
              {{ slotProps.placeholder }}
            </span>
          </template>
          <template #option="slotProps">
            <div class="flex flex-wrap items-center">
              <div class="flex-1 font-bold">
                {{ slotProps.option.name }}
              </div>
            </div>
          </template>
        </Select>
      </div>
      <div class="mb-4 w-full flex flex-col">
        <label
          for="password"
          class="block text-sm font-medium text-[#223a5f] mb-1"
          >Password</label
        >
        <Password
          id="password"
          v-model="form.password"
          type="password"
          inputClass="w-full"
          required
          :feedback="false"
          toggleMask
          placeholder="Enter your password"
        />
      </div>
      <div class="mb-4 w-full flex flex-col">
        <label
          for="password"
          class="block text-sm font-medium text-[#223a5f] mb-1"
          >Password</label
        >
        <Password
          id="password"
          v-model="form.password_confirmation"
          inputClass=" w-full"
          type="password"
          required
          :feedback="false"
          toggleMask
          placeholder="Enter your password"
        />
      </div>
      <PasswordPattern
        :password="form.password"
        :confirmPassword="form.password_confirmation"
        class="mb-4"
      />
      <button
        type="submit"
        class="w-full py-2 m-y rounded-lg transition ease-in-out duration-300 bg-gradient-to-r from-[#223a5f] to-[#2d4e7c] text-white font-semibold text-lg shadow hover:from-[#2d4e7c] hover:to-[#2d4e7c] hover:shadow-xl hover:scale-102"
      >
        Sign Up
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import router from "@/router";
import { useToast } from "primevue/usetoast";
import { useAuthStore } from "@/stores/auth";
import { Password } from "primevue";
import { signupSchema } from "@/utils/validationSchemas";
import PasswordPattern from "@/components/passwordPattern.vue";

const toast = useToast();
const auth = useAuthStore();
const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const emailError = ref("");
const passwordError = ref("");
const isloading = ref(false);
const selectedRole = ref(null);
const RoleList = ref([
  {
    name: "Admin",
    code: "admin",
  },
  {
    name: "User",
    code: "user",
  },
]);
const form = reactive({
  email: "",
  password: "",
  name: "",
  role: "",
});
const errors = ref({});
const validateForm = async () => {
  try {
    await signupSchema.validate(form.value, { abortEarly: false });
    return true;
  } catch (validationErrors) {
    errors.value = {};
    validationErrors.inner.forEach((error) => {
      errors.value[error.path] = error.message;
    });
    return false;
  }
};

const Register = async () => {
  try {
    isloading.value = true;
    await validateForm();
    await auth.register(form.email, form.password, form.name, form.role);
    toast.add({
      severity: "success",
      summary: "Success",
      detail: "Registration Successful!",
      life: 3000,
      group: "app_toast",
    });
    router.replace("/signin");
    isloading.value = false;
    if (rememberMe.value) {
      localStorage.setItem("remember_email", form.email);
    } else {
      localStorage.removeItem("remember_email");
    }
  } catch (error) {
    toast.add({
      severity: "error",
      summary: "Error",
      detail: error.message,
      life: 3000,
      group: "app_toast",
    });
  }
};
onMounted(async () => {
  // Load saved email if "Remember me" was checked
  const savedEmail = localStorage.getItem("remember_email");
  if (savedEmail) {
    email.value = savedEmail;
    rememberMe.value = true;
  }
});
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
