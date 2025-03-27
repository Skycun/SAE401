<template>
    <div>
        <h1>Product page {{ route.params.id }}</h1>
        <p>{{ product.product_name }}</p>
    </div>
</template>

<script setup>
const route = useRoute();

const { data:product} = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/products/${route.params.id}`);

</script>