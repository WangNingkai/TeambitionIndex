import Layout from './views/Layout.vue'
export const routes = [
  {
    name: 'Main',
    component: Layout,
    path: '/',
    children: [
      {
        name: 'Home',
        path: '/',
        component: () => import('./views/Home.vue'),
      },
      {
        name: 'Login',
        path: '/login',
        component: () => import('./views/Login.vue'),
      },
      {
        name: 'Preview',
        path: '/preview',
        component: () => import('./views/Preview.vue'),
      },
      {
        name: 'Share',
        path: '/share',
        component: () => import('./views/Share.vue'),
      },
      {
        name: 'ShareDetail',
        path: '/s/:hash',
        component: () => import('./views/ShareDetail.vue'),
      },
    ],
  },
]
