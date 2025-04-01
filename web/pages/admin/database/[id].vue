<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <div class="m-5 bg-white rounded-[20px] p-5 mt-10">
            <h2 class="text-indigo-950 text-2xl flex justify-center items-center mb-5">Action</h2>
            <USelect label="Select Category" v-model="mode" :items="actions" placeholder="Select an action" class="w-full mb-2"/>
        </div>
        <div v-if="mode == 'update'" class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select the {{ table }}</h2>
            <USelectMenu label="Select {{ table }}" v-model="itemToEdit" :items="editFetch" placeholder="Select an action" class="w-full mb-2"/>
        </div>


        <div class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">{{ mode }} - {{ table }}</h2>
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

// Edit
const itemToEdit = ref(null);
const editFetch = ref('');

watch(mode, async (newMode) => {
  if(newMode === 'update') {
    const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`);
    console.log(data.value);
    switch(table.value) {
      case 'brands':
        editFetch.value = data.value.map(brand => ({
          label: brand.brand_name,
          value: brand.brand_id
        }));
        break;
      case 'categories':
        editFetch.value = data.value.map(category => ({
          label: category.category_name,
          value: category.category_id
        }));
        break;
      case 'products':
        editFetch.value = data.value.map(product => ({
          label: product.product_name,
          value: product.product_id
        }));
        break;
      case 'stocks':
        editFetch.value = data.value.map(stock => ({
          label: stock.stock_id,
          value: stock.stock_id
        }));
        break;
      case 'stores':
        editFetch.value = data.value.map(store => ({
          label: store.store_name,
          value: store.store_id
        }));
        break;
      case 'employees':
        editFetch.value = data.value.map(employee => ({
          label: employee.employees_name,
          value: employee.employees_id
        }));
        break;
    }
  } else {
    editFetch.value = null; 
    itemToEdit.value = null;
  }
})
</script>