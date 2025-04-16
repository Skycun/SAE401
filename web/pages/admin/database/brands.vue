<template>
    <section class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Brands</h2>
            <div class="bg-white rounded-[20px] p-5 mt-5">
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
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select your brand</h2>
                    <USelectMenu label="Select your brand" v-model="selectedBrand" :loading="loading" placeholder="Select a data" class="w-full mb-2" @change="fetchSelectedData" :items="selectBrands"/>
                    <div v-if="action.value == 'delete' && selectedBrand.value != null">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteBrand">Delete</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a brand</h2>
                    <UFormField label="Brand Name" required>
                        <UInput label="Brand name" placeholder="Enter the brand name" v-model="modelData.brand_name" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addBrand">Add</Button>
                </div>
                <div v-if="action.value == 'edit' && selectedBrand.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit a brand</h2>
                    <UFormField label="Brand Name" required>
                        <UInput label="Brand name" placeholder="Enter the brand name" v-model="fetchedSelectedData.brand_name" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="editBrand">Edit</Button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>

const action = ref("");
const brands = ref('');
const selectBrands = ref({});
const selectedBrand = ref('');
const fetchedSelectedData = ref(''); 
const loading = ref(false);
const toast = useToast()
const modelData = ref({
    brand_name: null
});

onMounted(() => {
    fetchBrands(); 
});

async function fetchBrands(){
    const { status, data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    brands.value = data.value;
    loading.value = status.value === 200 ? true : false;
    selectBrands.value = data.value.map((item) => {
        return {
            label: item.brand_name,
            value: item.brand_id
        }
    });
}

async function fetchSelectedData(){
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands/' + selectedBrand.value.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    fetchedSelectedData.value = data.value;
    loading.value = status.value === 200 ? true : false;
}

// Fonction qui permet d'ajouter une marque à la base de données via l'API
async function addBrand() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            brand_name: modelData.value.brand_name
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null){
        toast.add({
            title: 'Error',
            description: "an error occurred while adding the brand",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchBrands(); // Recharger la liste des marques
        toast.add({
            title: 'Success',
            description: 'Brand added successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

// Fonction qui permet de supprimer une marque à la base de données via l'API
async function deleteBrand() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands/' + selectedBrand.value.value, {
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
            description: "an error occurred while deleting the brand",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchBrands(); // Recharger la liste des marques
        toast.add({
            title: 'Success',
            description: 'Brand deleted successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

//Fonction qui permet de modifier une marque à la base de données via l'API
async function editBrand() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands/' + selectedBrand.value.value, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            brand_id: fetchedSelectedData.value.brand_id,
            brand_name: fetchedSelectedData.value.brand_name
        }
    });
    console.log(data.value);
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null){
        toast.add({
            title: 'Error',
            description: "an error occurred while editing the brand",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        fetchBrands(); // Recharger la liste des marques
        toast.add({
            title: 'Success',
            description: 'Brand edited successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

</script>