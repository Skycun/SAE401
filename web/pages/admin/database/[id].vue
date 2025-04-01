<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <div class="m-5 bg-white rounded-[20px] p-5 mt-10">
            <h2 class="text-indigo-950 text-2xl flex justify-center items-center mb-5">Action</h2>
            <USelect label="Select Category" v-model="mode" :items="actions" placeholder="Select an action" class="w-full mb-2"/>
        </div>
        <div class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">{{ mode}} - {{ table }}</h2>
        </div>
    </div>
</template>

<script setup>

//Prévenir des gens qui n'ont pas le cookie de connection
const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}
const mode = ref('create');

const actions = ref([
    {
        label: 'Create',
        value: 'create'
    },
    {
        label: 'Update',
        value: 'update'
    },
    {
        label: 'Delete',
        value: 'delete'
    }
]);

const route = useRoute();
const table = ref(route.params.id);

if(route.params.id != 'brands' && route.params.id != 'categories' && route.params.id != 'products' && route.params.id != 'stocks' && route.params.id != 'stores' && route.params.id != 'employees'){
    throw createError({ statusCode: 404, statusMessage: 'ID invalide' });
}
</script>