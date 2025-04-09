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
                <Button class="p-3 mt-5">Add</Button>
            </div>
            <div v-else-if="table === 'categories'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a category</h2>
                <UFormField label="Category Name" required>
                    <UInput label="Category name" v-model="dataFromTable.category_name" placeholder="Enter the category name" class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3 mt-5">Add</Button>
            </div>
            <div v-else-if="table === 'employees'">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add an employee</h2>
                <UFormField label="Employee Name" required>
                    <UInput label="Employee name" v-model="dataFromTable.employee_name" placeholder="Enter the employee name" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Employee Email" required>
                    <UInput label="Employee email" v-model="dataFromTable.employee_email" placeholder="Enter the employee email" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Employee Password" required>
                    <UInput label="Employee password" v-model="dataFromTable.employee_password" placeholder="Enter the employee password" class="w-full mb-2"/>
                </UFormField>

                <UFormField label="Select Employee Role" required>
                    <USelectMenu label="Select Employee Role" placeholder="Select a role" class="w-full mb-2" :items="[{
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
                    <USelectMenu label="Select Employee Store" placeholder="Select a Store" class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3 mt-5">Add</Button>
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

const dataFromTable = ref({
    // From Brands
    brand_id: null,
    brand_name: null,
    // From Categories
    category_id: null,
    category_name: null,
    // From Employees
    employee_id: null,
    employee_name: null,
    employee_email: null,
    employee_password: null,
    employee_role: null,
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
    quantity: null
});

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
        dataFromTable.value = data.value; // Assuming the API returns an array of objects
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
                    label: item.employee_name,
                    value: item.employee_id
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
                return {
                    label: item.stock_id,
                    value: item.stock_id
                };
            }
            return null; // Return null for unsupported tables
        });
    } else {
        console.error('Error fetching data:', data.error);
    }
}

</script>