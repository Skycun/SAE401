<template>
    <section v-if="user_data && user_data.employees_role" class="m-5 flex flex-col justify-center items-center">
        <div class="w-full lg:w-1/2 xl:w-1/3">
            <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Employees</h2>
            <div class="m-5 bg-white rounded-[20px] p-5 ">
                <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Action</h2>
                <USelectMenu label="Select your action" v-model="action" placeholder="Select an action" class="w-full mb-2" :items="[
                    { label: 'Add', value: 'add' },
                    { label: 'Edit', value: 'edit' },
                    { label: 'Delete', value: 'delete' }
                ]"/>
            </div>
            <div v-if="action">
                <div v-if="action.value !='add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select an employee</h2>
                    <USelectMenu label="Select an employee" v-model="selectedEmployee" :loading="loading" placeholder="Select an employee" class="w-full mb-2" @change="fetchSelectedData" :items="selectEmployees"/>
                    <div v-if="action.value == 'delete' && selectedEmployee && selectedEmployee.value != null">
                        <Button class="p-3 mt-5 bg-red-600" @click="deleteEmployee">Delete</Button>
                    </div>
                </div>
                <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add an employee</h2>
                    <UFormField label="Employee Name" required>
                        <UInput label="Employee name" placeholder="Enter the employee name" v-model="modelData.employees_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Email" required>
                        <UInput label="Employee email" placeholder="Enter the employee email" v-model="modelData.employees_email" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Password" required>
                        <UInput label="Employee password" placeholder="Enter the employee password" type="password" v-model="modelData.employees_password" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Role" required>
                        <USelectMenu
                            label="Select a role"
                            v-model="modelData.employees_role"
                            placeholder="Select a role"
                            class="w-full mb-2"
                            :items="user_data?.employees_role === 'chief'
                                ? [{ label: 'Employee', value: 'employee' }]
                                : [
                                    { label: 'Employee', value: 'employee' },
                                    { label: 'Chief', value: 'chief' },
                                    { label: 'IT', value: 'it' }
                                ]"
                        />
                    </UFormField>
                    <!-- Affiche le champ store uniquement pour IT, pas pour chief -->
                    <UFormField v-if="user_data.value?.employees_role === 'it'" label="Store" required>
                        <USelectMenu label="Select a store" v-model="modelData.store_id" :loading="loadingStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="addEmployee">Add</Button>
                </div>
                <div v-if="action.value == 'edit' && selectedEmployee && selectedEmployee.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
                    <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit an employee</h2>
                    <UFormField label="Employee Name" required>
                        <UInput label="Employee name" placeholder="Enter the employee name" v-model="fetchedSelectedData.employees_name" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Email" required>
                        <UInput label="Employee email" placeholder="Enter the employee email" v-model="fetchedSelectedData.employees_email" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Password">
                        <UInput label="Employee password" placeholder="Leave empty to keep current password" type="password" v-model="newPassword" class="w-full mb-2"/>
                    </UFormField>
                    <UFormField label="Employee Role" required>
                        <USelectMenu
                            label="Select a role"
                            v-model="fetchedSelectedData.employees_role"
                            placeholder="Select a role"
                            class="w-full mb-2"
                            :items="user_data?.employees_role === 'chief'
                                ? [{ label: 'Employee', value: 'employee' }]
                                : [
                                    { label: 'Employee', value: 'employee' },
                                    { label: 'Chief', value: 'chief' },
                                    { label: 'IT', value: 'it' }
                                ]"
                        />
                    </UFormField>
                    <UFormField v-if="user_data.value?.employees_role === 'it'" label="Store" required>
                        <USelectMenu label="Select a store" v-model="fetchedSelectedData.store_id" :loading="loadingStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores"/>
                    </UFormField>
                    <Button class="p-3 mt-5" @click="editEmployee">Edit</Button>
                </div>
            </div>
        </div>
    </section>
    <div v-else class="flex justify-center items-center h-96">
        <span>Chargement...</span>
    </div>
</template>

<script setup>

//Sécurité si l'utilisateur n'est pas connecté
const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}

const action = ref("");
const employees = ref([]);
const selectEmployees = ref([]);
const selectedEmployee = ref(null);
const fetchedSelectedData = ref({}); 
const loading = ref(false);
const toast = useToast();
const newPassword = ref("");
const stores = ref([]);
const selectStores = ref([]);
const loadingStores = ref(false);

const modelData = ref({
    employees_name: null,
    employees_email: null,
    employees_password: null,
    employees_role: null,
    store_id: null
});

// Charger les magasins
fetchStores();
// Charger les employés selon le rôle
fetchEmployees();

