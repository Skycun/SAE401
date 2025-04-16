<template>
    <section class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Categories</h2>
            <div class="m-5 bg-white rounded-[20px] p-5 ">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Action</h2>
                <USelectMenu label="Select your action" v-model="action" placeholder="Select an action" class="w-full mb-2" :items="[
                    {
                        label: 'Add',
                        value: 'add'
                    },
                    {
                        label: 'Edit',
                        value: 'edit'
                    },
                    {
                        label: 'Delete',
                        value: 'delete'
                    }
                ]"/>
            </div>
            <div v-if="action">
                <div v-if="action.value !='add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select your category</h2>
                    <USelectMenu label="Select your category" v-model="selectedCategory" :loading="loading" placeholder="Select a data" class="w-full mb-2" @change="fetchSelectedData" :items="selectCategories"/>
                    <div v-if="action.value == 'delete' && selectedCategory.value != null">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteCategory">Delete</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a category</h2>
                    <UFormField label="Category Name" required>
                        <UInput label="Category name" placeholder="Enter the category name" v-model="modelData.category_name" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addCategory">Add</Button>
                </div>
                <div v-if="action.value == 'edit' && selectedCategory.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit a category</h2>
                    <UFormField label="Category Name" required>
                        <UInput label="Category name" placeholder="Enter the category name" v-model="fetchedSelectedData.category_name" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="editCategory">Edit</Button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const action = ref("");
const categories = ref('');
const selectCategories = ref({});
const selectedCategory = ref('');
const fetchedSelectedData = ref(''); 
const loading = ref(false);
const toast = useToast()
const modelData = ref({
    category_name: null
});

onMounted(() => {
    fetchCategories(); 
});

async function fetchCategories(){
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    categories.value = data.value;
    loading.value = status.value === 200 ? true : false;
    selectCategories.value = data.value.map((item) => {
        return {
            label: item.category_name,
            value: item.category_id
        }
    });
}

async function fetchSelectedData(){
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories/' + selectedCategory.value.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    fetchedSelectedData.value = data.value;
    loading.value = status.value === 200 ? true : false;
}

// Fonction qui permet d'ajouter une catégorie à la base de données via l'API
async function addCategory() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            category_name: modelData.value.category_name
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null){
        toast.add({
            title: 'Error',
            description: "An error occurred while adding the category",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchCategories(); // Recharger la liste des catégories
        toast.add({
            title: 'Success',
            description: 'Category added successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

// Fonction qui permet de supprimer une catégorie à la base de données via l'API
async function deleteCategory() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories/' + selectedCategory.value.value, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null){
        toast.add({
            title: 'Error',
            description: "An error occurred while deleting the category",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchCategories(); // Recharger la liste des catégories
        toast.add({
            title: 'Success',
            description: 'Category deleted successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

//Fonction qui permet de modifier une catégorie à la base de données via l'API
async function editCategory() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories/' + selectedCategory.value.value, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            id: fetchedSelectedData.value.category_id,
            category_name: fetchedSelectedData.value.category_name
        }
    });
    console.log(data.value);
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null){
        toast.add({
            title: 'Error',
            description: "An error occurred while editing the category " + data.value.error,
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchCategories(); // Recharger la liste des catégories
        toast.add({
            title: 'Success',
            description: 'Category edited successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}
</script>