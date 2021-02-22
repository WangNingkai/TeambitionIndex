<template>
  <div id="top" class="anchor"></div>
  <Appbar></Appbar>
  <Drawer></Drawer>
  <div class="mdui-container">
    <router-view />
  </div>
</template>

<script setup>
import {onMounted, computed} from 'vue'
import mdui from 'mdui'
import Appbar from '../components/Appbar.vue'
import Drawer from '../components/Drawer.vue'
import {useStore} from 'vuex'

const store = useStore()
const darkMode = computed(() => store.state.darkMode)

onMounted(() => {
  let isDarkMode = darkMode.value
  mdui.$('body').removeClass('mdui-theme-layout-auto')
  if (typeof isDarkMode == 'undefined' || isDarkMode === null) {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      store.commit('toggleDarkMode', true)
      mdui.$('body').addClass('mdui-theme-layout-dark')
    } else {
      store.commit('toggleDarkMode', false)
      mdui.$('body').removeClass('mdui-theme-layout-dark')
    }
  } else {
    if (!isDarkMode) {
      mdui.$('body').removeClass('mdui-theme-layout-dark')
    } else {
      mdui.$('body').addClass('mdui-theme-layout-dark')
    }
  }

  mdui.mutation()
})
</script>

<style></style>
