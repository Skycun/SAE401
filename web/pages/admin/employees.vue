<template>
    <section v-if="user_data != null" class="m-5 lg:mx-20 xl:mx-40">
        <div v-if="user_data.employees_role == 'chief'" class="bg-white rounded-[20px] p-5 my-10 w-full lg:w-1/2">
            <h2 class="text-indigo-950 text-2xl flex justify-center my-5">Employees</h2>
            <h2 class="text-indigo-950 text-2xl flex justify-center my-5">{{ user_data.store.store_name }}</h2>
            <div v-for="employee in employees" class="p-5 border-b border-indigo-200">
                <h2 class="text-indigo-600">{{ employee.employees_name }} #{{ employee.employees_id }}</h2>
                <h2 class="text-indigo-950">{{ employee.employees_email }}</h2>
                <h2 class="text-indigo-950">{{ employee.employees_role }}</h2>
            </div>
        </div>
        <div v-if="user_data.employees_role == 'it'">
            <h2 class="text-indigo-950 text-2xl flex justify-center my-5">All the Employees</h2>
            <section class="flex flex-row gap-5 flex-wrap justify-center">
                <div v-for="store in employees" class="bg-white rounded-[20px] p-5 my-10 w-80">
                    <h2 class="text-indigo-950 text-2xl flex justify-center">{{ store[0].store.store_name }}</h2>
                    <div v-for="employee in store" class="p-5 border-b border-indigo-200">
                        <h2 class="text-indigo-600">{{ employee.employees_name }} #{{ employee.employees_id }}</h2>
                        <h2 class="text-indigo-950">{{ employee.employees_email }}</h2>
                        <h2 class="text-indigo-950">{{ employee.employees_role }}</h2>
                    </div>
                </div>
            </section>
        </div>
    </section>
</template>
<script setup>

const user_data = useCookie('user_data');
if(!user_data.value || user_data.value.employees_role == "employee"){
    const router = useRouter();
    router.push('/');
}
const employees = ref([]);
const stores = ref([]);

if(user_data.value.employees_role == "chief"){
    employees.value = await $fetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/store/' + user_data.value.store.store_id);
}

stores.value = await $fetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores');

if(user_data.value.employees_role == "it"){
    fetchEmployees();
}

async function fetchEmployees(){
    stores.value.forEach(async store => {
        employees.value.push(await $fetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/store/' + store.store_id));
    });
}

</script>