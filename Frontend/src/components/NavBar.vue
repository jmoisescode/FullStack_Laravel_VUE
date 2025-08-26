<template>
  <nav class="navbar flex flex-col h-full justify-between max-h-screen overflow-hidden"> 
    <div class="navbar-top flex justify-center order-1 bg-blue-300"> 
      <div class="max-w-[1200px] w-full flex justify-between">
        <div class="navbar-left">
          <img
            src="../assets/logo.png"
            alt="Versatech Logo"
            class="w-10 p-1 lg:w-12  ml-[-20px]"
          />
          <span class="logo font-semibold text-2xl lg:text-3xl">Task Manager</span> 
        </div>
       <div class="navbar-right  flex items-center gap-4">
          <div class="    flex items-center gap-4" > 
            <span class="user-avatar">{{ initial }}</span>
            <button class="logout-btn"  @click="signOut()">
              <i class="fa fa-sign-out-alt"></i>
            </button>
          </div>
          
        </div> 
        
      </div>
    </div>

    <div class="navbar-bottom px-4 pt-4 border-y-1 shadow-md border-gray-200 w-full flex justify-center order-3  md:order-2 z-4" v-if="isAdmin">
      <div class="max-w-[1200px] w-full ">
        <ul class="nav-links justify-evenly md:justify-start md:px-0 md:gap-10 flex-wrap md:flex-nowrap ">
 
          <li
            v-for="item in navigationMenu"
            :key="item.value"
            @click="item.child? isSubmenu=!isSubmenu : router.push('/'+item.value)" 
            class=" w-full lg:w-auto cursor-pointer flex-1 flex justify-center lg:flex-none "  
              > 
          <div class="nav-link-item "  :class="{ active: defineRoute(route.path) === '/'+item.value }">
            <span class="nav-link-title  lg:inline hidden">{{ item.title }}</span>
            <span :class="item.icon + ' nav-link-icon lg:hidden inline'" />
            <span :class="!isSubmenu ? 'pi pi-angle-down' :'pi pi-angle-up'"  v-if="item.child"/>
          </div>
            <div class="relative  ">
            <div v-if="isSubmenu" class="absolute top-10 right-0 flex flex-col w-20 items-center p-4">
             <i v-for="subitem in item.child">
             {{ subitem.value }}
             </i>
             </div>
            </div>
          </li>
          
        </ul>
      </div>
    </div>
     
     <div class="order-2 md:order-3 h-full max-h-[cal(100% -136px)] overflow-auto mt-2">
      <slot name="page"  >
      </slot>
     </div>
     
  </nav>
  
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router' 

import { useAuthStore } from '../stores/auth';
import { useToast } from "primevue/usetoast";  

const toast = useToast()
const auth = useAuthStore();
const route = useRoute()
const router = useRouter()
const isSubmenu = ref(false) 
const isAdmin = ref(false)
const navigationMenu = ref([
  { title: 'Dashboard', value: 'admin', icon: 'pi pi-home', access:'all' },
  { title: 'Tasks', value: 'task', icon: 'pi pi-chart-bar', access:'all'} 
]) 
const me = ref('')
const initial = ref('')
const defineRoute = (routepath) => {
  return route.path.split('/').length == 4
    ? route.path.split('/').slice(0, -1).join('/')
    : route.path
}
const getMe = async()=>{ 
  try { 
      me.value = auth.user
        initial.value =   me.value.name.charAt(0).toUpperCase() || '' 
        isAdmin.value = me.value.role === 'admin'
  } catch (error) {  
     
  } 
}

const signOut = async()=>{ 
  try {
    auth.logout();
    location.reload()
    
  } catch (error) { 
     toast.add({ severity: 'error', summary: 'Error logging out', detail: error.message, life: 3000 ,
              group: "app_toast",});
  } finally { 
  } 
} 
onMounted(()=>{
getMe()
})
</script>


<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.navbar {
  width: 100%; 
  box-shadow: 0 1px 0 #e5e7eb;
}

.navbar-top { 
  display: flex;
  align-items: center;
  padding: 0 40px;
  height: 64px;
  color: #fff;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo {  
  letter-spacing: 0.5px;
}
.vip-badge {
  background: #4d7cff;
  color: #fff; 
  font-weight: 600;
  border-radius: 6px;
  padding: 3px 12px;
  margin-left: 8px;
}
 
.welcome {
  font-size: 1rem;
  font-weight: 500;
  margin-right: 8px;
}
.user-avatar {
  background: #e5e7eb;
  color: #223a5f;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
  margin-right: 8px;
}
.logout-btn {
  background: transparent;
  border: 1px solid #e5e7eb;
  color: #fff;
  border-radius: 6px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background 0.2s, color 0.2s;
}
.logout-btn:hover {
  background: #fff;
  color: #223a5f;
}

.navbar-bottom {
  background: #fff; 
}
ul.nav-links {
  display: flex;
  margin: 0;
  height: 48px;
  align-items: center;
  list-style: none;
  padding-left: 0;
}
.nav-link-item {
  font-weight: 600;
  color: #223a5f;
  cursor: pointer;
  padding: 0 0 6px 0;
  border-bottom: 2px solid transparent;
  transition: color 0.2s, border-bottom 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.nav-link-item.active {
  color: #223a5f;
  border-bottom: 3px solid #223a5f;
  background: none;
}
.nav-link-item:hover:not(.active) {
  color: #4d7cff;
}
.nav-link-title {
  /* Hidden below lg, visible on lg+ */
}
.nav-link-icon {
  font-size: 1.2rem;
  /* Visible below lg, hidden on lg+ */
}

/* Responsive utility classes (if not using Tailwind, add these) */
@media (min-width: 769px) {
  .lg\:inline {
    display: inline !important;
  }
  .lg\:hidden {
    display: none !important;
  }
}
@media (max-width: 768px) {
  .lg\:inline {
    display: none !important;
  }
  .lg\:hidden {
    display: inline !important;
  }
}
</style>