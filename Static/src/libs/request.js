import axios from 'axios'
import mdui from 'mdui'
import store from '../store'
import {getToken, removeToken} from './auth'

const config = {
  baseURL: process.env.NODE_ENV === 'development' ? 'http://192.168.199.101:9501' : '/',
  // withCredentials: true, // Check cross-site Access-Control
}
const _axios = axios.create(config)

_axios.interceptors.request.use(
  function (config) {
    const token = getToken()
    if (token) {
      config.headers.common['Token'] = token
    }
    return config
  },
  function (error) {
    return Promise.reject(error)
  },
)

// Add a response interceptor
_axios.interceptors.response.use(
  function (response) {
    const data = response.data
    if (data.code !== 200) {
      mdui.snackbar({
        message: data.msg,
        timeout: 0,
        position: 'right-top',
      })
    }
    return data
  },
  function (error) {
    if (error && error.response) {
      switch (error.response.status) {
        case 400:
          if (error.response.data.msg) {
            error.message = error.response.data.msg
          } else {
            error.message = '请求错误'
          }
          break
        case 401:
          error.message = '登录失效'
          // 退出登录
          removeToken()
          store.commit('clearUser')
          break
        case 403:
          error.message = '拒绝访问'
          break

        case 500:
          error.message = error.response.data.msg
          break
        default:
          error.message = ': ( 请求失败...刷新页面或点击按钮以重试'
          break
      }
    }
    // Do something with response error
    mdui.snackbar({
      message: error.message,
      buttonText: '重试',
      timeout: 0,
      onButtonClick: function () {
        window.location.reload()
      },
    })
    return Promise.reject(error)
  },
)

export default _axios
