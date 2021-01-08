import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import storage from 'store'
import {createRouter, createWebHistory} from 'vue-router'
import {getToken} from '../libs/auth'
import store from '../store'
import Layout from '../views/Layout.vue'

const loadView = (view) => {
  return () => import(`../views/${view}.vue`)
}

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      name: 'Main',
      component: Layout,
      path: '/',
      children: [
        {
          name: 'Home',
          path: '/',
          component: loadView('Home'),
        },
        {
          name: 'Login',
          path: '/login',
          component: loadView('Login'),
        },
        {
          name: 'Preview',
          path: '/preview',
          component: loadView('Preview'),
        },
        {
          name: 'Share',
          path: '/share',
          component: loadView('Share'),
        },
        {
          name: 'ShareDetail',
          path: '/s/:hash',
          component: loadView('ShareDetail'),
        },
      ],
    },
  ],
})

router.beforeEach((to, from, next) => {
  NProgress.start()
  const ACCESS_TOKEN = getToken()
  const user = storage.get('user')
  const darkMode = storage.get('darkMode')

  store.commit('toggleDarkMode', darkMode)
  store.commit('setUser', user)

  const LOGIN_PAGE_NAME = 'Login'

  if (user === null || typeof user === 'undefined' || typeof user._id === 'undefined' || !ACCESS_TOKEN) {
    if (to.name === LOGIN_PAGE_NAME) {
      next()
    } else {
      next({
        name: LOGIN_PAGE_NAME,
      })
    }
  } else {
    if (to.name === LOGIN_PAGE_NAME) {
      next({name: 'Home'})
    } else {
      next()
    }
  }
})

router.afterEach(() => {
  NProgress.done()
})

export default router
