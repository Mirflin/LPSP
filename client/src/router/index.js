import { useAuthStore } from '@/storage/auth.js';
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Ecommerce',
      component: () => import('../views/Ecommerce.vue'),
      meta: {
        title: 'eCommerce Dashboard',
        requireAuth: true
      },
    },
    {
      path: '/calendar',
      name: 'Calendar',
      component: () => import('../views/Others/Calendar.vue'),
      meta: {
        title: 'Calendar',
        requireAuth: true
      },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('../views/Others/UserProfile.vue'),
      meta: {
        title: 'Profile',
        requireAuth: true
      },
    },
    {
      path: '/credentials',
      name: 'Credentials',
      component: () => import('../views/Admin/LPSP.vue'),
      meta: {
        title: 'Credentials',
        requireAuth: true
      },
    },
    {
      path: '/clients',
      name: 'Clients',
      component: () => import('../views/Pages/Clients.vue'),
      meta: {
        title: 'Clients',
        requireAuth: true
      },
    },
    {
      path: '/form-elements',
      name: 'Form Elements',
      component: () => import('../views/Forms/FormElements.vue'),
      meta: {
        title: 'Form Elements',
        requireAuth: true
      },
    },
    {
      path: '/basic-tables',
      name: 'Basic Tables',
      component: () => import('../views/Tables/BasicTables.vue'),
      meta: {
        title: 'Basic Tables',
        requireAuth: true
      },
    },
    {
      path: '/line-chart',
      name: 'Line Chart',
      component: () => import('../views/Chart/LineChart/LineChart.vue'),
      meta: {
        requireAuth: true
      },
    },
    {
      path: '/bar-chart',
      name: 'Bar Chart',
      component: () => import('../views/Chart/BarChart/BarChart.vue'),
      meta: {
        requireAuth: true
      },
    },
    {
      path: '/alerts',
      name: 'Alerts',
      component: () => import('../views/UiElements/Alerts.vue'),
      meta: {
        title: 'Alerts',
        requireAuth: true
      },
    },
    {
      path: '/avatars',
      name: 'Avatars',
      component: () => import('../views/UiElements/Avatars.vue'),
      meta: {
        title: 'Avatars',
        requireAuth: true
      },
    },
    {
      path: '/badge',
      name: 'Badge',
      component: () => import('../views/UiElements/Badges.vue'),
      meta: {
        title: 'Badge',
        requireAuth: true
      },
    },

    {
      path: '/buttons',
      name: 'Buttons',
      component: () => import('../views/UiElements/Buttons.vue'),
      meta: {
        title: 'Buttons',
        requireAuth: true
      },
    },

    {
      path: '/images',
      name: 'Images',
      component: () => import('../views/UiElements/Images.vue'),
      meta: {
        title: 'Images',
        requireAuth: true
      },
    },
    {
      path: '/videos',
      name: 'Videos',
      component: () => import('../views/UiElements/Videos.vue'),
      meta: {
        title: 'Videos',
        requireAuth: true
      },
    },
    {
      path: '/blank',
      name: 'Blank',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: {
        title: 'Blank',
        requireAuth: true
      },
    },

    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('../views/Errors/FourZeroFour.vue'),
      meta: {
        title: '404 Error',
        requireAuth: true
      },
    },

    {
      path: '/signin',
      name: 'Signin',
      component: () => import('../views/Auth/Signin.vue'),
      meta: {
        title: 'Signin',
        guest: true
      },
    },
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('../views/Auth/Signup.vue'),
      meta: {
        title: 'Signup',
        guest: true
      },
    },
    {
      path: '/product',
      name: 'Product',
      component: () => import('../views/Production/Products/Product.vue'),
      meta: {
        title: 'Product',
        requireAuth: true
      },
    },
    {
      path: '/plan',
      name: 'Plan',
      component: () => import('../views/Production/Plan.vue'),
      meta: {
        title: 'Plan',
        requireAuth: true
      },
    },
    {
      path: '/materials',
      name: 'Materials',
      component: () => import('../views/Production/Materials/Materials.vue'),
      meta: {
        title: 'Materials',
        requireAuth: true
      },
    },
    {
      path: '/product/:id',
      name: 'ProductView',
      component: () => import('../views/Production/Products/ProductView.vue'),
      meta: {
        title: 'ProductView',
        requireAuth: true
      },
    },
    {
      path: '/product-create',
      name: 'ProductView',
      component: () => import('../views/Production/Products/ProductView.vue'),
      meta: {
        title: 'ProductView',
        requireAuth: true
      },
    },
  ],
})

router.beforeEach(async (to , from, next) => {
  const auth = useAuthStore()

  if(!auth.isAuthResolved){
    await auth.attempt()
  }

  if(to.meta.requireAuth && !auth.authenticated){
    next('/signin')
  }else if(to.meta.guest && auth.authenticated){
    next('/')
  }
  else{
    next()
  }

})

export default router
