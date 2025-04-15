<template>
    <h1 class="flex justify-center text-indigo-950 mt-10 text-3xl">Our Stores</h1>
    <div v-if="status === 'pending'" class="m-5 bg-white rounded-[20px] p-5 ">
        <h2>Loading Stores</h2>
    </div>
    <div v-else>
        <div v-for="store in storesData" :key="store.store_id" class="m-5 bg-white rounded-[20px] p-5 py-10">
            <h2 class="text-indigo-600 text-xl flex justify-center items-center mb-5">{{ store.store_name }}</h2>
            <p class="text-indigo-950 mb-5">Address: {{ store.street }}, {{ store.city }}, {{ store.state }} {{ store.zip_code }}</p>
            <h3 class="text-indigo-600 ">Contact the store at :</h3>
            <p class="text-indigo-950">Phone: {{ store.phone }}</p>
            <p class="text-indigo-950">Email: {{ store.email }}</p>
        </div>
    </div>
</template>

<script setup>

const {status, data:storesData} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores');

</script>