<template>
    <header class="relative h-16 flex items-center justify-between px-8 mt-6 lg:mx-20 xl:mx-40">
        <!-- Élément vide pour équilibrer la mise en page (même largeur que le menu) -->
        <div class="w-10 lg:w-0"></div>
        
        <NuxtLink to="/" class="absolute max-lg:left-1/2 max-lg:transform max-lg:-translate-x-1/2">
            <img src="../public/images/Bikestoreslogo.png" alt="Bike Stores" class="h-6 lg:h-9">
        </NuxtLink>
        
        <UDropdownMenu
            :items="menu"
            :ui="{
                content: 'w-48'
            }">
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
                label: 'Dashboard',
                icon: 'i-lucide-grid',
                to: '/admin'
            },
            {
                label: 'Profile',
                icon: 'bx:bxs-user',
                to: '/profile'
            },
            {
                label: 'Database',
                icon: 'material-symbols:database-outline',
                children: [
                    [
                    {
                        label: 'Brands',
                        icon: 'bx:bookmark',
                        to: '/admin/database/brands'
                    },
                    {
                        label: 'Categories',
                        icon: 'bx:bxs-cabinet',
                        to: '/admin/database/categories'
                    },
                    {
                        label: 'Employees',
                        icon: 'bx:bxs-user-badge',
                        to: '/admin/database/employees'
                    },
                    {
                        label: 'Products',
                        icon: 'bx:bxs-cube',
                        to: '/admin/database/products'
                    },
                    {
                        label: 'Stocks',
                        icon: 'bx:box',
                        to: '/admin/database/stocks'
                    },
                    {
                        label: 'Stores',
                        icon: 'bx:bxs-store',
                        to: '/admin/database/stores'
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