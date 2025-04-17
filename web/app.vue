<template>
    <UApp>
        <div class="min-h-screen flex flex-col">
            <NuxtLoadingIndicator />
            <NuxtRouteAnnouncer />
            <AppHeader />
            <main class="flex-1">
                <NuxtPage />
            </main>
            <AppFooter />
        </div>
    </UApp>
</template>
