<template>
  <div class="mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3" style="border-radius: 8px">
    <ul class="mdui-list">
      <li class="mdui-list-item mdui-ripple">
        <div class="mdui-row mdui-col-xs-12">
          <div class="mdui-col-xs-12 mdui-col-sm-7">文件</div>
          <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">修改时间</div>
          <div class="mdui-col-sm-2 mdui-hidden-sm-down mdui-text-right">大小</div>
        </div>
      </li>
      <Loading v-if="data.loading" color="mdui-color-blue-200"></Loading>
      <template v-else>
        <li v-if="!data.isRoot" class="mdui-list-item mdui-ripple" @click="go(data.parentId)">
          <div class="mdui-col-sm-12">
            <a href="javascript:void(0);">
              <i class="mdui-icon material-icons">arrow_back</i>
              返回上级
            </a>
          </div>
        </li>
        <li v-if="isEmpty(data.list)" class="mdui-list-item mdui-ripple">
          <div class="mdui-col-sm-12"><i class="mdui-icon material-icons">info</i> 没有更多数据呦</div>
        </li>
        <template v-else>
          <li
            v-for="node in data.list"
            :key="node.id"
            class="mdui-list-item mdui-ripple"
            @click="go(node.nodeId, node.kind)"
          >
            <div class="mdui-row mdui-col-sm-12">
              <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                <a
                  v-if="node.kind === 'folder'"
                  data-name="{{ node.name }}"
                  href="javascript:void(0);"
                  aria-label="Folder"
                >
                  <i class="mdui-icon material-icons">folder_open</i>
                  <span>&nbsp;{{ node.name }}</span>
                </a>
                <a v-else data-name="{{ node.name }}" href="javascript:void(0);" aria-label="File">
                  <i class="mdui-icon material-icons"> insert_drive_file </i>
                  <span>&nbsp;{{ node.name }}</span>
                </a>
              </div>
              <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">
                {{ new Date(node.updated).Format('yyyy-MM-dd hh:mm:ss') }}
              </div>
              <div class="mdui-col-sm-2 mdui-hidden-sm-down mdui-text-right">
                {{ node.kind === 'folder' ? '-' : formatSize(node.size) }}
              </div>
            </div>
            <a
              v-if="node.kind === 'file'"
              class="mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download"
              aria-label="Download"
              @click.stop="download(node.downloadUrl)"
              target="_blank"
            >
              <i class="mdui-icon material-icons">file_download</i>
            </a>
          </li>
        </template>
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
import {fetchList} from '../api/teambition'
import Loading from '../components/Loading.vue'
import {isEmpty, defaultValue, formatSize} from '../libs/utils'
import {useStore} from 'vuex'

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

const store = useStore()
const router = useRouter()
const route = useRoute()

const data = reactive({
  parentId: '',
  list: [],
  item: {},
  limit: 100,
  offset: 0,
  totalCount: 0,
  isRoot: 1,
  loading: false,
})

const nodeId = computed(() => defaultValue(route.query.nodeId, ''))

const user = computed(() => store.state.user)

const fetchNodes = async () => {
  data.loading = true
  await fetchList({
    _id: user._id,
    nodeId: nodeId.value,
    offset: data.offset,
    limit: data.limit,
  }).then((res) => {
    data.loading = false
    const result = res.result
    data.limit = result.limit
    data.offset = result.offset
    data.totalCount = result.totalCount
    data.list = result.list
    data.item = result.item
    data.nodeId = result.item.nodeId
    data.parentId = result.item.parentId
    data.isRoot = result.isRoot
  })
}

const fetchMore = async () => {
  data.loading = true
  await fetchList({
    _id: user._id,
    nodeId: nodeId.value,
    offset: data.offset,
    limit: data.limit,
  }).then((res) => {
    data.loading = false
    const result = res.result
    data.list = data.list.concat(result.list)
  })
}

const go = (nodeId, type = 'folder') => {
  if (data.loading) {
    return false
  }
  if (type === 'file') {
    router.push({name: 'Preview', query: {nodeId: nodeId}})
  } else {
    router.push({name: 'Home', query: {nodeId: nodeId}})
  }
}

const download = (url) => {
  window.open(url, '_blank')
}

watch(
  () => route.query.nodeId,
  async (nodeId) => {
    await fetchNodes()
  },
)

onMounted(() => {
  fetchNodes()
})
</script>
