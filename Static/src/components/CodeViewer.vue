<template>
  <pre>
    <code :class="`language-${language}`">{{ code }}</code>
  </pre>
</template>
<script setup>
import {defineProps} from 'vue'

defineProps({
  language: {
    type: String,
    default: 'markup',
  },
  code: {
    type: String,
    default: '',
  },
})
</script>
