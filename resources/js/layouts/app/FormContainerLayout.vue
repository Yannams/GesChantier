<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = []

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                 <div class="flex flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
                    <div class="w-full max-w-sm">
                        <div class="flex flex-col gap-8">
                            <div class="flex flex-col items-center gap-4">
                                <div class="space-y-2 text-center">
                                    <h1 class="text-xl font-medium">{{ title }}</h1>
                                    <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                                </div>
                            </div>
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
   
</template>