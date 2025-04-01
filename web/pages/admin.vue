<template>
    <div v-if="!user_data" class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <h2 class="text-indigo-950 text-3xl flex justify-center items-center">Not logged, redirection</h2>
    </div>
    <div v-else>
        <div class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
            <UButton label="Show toast" color="neutral" variant="outline" @click="showToast"/>
            <UDropdownMenu
                :items="items1"
                :ui="{
                content: 'w-48'
                }"
            >
                <UButton icon="i-lucide-menu" color="neutral" variant="outline" />
            </UDropdownMenu>
        </div>
    </div>
</template>

<script setup>

const user_data = useCookie('user_data');
if(!user_data.value){
    const router = useRouter();
    router.push('/login');
}


const toast = useToast()
const data = {
    title: 'Hello Brice 👋'
}

function showToast() {
  toast.add(data)
}

const items1 = ref([
    [
        {
            label: user_data.value.employees_name,
            avatar: {
                alt: user_data.value.employees_name,
        },
        type: 'label'
        }
    ],
    [
        {
        label: 'Profile',
        icon: 'i-lucide-user'
        },
        {
            label: 'Settings',
            icon: 'i-lucide-cog',
            kbds: [',']
        }
    ],
    [
        {
            label: 'Logout',
            icon: 'i-lucide-log-out'
        }
    ]
]);

const items2 = ref([
    [
        {
            label: 'Se connecter',
            icon: 'i-lucide-user'
        }
    ]
]);

</script>
