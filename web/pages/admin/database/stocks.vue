<template>
    <section class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Stocks</h2>
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
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select your stock</h2>
                    <UFormField label="Stores" required>
                        <USelectMenu label="Select a store" v-model="selectedStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores" @change="fetchProductFromStore"/>
                    </UFormField>
                    <div v-if="selectedStores.value">
                        <UFormField label="Products" required>
                            <USelectMenu label="Select a product" v-model="selectedFetchedProductFromStore" placeholder="Select a product" class="w-full mb-2" @change="getStockById" :items="selectFetchedProductFromStore"/>
                        </UFormField>
                    </div>
                    <!-- Si l'action est delete -->
                    <div v-if="action.value == 'delete' && selectedStores.value && selectedFetchedProductFromStore.value">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteStock">Delete</Button>
                    </div>
                    <!-- Si l'action est edit -->
                    <div v-else-if="action.value == 'edit' && selectedStores.value && selectedFetchedProductFromStore.value">
                        <UFormField label="Quantity" required>
                            <UInput label="Quantity" placeholder="Enter the quantity" v-model="selectedStock.quantity" class="w-full mb-2"/>
                        </UFormField>
                        <Button class="p-3 mt-5" @click="editStock">Edit</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a stock</h2>
                    <UFormField label="Product" required>
                        <USelectMenu label="Select a product" v-model="selectedProduct" placeholder="Select a product" class="w-full mb-2" :items="selectProducts"/>
                    </UFormField>
                    <UFormField label="Stores" required>
                        <USelectMenu label="Select a store" v-model="selectedStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores"/>
                    </UFormField>
                    <UFormField label="Quantity" required>
                        <UInput label="Quantity" placeholder="Enter the quantity" v-model="quantity" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addStock">Add</Button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>

const action = ref("");

const stores = ref('');
const selectStores = ref({});
const selectedStores = ref('');

const products = ref('');
const selectProducts = ref({});
const selectedProduct = ref('');

const quantity = ref(0);

const fetchedSelectedData = ref(''); 
const loading = ref(false);
const toast = useToast()
const modelData = ref({
    brand_name: null
});

const selectedStock = ref('');

const fetchedProductFromStore = ref('');
const selectFetchedProductFromStore = ref({});
const selectedFetchedProductFromStore = ref('');

onMounted(() => {
    fetchStores();
    fetchProducts();
});

async function fetchStores() {
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    stores.value = data.value;
    loading.value = status.value === 200 ? true : false;
    selectStores.value = data.value.map((item) => {
        return {
            label: item.store_name,
            value: item.store_id
        }
    });
}

async function fetchProducts(){
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    products.value = data.value;
    loading.value = status.value === 200 ? true : false;
    selectProducts.value = data.value.map((item) => {
        return {
            label: item.product_name,
            value: item.product_id
        }
    });
}

// Fonction qui permet de récupérer les produits d'un magasin spécifique via l'API
async function fetchProductFromStore(){
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks/store/' + selectedStores.value.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    console.log(data.value);
    console.log(products.value);

    fetchedProductFromStore.value = data.value;
    loading.value = status.value === 200 ? true : false;
    selectFetchedProductFromStore.value = data.value.map((item) => {
        return {
            label: item.product.product_name + " - #" + item.stock_id,
            value: item.stock_id
        }
    });
}

// Fonction pour obtenir un stock par son ID
function getStockById() {
    let stocksList = fetchedProductFromStore.value;
    let stockId = selectedFetchedProductFromStore.value.value;
    if (!stocksList || !stockId) return null;
    // Utiliser find pour récupérer le premier objet avec le stock_id correspondant
    selectedStock.value = stocksList.find(stock => stock.stock_id === stockId) || null;
}


// Fonction qui permet d'ajouter un sotck à la base de données via l'API
async function addStock(){
    const {status, data} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            product_id: selectedProduct.value.value,
            store_id: selectedStores.value.value,
            quantity: quantity.value
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null || status.value != "success"){
        toast.add({
            title: 'Error',
            description: "an error occurred while adding the stock",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        toast.add({
            title: 'Success',
            description: 'Stock added successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

// Fonction qui permet de modifier un stock à la base de données via l'API
async function editStock(){
    const {status, data} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            id: selectedFetchedProductFromStore.value.value,
            quantity: selectedStock.value.quantity
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null || status.value != "success"){
        toast.add({
            title: 'Error',
            description: "an error occurred while editing the stock",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        toast.add({
            title: 'Success',
            description: 'Stock edited successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

// Fonction qui permet de supprimer un stock à la base de données via l'API
async function deleteStock(){
    const {status, data} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks/' + selectedFetchedProductFromStore.value.value, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    //Si il y a une erreur dans la réponse de l'API, on affiche un message d'erreur
    if(data.value.error != null || status.value != "success"){
        toast.add({
            title: 'Error',
            description: "an error occurred while deleting the stock",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }else{ //Sinon on affiche un message de succès
        console.log(data.value);
        toast.add({
            title: 'Success',
            description: 'Stock deleted successfully',
            color: 'success',
            icon: 'bx:check'
        });
    }
}

</script>