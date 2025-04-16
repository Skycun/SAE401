<template>
    <UApp>
        <body class="min-h-screen flex flex-col">
        <NuxtLoadingIndicator />
        <NuxtRouteAnnouncer />
        <AppHeader />
            <main class="flex-1">
        <NuxtPage />
        </main>
        <AppFooter />
        </body>
    </UApp>
</template>
