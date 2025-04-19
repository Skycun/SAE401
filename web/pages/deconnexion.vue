<template>
    
</template>

<script setup>
    const router = useRouter();

    const user_data = useCookie('user_data');
    if(!user_data.value){
        router.push('/');
    }else{
        const userCookie = useCookie('user_data', {
            maxAge: 0,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'strict'
        });
        userCookie.value = null;
        router.push('/');
        window.location.reload();
    }
</script>
