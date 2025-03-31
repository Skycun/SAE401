<script setup lang="ts">
import type { NuxtError } from '#app'

const props = defineProps({
  error: Object as () => NuxtError
})

//Le message d'erreur est affiché dans la console mais il fonctionne
</script>

<template>
  <div class="h-svh justify-center items-center flex flex-col">
    <img src="public/images/Bikestoreslogo.png" alt="Bike Stores" class="w-1/2 mb-10" />
    <div class="flex flex-col gap-5">
      <h1 class="text-indigo-950 flex justify-center text-4xl">Erreur</h1>
      <h2 class="text-indigo-950 flex justify-center text-6xl">{{ error.statusCode }}</h2>
      <NuxtLink to="/">
        <Button class="p-5 px-10">Revenir à la page d'accueil</Button>
      </NuxtLink>
    </div>
  </div>
</template>