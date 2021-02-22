<template>
  <div class="mdui-m-t-5">
    <div class="mdui-row">
      <div class="mdui-col-md-6 mdui-col-offset-md-3 mdui-p-a-3">
        <div class="mdui-typo mdui-m-y-2">
          <div class="mdui-typo-title-opacity mdui-text-center">
            <i class="mdui-icon material-icons">lightbulb_outline</i>
            Teambition 解析索引
          </div>
        </div>
        <form @submit.prevent="handleSubmit()">
          <div class="mdui-textfield">
            <i class="mdui-icon material-icons">face</i>
            <label class="mdui-textfield-label" for="username">请输入手机号或邮箱</label>
            <input
              id="username"
              name="username"
              class="mdui-textfield-input"
              type="text"
              required
              v-model="data.username"
              autocomplete
            />
            <div class="mdui-textfield-error">手机号不能为空</div>
            <div class="mdui-textfield-helper">填写teambition账号</div>
          </div>
          <div class="mdui-textfield">
            <i class="mdui-icon material-icons">https</i>
            <label class="mdui-textfield-label" for="password">请输入密码</label>
            <input
              id="password"
              name="password"
              class="mdui-textfield-input"
              type="password"
              required
              v-model="data.password"
              autocomplete
            />
            <div class="mdui-textfield-error">密码不能为空</div>
          </div>
          <button type="submit" class="mdui-center mdui-btn mdui-ripple mdui-color-theme-accent">
            <i class="mdui-icon material-icons">fingerprint</i>
            确认
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import {onMounted, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import mdui from 'mdui'
import {login} from '../api/user'
import {useStore} from 'vuex'

const store = useStore()
const router = useRouter()
const route = useRoute()

const data = reactive({
  username: '',
  password: '',
})

const handleSubmit = () => {
  store
    .dispatch('login', {
      username: data.username,
      password: data.password,
    })
    .then((res) => {
      const code = res.code
      if (code !== 0 || res.data._id === null) {
        mdui.snackbar({
          message: ':( ' + res.msg,
          timeout: 2000,
          position: 'right-top',
        })
        return
      }
      setTimeout(() => {
        mdui.snackbar({
          message: ':) 欢迎回来',
          timeout: 2000,
          position: 'right-top',
        })
      }, 500)
      router.push({name: 'Home'})
    })
}

onMounted(() => {
  mdui.mutation()
})
</script>
