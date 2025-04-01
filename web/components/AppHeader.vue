<template>
    <header class="relative h-16 flex items-center justify-between px-8 mt-6">
        <!-- Élément vide pour équilibrer la mise en page (même largeur que le menu) -->
        <div class="w-10"></div>
        
        <!-- Logo centré -->
        <NuxtLink to="/" class="absolute left-1/2 transform -translate-x-1/2">
            <img src="../public/images/Bikestoreslogo.png" alt="Bike Stores" class="h-6">
        </NuxtLink>
        
        <!-- Menu à droite -->
        <UDropdownMenu
            :items="menu"
            :ui="{
                content: 'w-48'
            }"
        >
            <UButton icon="i-lucide-menu" color="primary" variant="ghost" />
        </UDropdownMenu>
    </header>
    <div class="border-b border-2 border-indigo-200"></div>
</template>

<script setup>
//Avoir le cookie de connection
const user_data = useCookie('user_data');

const menu = ref();
if(!user_data.value){
    menu.value = [
        [
            {
                label: 'Se connecter',
                icon: 'i-lucide-user',
                to: '/login'
            }
        ]
    ]
}else{
    menu.value = [
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
                label: 'Accueil',
                icon: 'bx:home-alt-2',
                to: '/'
            },
            {
                label: 'Profile',
                icon: 'i-lucide-user'
            },
            {
                label: 'Dashboard',
                icon: 'i-lucide-grid',
            },
            {
                label: 'Database',
                icon: 'material-symbols:database-outline',
                children: [
                    [
                    {
                        label: 'Brands',
                        icon: 'i-lucide-mail'
                    },
                    {
                        label: 'Categories',
                        icon: 'i-lucide-mail'
                    },
                    {
                        label: 'Employees',
                        icon: 'i-lucide-mail'
                    },
                    {
                        label: 'Products',
                        icon: 'i-lucide-mail'
                    },
                    {
                        label: 'Stocks',
                        icon: 'i-lucide-mail'
                    },
                    {
                        label: 'Stores',
                        icon: 'i-lucide-mail'
                    }
                    ],
                ]
            }
        ],
        [
            {
                label: 'Logout',
                icon: 'i-lucide-log-out',
                to: '/deconnexion'
            }
        ]
    ]
}
</script>