<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <div class="m-5 bg-white rounded-[20px] p-5 mt-10">
            <h2 class="text-indigo-950 text-2xl flex justify-center items-center mb-5">Action</h2>
            <USelect label="Select Action" v-model="mode" :items="actions" placeholder="Select an action"
                class="w-full mb-2" />
        </div>

        <!-- CREATE FORM -->
        <div v-if="mode == 'create'" class="m-5 bg-white rounded-[20px] p-5">
            <h2 class="text-indigo-950 text-2xl flex justify-center items-center mb-5">Create new {{ table }}</h2>

            <!-- Brands Form -->
            <div v-if="table == 'brands'" class="flex flex-col gap-5">
                <UFormField label="Brand Name" required>
                    <UInput v-model="formData.brand_name" placeholder="Enter brand name" class="w-full"/>
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Brand</UButton>
            </div>

            <!-- Categories Form -->
            <div v-if="table == 'categories'" class="flex flex-col gap-5">
                <UFormField label="Category Name" required>
                    <UInput v-model="formData.category_name" placeholder="Enter category name" class="w-full"/>
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Category</UButton>
            </div>

            <!-- Products Form -->
            <div v-if="table == 'products'" class="flex flex-col gap-5">
                <UFormField label="Product Name" required>
                    <UInput v-model="formData.product_name" placeholder="Enter product name" class="w-full"/>
                </UFormField>
                <UFormField label="Model Year" required>
                    <UInput v-model="formData.model_year" type="number" placeholder="Enter model year" class="w-full"/>
                </UFormField>
                <UFormField label="List Price" required>
                    <UInput v-model="formData.list_price" type="number" step="0.01" placeholder="Enter price" class="w-full"/>
                </UFormField>
                <UFormField label="Brand" required>
                    <USelect v-model="formData.brand_id" :items="brandOptions" placeholder="Select a brand" class="w-full"/>
                </UFormField>
                <UFormField label="Category" required>
                    <USelect v-model="formData.category_id" :items="categoryOptions" placeholder="Select a category" class="w-full"/>
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Product</UButton>
            </div>

            <!-- Stores Form -->
            <div v-if="table == 'stores'" class="flex flex-col gap-5">
                <UFormField label="Store Name" required>
                    <UInput v-model="formData.store_name" placeholder="Enter store name" class="w-full"/>
                </UFormField>
                <UFormField label="Street">
                    <UInput v-model="formData.street" placeholder="Enter street address" class="w-full"/>
                </UFormField>
                <UFormField label="City">
                    <UInput v-model="formData.city" placeholder="Enter city" class="w-full"/>
                </UFormField>
                <UFormField label="State">
                    <UInput v-model="formData.state" placeholder="Enter state" class="w-full"/>
                </UFormField>
                <UFormField label="Zip Code">
                    <UInput v-model="formData.zip_code" placeholder="Enter zip code" class="w-full"/>
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Store</UButton>
            </div>

            <!-- Employees Form -->
            <div v-if="table == 'employees'" class="flex flex-col gap-5">
                <UFormField label="Full name" required>
                    <UInput v-model="formData.employees_name" placeholder="Enter full name" class="w-full"/>
                </UFormField>
                <UFormField label="Email" required>
                    <UInput v-model="formData.email" placeholder="Enter email" class="w-full"/>
                </UFormField>
                <UFormField label="Password" required>
                    <UInput v-model="formData.password" type="password" placeholder="Enter password" class="w-full"/>
                </UFormField>
                <UFormField label="Role" required>
                    <USelect v-model="formData.role" :items="roleOptions" placeholder="Select a Role" class="w-full" />
                </UFormField>
                <UFormField label="Store" required>
                    <USelect v-model="formData.store_id" :items="storeOptions" placeholder="Select a store" class="w-full" />
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Employee</UButton>
            </div>

            <!-- Stocks Form -->
            <div v-if="table == 'stocks'" class="flex flex-col gap-5">
                <UFormField label="Product" required>
                    <USelect v-model="formData.product_id" :items="productOptions" placeholder="Select a product" class="w-full"/>
                </UFormField>
                <UFormField label="Store" required>
                    <USelect v-model="formData.store_id" :items="storeOptions" placeholder="Select a store" class="w-full" />
                </UFormField>
                <UFormField label="Quantity" required>
                    <UInput v-model="formData.quantity" type="number" placeholder="Enter quantity" class="w-full"/>
                </UFormField>
                <UButton class="mt-3" @click="createItem">Create Stock</UButton>
            </div>
        </div>

        <!-- UPDATE/DELETE SELECTION -->
        <div v-if="mode == 'update' || mode == 'delete'" class="m-5 bg-white rounded-[20px] p-5">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select the {{ table }}</h2>
            <USelectMenu label="Select {{ table }}" v-model="itemToEdit" :items="editFetch"
                placeholder="Select an item" class="w-full mb-2" @change="fetchData"/>
            
            <!-- DELETE CONFIRMATION -->
            <div v-if="mode == 'delete' && itemToEdit" class='mt-5'>
                <UModal :open="openModal" :title="`Delete ${itemToEdit.label}`">
                    <UButton color="error" @click="openModal = true">Delete</UButton>

                    <template #body>
                        <p>Are you sure to delete {{ itemToEdit.label }}?</p>
                        <div class="mt-4 flex justify-end gap-3">
                            <UButton color="neutral" variant="outline" @click="openModal = false">Cancel</UButton>
                            <UButton color="error" @click="deleteItem">Delete anyway</UButton>
                        </div>
                    </template>
                </UModal>
            </div>

            <!-- UPDATE FORM -->
            <div v-if="mode == 'update' && itemToEdit && selectedItem && selectedItem.value" class="mt-5">
                <h3 class="text-indigo-950 text-lg mb-4">Update {{ itemToEdit.label }}</h3>

                <!-- Brands Update Form -->
                <div v-if="table == 'brands'" class="flex flex-col gap-5">
                    <UFormField label="Brand Name" required>
                        <UInput v-model="updateData.brand_name" :placeholder="selectedItem.value.brand_name" class="w-full"/>
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Brand</UButton>
                </div>

                <!-- Categories Update Form -->
                <div v-if="table == 'categories'" class="flex flex-col gap-5">
                    <UFormField label="Category Name" required>
                        <UInput v-model="updateData.category_name" :placeholder="selectedItem.value.category_name" class="w-full"/>
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Category</UButton>
                </div>

                <!-- Products Update Form -->
                <div v-if="table == 'products'" class="flex flex-col gap-5">
                    <UFormField label="Product Name" required>
                        <UInput v-model="updateData.product_name" :placeholder="selectedItem.value.product_name" class="w-full"/>
                    </UFormField>
                    <UFormField label="Model Year" required>
                        <UInput v-model="updateData.model_year" type="number" :placeholder="selectedItem.value.model_year" class="w-full"/>
                    </UFormField>
                    <UFormField label="List Price" required>
                        <UInput v-model="updateData.list_price" type="number" step="0.01" :placeholder="selectedItem.value.list_price" class="w-full"/>
                    </UFormField>
                    <UFormField label="Brand" required>
                        <USelect v-model="updateData.brand_id" :items="brandOptions" :placeholder="selectedItem.value.brand_id" class="w-full"/>
                    </UFormField>
                    <UFormField label="Category" required>
                        <USelect v-model="updateData.category_id" :items="categoryOptions" :placeholder="selectedItem.value.category_id" class="w-full"/>
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Product</UButton>
                </div>

                <!-- Stores Update Form -->
                <div v-if="table == 'stores'" class="flex flex-col gap-5">
                    <UFormField label="Store Name" required>
                        <UInput v-model="updateData.store_name" :placeholder="selectedItem.value.store_name" class="w-full"/>
                    </UFormField>
                    <UFormField label="Street">
                        <UInput v-model="updateData.street" :placeholder="selectedItem.value.street" class="w-full"/>
                    </UFormField>
                    <UFormField label="City">
                        <UInput v-model="updateData.city" :placeholder="selectedItem.value.city" class="w-full"/>
                    </UFormField>
                    <UFormField label="State">
                        <UInput v-model="updateData.state" :placeholder="selectedItem.value.state" class="w-full"/>
                    </UFormField>
                    <UFormField label="Zip Code">
                        <UInput v-model="updateData.zip_code" :placeholder="selectedItem.value.zip_code" class="w-full"/>
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Store</UButton>
                </div>

                <!-- Employees Update Form -->
                <div v-if="table == 'employees'" class="flex flex-col gap-5">
                    <UFormField label="Full name" required>
                        <UInput v-model="updateData.employees_name" :placeholder="selectedItem.value.employees_name" class="w-full"/>
                    </UFormField>
                    <UFormField label="Email" required>
                        <UInput v-model="updateData.email" :placeholder="selectedItem.value.email" class="w-full"/>
                    </UFormField>
                    <UFormField label="New Password">
                        <UInput v-model="updateData.password" type="password" placeholder="Enter new password (leave empty to keep current)" class="w-full"/>
                    </UFormField>
                    <UFormField label="Role" required>
                        <USelect v-model="updateData.role" :items="roleOptions" :placeholder="selectedItem.value.role" class="w-full" />
                    </UFormField>
                    <UFormField label="Store" required>
                        <USelect v-model="updateData.store_id" :items="storeOptions" :placeholder="selectedItem.value.store_id" class="w-full" />
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Employee</UButton>
                </div>

                <!-- Stocks Update Form -->
                <div v-if="table == 'stocks'" class="flex flex-col gap-5">
                    <UFormField label="Product" required>
                        <USelect v-model="updateData.product_id" :items="productOptions" :placeholder="selectedItem.value.product_id" class="w-full"/>
                    </UFormField>
                    <UFormField label="Store" required>
                        <USelect v-model="updateData.store_id" :items="storeOptions" :placeholder="selectedItem.value.store_id" class="w-full" />
                    </UFormField>
                    <UFormField label="Quantity" required>
                        <UInput v-model="updateData.quantity" type="number" :placeholder="selectedItem.value.quantity" class="w-full"/>
                    </UFormField>
                    <UButton color="primary" class="mt-3" @click="updateItem">Update Stock</UButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

