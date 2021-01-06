<template>
  <div class="mdui-m-t-5">
    <div class="mdui-card mdui-shadow-3 mdui-p-a-2" style="border-radius: 8px">
      <Loading v-if="data.loading" color="mdui-color-blue-200"></Loading>
      <div v-else class="mdui-card-content">
        <div class="mdui-typo mdui-m-t-2">
          <div class="mdui-typo-title-opacity">{{ data.item.name }}</div>
          <div class="mdui-typo-subheading-opacity">{{ formatSize(data.item.size) }}</div>
        </div>
        <div class="mdui-m-t-2" style="min-height: 300px">
          <div v-if="in_array(data.item.ext, fileExtension.image)">
            <img class="mdui-img-fluid" :src="data.item.downloadUrl" :alt="data.item.name" />
          </div>
          <div v-else-if="in_array(data.item.ext, fileExtension.video)">
            <Player :source="data.item.downloadUrl" type="video" />
          </div>
          <div v-else-if="in_array(data.item.ext, fileExtension.audio)">
            <Player :source="data.item.downloadUrl" type="audio" />
          </div>
          <div v-else>
            <p>此文件暂不支持预览</p>
          </div>
        </div>
        <div class="mdui-typo mdui-m-t-2">
          <div class="mdui-textfield">
            <i class="mdui-icon material-icons">links</i>
            <input class="mdui-textfield-input" type="text" id="link" :value="data.item.downloadUrl" />
          </div>
          <button
            @click="copy()"
            data-clipboard-target="#link"
            class="clipboard mdui-btn mdui-btn-raised mdui-btn-dense mdui-ripple mdui-color-theme-accent mdui-float-right"
          >
            <i class="mdui-icon material-icons">content_copy</i> 复制
          </button>
        </div>
      </div>
    </div>
  </div>
  <a :href="data.item.downloadUrl" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"
    ><i class="mdui-icon material-icons">file_download</i></a
  >
</template>
<script setup>
import {computed, onMounted, reactive, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {in_array, defaultValue, formatSize, fileExtension, isEmpty} from '../libs/utils'
import {fetchItem} from '../api/teambition'
import Clipboard from 'clipboard'
import mdui from 'mdui'
import Player from '../components/Player.vue'
import Loading from '../components/Loading.vue'
import {useStore} from 'vuex'

const store = useStore()
const router = useRouter()
const route = useRoute()
const data = reactive({
  parentId: '',
  item: {},
  loading: false,
})
const nodeId = computed(() => defaultValue(route.query.nodeId, ''))

const user = computed(() => store.state.user)

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

const fetchNode = async () => {
  data.loading = true
  await fetchItem({
    _id: user._id,
    nodeId: nodeId.value,
    offset: data.offset,
    limit: data.limit,
  }).then((res) => {
    data.loading = false
    const result = res.result
    data.item = result
    data.parentId = result.parentId
  })
}

const go = (nodeId) => {
  if (data.loading) {
    return false
  }
  router.push({name: 'Home', query: {nodeId: nodeId}})
}

const download = (url) => {
  window.open(url, '_blank')
}

watch(
  () => route.query.nodeId,
  async (query) => {
    if (defaultValue(query, false) !== false || query === '') {
      await fetchNode()
    }
  },
)

onMounted(() => {
  fetchNode()
})
</script>
