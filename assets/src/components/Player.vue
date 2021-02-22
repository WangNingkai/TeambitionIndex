<template>
  <video v-if="type === 'video'" id="player" crossorigin playsinline controls :poster="poster">
    <source :src="source" type="video/mp4" />
  </video>
  <audio v-else id="player" crossorigin controls>
    <source :src="source" type="audio/mp3" />
    <source :src="source" type="audio/ogg" />
  </audio>
</template>
<script setup>
import {defineProps, onMounted} from 'vue'
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

defineProps({
  source: {
    type: String,
    default: '',
  },
  poster: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'video',
  },
})

onMounted(() => {
  const player = new Plyr(`#player`)
})
</script>
