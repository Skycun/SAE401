<template>
    <SearchBar v-model="query"/>
    <!-- Filtrer -->
    <UCollapsible class="flex flex-col gap-2 mx-5 mt-5 rounded-[20px] bg-indigo-600 p-2">
        <UButton class="group" label="Filtres" color="neutral" variant="ghost" trailing-icon="i-lucide-chevron-down"
            :ui="{
                trailingIcon: 'group-data-[state=open]:rotate-180 transition-transform duration-200'
            }" block />
        <template #content>
            <div class="p-2">
                <p class="mb-2 text-lg">By Price</p>
                <div class="flex flex-row gap-2">
                    <UInput type="number" label="Min Price" placeholder="0" class="w-full mb-2" />
                    <p>to</p>
                    <UInput type="number" label="Max Price" placeholder="1000" class="w-full mb-2" />
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
                <USelect label="Select Brand" placeholder="Select Brand" class="w-full mb-2µ" />
            </div>
        </template>
    </UCollapsible>

    <!-- Des Produits mis en avant -->
     <p>{{ query }}</p>
</template>

<script setup>
const query = ref('');
</script>
