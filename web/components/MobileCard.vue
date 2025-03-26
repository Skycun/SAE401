<template>
    <!-- Product Card Mobile -->
    <div class="bg-white rounded-[20px] h-60 p-5 flex flex-col justify-between">
        <img src="../public/images/packshots/ps1.png" alt="Image de vélo" class="max-h-20 justify-center mx-auto mb-5">
        <h3 class="leading-4 break-all">Ritchey Timberwolf Frameset - 2016</h3>
        <h3 class="text-indigo-600 text-xl mb-5">749.99$</h3>
        <ReverseButton class="text-xs mt-auto">See product</ReverseButton>
    </div>
</template>

<script setup>
defineProps({
    product: {
        type: Object,
        required: true
    }
})

const randomImage = ref();
console.log(randomImage.value);
</script>