<template>
  <div class="mdui-drawer mdui-drawer-close" id="main-drawer">
    <ul class="mdui-list" mdui-collapse="{accordion: true}">
      <li v-if="user === null || typeof user === 'undefined' || !user._id" class="mdui-list-item mdui-ripple">
        <router-link :to="{name: 'Login'}">
          <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">account_circle</a>
          <a href="javascript:void(0);" class="mdui-list-item-content">登陆</a>
        </router-link>
      </li>
      <template v-else>
        <li class="mdui-list-item mdui-ripple">
          <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">account_circle</a>
          <a href="javascript:void(0);" class="mdui-list-item-content">{{ user.name }}</a>
        </li>
        <router-link :to="{name: 'Home'}" class="mdui-list-item mdui-ripple" tag="li">
          <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">home</a>
          <a href="javascript:void(0);" class="mdui-list-item-content"> 首页 </a>
        </router-link>

        <router-link :to="{name: 'Share'}" class="mdui-list-item mdui-ripple" tag="li">
          <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">link</a>
          <a href="javascript:void(0);" class="mdui-list-item-content">分享</a>
        </router-link>
        <li class="mdui-list-item mdui-ripple" @click="handleLogout()">
          <a href="javascript:void(0);" class="mdui-list-item-icon mdui-icon material-icons">exit_to_app</a>
          <a href="javascript:void(0);" class="mdui-list-item-content">退出登陆</a>
        </li>
      </template>

      <li class="mdui-divider"></li>
      <li class="mdui-list-item mdui-ripple">
        <a
          href="https://github.com/WangNingkai/teambition-index"
          target="_blank"
          class="mdui-list-item-icon mdui-icon material-icons"
          >code</a
        >
        <a href="https://github.com/WangNingkai/teambition-index" target="_blank" class="mdui-list-item-content"
          >Github</a
        >
      </li>
    </ul>
  </div>
</template>

<script setup>
import {computed, onMounted} from 'vue'
import {useRouter} from 'vue-router'
import mdui from 'mdui'
import {useStore} from 'vuex'
const store = useStore()
const router = useRouter()
const user = computed(() => store.state.user)
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
