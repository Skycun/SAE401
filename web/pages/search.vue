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
                    <UInput type="number" v-model="maxPrice" label="Max Price" placeholder="100000" class="w-full mb-2" />
                </div>
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Year</p>
                <div class="flex flex-row gap-2">
                    <UInput type="number" v-model="minYear" label="Min Year" placeholder="2012" class="w-full mb-2" />
                    <p>to</p>
                    <UInput type="number" v-model="maxYear" label="Max Year" placeholder="2023" class="w-full mb-2" />
                </div>
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Category</p>
                <USelect label="Select Category" v-model="categoryFiltrer" :items="CategorySelect" placeholder="Select Category" class="w-full mb-2" />
            </div>
            <div class="p-2">
                <p class="mb-2 text-lg">By Brand</p>
                <USelect label="Select Brand" v-model="brandFiltrer" placeholder="Select Brand" class="w-full mb-2" :items="BrandSelect" name="brand_name" option-attribute="store_name"/>
            </div>
        </template>
    </UCollapsible>

    <p v-if="filteredProducts.length === 0" class="text-center mt-5 text-sky-950">No product found</p>

    <!-- Des Produits mis en avant -->
    <div v-else class="mb-5">
        <div class="grid grid-cols-2 px-5 gap-5">
            <!-- Mobile Card -->
            <div v-for="product in filteredProducts" :key="product.product_id">
                <MobileCard :product="product"/>
            </div>
        </div>
    </div>
</template>

<script setup>

const route = useRoute();
const query = ref('');
if(route.query.q) {
    query.value = route.query.q;
}

const minPrice = ref(0);
const maxPrice = ref(12000);
const minYear = ref(2016);
const maxYear = ref(2025);
const brandFiltrer = ref('all');
const categoryFiltrer = ref('all');

const { data: brands } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands');
const { data: categories } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories');


const BrandSelect = computed(() => {
    if (!brands.value) return [];

    let array = brands.value.map(brand => ({
        label: brand.brand_name,
        value: brand.brand_id
    }));

    array.unshift({
        label: "All brands",
        value: "all"
    });
    return array;


});
const CategorySelect = computed(() => {
    if (!categories.value) return [];


    let array = categories.value.map(category => ({
        label: category.category_name,
        value: category.category_id
    }));

    array.unshift({
        label: "All categories",
        value: "all"
    });
    return array;
});


// Récupération des produits depuis l'API
const { data: products } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products');

// Création d'une propriété calculée qui filtrera automatiquement les produits
const filteredProducts = computed(() => {
    if (!products.value) return [];
    
    return products.value.filter(product => {
        // Filtre par prix
        const matchesPrice = product.list_price >= minPrice.value && product.list_price <= maxPrice.value;
        // Filtre par année
        const matchesYear = product.model_year >= minYear.value && product.model_year <= maxYear.value;
        // Filtre par marque
        if (brandFiltrer.value === 'all') {
            brandFiltrer.value = '';
        }

        const matchesBrand = brandFiltrer.value === '' || product.brand.brand_id === brandFiltrer.value;
        // Filtre par catégorie
        if (categoryFiltrer.value === 'all') {
            categoryFiltrer.value = '';
        }

        const matchesCategory = categoryFiltrer.value === '' || product.category.category_id === categoryFiltrer.value;

        // Filtre par recherche (nom du produit)
        const matchesQuery = query.value.trim() === '' || 
            product.product_name.toLowerCase().includes(query.value.toLowerCase());
        
        // Retourne true seulement si les deux conditions sont remplies
        return matchesPrice && matchesQuery && matchesYear && matchesBrand && matchesCategory;
    });
});
</script>
