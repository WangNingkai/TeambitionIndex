<template>
  <div class="mdui-drawer mdui-drawer-close" id="main-drawer">
    <ul class="mdui-list" mdui-collapse="{accordion: true}">
      <router-link
        v-if="data.user === null || !data.user._id"
        :to="{name: 'Login'}"
        class="mdui-list-item mdui-ripple"
        tag="li"
      >
        <a class="mdui-list-item-icon mdui-icon material-icons">account_circle</a>
        <a class="mdui-list-item-content">登陆</a>
      </router-link>
      <template v-else>
        <li class="mdui-list-item mdui-ripple">
          <a class="mdui-list-item-icon mdui-icon material-icons">account_circle</a>
          <a class="mdui-list-item-content">{{ data.user.name }}</a>
        </li>
        <li class="mdui-list-item mdui-ripple" @click="handleLogout()">
          <a class="mdui-list-item-icon mdui-icon material-icons">exit_to_app</a>
          <a class="mdui-list-item-content">退出登陆</a>
        </li>
      </template>
      <router-link to="/" class="mdui-list-item mdui-ripple" tag="li">
        <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">home</a>
        <a href="javascript:void(0);" class="mdui-list-item-content"> 首页 </a>
      </router-link>
      <li class="mdui-divider"></li>
      <li class="mdui-list-item mdui-ripple">
        <a href="javascript:void(0);" @click="toggleTheme()" class="mdui-list-item-icon mdui-icon material-icons"
          >brightness_4</a
        >
        <a href="javascript:void(0);" @click="toggleTheme()" class="mdui-list-item-content"
          >{{ data.darkMode ? '正常' : '暗黑' }}模式</a
        >
      </li>
      <li class="mdui-list-item mdui-ripple">
        <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">code</a>
        <a href="javascript:void(0);" class="mdui-list-item-content">Github</a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import {onMounted, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import mdui from 'mdui'
import store from '../libs/store'

const router = useRouter()
const route = useRoute()
const data = reactive({
  user: {
    _id: '',
  },
  darkMode: false,
})

data.darkMode = store.get('darkMode')
data.user = store.get('user')

const toggleTheme = () => {
  let darkMode = store.get('darkMode')
  if (typeof darkMode == 'undefined' || darkMode === null) {
    darkMode = false
  }
  if (darkMode) {
    mdui.$('body').removeClass('mdui-theme-layout-dark')
    store.set('darkMode', false)
  } else {
    mdui.$('body').addClass('mdui-theme-layout-dark')
    store.set('darkMode', true)
  }
  data.darkMode = store.get('darkMode')
}

const handleLogout = () => {
  store.set('user', {})
  router.push({path: '/'})
  setTimeout(() => {
    mdui.snackbar({
      message: ':) 退出成功',
      timeout: 2000,
      position: 'right-top',
    })
  }, 1000)
}
onMounted(() => {
  mdui.mutation()
})
</script>
