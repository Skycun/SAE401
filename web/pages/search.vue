<template>
    <UCollapsible class="flex flex-col gap-2 w-48">
      <UButton
        label="Open"
        color="neutral"
        variant="subtle"
        trailing-icon="i-lucide-chevron-down"
        block
      />
  
      <template #content>
        <Placeholder class="h-48" />
      </template>
    </UCollapsible>
  </template>
  