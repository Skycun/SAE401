<template>
    <section class="flex flex-col justify-center items-center m-5">
        <div class="bg-white rounded-[20px] p-5 py-10 my-10 w-full lg:w-1/2 xl:w-1/3">
            <h2 class="text-3xl text-indigo-950 flex justify-center mb-10">My profile</h2>
            <div class="flex flex-col gap-5 w-full">
                <UFormField label="Name" required>
                    <UInput label="Name" placeholder="Enter your name" v-model="user_data.employees_name" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Email" required>
                    <UInput label="Email" placeholder="Enter your email" v-model="user_data.employees_email" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Password" required>
                    <UInput label="password" placeholder="Leave it blank if you don't want to modify it" v-model="password" class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Role" required>
                    <UInput label="Role" v-model="user_data.employees_role" disabled class="w-full mb-2"/>
                </UFormField>
                <UFormField label="Store" required>
                    <UInput label="Store" v-model="user_data.store.store_name" disabled class="w-full mb-2"/>
                </UFormField>
                <Button class="p-3" @click="updateProfile">Update your profile</Button>
            </div>
        </div>
    </section>
    <pre class="text-indigo-950">{{ user_data }}</pre>
</template>

<script setup>

const router = useRouter();
const user_data = useCookie('user_data');
if(!user_data.value){
    router.push('/');
}
const password = ref('');

async function updateProfile() {
    const data = await $fetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/' + user_data.value.employees_id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            "Api": "e8f1997c763"
        },
        body: JSON.stringify({
            id: user_data.value.employees_id,
            employees_name: user_data.value.employees_name,
            employees_email: user_data.value.employees_email,
            employees_password: password.value
        })
    });
    console.log(data);
}


</script>