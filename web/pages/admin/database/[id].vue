<template>
    <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">{{ table }}</h2>
    <div class="m-5 bg-white rounded-[20px] p-5 ">
        <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Action</h2>
        <USelectMenu label="Select your action" v-model="action" placeholder="Select an action" class="w-full mb-2" @change="fetchDataForSelect" :items="[
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
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select your data</h2>
            <USelectMenu label="Select your data" v-model="selectedItem" :loading="loading" placeholder="Select a data" class="w-full mb-2" @change="fetchSelectedData" :items="dataSelect"/>
        </div>
        <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
            <!-- Tout les select en fonction de la table -->
            <div v-if="table === 'brands'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a brand</h2>
                <UFormField label="Brand Name" required>
                    <UInput label="Brand name" v-model="dataFromTable.brand_name" placeholder="Enter the brand name" class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
            <div v-else-if="table === 'categories'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a category</h2>
                <UFormField label="Category Name" required>
                    <UInput label="Category name" v-model="dataFromTable.category_name" placeholder="Enter the category name" class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
            <div v-else-if="table === 'employees'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add an employee</h2>
                <UFormField label="Employee Name" required>
                    <UInput label="Employee name" v-model="dataFromTable.employees_name" placeholder="Enter the employee name" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Employee Email" required>
                    <UInput label="Employee email" v-model="dataFromTable.employees_email" placeholder="Enter the employee email" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Employee Password" required>
                    <UInput label="Employee password" v-model="dataFromTable.employees_password" placeholder="Enter the employee password" class="w-full mb-2"/>
                </UFormField>

                <UFormField label="Select Employee Role" required>
                    <USelectMenu label="Select Employee Role" v-model="dataFromTable.employees_role" placeholder="Select a role" class="w-full mb-2" :items="[{
                        label: 'Employee',
                        value: 'employee'
                    },
                    {
                        label: 'Chief',
                        value: 'chief'
                    },
                    {
                        label: 'IT',
                        value: 'it'
                    }]"/>
                </UFormField>
                <UFormField label="Select Employee Store" required>
                    <USelectMenu label="Select Employee Store" v-model="dataFromTable.store_id" placeholder="Select a Store" class="w-full mb-2" :items="storeSelect" :loading="loadingStores"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
            <div v-else-if="table === 'products'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a product</h2>
                <UFormField label="Product Name" required>
                    <UInput label="Product name" v-model="dataFromTable.product_name" placeholder="Enter the product name" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Model Year" required>
                    <UInput type="number" label="Model year" v-model.number="dataFromTable.model_year" placeholder="Enter the model year" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="List Price" required>
                    <UInput type="number" step="0.01" label="List price" v-model.number="dataFromTable.list_price" placeholder="Enter the price" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Select Brand" required>
                    <USelectMenu label="Select Brand" v-model="dataFromTable.brand_id" placeholder="Select a brand" class="w-full mb-2" :items="brandSelect" :loading="loadingBrands"/>
                </UFormField>
                <UFormField label="Select Category" required>
                    <USelectMenu label="Select Category" v-model="dataFromTable.category_id" placeholder="Select a category" class="w-full mb-2" :items="categorySelect" :loading="loadingCategories"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
            <div v-else-if="table === 'stores'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a store</h2>
                <UFormField label="Store Name" required>
                    <UInput label="Store name" v-model="dataFromTable.store_name" placeholder="Enter the store name" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Phone" required>
                    <UInput label="Phone" v-model="dataFromTable.phone" placeholder="Enter the phone number" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Email" required>
                    <UInput type="email" label="Email" v-model="dataFromTable.email" placeholder="Enter the email" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Street" required>
                    <UInput label="Street" v-model="dataFromTable.street" placeholder="Enter the street" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="City" required>
                    <UInput label="City" v-model="dataFromTable.city" placeholder="Enter the city" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="State" required>
                    <UInput label="State" v-model="dataFromTable.state" placeholder="Enter the state" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Zip Code" required>
                    <UInput label="Zip code" v-model="dataFromTable.zip_code" placeholder="Enter the zip code" class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
            <div v-else-if="table === 'stocks'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a stock</h2>
                <UFormField label="Quantity" required>
                    <UInput type="number" label="Quantity" v-model.number="dataFromTable.quantity" placeholder="Enter the quantity" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Select Product" required>
                    <USelectMenu label="Select Product" v-model="dataFromTable.product_id" placeholder="Select a product" class="w-full mb-2" :items="productSelect" :loading="loadingProducts"/>
                </UFormField>
                <UFormField label="Select Store" required>
                    <USelectMenu label="Select Store" v-model="dataFromTable.store_id" placeholder="Select a store" class="w-full mb-2" :items="storeSelect" :loading="loadingStores"/>
                </UFormField>
                <Button class="p-3 mt-5" @click="addData">Add</Button>
            </div>
        </div>
    </div>
    <div>
        <pre class="text-indigo-950">
            {{ selectedItem }}
        </pre>
        <pre class="text-indigo-950">
            {{ dataFromTable }}
        </pre>
    </div>
</template>

<script setup>


const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}

