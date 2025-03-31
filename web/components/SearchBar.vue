<template>
  <div class="mt-5 px-4">
      <input 
          type="text" 
          v-model="inputValue"
          @keyup.enter="handleSearch"
          class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-3xl h-14 px-6" 
          placeholder="Search for a product, a brand..."
      >
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
const router = useRouter();

const props = defineProps({
modelValue: {
  type: String,
  default: ''
}
});

const emit = defineEmits(['update:modelValue']);

// Créer une propriété locale qui reflète la valeur du modèle
const inputValue = ref(props.modelValue);

// Synchroniser inputValue avec modelValue (bidirectionnellement)
watch(() => props.modelValue, (newVal) => {
inputValue.value = newVal;
});

watch(inputValue, (newVal) => {
emit('update:modelValue', newVal);
});

function handleSearch() {
console.log('Recherche avec:', inputValue.value); // Pour déboguer
if (inputValue.value.trim()) {
  router.push({
    path: '/search',
    query: { q: inputValue.value }
  });
}
}
</script>