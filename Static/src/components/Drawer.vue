<template>
  <div class="mdui-drawer mdui-drawer-close" id="main-drawer">
    <ul class="mdui-list" mdui-collapse="{accordion: true}">
      <router-link
        v-if="user === null || typeof user === 'undefined' || !user._id"
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
          <a class="mdui-list-item-content">{{ user.name }}</a>
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
      <li class="mdui-list-item mdui-ripple" @click="toggleTheme()">
        <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">brightness_4</a>
        <a href="javascript:void(0);" class="mdui-list-item-content">{{ darkMode ? '正常' : '暗黑' }}模式</a>
      </li>
      <li class="mdui-list-item mdui-ripple">
        <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">code</a>
        <a href="javascript:void(0);" class="mdui-list-item-content">Github</a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import {onMounted, computed, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import mdui from 'mdui'
import {useStore} from 'vuex'

const store = useStore()
const router = useRouter()
const route = useRoute()

const user = computed(() => store.state.user)
const darkMode = computed(() => store.state.darkMode)

const toggleTheme = () => {
  let isDarkMode = darkMode.value
  if (typeof isDarkMode == 'undefined' || isDarkMode === null) {
    isDarkMode = false
  }

  if (isDarkMode) {
    mdui.$('body').removeClass('mdui-theme-layout-dark')
    store.commit('toggleDarkMode', false)
  } else {
    mdui.$('body').addClass('mdui-theme-layout-dark')
    store.commit('toggleDarkMode', true)
  }
}

const handleLogout = () => {
  store.dispatch('logout')
  setTimeout(() => {
    mdui.snackbar({
      message: ':) 退出成功',
      timeout: 2000,
      position: 'right-top',
    })
    router.push({name: 'Login'})
  }, 500)
}

onMounted(() => {
  mdui.mutation()
})
</script>
