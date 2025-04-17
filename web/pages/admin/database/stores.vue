<template>
    <section class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Stores</h2>
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
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select a store</h2>
                    <USelectMenu label="Select a store" v-model="selectedStore" :loading="loading" placeholder="Select a store" class="w-full mb-2" @change="fetchSelectedData" :items="selectStores"/>
                    <div v-if="action.value == 'delete' && selectedStore && selectedStore.value != null">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteStore">Delete</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a store</h2>
                    <UFormField label="Store Name" required>
                        <UInput label="Store name" placeholder="Enter the store name" v-model="modelData.store_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Phone" required>
                        <UInput label="Phone" placeholder="Enter the phone number" v-model="modelData.phone" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Email" required>
                        <UInput label="Email" type="email" placeholder="Enter the email address" v-model="modelData.email" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Street" required>
                        <UInput label="Street" placeholder="Enter the street address" v-model="modelData.street" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="City" required>
                        <UInput label="City" placeholder="Enter the city" v-model="modelData.city" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="State" required>
                        <UInput label="State" placeholder="Enter the state" v-model="modelData.state" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="ZIP Code" required>
                        <UInput label="ZIP Code" placeholder="Enter the ZIP code" v-model="modelData.zip_code" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addStore">Add</Button>
                </div>
                <div v-if="action.value == 'edit' && selectedStore && selectedStore.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit a store</h2>
                    <UFormField label="Store Name" required>
                        <UInput label="Store name" placeholder="Enter the store name" v-model="fetchedSelectedData.store_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Phone" required>
                        <UInput label="Phone" placeholder="Enter the phone number" v-model="fetchedSelectedData.phone" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Email" required>
                        <UInput label="Email" type="email" placeholder="Enter the email address" v-model="fetchedSelectedData.email" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Street" required>
                        <UInput label="Street" placeholder="Enter the street address" v-model="fetchedSelectedData.street" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="City" required>
                        <UInput label="City" placeholder="Enter the city" v-model="fetchedSelectedData.city" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="State" required>
                        <UInput label="State" placeholder="Enter the state" v-model="fetchedSelectedData.state" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="ZIP Code" required>
                        <UInput label="ZIP Code" placeholder="Enter the ZIP code" v-model="fetchedSelectedData.zip_code" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="editStore">Edit</Button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>

//Sécurité si l'utilisateur n'est pas connecté
const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}


const action = ref("");
const stores = ref([]);
const selectStores = ref([]);
const selectedStore = ref(null);
const fetchedSelectedData = ref({});
const loading = ref(false);
const toast = useToast();

const modelData = ref({
    store_name: null,
    phone: null,
    email: null,
    street: null,
    city: null,
    state: null,
    zip_code: null
});

// Charger les magasins au démarrage


fetchStores(); 


async function fetchStores(){
    loading.value = true;
    const { status, data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        stores.value = data.value;
        selectStores.value = data.value.map((item) => {
            return {
                label: `${item.store_name} (${item.city}, ${item.state})`,
                value: item.store_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load stores",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchSelectedData(){
    if (!selectedStore.value || !selectedStore.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores/' + selectedStore.value.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        fetchedSelectedData.value = data.value;
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load store details",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour ajouter un magasin
async function addStore() {
    // Validation des champs obligatoires
    if (!modelData.value.store_name || 
        !modelData.value.phone || 
        !modelData.value.email || 
        !modelData.value.street || 
        !modelData.value.city || 
        !modelData.value.state || 
        !modelData.value.zip_code) {
        toast.add({
            title: 'Error',
            description: "All fields are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            store_name: modelData.value.store_name,
            phone: modelData.value.phone,
            email: modelData.value.email,
            street: modelData.value.street,
            city: modelData.value.city,
            state: modelData.value.state,
            zip_code: modelData.value.zip_code
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Store added successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Réinitialiser les champs
        modelData.value = {
            store_name: null,
            phone: null,
            email: null,
            street: null,
            city: null,
            state: null,
            zip_code: null
        };
        
        // Actualiser la liste
        fetchStores();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to add store",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour modifier un magasin
async function editStore() {
    if (!selectedStore.value || !selectedStore.value.value) return;
    
    // Validation des champs obligatoires
    if (!fetchedSelectedData.value.store_name || 
        !fetchedSelectedData.value.phone || 
        !fetchedSelectedData.value.email || 
        !fetchedSelectedData.value.street || 
        !fetchedSelectedData.value.city || 
        !fetchedSelectedData.value.state || 
        !fetchedSelectedData.value.zip_code) {
        toast.add({
            title: 'Error',
            description: "All fields are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores/' + selectedStore.value.value, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            id: fetchedSelectedData.value.store_id,
            store_name: fetchedSelectedData.value.store_name,
            phone: fetchedSelectedData.value.phone,
            email: fetchedSelectedData.value.email,
            street: fetchedSelectedData.value.street,
            city: fetchedSelectedData.value.city,
            state: fetchedSelectedData.value.state,
            zip_code: fetchedSelectedData.value.zip_code
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Store updated successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Actualiser la liste et les données sélectionnées
        fetchStores();
        fetchSelectedData();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to update store",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour supprimer un magasin
async function deleteStore() {
    if (!selectedStore.value || !selectedStore.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores/' + selectedStore.value.value, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Store deleted successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Réinitialiser la sélection et actualiser la liste
        selectedStore.value = null;
        fetchedSelectedData.value = {};
        fetchStores();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to delete store",
            color: 'error',
            icon: 'bx:x'
        });
    }
}
</script>