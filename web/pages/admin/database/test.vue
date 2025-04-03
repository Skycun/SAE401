<template>
    <UTable :data="data" class="bg-indigo-600 m-5 rounded-[20px]"/>
</template>

<script setup>


const data = ref([
    {
        brand_id: "1",
        brand_name: "Brand 1",
    },
    {
        brand_id: "2",
        brand_name: "Brand 2",
    }
]);

</script>