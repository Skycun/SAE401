<template>
    <!-- Product Card Mobile -->
    <NuxtLink :to="`/product/${product?.product_id}`">
    <div class="bg-white rounded-[20px] h-60 p-5 flex flex-col justify-between">
        <img src="../public/images/packshots/ps2.png" alt="Image de vélo" class="max-h-20 justify-center mx-auto mb-3">
        <h3 class="leading-4 break-all">{{ product?.product_name }}</h3>
        <h3 class="text-indigo-600 text-xl">{{ product?.list_price }}$</h3>
        <ReverseButton class="text-xs mt-auto">See product</ReverseButton>
    </div>
    </NuxtLink>
</template>

<script setup>
defineProps({
    product: {
        type: Object,
        required: false
    }
});
</script>