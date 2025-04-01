<template>
    <div class="m-5 bg-white rounded-[20px] p-5 py-10 my-10 h-[60vh]">
        <UButton label="Show toast" color="neutral" variant="outline" @click="showToast"/>
        <UDropdownMenu
            :items="items1"
            :ui="{
            content: 'w-48'
            }"
        >
        </UDropdownMenu>
        <UDropdownMenu
            :items="items2"
            :ui="{
            content: 'w-48'
            }"
        >
            <UButton icon="i-lucide-menu" color="neutral" variant="outline" />
        </UDropdownMenu>
    </div>
</template>

<script setup>




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
            label: 'Benjamin',
            avatar: {
                src: 'https://github.com/benjamincanac.png'
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
