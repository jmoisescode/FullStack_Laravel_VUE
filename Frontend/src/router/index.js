import { createRouter, createWebHistory } from 'vue-router'
import { VueCookieNext } from 'vue-cookie-next';
import { useAuthStore } from '@/stores/auth';

const defaultTitle =   import.meta.env.VITE_APP_APPNAME;
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Main',
      component: () => import('@/pages/main.vue'),
      meta: {
        title: defaultTitle,
        auth: true,
      },
      
      children: [ 
        {
          path: '/task',
          name: 'Task',
          component: () => import('@/pages/TaskItem/tasklist.vue'),
        },  
           {
          path: '/admin',
          name: 'adminDashboard',
          component: () => import('@/pages/admin.vue'),
        },  
      ],
    },
    {
      path: '/login',
      name: 'auth',
      component: () => import('@/pages/login.vue'),
      meta: {
        title: defaultTitle,
        auth: false,
      },
       
      children: [ 
        {
          path: '/signin',
          name: 'signin',
          component: () => import('@/pages/auth/signin.vue'),
        },  
        
          {
          path: '/signup',
          name: 'signup',
          component: () => import('@/pages/auth/signup.vue'),
        },  
      ],
    },    
 
    {
      path: '/:pathMatch(.*)*',
      name: 'error',
      component: () => import('@/layout/NotFoundPage.vue'),
      meta: {
        error: true,
      },
    }
  ],
})
router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? to.meta.title : defaultTitle;
  const auth = useAuthStore();

  const hasToken = VueCookieNext.isCookieAvailable(import.meta.env.VITE_APP_ACCESS_TOKEN);

  // 1. If route requires auth and no token → go to signin
  if (to.meta.auth === true && !hasToken) {
    if (to.name === 'signup') {
      return next(); // allow signup
    }
    return next('/signin');
  }

  // 2. If route requires auth and token exists → check role
  if (to.meta.auth === true && hasToken && auth.user) {
    if (auth.user.role === 'user' && to.path !== '/task') {
      return next('/task'); // only redirect if not already on /task
    }
   
  }

  // 3. Otherwise continue
  return next();
});
export default router