//Prévenir des gens qui n'ont pas le cookie de connection
const user_data = useCookie('user_data');
if (!user_data.value) {
    const router = useRouter();
    router.push('/login');
}

const selectedItem = ref();
const formData = ref({});
const updateData = ref({});
const openModal = ref(false);
const mode = ref('create');
const route = useRoute();
const table = ref(route.params.id);
const toast = useToast();
const itemToEdit = ref(null);
const editFetch = ref([]);

// Options pour les listes déroulantes
const brandOptions = ref([]);
const categoryOptions = ref([]);
const productOptions = ref([]);
const storeOptions = ref([]);
const roleOptions = ref([
    { label: 'Admin', value: 'admin' },
    { label: 'Staff', value: 'staff' },
    { label: 'Manager', value: 'manager' }
]);

// Vérification que la table existe
if (route.params.id != 'brands' && route.params.id != 'categories' && route.params.id != 'products' && 
    route.params.id != 'stocks' && route.params.id != 'stores' && route.params.id != 'employees') {
    throw createError({ statusCode: 404, statusMessage: 'ID invalide' });
}

// Actions disponibles
const actions = ref([
    { label: 'Create', value: 'create' },
    { label: 'Update', value: 'update' },
    { label: 'Delete', value: 'delete' }
]);

