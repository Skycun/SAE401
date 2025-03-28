<template>
  coucou
  <input type="text" v-model="inputValue" placeholder="Enter text" class="border p-2 rounded" />
  <p>{{ inputValue }}</p>
</template>

<script setup>
const inputValue = ref('');
</script>