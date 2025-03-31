<template>
    <div class="mt-5 px-4">
        <input 
            type="text" 
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @keyup.enter="handleSearch"
            class="w-full placeholder-indigo-900 bg-indigo-200 rounded-3xl h-14 px-6" 
            placeholder="Search for a product, a brand..."
        >
    </div>
</template>

<script setup>
const router = useRouter();

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
});

function handleSearch() {
  if (props.modelValue.trim()) {
    router.push({
      path: '/search',
      query: { q: props.modelValue }
    });
  }
}

const emit = defineEmits(['update:modelValue']);
</script>