// Charge les détails d'un élément spécifique
async function fetchData() {
    if (!itemToEdit.value || !itemToEdit.value.value) return;
    
    try {
        const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}/${itemToEdit.value.value}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Api': 'e8f1997c763'
            }
        });
        
        selectedItem.value = data;
        
        // Préremplir le formulaire de mise à jour avec les données actuelles
        if (data.value) {
            updateData.value = { ...data.value };
            
            // Supprimer les champs qui ne doivent pas être modifiés
            if (table.value === 'brands') {
                updateData.value = {
                    brand_id: data.value.brand_id,
                    brand_name: data.value.brand_name
                };
            } else if (table.value === 'categories') {
                updateData.value = {
                    category_id: data.value.category_id,
                    category_name: data.value.category_name
                };
            } else if (table.value === 'employees') {
                // Ne pas inclure le mot de passe dans updateData
                delete updateData.value.password;
            }
        }
        
        return data.value;
    } catch (error) {
        console.error('Error fetching item details:', error);
        toast.add({
            title: 'Error',
            description: 'Failed to fetch item details',
            color: 'red'
        });
    }
}

// Fonction pour créer un nouvel élément
async function createItem() {
    try {
        // Validation selon le type de table
        if (table.value === 'brands' && !formData.value.brand_name) {
            return toast.add({ title: 'Error', description: 'Brand name is required', color: 'yellow' });
        } else if (table.value === 'categories' && !formData.value.category_name) {
            return toast.add({ title: 'Error', description: 'Category name is required', color: 'yellow' });
        } else if (table.value === 'products' && 
                  (!formData.value.product_name || !formData.value.brand_id || 
                   !formData.value.category_id || !formData.value.list_price)) {
            return toast.add({ title: 'Error', description: 'All product fields are required', color: 'yellow' });
        } else if (table.value === 'stores' && !formData.value.store_name) {
            return toast.add({ title: 'Error', description: 'Store name is required', color: 'yellow' });
        } else if (table.value === 'employees' && 
                  (!formData.value.employees_name || !formData.value.email || 
                   !formData.value.password || !formData.value.store_id || !formData.value.role)) {
            return toast.add({ title: 'Error', description: 'All employee fields are required', color: 'yellow' });
        } else if (table.value === 'stocks' && 
                  (!formData.value.store_id || !formData.value.product_id || !formData.value.quantity)) {
            return toast.add({ title: 'Error', description: 'All stock fields are required', color: 'yellow' });
        }

        const { data, error } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Api': 'e8f1997c763'
            },
            body: formData.value
        });

        if (error.value) {
            throw new Error(error.value.message || 'Failed to create item');
        }

        if (data.value && data.value.state === 'success') {
            toast.add({
                title: 'Success',
                description: `${table.value} created successfully`,
                color: 'green'
            });
            
            // Réinitialiser le formulaire
            formData.value = {};
        } else {
            throw new Error(data.value?.error || 'Unknown error occurred');
        }
    } catch (error) {
        console.error('Error creating item:', error);
        toast.add({
            title: 'Error',
            description: `Failed to create ${table.value}: ${error.message}`,
            color: 'red'
        });
    }
}

