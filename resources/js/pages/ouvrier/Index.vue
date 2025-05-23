<script setup lang="ts">
import {Table,TableBody,TableCaption,TableCell,TableHead,TableHeader,TableRow,} from '@/components/ui/table'
import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger,DropdownMenuGroup} from '@/components/ui/dropdown-menu'
import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import { EllipsisVertical } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';


defineProps({
    ouvriers: Object
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'ouvriers',
        href: route('ouvrier.index'),
    },
];
</script>

<template>
    <ContainerLayout :breadcrumbs>
        <Table>
            <TableCaption>Tous les chantiers</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Nom & Prenon</TableHead>
                    <TableHead>Specialisation</TableHead>
                    <TableHead>Contact</TableHead>
                    <TableHead>Adresse</TableHead>
                    <TableHead class="text-right">Action</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
            <TableRow v-for="ouvrier in ouvriers" :key="ouvrier.id">
                <TableCell class="font-medium">
                    {{ ouvrier.nom }} {{ ouvrier.Prenom }}
                </TableCell>
                <TableCell>{{ ouvrier.specialisation  }}</TableCell>
                <TableCell>{{ ouvrier.Contact }}</TableCell>
                <TableCell>{{ ouvrier.Adresse}}</TableCell>
                <TableCell class="text-right">
                    <DropdownMenu>
                        <DropdownMenuTrigger>
                            <Button variant="outline">
                                <EllipsisVertical></EllipsisVertical>
                            </Button>
                            <DropdownMenuContent>
                                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                <DropdownMenuSeparator/>
                                <DropdownMenuGroup>
                                    <DropdownMenuItem><Link :href="route('ouvrier.show',ouvrier.id)" class="w-full">Voir</Link></DropdownMenuItem>
                                    <DropdownMenuItem><Link :href="route('ouvrier.edit',ouvrier.id)">Modifier</Link></DropdownMenuItem>
                                </DropdownMenuGroup>
                            </DropdownMenuContent>
                        </DropdownMenuTrigger>
                    </DropdownMenu>
                </TableCell>
            </TableRow>
            </TableBody>    
        </Table>
    </ContainerLayout>
</template>