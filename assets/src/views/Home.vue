<template>
  <div class="mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3" style="border-radius: 8px">
    <ul class="mdui-list">
      <li class="mdui-list-item mdui-ripple">
        <div class="mdui-row mdui-col-xs-12">
          <div class="mdui-col-xs-8 mdui-col-sm-6">文件</div>
          <div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">修改时间</div>
          <div class="mdui-col-sm-1 mdui-hidden-sm-down mdui-text-right">大小</div>
          <div class="mdui-col-xs-4 mdui-col-sm-2 mdui-text-right">操作</div>
        </div>
      </li>
      <Loading v-if="data.loading" color="mdui-color-blue-200"></Loading>
      <template v-else>
        <li v-if="!data.isRoot" class="mdui-list-item mdui-ripple" @click="go(data.parentId)">
          <div class="mdui-col-xs-12">
            <a href="javascript:void(0);">
              <i class="mdui-icon material-icons">arrow_back</i>
              返回上级
            </a>
          </div>
        </li>
        <li v-if="isEmpty(data.list)" class="mdui-list-item mdui-ripple">
          <div class="mdui-col-xs-12"><i class="mdui-icon material-icons">info</i> 没有更多数据呦</div>
        </li>
        <template v-else>
          <li
            v-for="node in data.list"
            :key="node.id"
            class="mdui-list-item mdui-ripple"
            @click="go(node.nodeId, node.kind)"
          >
            <div class="mdui-row mdui-col-xs-12">
              <div class="mdui-col-xs-8 mdui-col-sm-6 mdui-text-truncate">
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
                {{ node.updated }}
              </div>
              <div class="mdui-col-sm-1 mdui-hidden-sm-down mdui-text-right">
                {{ node.kind === 'folder' ? '-' : formatSize(node.size) }}
              </div>
              <div v-if="node.kind === 'file'" class="mdui-col-xs-4 mdui-col-sm-2 mdui-text-right">
                <a
                  class="mdui-btn mdui-ripple mdui-btn-icon share"
                  aria-label="Share"
                  mdui-tooltip="{content: '分享'}"
                  @click.stop="share(node.nodeId, node.name)"
                >
                  <i class="mdui-icon material-icons">link</i>
                </a>
                <a
                  class="mdui-btn mdui-ripple mdui-btn-icon download"
                  aria-label="Download"
                  mdui-tooltip="{content: '下载'}"
                  @click.stop="download(node.downloadUrl)"
                  target="_blank"
                >
                  <i class="mdui-icon material-icons">file_download</i>
                </a>
              </div>
              <div v-else class="mdui-col-xs-4 mdui-col-sm-2 mdui-text-right">-</div>
            </div>
          </li>
        </template>
        <li v-if="data.totalCount > data.list.length" @click="loadMore()" class="mdui-list-item mdui-ripple">
          <div class="mdui-col-xs-12 mdui-typo-body-1-opacity mdui-text-center">
            加载更多 <i class="mdui-icon material-icons">expand_more</i>
          </div>
        </li>
        <li class="mdui-list-item mdui-ripple">
          <div class="mdui-col-xs-12 mdui-typo-body-1-opacity">
            {{ data.totalCount }}
            个项目
          </div>
        </li>
      </template>
    </ul>
  </div>
  <div class="mdui-dialog" id="shareDialog">
    <div class="mdui-dialog-title">分享资源</div>
    <div class="mdui-dialog-content">
      <div class="mdui-textfield">
        <i class="mdui-icon material-icons">link</i>
        <input class="mdui-textfield-input" type="text" v-model="data.shareLink" placeholder="分享链接" />
      </div>
    </div>
    <div class="mdui-dialog-actions">
      <button class="mdui-btn mdui-ripple" mdui-dialog-close mdui-dialog-cancel>关闭</button>
      <button class="mdui-btn mdui-ripple clipboard" :data-clipboard-text="data.shareLink" @click="copy()">
        复制链接
      </button>
    </div>
  </div>
  <a href="javascript:void(0);" @click="refresh()" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"
    ><i class="mdui-icon material-icons">refresh</i></a
  >
</template>
<script setup>
import {onMounted, watch, computed, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import mdui from 'mdui'
import {fetchList} from '../api/teambition'
import {createShare} from '../api/share'
import Loading from '../components/Loading.vue'
import {isEmpty, defaultValue, formatSize} from '../libs/utils'
import Clipboard from 'clipboard'

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
  shareLink: '',
})

const nodeId = computed(() => defaultValue(route.query.nodeId, ''))

const fetchNodes = async (refresh = false) => {
  data.loading = true
  await fetchList({
    nodeId: nodeId.value,
    offset: data.offset,
    limit: data.limit,
    refresh,
  }).then((res) => {
    data.loading = false
    const code = res.code
    if (code !== 0) {
      mdui.snackbar({
        message: ':( ' + res.msg,
        timeout: 2000,
        position: 'right-top',
      })
      return
    }

    const result = res.data
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

const refresh = () => {
  fetchNodes(true)
}

const loadMore = async () => {
  await fetchList({
    nodeId: nodeId.value,
    offset: data.offset,
    limit: data.limit,
  }).then((res) => {
    const code = res.code
    if (code !== 0) {
      mdui.snackbar({
        message: ':( ' + res.msg,
        timeout: 2000,
        position: 'right-top',
      })
      return
    }
    const result = res.data
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

const share = async (nodeId, name) => {
  await createShare({nodeId, name}).then((res) => {
    const result = res.data
    const code = res.code
    if (code !== 0 || res.data === null) {
      mdui.snackbar({
        message: ':( ' + res.msg,
        timeout: 2000,
        position: 'right-top',
      })
      return
    }

    data.shareLink = window.location.origin + '/s/' + result
    let inst = new mdui.Dialog('#shareDialog')
    inst.open()
  })
}

watch(
  [() => route.query, () => route.name],
  ([query, name], [preQuery, preName]) => {
    if (name === preName) {
      // 回调函数
      fetchNodes()
    }
  },
  {
    immediate: true,
    deep: true,
  },
)

onMounted(() => {
  fetchNodes()
})
</script>
