<template>
  <div class="mdui-appbar mdui-appbar-fixed mdui-shadow-0 mdui-appbar-scroll-toolbar-hide">
    <div class="mdui-toolbar">
      <span
        class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white"
        mdui-tooltip="{content: '菜单'}"
        id="toggle-drawer"
        mdui-drawer="{target: '#main-drawer', swipe: true}"
        ><i class="mdui-icon material-icons">menu</i></span
      >
      <a
        mdui-tooltip="{content: '返回'}"
        class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white mdui-hidden-sm-up"
        onclick="window.history.back()"
      >
        <i class="mdui-icon material-icons">arrow_back</i>
      </a>
      <a href="#" class="mdui-typo-headline"> Teambition-Index </a>
      <div class="mdui-toolbar-spacer"></div>
    </div>
  </div>
</template>
<script setup></script>
