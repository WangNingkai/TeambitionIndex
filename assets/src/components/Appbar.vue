<template>
  <div class="mdui-appbar mdui-appbar-fixed mdui-shadow-0 mdui-appbar-scroll-toolbar-hide">
    <div class="mdui-toolbar">
      <span
        class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
        mdui-drawer="{target: '#main-drawer', swipe: true}"
        ><i class="mdui-icon material-icons">menu</i></span
      >
      <a
        mdui-tooltip="{content: '返回上一级'}"
        class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
        onclick="window.history.back()"
      >
        <i class="mdui-icon material-icons">arrow_back</i>
      </a>
      <a href="#" class="mdui-typo-headline"> Teambition-Index </a>
      <div class="mdui-toolbar-spacer"></div>
      <span
        class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
        mdui-tooltip="{content: '切换显示模式'}"
        @click="toggleTheme()"
      >
        <i class="mdui-icon material-icons">{{ darkMode ? 'brightness_7' : 'brightness_4' }}</i>
      </span>
      <template v-if="user !== null && typeof user !== 'undefined' && user._id">
        <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-menu="{target: '#userBox'}">
          <i class="mdui-icon material-icons">account_circle</i>
        </span>
        <ul id="userBox" class="mdui-menu">
          <li class="mdui-menu-item" disabled>
            <a href="javascript:;" class="mdui-ripple">
              <i class="mdui-menu-item-icon mdui-icon material-icons">face</i>{{ user.name }}
            </a>
          </li>
          <li class="mdui-menu-item">
            <a href="javascript:;" class="mdui-ripple" @click="handleLogout()">
              <i class="mdui-menu-item-icon mdui-icon material-icons">exit_to_app</i>退出登陆
            </a>
          </li>
        </ul>
      </template>
    </div>
  </div>
</template>
<script setup>
import {computed, onMounted} from 'vue'
import mdui from 'mdui'
import {useStore} from 'vuex'
import {useRouter} from 'vue-router'

const store = useStore()
const router = useRouter()
const darkMode = computed(() => store.state.darkMode)
const user = computed(() => store.state.user)

const toggleTheme = () => {
  let isDarkMode = darkMode.value
  if (typeof isDarkMode === 'undefined' || isDarkMode === null) {
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
