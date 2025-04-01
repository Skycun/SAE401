<template>
    {{ table }}
</template>

<script setup>

//Prévenir des gens qui n'ont pas le cookie de connection
const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}

const route = useRoute();
const table = ref(route.params.id);

if(route.params.id != 'brands' && route.params.id != 'categories' && route.params.id != 'products' && route.params.id != 'stocks' && route.params.id != 'stores' && route.params.id != 'employees'){
    throw createError({ statusCode: 404, statusMessage: 'ID invalide' });
}
</script>