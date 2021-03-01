import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import storage from 'store'
import {createRouter, createWebHistory} from 'vue-router'
import {getToken} from '../libs/auth'
import {routes} from '../routes'
import store from '../store'

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  NProgress.start()
  const ACCESS_TOKEN = getToken()
  const user = storage.get('user')
  const darkMode = storage.get('darkMode')

  if (typeof user !== 'undefined') {
    store.commit('setUser', user)
  }
  if (typeof darkMode !== 'undefined') {
    store.commit('toggleDarkMode', darkMode)
  }

  const LOGIN_PAGE_NAME = 'Login'
  const allowList = ['Login', 'ShareDetail']

  if (user === null || typeof user === 'undefined' || typeof user._id === 'undefined' || !ACCESS_TOKEN) {
    if (allowList.includes(to.name)) {
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
