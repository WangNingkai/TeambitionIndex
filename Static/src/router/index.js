import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import store from '../libs/store'
import {createRouter, createWebHashHistory} from 'vue-router'

const loadView = (view) => {
    return () => import(`../views/${view}.vue`)
}
const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            name: 'Home',
            path: '/',
            component: loadView('Home'),
            meta: {
                title: 'Home',
            },
        },
        {
            name: 'Login',
            path: '/login',
            component: loadView('Login'),
            meta: {
                title: 'Login',
            },
        },
        {
            name: 'Preview',
            path: '/preview',
            component: loadView('Preview'),
            meta: {
                title: 'Preview',
            },
        }
    ]
})

router.beforeEach(async (to, from, next) => {
    NProgress.start()
    const LOGIN_PAGE_NAME = 'Login'
    const user = store.get('user')
    if (user === null || typeof user === 'undefined' || typeof user._id === 'undefined') {
        if (to.name === LOGIN_PAGE_NAME) {
            next()
        }
        next({
            name: LOGIN_PAGE_NAME,
        })
    } else {
        if (to.name === LOGIN_PAGE_NAME) {
            next({name: 'Home'})
        }
    }
    next()
})
router.afterEach(() => {
    NProgress.done()
})

export default router
