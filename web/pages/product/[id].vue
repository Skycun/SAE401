<template>
    <SearchBar />

    <div class="bg-white min-h-[60vh] mt-10 m-5 rounded-[20px] p-5">
        <img src="../../public/images/packshots/c-ps2.png" alt="Image de vélo" class="aspect-square object justify-center mx-auto mb-3">
        <h3 class="text-3xl">{{ product.product_name }}</h3>
        <h3 class="text-indigo-600 text-3xl">{{ product.list_price }}$</h3>
        <p>{{ product.brand.brand_name }} - {{ product.model_year }}</p>
        <p>{{ product.category.category_name }}</p>
        <p class="text-indigo-600 text-lg">in Stock</p>
        <Button class="text-xl p-3 mt-5">Buy Now</Button>
    </div>
</template>

<script setup>
const route = useRoute();

const { data:product} = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/products/${route.params.id}`);

</script>