// Fonction pour mettre à jour un élément
async function updateItem() {
    try {
        if (!itemToEdit.value || !itemToEdit.value.value) {
            return toast.add({ title: 'Error', description: 'No item selected', color: 'yellow' });
        }

        // Validation selon le type de table
        if (table.value === 'brands' && !updateData.value.brand_name) {
            return toast.add({ title: 'Error', description: 'Brand name is required', color: 'yellow' });
        } else if (table.value === 'categories' && !updateData.value.category_name) {
            return toast.add({ title: 'Error', description: 'Category name is required', color: 'yellow' });
        }

        // Ajouter l'ID de l'élément à mettre à jour
        let requestBody = { ...updateData.value };
        
        // Spécifier la clé d'ID correcte selon la table
        switch (table.value) {
            case 'brands':
                requestBody.brand_id = itemToEdit.value.value;
                break;
            case 'categories':
                requestBody.category_id = itemToEdit.value.value;
                break;
            case 'products':
                requestBody.product_id = itemToEdit.value.value;
                break;
            case 'stores':
                requestBody.store_id = itemToEdit.value.value;
                break;
            case 'employees':
                requestBody.employees_id = itemToEdit.value.value;
                break;
            case 'stocks':
                requestBody.stock_id = itemToEdit.value.value;
                break;
        }

        const { data, error } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Api': 'e8f1997c763'
            },
            body: requestBody
        });

        if (error.value) {
            throw new Error(error.value.message || 'Failed to update item');
        }

        if (data.value && data.value.state === 'success') {
            toast.add({
                title: 'Success',
                description: `${table.value} updated successfully`,
                color: 'green'
            });
            
            // Réinitialiser
            updateData.value = {};
            itemToEdit.value = null;
        } else {
            throw new Error(data.value?.error || 'Unknown error occurred');
        }
    } catch (error) {
        console.error('Error updating item:', error);
        toast.add({
            title: 'Error',
            description: `Failed to update ${table.value}: ${error.message}`,
            color: 'red'
        });
    }
}

