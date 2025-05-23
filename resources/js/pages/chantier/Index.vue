<script setup lang="ts">
import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
import {Table,TableBody,TableCaption,TableCell,TableHead,TableHeader,TableRow,} from '@/components/ui/table'
import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger,} from '@/components/ui/dropdown-menu'
import { EllipsisVertical } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import DropdownMenuGroup from '@/components/ui/dropdown-menu/DropdownMenuGroup.vue';
import { Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'


defineProps({
    chantiers: Object
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Chantiers',
        href: route('chantier.index'),
    },
];


</script>

<template>
   <ContainerLayout :breadcrumbs>
      <Table>
            <TableCaption>Tous les chantiers</TableCaption>
            <TableHeader>
            <TableRow>
                <TableHead>Nom chantier</TableHead>
                <TableHead>Localisation</TableHead>
                <TableHead>Etat</TableHead>
                <TableHead class="text-right">Action</TableHead>
            </TableRow>
            </TableHeader>
            <TableBody>
            <TableRow v-for="chantier in chantiers" :key="chantier.id">
                <TableCell class="font-medium">
                {{ chantier.nom_chantier }}
                </TableCell>
                <TableCell>{{ chantier.localisation  }}</TableCell>
                <TableCell>{{ chantier.Etat }}</TableCell>
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
                                    <DropdownMenuItem><Link :href="route('chantier.show',chantier.id)" class="w-full">Voir</Link></DropdownMenuItem>
                                    <DropdownMenuItem><Link :href="route('chantier.edit',chantier.id)">Modifier</Link></DropdownMenuItem>
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