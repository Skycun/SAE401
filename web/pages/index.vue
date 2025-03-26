<template>
    <SearchBar />
    <!-- Les 3 boutons d'accueil -->
    <section class="mt-10 mb-10">
        <div class="bg-indigo-600 min-h-52 mx-5 rounded-[20px] bg-cover bg-center bg-no-repeat p-5 flex justify-between flex-col" 
             style="background-image: url('/images/moutain-rider.jpg');">
             <h2 class="text-indigo-600 text-2xl w-[50%]">Discover the Electra bikes</h2>
             <button class="bg-indigo-600 text-indigo-50 py-2 rounded-[20px] w-[50%]">
                Discover
             </button>
        </div>
        <div class="grid grid-cols-2 mt-5 gap-5">
            <div class="bg-indigo-600 ml-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
            style="background-image: url('/images/look-sky.png');">
                <h2 class="text-indigo-600 text-2xl">Our Stores</h2>
            </div>
            <div class="bg-indigo-600 mr-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
            style="background-image: url('/images/grass-bike.jpg');">
                <h2 class="text-indigo-600 text-2xl">Road Bikes</h2>
            </div>
        </div>
    </section>
    <!-- Des Produits mis en avant -->
    <section class="grid grid-cols-2 px-5 gap-5">
        <!-- Mobile Card -->
        <MobileCard />
    </section>
</template>

<script setup>
const {data: products} = await useFetch('');
</script>