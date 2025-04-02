<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <div class="m-5 bg-white rounded-[20px] p-5 mt-10">
            <h2 class="text-indigo-950 text-2xl flex justify-center items-center mb-5">Action</h2>
            <USelect label="Select Category" v-model="mode" :items="actions" placeholder="Select an action"
                class="w-full mb-2" />
        </div>
        <div v-if="mode == 'update' || mode == 'delete'" class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select the {{ table }}</h2>
            <USelectMenu label="Select {{ table }}" v-model="itemToEdit" :items="editFetch"
                placeholder="Select an action" class="w-full mb-2" />
            <div v-if="mode == 'delete' && itemToEdit" class='mt-5'>
                <UModal :open="openModal" :title="`Delete ${itemToEdit.label}`">
                    <Button class="bg-red-600 p-2" @click="openModal = true" >Delete</Button>

                    <template #body>
                        <p>Are you sure to delete {{ itemToEdit.label }}?</p>
                        <pre>{{  itemToEdit}}</pre>
                        <Button class=" p-2 mt-5" @click="openModal = false">Cancel</Button>
                        <Button class="bg-red-600 p-2 my-5" @click="deleteItem" >Delete anyway</Button>
                    </template>
                </UModal>
            </div>
        </div>


    </div>
</template>

<script setup>
//Prévenir des gens qui n'ont pas le cookie de connection
const user_data = useCookie('user_data');
if (!user_data.value) {
    const router = useRouter();
    router.push('/login');
}

const openModal = ref(false);

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
const toast = useToast();

if (route.params.id != 'brands' && route.params.id != 'categories' && route.params.id != 'products' && route.params.id != 'stocks' && route.params.id != 'stores' && route.params.id != 'employees') {
    throw createError({ statusCode: 404, statusMessage: 'ID invalide' });
}

//Delete
async function deleteItem() {
    const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}/${itemToEdit.value.value}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Api': `e8f1997c763`
        }
    });
    console.log(data.value);
    const toast = useToast();
    toast.add({
        title: 'Item deleted',
        description: `The item ${itemToEdit.value.label} from ${table.value} has been deleted`
    });
    openModal.value = false;
    itemToEdit.value = null;
}


// Edit
const itemToEdit = ref(null);
const editFetch = ref('');

watch(mode, async (newMode) => {
    if (newMode === 'update' || newMode === 'delete') {
        const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`);
        console.log(data.value);
        switch (table.value) {
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

/*


        <div v-if="table == 'employees'" class="m-5 bg-white rounded-[20px] p-5 flex flex-col gap-5">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Fill informations</h2>
            <UFormField label="Full name" required>
                <UInput placeholder="Enter full name" class="w-full"/>
            </UFormField>
            <UFormField label="Email" required>
                <UInput placeholder="Enter email" class="w-full"/>
            </UFormField>
            <UFormField label="Password" required>
                <UInput placeholder="Enter password" class="w-full"/>
            </UFormField>
            <UFormField label="Role" required>
                <USelect label="Select Role" :items="[{ label: 'Role 1', value: 'store1' }, { label: 'Role 2', value: 'store2' }]" 
                placeholder="Select a Role" class="w-full mb-2" />
            </UFormField>
            <UFormField label="Store" required>
                <USelect label="Select Store" :items="[{ label: 'Store 1', value: 'store1' }, { label: 'Store 2', value: 'store2' }]" 
                placeholder="Select a store" class="w-full mb-2" />
            </UFormField>
        </div>

*/

</script>