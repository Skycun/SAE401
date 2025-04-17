<template>
    <section class="flex flex-col justify-center items-center m-5">
        <div class="bg-white rounded-[20px] p-5 py-10 my-10 w-full lg:w-1/2 xl:w-1/3">
            <h2 class="text-3xl text-indigo-950 flex justify-center mb-10">Login</h2>
            <p v-show="error" class="text-red-600 flex justify-center mb-2">{{ error }}</p>
            <div class="flex flex-col gap-5 w-full">
                <label for="email" class="text-indigo-950">Email</label>
                <input type="text" v-model="email" id="email" placeholder="Email" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
                <label for="password" class="text-indigo-950">Password</label>
                <input type="password" id="password" v-model="password" placeholder="Password" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
                <div>
                    <input type="checkbox" id="remember" v-model="remember" class="w-4 h-4 text-indigo-600"/> <label for="remember" class="text-indigo-950">Remember me</label>
                </div>
                <Button class="bg-indigo-600 text-white rounded-full p-3" @click="login">Login</Button>
            </div>
        </div>
    </section>
</template>

<script setup>

// Si le cookie user_data existe, on redirige vers la page d'accueil
const router = useRouter();
const user_data = useCookie('user_data');
if(user_data.value){
    router.push('/');
}


const email = ref('');
const password = ref('');
const remember = ref(false);
const error = ref('');

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
    if(json.state === "success"){
        const userCookie = useCookie('user_data', {
            maxAge: remember.value ? 60 * 60 * 24 * 7 : 60 * 60 * 24,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'strict'
        });
        userCookie.value = JSON.stringify(json.employee);
        router.push('/admin');
    }else{
        error.value = "Email ou mot de passe incorrect";
    }
}

</script>
