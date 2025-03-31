<template>
    <div class="m-5 bg-white rounded-[20px] p-5 py-10 my-10">
        <h2 class="text-3xl text-indigo-950 flex justify-center mb-10">Login</h2>
        <form class="flex flex-col gap-5 w-full">
            <label for="email" class="text-indigo-950">Email</label>
            <input type="text" id="email" placeholder="Email" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
            <label for="password" class="text-indigo-950">Password</label>
            <input type="password" id="password" placeholder="Password" class="w-full placeholder-indigo-900 bg-indigo-200 text-indigo-950 rounded-full h-14 px-6" />
            <div>
                <input type="checkbox" id="remember" class="w-4 h-4 text-indigo-600"/> <label for="remember" class="text-indigo-950">Remember me</label>
            </div>
            <button class="bg-indigo-600 text-white rounded-full h-14">Login</button>
        </form>
    </div>
    
</template>

<script setup>

</script>
