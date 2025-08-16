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
          path: '/',
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
  if (to.meta.auth && 
    (!VueCookieNext.isCookieAvailable(import.meta.env.VITE_APP_ACCESS_TOKEN  ) )
  ) {   
    if ( to.name === 'signup') {
      return next(to.path);
    } 
    else{
      return next('/signin')
    }
  } 
  else{
  return next(); 

  }
});

export default router
