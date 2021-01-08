<template>
  <div class="mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3" style="border-radius: 8px">
    <ul class="mdui-list">
      <li class="mdui-list-item mdui-ripple">
        <div class="mdui-row mdui-col-xs-12">
          <div class="mdui-col-xs-12 mdui-col-sm-6">文件</div>
          <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">分享时间</div>
          <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">操作</div>
        </div>
      </li>
      <Loading v-if="data.loading" color="mdui-color-blue-200"></Loading>
      <template v-else>
        <li v-if="isEmpty(data.list)" class="mdui-list-item mdui-ripple">
          <div class="mdui-col-sm-12"><i class="mdui-icon material-icons">info</i> 没有更多数据呦</div>
        </li>
        <template v-else>
          <li v-for="node in data.list" :key="node.id" class="mdui-list-item mdui-ripple">
            <div class="mdui-row mdui-col-sm-12 mdui-valign">
              <div class="mdui-col-xs-12 mdui-col-sm-6 mdui-text-truncate">
                <a data-name="{{ node.name }}" href="javascript:void(0);" aria-label="File">
                  <i class="mdui-icon material-icons"> insert_drive_file </i>
                  <span>&nbsp;{{ node.name }}</span>
                </a>
              </div>
              <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">
                {{ new Date(node.created_at * 1000).Format('yyyy-MM-dd hh:mm:ss') }}
              </div>
              <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">
                <a
                  class="clipboard mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download"
                  aria-label="Delete"
                  mdui-tooltip="{content: '复制链接'}"
                  :data-clipboard-text="`${host}/s/${node.hash}`"
                  @click="copy()"
                >
                  <i class="mdui-icon material-icons">content_copy</i>
                </a>
                <a
                  class="mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download"
                  aria-label="Delete"
                  mdui-tooltip="{content: '删除'}"
                  @click="deleteItem(node.id)"
                >
                  <i class="mdui-icon material-icons">delete</i>
                </a>
              </div>
            </div>
          </li>
        </template>
        <li v-if="data.currentPage < data.totalPage" @click="loadMore()" class="mdui-list-item mdui-ripple">
          <div class="mdui-col-sm-12 mdui-typo-body-1-opacity mdui-text-center">
            加载更多 <i class="mdui-icon material-icons">expand_more</i>
          </div>
        </li>
        <li class="mdui-list-item mdui-ripple">
          <div class="mdui-col-sm-12 mdui-typo-body-1-opacity">
            {{ data.totalCount }}
            个项目
          </div>
        </li>
      </template>
    </ul>
  </div>
</template>
<script setup>
import {onMounted, watch, computed, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import mdui from 'mdui'
import {shareList, deleteShare} from '../api/share'
import Loading from '../components/Loading.vue'
import {isEmpty, defaultValue, formatSize} from '../libs/utils'
import Clipboard from 'clipboard'

Date.prototype.Format = function (fmt) {
  let o = {
    'M+': this.getMonth() + 1, //月份
    'd+': this.getDate(), //日
    'h+': this.getHours(), //小时
    'm+': this.getMinutes(), //分
    's+': this.getSeconds(), //秒
    'q+': Math.floor((this.getMonth() + 3) / 3), //季度
    S: this.getMilliseconds(), //毫秒
  }
  if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length))
  for (let k in o)
    if (new RegExp('(' + k + ')').test(fmt))
      fmt = fmt.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ('00' + o[k]).substr(('' + o[k]).length))
  return fmt
}

const router = useRouter()
const route = useRoute()
const host = computed(() => window.location.origin)

const data = reactive({
  list: [],
  perPage: 100,
  currentPage: 1,
  totalCount: 0,
  totalPage: 1,
  loading: false,
})

const fetchList = async () => {
  data.loading = true
  await shareList({
    currentPage: data.currentPage,
    perPage: data.perPage,
  }).then((res) => {
    data.loading = false
    const result = res.result
    data.list = result.list
    data.currentPage = result.currentPage
    data.perPage = result.perPage
    data.totalCount = result.totalCount
    data.totalPage = result.totalPage
  })
}

const copy = () => {
  const clipboard = new Clipboard('.clipboard')
  clipboard.on('success', (e) => {
    mdui.snackbar({
      message: ':) 复制成功',
      timeout: 2000,
      position: 'right-top',
    })
    // 释放内存
    clipboard.destroy()
  })
  clipboard.on('error', (e) => {
    // 不支持复制
    console.error('该浏览器不支持自动复制')
    // 释放内存
    clipboard.destroy()
  })
}

const deleteItem = (id) => {
  mdui.confirm('确定删除吗', function () {
    deleteShare(id).then((res) => {
      const result = res.result
      console.log(result)
      mdui.snackbar({
        message: ':) 删除成功',
        timeout: 2000,
        position: 'right-top',
      })
      fetchList()
    })
  })
}

const loadMore = async () => {
  await shareList({
    currentPage: data.currentPage,
    perPage: data.perPage,
  }).then((res) => {
    const result = res.result
    data.currentPage = result.currentPage
    data.list = data.list.concat(result.list)
  })
}

onMounted(() => {
  fetchList()
})
</script>
