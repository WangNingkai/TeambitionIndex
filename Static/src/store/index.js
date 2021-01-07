import storage from 'store'
import {createStore} from 'vuex'
import {login as handleLogin} from '../api/user'
import {setToken} from '../libs/auth'
const store = createStore({
  state() {
    return {
      darkMode: false,
      user: {
        _id: '',
      },
    }
  },
  mutations: {
    toggleDarkMode(state, mode) {
      state.darkMode = mode
      storage.set('darkMode', mode)
    },
    setUser(state, user) {
      state.user = user
      storage.set('user', user)
    },
    clearUser(state) {
      state.user = {}
      storage.set('user', {})
    },
  },
  actions: {
    login(context, data) {
      return new Promise((resolve, reject) => {
        handleLogin({
          phone: data.phone,
          password: data.password,
        })
          .then((res) => {
            const code = res.code
            if (code === 200 || res.result._id !== null) {
              context.commit('setUser', res.result.user)
              setToken(res.result.token)
            }

            resolve(res)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    logout(context) {
      context.commit('clearUser')
    },
  },
})
export default store
