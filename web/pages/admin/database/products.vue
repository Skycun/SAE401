<template>
    <section class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Products</h2>
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
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select a product</h2>
                    <USelectMenu label="Select a product" v-model="selectedProduct" :loading="loading" placeholder="Select a product" class="w-full mb-2" @change="fetchSelectedData" :items="selectProducts"/>
                    <div v-if="action.value == 'delete' && selectedProduct && selectedProduct.value != null">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteProduct">Delete</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a product</h2>
                    <UFormField label="Product Name" required>
                        <UInput label="Product name" placeholder="Enter the product name" v-model="modelData.product_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Brand" required>
                        <USelectMenu label="Select a brand" v-model="modelData.brand_id" :loading="loadingBrands" placeholder="Select a brand" class="w-full mb-2" :items="selectBrands"/>
                    </UFormField>
                    <UFormField label="Category" required>
                        <USelectMenu label="Select a category" v-model="modelData.category_id" :loading="loadingCategories" placeholder="Select a category" class="w-full mb-2" :items="selectCategories"/>
                    </UFormField>
                    <UFormField label="Model Year" required>
                        <UInput type="number" label="Model year" placeholder="Enter the model year" v-model.number="modelData.model_year" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="List Price" required>
                        <UInput type="number" step="0.01" label="List price" placeholder="Enter the product price" v-model.number="modelData.list_price" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addProduct">Add</Button>
                </div>
                <div v-if="action.value == 'edit' && selectedProduct && selectedProduct.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit a product</h2>
                    <UFormField label="Product Name" required>
                        <UInput label="Product name" placeholder="Enter the product name" v-model="fetchedSelectedData.product_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Brand" required>
                        <USelectMenu label="Select a brand" v-model="fetchedSelectedData.brand_id" :loading="loadingBrands" placeholder="Select a brand" class="w-full mb-2" :items="selectBrands"/>
                    </UFormField>
                    <UFormField label="Category" required>
                        <USelectMenu label="Select a category" v-model="fetchedSelectedData.category_id" :loading="loadingCategories" placeholder="Select a category" class="w-full mb-2" :items="selectCategories"/>
                    </UFormField>
                    <UFormField label="Model Year" required>
                        <UInput type="number" label="Model year" placeholder="Enter the model year" v-model.number="fetchedSelectedData.model_year" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="List Price" required>
                        <UInput type="number" step="0.01" label="List price" placeholder="Enter the product price" v-model.number="fetchedSelectedData.list_price" class="w-full mb-2"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="editProduct">Edit</Button>
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
const products = ref([]);
const selectProducts = ref([]);
const selectedProduct = ref(null);
const fetchedSelectedData = ref({});
const loading = ref(false);
const toast = useToast();

// Données pour les marques
const brands = ref([]);
const selectBrands = ref([]);
const loadingBrands = ref(false);

// Données pour les catégories
const categories = ref([]);
const selectCategories = ref([]);
const loadingCategories = ref(false);

const modelData = ref({
    product_name: null,
    brand_id: null,
    category_id: null,
    model_year: null,
    list_price: null
});

// Charger les produits, marques et catégories au démarrage

fetchProducts(); 
fetchBrands(); 
fetchCategories(); 

async function fetchProducts(){
    loading.value = true;
    const { status, data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        products.value = data.value;
        selectProducts.value = data.value.map((item) => {
            return {
                label: `${item.product_name} - ${item.list_price}$`,
                value: item.product_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load products",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchBrands(){
    loadingBrands.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingBrands.value = false;
    
    if (data.value) {
        brands.value = data.value;
        selectBrands.value = data.value.map((item) => {
            return {
                label: item.brand_name,
                value: item.brand_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load brands",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchCategories(){
    loadingCategories.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingCategories.value = false;
    
    if (data.value) {
        categories.value = data.value;
        selectCategories.value = data.value.map((item) => {
            return {
                label: item.category_name,
                value: item.category_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load categories",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchSelectedData(){
    if (!selectedProduct.value || !selectedProduct.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products/' + selectedProduct.value.value + '?action=get', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        // Format the fetched data for the form
        fetchedSelectedData.value = {
            ...data.value,
            brand_id: {
                label: data.value.brand.brand_name,
                value: data.value.brand.brand_id
            },
            category_id: {
                label: data.value.category.category_name,
                value: data.value.category.category_id
            }
        };
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load product details",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour ajouter un produit
async function addProduct() {
    // Validation des champs obligatoires
    if (!modelData.value.product_name || 
        !modelData.value.brand_id || 
        !modelData.value.category_id || 
        !modelData.value.model_year || 
        !modelData.value.list_price) {
        toast.add({
            title: 'Error',
            description: "All fields are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            product_name: modelData.value.product_name,
            brand_id: parseInt(modelData.value.brand_id.value),
            category_id: parseInt(modelData.value.category_id.value),
            model_year: parseInt(modelData.value.model_year),
            list_price: parseFloat(modelData.value.list_price)
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Product added successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Réinitialiser les champs
        modelData.value = {
            product_name: null,
            brand_id: null,
            category_id: null,
            model_year: null,
            list_price: null
        };
        
        // Actualiser la liste
        fetchProducts();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to add product",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour modifier un produit
async function editProduct() {
    if (!selectedProduct.value || !selectedProduct.value.value) return;
    
    if (!fetchedSelectedData.value.product_name || 
        !fetchedSelectedData.value.brand_id || 
        !fetchedSelectedData.value.category_id || 
        !fetchedSelectedData.value.model_year || 
        !fetchedSelectedData.value.list_price) {
        toast.add({
            title: 'Error',
            description: "All fields are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    
    // Préparer les données à envoyer
    const payload = {
        id: fetchedSelectedData.value.product_id,
        product_name: fetchedSelectedData.value.product_name,
        brand_id: parseInt(fetchedSelectedData.value.brand_id.value),
        category_id: parseInt(fetchedSelectedData.value.category_id.value),
        model_year: parseInt(fetchedSelectedData.value.model_year),
        list_price: parseFloat(fetchedSelectedData.value.list_price)
    };
    
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products/' + selectedProduct.value.value, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: payload
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Product updated successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Actualiser la liste et les données sélectionnées
        fetchProducts();
        fetchSelectedData();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to update product",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour supprimer un produit
async function deleteProduct() {
    if (!selectedProduct.value || !selectedProduct.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products/' + selectedProduct.value.value, {
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
            description: "Product deleted successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Réinitialiser la sélection et actualiser la liste
        selectedProduct.value = null;
        fetchedSelectedData.value = {};
        fetchProducts();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to delete product",
            color: 'error',
            icon: 'bx:x'
        });
    }
}
</script>