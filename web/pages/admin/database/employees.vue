<template>
    <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Employees</h2>
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
                <USelectMenu label="Select a role" v-model="modelData.employees_role" placeholder="Select a role" class="w-full mb-2" :items="[
                    { label: 'Employee', value: 'employee' },
                    { label: 'Chief', value: 'chief' },
                    { label: 'IT', value: 'it' }
                ]"/>
            </UFormField>
            <UFormField label="Store" required>
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
                <USelectMenu label="Select a role" v-model="fetchedSelectedData.employees_role" placeholder="Select a role" class="w-full mb-2" :items="[
                    { label: 'Employee', value: 'employee' },
                    { label: 'Chief', value: 'chief' },
                    { label: 'IT', value: 'it' }
                ]"/>
            </UFormField>
            <UFormField label="Store" required>
                <USelectMenu label="Select a store" v-model="fetchedSelectedData.store_id" :loading="loadingStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores"/>
            </UFormField>
            <Button class="p-3 mt-5" @click="editEmployee">Edit</Button>
        </div>
    </div>
</template>

<script setup>
const action = ref("");
const employees = ref([]);
const selectEmployees = ref([]);
const selectedEmployee = ref(null);
const fetchedSelectedData = ref({}); 
const loading = ref(false);
const toast = useToast();

// Variable pour stocker un nouveau mot de passe lors de l'édition
const newPassword = ref("");

// Données pour les stores (magasins)
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

// Charger les employés et les magasins au démarrage
onMounted(() => {
    fetchEmployees(); 
    fetchStores();
});

async function fetchEmployees(){
    loading.value = true;
    const { status, data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        employees.value = data.value;
        selectEmployees.value = data.value.map((item) => {
            return {
                label: `${item.employees_name} (${item.employees_email})`,
                value: item.employees_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load employees",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchStores(){
    loadingStores.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingStores.value = false;
    
    if (data.value) {
        stores.value = data.value;
        selectStores.value = data.value.map((item) => {
            return {
                label: item.store_name,
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
        // Réinitialiser le champ mot de passe
        newPassword.value = "";
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load employee details",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

// Fonction pour ajouter un employé
async function addEmployee() {
    // Validation des champs obligatoires
    if (!modelData.value.employees_name || !modelData.value.employees_email || 
        !modelData.value.employees_password || !modelData.value.employees_role || 
        !modelData.value.store_id) {
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
            employees_role: modelData.value.employees_role.value,
            store_id: parseInt(modelData.value.store_id.value)
        }
    });
    loading.value = false;
    console.log(modelData.value.employees_name, modelData.value.employees_email, modelData.value.employees_password, modelData.value.employees_role, modelData.value.store_id);
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Employee added successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Réinitialiser les champs
        modelData.value = {
            employees_name: null,
            employees_email: null,
            employees_password: null,
            employees_role: null,
            store_id: null
        };
        
        // Actualiser la liste
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
    
    if (!fetchedSelectedData.value.employees_name || 
        !fetchedSelectedData.value.employees_email || 
        !fetchedSelectedData.value.employees_role || 
        !fetchedSelectedData.value.store_id) {
        toast.add({
            title: 'Error',
            description: "Name, email, role and store are required",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    
    // Préparer les données à envoyer
    const payload = {
        id: fetchedSelectedData.value.employees_id,
        employees_name: fetchedSelectedData.value.employees_name,
        employees_email: fetchedSelectedData.value.employees_email,
        employees_role: fetchedSelectedData.value.employees_role.value,
        store_id: parseInt(fetchedSelectedData.value.store_id.value)
    };
    
    // Ajouter le mot de passe seulement s'il a été modifié
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
        
        // Réinitialiser le mot de passe
        newPassword.value = "";
        
        // Actualiser la liste et les données sélectionnées
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
        
        // Réinitialiser la sélection et actualiser la liste
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