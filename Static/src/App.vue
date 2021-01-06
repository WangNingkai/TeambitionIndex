<template>
  <div id="top" class="anchor"></div>
  <Appbar></Appbar>
  <Drawer></Drawer>
  <div class="mdui-container">
    <router-view />
  </div>
</template>

<script setup>
import {onMounted} from 'vue'
import mdui from 'mdui'
import store from './libs/store'
import Appbar from './components/Appbar.vue'
import Drawer from './components/Drawer.vue'

onMounted(() => {
  mdui.$('body').removeClass('mdui-theme-layout-auto')
  let darkMode = store.get('darkMode')
  if (typeof darkMode == 'undefined' || darkMode === null) {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      store.set('darkMode', true)
      mdui.$('body').addClass('mdui-theme-layout-dark')
    } else {
      store.set('darkMode', false)
      mdui.$('body').removeClass('mdui-theme-layout-dark')
    }
  }
  if (!darkMode) {
    mdui.$('body').removeClass('mdui-theme-layout-dark')
    store.set('darkMode', false)
  } else {
    mdui.$('body').addClass('mdui-theme-layout-dark')
    store.set('darkMode', true)
  }
  mdui.mutation()
})
</script>

<style></style>
