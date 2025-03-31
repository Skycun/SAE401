<template>
    <div class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-3xl text-indigo-950 flex justify-center mb-10">Hello Brice 👋</h2>
        <UButton label="Show toast" color="neutral" variant="outline" @click="showToast" />
    </div>
    
</template>

<script setup>

const toast = useToast()
const data = {
    title: 'Hello Brice 👋'
}

function showToast() {
  toast.add(data)
}
</script>
