<script setup>
defineProps(["message"]);
</script>
<template>
  <ul v-if="message">
    <li v-for="msg in message" class="text-sm text-red-600 dark:text-red-400 space-y-1">{{ msg }}</li>
  </ul>
</template>