const route = useRoute();
const table = ref(route.params.id);
const action = ref();
const dataSelect = ref([]);
const selectedItem = ref('');
const loading = ref(false);

// Variables for select menus
const storeSelect = ref([]);
const brandSelect = ref([]);
const categorySelect = ref([]);
const productSelect = ref([]);
const loadingStores = ref(false);
const loadingBrands = ref(false);
const loadingCategories = ref(false);
const loadingProducts = ref(false);

// Modified dataFromTable to match API field names
const dataFromTable = ref({
    // From Brands
    brand_id: null,
    brand_name: null,
    // From Categories
    category_id: null,
    category_name: null,
    // From Employees
    employees_id: null,
    employees_name: null,
    employees_email: null,
    employees_password: null,
    employees_role: null,
    store_id: null,
    // From Products
    product_id: null,
    product_name: null,
    model_year: null,
    list_price: null,
    // From Stores
    store_id: null,
    store_name: null,
    phone: null,
    email: null,
    street: null,
    city: null,
    state: null,
    zip_code: null,
    // From Stocks
    stock_id: null,
    product_id: null,
    quantity: null
});

// Watch for table changes to load relevant select data
watch(table, () => {
    if (action.value?.value === 'add') {
        loadSelectData();
    }
});

// Watch for action changes
watch(action, () => {
    if (action.value?.value === 'add') {
        loadSelectData();
    }
});

// Function to load select data based on current table
async function loadSelectData() {
    if (table.value === 'employees' || table.value === 'stocks') {
        await fetchStores();
    }
    
    if (table.value === 'products') {
        await fetchBrands();
        await fetchCategories();
    }
    
    if (table.value === 'stocks') {
        await fetchProducts();
    }
}

// Function to fetch stores for select menus
async function fetchStores() {
    loadingStores.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loadingStores.value = false;
    
    if (data.value) {
        storeSelect.value = data.value.map(item => ({
            label: item.store_name,
            value: item.store_id
        }));
    } else {
        console.error('Error fetching stores:', data.error);
    }
}

// Function to fetch brands for select menus
async function fetchBrands() {
    loadingBrands.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/brands', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loadingBrands.value = false;
    
    if (data.value) {
        brandSelect.value = data.value.map(item => ({
            label: item.brand_name,
            value: item.brand_id
        }));
    } else {
        console.error('Error fetching brands:', data.error);
    }
}

// Function to fetch categories for select menus
async function fetchCategories() {
    loadingCategories.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/categories', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loadingCategories.value = false;
    
    if (data.value) {
        categorySelect.value = data.value.map(item => ({
            label: item.category_name,
            value: item.category_id
        }));
    } else {
        console.error('Error fetching categories:', data.error);
    }
}

// Function to fetch products for select menus
async function fetchProducts() {
    loadingProducts.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loadingProducts.value = false;
    
    if (data.value) {
        productSelect.value = data.value.map(item => ({
            label: item.product_name,
            value: item.product_id
        }));
    } else {
        console.error('Error fetching products:', data.error);
    }
}

async function addData(){
    switch (table.value) {
        case "brand":
            
            break;
    
        default:
            break;
    }
    
}


async function fetchSelectedData(){
    //Prend la valeur de selectedItem et va chercher les données dans la table correspondante et remplace les données dans dataFromTable
    const { data } = await useFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/${table.value}/${selectedItem.value.value}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    if (data.value) {
        // Adjust field mapping if needed
        if (table.value === 'employees') {
            // Map API response fields to form fields
            dataFromTable.value = {
                ...dataFromTable.value,
                employees_id: data.value.employee_id,
                employees_name: data.value.employee_name,
                employees_email: data.value.employee_email,
                employees_password: data.value.employee_password,
                employees_role: data.value.employee_role,
                store_id: data.value.store_id
            };
        } else {
            dataFromTable.value = data.value;
        }
        console.log(data.value);
    } else {
        console.error('Error fetching data:', data.error);
    }
}

async function fetchDataForSelect(){
    loading.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/' + table.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loading.value = false;
    if (data.value) {
        dataSelect.value = data.value.map(item => {
            if (table.value === 'brands') {
                return {
                    label: item.brand_name,
                    value: item.brand_id
                };
            } else if (table.value === 'categories') {
                return {
                    label: item.category_name,
                    value: item.category_id
                };
            } else if (table.value === 'employees') {
                return {
                    label: item.employee_name, // Fixed field name
                    value: item.employee_id  // Fixed field name
                };
            } else if (table.value === 'products') {
                return {
                    label: item.product_name,
                    value: item.product_id
                };
            } else if (table.value === 'stores') {
                return {
                    label: item.store_name,
                    value: item.store_id
                };
            } else if (table.value === 'stocks') {
                // For stocks, create a more descriptive label
                return {
                    label: `Stock ID: ${item.stock_id} (Product: ${item.product_id}, Store: ${item.store_id})`,
                    value: item.stock_id
                };
            }
            return null; // Return null for unsupported tables
        }).filter(item => item !== null); // Remove null items
    } else {
        console.error('Error fetching data:', data.error);
    }
}

</script>