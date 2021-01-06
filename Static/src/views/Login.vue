<template>
  <div class="mdui-m-t-5">
    <div class="mdui-row">
      <div class="mdui-col-md-6 mdui-col-offset-md-3 mdui-p-a-3">
        <div class="mdui-typo">
          <div class="mdui-typo-title-opacity mdui-text-center">
            <i class="mdui-icon material-icons">info</i>
            Teambition 解析
          </div>
        </div>
        <form>
          <div class="mdui-textfield">
            <i class="mdui-icon material-icons">face</i>
            <label class="mdui-textfield-label" for="phone">请输入手机号或邮箱</label>
            <input
              id="phone"
              name="phone"
              class="mdui-textfield-input"
              type="text"
              required
              v-model="data.phone"
              autocomplete
            />
            <div class="mdui-textfield-error">手机号不能为空</div>
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
          <button
            type="button"
            class="mdui-center mdui-btn mdui-ripple mdui-color-theme"
            @click.prevent="handleSubmit()"
          >
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
import store from '../libs/store'
import {login} from '../api/user'

const router = useRouter()
const route = useRoute()
const data = reactive({
  phone: '',
  password: '',
})

const handleSubmit = async () => {
  await login({
    phone: data.phone,
    password: data.password,
  }).then((res) => {
    console.log(res)
    const code = res.code
    if (code !== 200 || res.result._id === null) {
      mdui.snackbar({
        message: ':( ' + res.msg,
        timeout: 2000,
        position: 'right-top',
      })
    } else {
      store.set('user', res.result)
      router.push({path: '/'})
      // 延迟 1 秒显示欢迎信息
      setTimeout(() => {
        mdui.snackbar({
          message: ':) 欢迎回来',
          timeout: 2000,
          position: 'right-top',
        })
      }, 1000)
    }
  })
}

onMounted(() => {
  mdui.mutation()
})
</script>
