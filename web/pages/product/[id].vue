<template>
    <SearchBar />

    <div class="bg-white min-h-[60vh] mt-10 m-5 rounded-[20px] p-5">
        <img src="../../public/images/packshots/c-ps2.png" alt="Image de vélo" class="aspect-square object justify-center mx-auto mb-3">
        <h3 class="text-3xl text-indigo-950">{{ product.product_name }}</h3>
        <h3 class="text-indigo-600 text-3xl">{{ product.list_price }}$</h3>
        <p class="text-indigo-950">{{ product.brand.brand_name }} - {{ product.model_year }}</p>
        <p class="text-indigo-950">{{ product.category.category_name }}</p>

        <p v-if="product.stocks[0].quantity > 0" class="text-indigo-600 text-lg">{{ product.stocks[0].quantity }} in Stock at {{ product.stocks[0].store.store_name }}</p>
        <p v-else class="text-red-600 text-lg">0 in Stock</p>
        <Button class="text-xl p-3 mt-5">Buy Now</Button>
    </div>
    <div class="bg-white min-h-20 my-10 m-5 rounded-[20px] p-5">
        <h2 class="text-2xl text-indigo-950">Choose your Store</h2>
        <!-- Stores -->
        <div v-for="stocks in product.stocks">
            <div class="border-2 border-indigo-600 p-3 rounded-[20px] mt-5 flex justify-between self-center">
                <p class="self-center flex justify-between m-auto text-xl text-indigo-600">{{ stocks.store.store_name }}</p>
            </div>
            <p v-if="stocks.quantity > 0" class="text-lg text-indigo-600 flex justify-center">{{ stocks.quantity }} in Stock</p>
            <p v-else class="text-lg text-red-600 flex justify-center">0 in stocks</p>
        </div>
    </div>
    <div>
        <h2 class="text-2xl flex justify-center mb-10 text-indigo-950">More of this brand</h2>
        <section class="grid grid-cols-2 px-5 gap-5 mb-10">
            <!-- Mobile Card -->
            <div v-for="product in brandProduct">
                <MobileCard :product="product"/>
            </div>
        </section>
    </div>
</template>

<script setup>
const route = useRoute();

const { data:product} = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/products/${route.params.id}`);
const { data:brandProduct} = await useLazyFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/products/brand/${product.value.brand.brand_id}/10`);

</script>