// Fonction pour supprimer un élément
async function deleteItem() {
    try {
        const { data, error } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Api': 'e8f1997c763'
            },
            query: {
                id: itemToEdit.value.value
            }
        });

        if (error.value) {
            throw new Error(error.value.message || 'Failed to delete item');
        }

        if (data.value && data.value.state === 'success') {
            toast.add({
                title: 'Success',
                description: `${itemToEdit.value.label} has been deleted successfully`,
                color: 'green'
            });
            
            openModal.value = false;
            itemToEdit.value = null;
        } else if (data.value && data.value.error) {
            throw new Error(data.value.error);
        } else {
            throw new Error('Unknown error occurred');
        }
    } catch (error) {
        console.error('Error deleting item:', error);
        toast.add({
            title: 'Error',
            description: `Failed to delete ${table.value}: ${error.message}`,
            color: 'red'
        });
    }
}

// Observer le mode sélectionné pour charger les options de sélection
watch(mode, async (newMode) => {
    if (newMode === 'update' || newMode === 'delete') {
        try {
            const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}`);
            
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
                        label: `Stock ID: ${stock.stock_id}`,
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
        } catch (error) {
            console.error('Error fetching items for selection:', error);
            toast.add({
                title: 'Error',
                description: 'Failed to load items for selection',
                color: 'red'
            });
        }
    } else {
        editFetch.value = [];
        itemToEdit.value = null;
    }
});

// Charger les données nécessaires pour les listes déroulantes
async function loadSelectOptions() {
    try {
        // Charger les marques
        const { data: brandsData } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands');
        if (brandsData.value) {
            brandOptions.value = brandsData.value.map(brand => ({
                label: brand.brand_name,
                value: brand.brand_id
            }));
        }
        
        // Charger les catégories
        const { data: categoriesData } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories');
        if (categoriesData.value) {
            categoryOptions.value = categoriesData.value.map(category => ({
                label: category.category_name,
                value: category.category_id
            }));
        }
        
        // Charger les produits
        const { data: productsData } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products');
        if (productsData.value) {
            productOptions.value = productsData.value.map(product => ({
                label: product.product_name,
                value: product.product_id
            }));
        }
        
        // Charger les magasins
        const { data: storesData } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores');
        if (storesData.value) {
            storeOptions.value = storesData.value.map(store => ({
                label: store.store_name,
                value: store.store_id
            }));
        }
    } catch (error) {
        console.error('Error loading select options:', error);
        toast.add({
            title: 'Warning',
            description: 'Some options may not be available',
            color: 'yellow'
        });
    }
}

// Charger les données au montage du composant
onMounted(() => {
    loadSelectOptions();
});
</script>