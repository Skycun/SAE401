<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <h2 class="text-indigo-950 text-2xl flex justify-center my-5">Hello {{ user_data.employees_name }} 👋</h2>
        <UBreadcrumb :items="items" class="flex justify-center mt-10"/>
        <div class="m-5 flex flex-col gap-5">
            <h2 class="text-indigo-950 text-xl flex justify-center my-5">Database</h2>
            <Button class="p-2">Brands</Button>
            <Button class="p-2">Categories</Button>
            <Button class="p-2">Products</Button>
            <Button class="p-2">Stocks</Button>
            <Button class="p-2">Stores</Button>
            <Button class="p-2">Employees</Button>
        </div>
        <h2 class="text-indigo-950 text-xl flex justify-center my-5">Sales</h2>
    </div>
</template>

<script setup>

const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}

const items = ref([
  {
    label: 'Dashboard',
    icon: 'i-lucide-grid',
  }
])

const toast = useToast()
const data = {
    title: 'Hello Brice 👋'
}

</script>