async function fetchEmployees(){
    loading.value = true;
    let url = 'https://mirrorsoul.alwaysdata.net/sae401/API/API/employees';
    let employeesData = [];
    // Si chief, ne récupère que les employés de son magasin
    if(user_data.value.employees_role === "chief") {
        url += `/store/${user_data.value.store.store_id}`;
        const { data } = await useFetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        employeesData = data.value || [];
    } else {
        // IT : récupère tous les employés
        const { data } = await useFetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        employeesData = data.value || [];
    }
    employees.value = employeesData;
    selectEmployees.value = employeesData.map((item) => ({
        label: `${item.employees_name} (${item.employees_email})`,
        value: item.employees_id
    }));
    loading.value = false;
}

async function fetchStores(){
    loadingStores.value = true;
    const { data } = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingStores.value = false;
    if (data.value) {
        stores.value = data.value;
        selectStores.value = data.value.map((item) => ({
            label: item.store_name,
            value: item.store_id
        }));
    }
}

async function fetchSelectedData(){
    if (!selectedEmployee.value || !selectedEmployee.value.value) return;
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/' + selectedEmployee.value.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    if (data.value) {
        fetchedSelectedData.value = data.value;
        newPassword.value = "";
    }
}

// Fonction pour ajouter un employé
async function addEmployee() {
    // Chief : force le store_id à celui du chief
    let storeId = modelData.value.store_id;
    if(user_data.value.employees_role === "chief") {
        storeId = user_data.value.store.store_id;
    } else {
        storeId = parseInt(modelData.value.store_id?.value || modelData.value.store_id);
    }
    if (!modelData.value.employees_name || !modelData.value.employees_email || 
        !modelData.value.employees_password || !modelData.value.employees_role || 
        !storeId) {
        toast.add({
            title: 'Error',
            description: "All fields are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            employees_name: modelData.value.employees_name,
            employees_email: modelData.value.employees_email,
            employees_password: modelData.value.employees_password,
            employees_role: modelData.value.employees_role.value || modelData.value.employees_role,
            store_id: storeId
        }
    });
    loading.value = false;
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Employee added successfully",
            color: 'success',
            icon: 'bx:check'
        });
        modelData.value = {
            employees_name: null,
            employees_email: null,
            employees_password: null,
            employees_role: null,
            store_id: null
        };
        fetchEmployees();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to add employee",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour modifier un employé
async function editEmployee() {
    if (!selectedEmployee.value || !selectedEmployee.value.value) return;
    let storeId = fetchedSelectedData.value.store_id;
    if(user_data.value.employees_role === "chief") {
        storeId = user_data.value.store.store_id;
    } else {
        storeId = parseInt(fetchedSelectedData.value.store_id?.value || fetchedSelectedData.value.store_id);
    }
    if (!fetchedSelectedData.value.employees_name || 
        !fetchedSelectedData.value.employees_email || 
        !fetchedSelectedData.value.employees_role || 
        !storeId) {
        toast.add({
            title: 'Error',
            description: "Name, email, role and store are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }
    loading.value = true;
    const payload = {
        id: fetchedSelectedData.value.employees_id,
        employees_name: fetchedSelectedData.value.employees_name,
        employees_email: fetchedSelectedData.value.employees_email,
        employees_role: fetchedSelectedData.value.employees_role.value || fetchedSelectedData.value.employees_role,
        store_id: storeId
    };
    if (newPassword.value) {
        payload.employees_password = newPassword.value;
    }
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/' + selectedEmployee.value.value, {
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
            description: "Employee updated successfully",
            color: 'success',
            icon: 'bx:check'
        });
        newPassword.value = "";
        fetchEmployees();
        fetchSelectedData();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to update employee",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour supprimer un employé
async function deleteEmployee() {
    if (!selectedEmployee.value || !selectedEmployee.value.value) return;
    // Chief : ne peut supprimer que dans son magasin
    if(user_data.value.employees_role === "chief") {
        const employee = employees.value.find(e => e.employees_id === selectedEmployee.value.value);
        if(!employee || employee.store_id !== user_data.value.store.store_id) {
            toast.add({
                title: 'Error',
                description: "You can only delete employees from your store.",
                color: 'error',
                icon: 'bx:x'
            });
            return;
        }
    }
    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/' + selectedEmployee.value.value, {
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
            description: "Employee deleted successfully",
            color: 'success',
            icon: 'bx:check'
        });
        selectedEmployee.value = null;
        fetchedSelectedData.value = {};
        fetchEmployees();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to delete employee",
            color: 'error',
            icon: 'bx:x'
        });
    }
}
</script>