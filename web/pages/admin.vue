<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <h2 class="text-indigo-950 text-2xl flex justify-center my-5">Hello {{ user_data.employees_name }} 👋</h2>
        <UBreadcrumb :items="items" class="flex justify-center"/>
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
    icon: 'material-symbols:database-outline'
  }
])

const toast = useToast()
const data = {
    title: 'Hello Brice 👋'
}

</script>
