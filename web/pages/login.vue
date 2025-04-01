<template>
    <div class="m-5 bg-white rounded-[20px] p-5 py-10 my-10">
        <h2 class="text-3xl text-indigo-950 flex justify-center mb-10">Login</h2>
        <div class="flex flex-col gap-5 w-full">
            <label for="email" class="text-indigo-950">Email</label>
            <input type="text" v-model="email" id="email" placeholder="Email" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
            <label for="password" class="text-indigo-950">Password</label>
            <input type="password" id="password" v-model="password" placeholder="Password" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
            <div>
                <input type="checkbox" id="remember" v-model="remember" class="w-4 h-4 text-indigo-600"/> <label for="remember" class="text-indigo-950">Remember me</label>
            </div>
            <button class="bg-indigo-600 text-white rounded-full h-14" @click="login">Login</button>
        </div>
    </div>
    
</template>

<script setup>

const email = ref('');
const password = ref('');
const remember = ref(false);

async function login(){
    let data = {
        email: email.value,
        password: password.value
    }
    let res = await fetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/employees/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            "Api": "e8f1997c763"
        },
        body: JSON.stringify(data)
    });
    let json = await res.json();
    console.log(json);
}

</script>
