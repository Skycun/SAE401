<template>
    <SearchBar v-model="query"/>
    <!-- Filtrer -->
    <UCollapsible class="flex flex-col gap-2 mx-5 rounded-[20px] bg-indigo-600 p-2 my-10">
        <UButton class="group" label="Filtres" color="neutral" variant="ghost" trailing-icon="i-lucide-chevron-down"
            :ui="{
                trailingIcon: 'group-data-[state=open]:rotate-180 transition-transform duration-200'
            }" block />
        <template #content>
            <div class="p-2">
                <p class="mb-2 text-lg">By Price</p>
                <div class="flex flex-row gap-2">
                    <UInput type="number" v-model="minPrice" label="Min Price" placeholder="0" class="w-full mb-2" />
                    <p>to</p>
                    <UInput type="number" v-model="maxPrice" label="Max Price" placeholder="1000" class="w-full mb-2" />
                </div>
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Year</p>
                <div class="flex flex-row gap-2">
                    <UInput type="number" label="Min Year" placeholder="2012" class="w-full mb-2" />
                    <p>to</p>
                    <UInput type="number" label="Max Year" placeholder="2023" class="w-full mb-2" />
                </div>
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Category</p>
                <USelect label="Select Category" placeholder="Select Category" class="w-full mb-2" />
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Brand</p>
                <USelect label="Select Brand" placeholder="Select Brand" class="w-full mb-2" :items="brands" name="brand_name" option-attribute="store_name"/>
            </div>
        </template>
    </UCollapsible>

    <p class="text-sky-950">Prix: {{ minPrice }} - {{ maxPrice }}</p>
    <p v-if="filteredProducts.length === 0" class="text-center mt-5 text-sky-950">Aucun résultat ne correspond à vos critères</p>

    <!-- Des Produits mis en avant -->
    <div v-else>
        <div class="grid grid-cols-2 px-5 gap-5">
            <!-- Mobile Card -->
            <div v-for="product in filteredProducts" :key="product.product_id">
                <MobileCard :product="product"/>
            </div>
        </div>
    </div>
    
</template>

<script setup>
import { ref, computed, watch } from 'vue';


const query = ref('');
const minPrice = ref(0);
const maxPrice = ref(1000);

const { data: brands } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands');


// Récupération des produits depuis l'API
const { data: products } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products');

// Création d'une propriété calculée qui filtrera automatiquement les produits
const filteredProducts = computed(() => {
    if (!products.value) return [];
    
    return products.value.filter(product => {
        // Filtre par prix
        const matchesPrice = product.list_price >= minPrice.value && product.list_price <= maxPrice.value;
        
        // Filtre par recherche (nom du produit)
        const matchesQuery = query.value.trim() === '' || 
            product.product_name.toLowerCase().includes(query.value.toLowerCase());
        
        // Retourne true seulement si les deux conditions sont remplies
        return matchesPrice && matchesQuery;
    });
});

// Observer les changements de query pour des opérations supplémentaires si nécessaire
watch(query, (newQuery) => {
    console.log('Recherche:', newQuery);
});

// Observer les changements de prix pour des opérations supplémentaires si nécessaire
watch([minPrice, maxPrice], ([newMin, newMax]) => {
    console.log('Plage de prix modifiée:', newMin, newMax);
});
</script>
