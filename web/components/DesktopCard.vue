<template>
    <!-- Desktop Card Mobile -->
    <NuxtLink :to="`/product/${product?.product_id}`">
    <div class="bg-white rounded-[20px] h-60 p-5 flex flex-row">
        <img src="../public/images/packshots/ps1.png" alt="Image de vélo" class="h-full justify-center p-5">
        <div class="flex flex-col justify-between w-full">
            <div>
                <h3 class="leading-4 break-all text-indigo-950 text-2xl">{{ product?.product_name }}</h3>
                <h3 class="text-indigo-950 text-xl mt-2">{{ product?.brand.brand_name }} - {{ product?.model_year }}</h3>
                <h3 class="text-indigo-950 text-xl">{{ product?.category.category_name }}</h3>
                <h3 class="text-indigo-600 text-4xl">{{ product?.list_price }}$</h3>
            </div>
            <ReverseButton class="text-s mt-auto self-end w-1/2">See product</ReverseButton>
        </div>
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