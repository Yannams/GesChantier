<script setup lang="ts">
    import Table from '@/components/ui/table/Table.vue';
    import TableBody from '@/components/ui/table/TableBody.vue';
    import TableCaption from '@/components/ui/table/TableCaption.vue';
    import TableCell from '@/components/ui/table/TableCell.vue';
    import TableHead from '@/components/ui/table/TableHead.vue';
    import TableHeader from '@/components/ui/table/TableHeader.vue';
    import TableRow from '@/components/ui/table/TableRow.vue';
    import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
    import { BreadcrumbItem } from '@/types';
    import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger,DropdownMenuGroup} from '@/components/ui/dropdown-menu'
    import Button from '@/components/ui/button/Button.vue';
    import { EllipsisVertical } from 'lucide-vue-next';
    import { Link } from '@inertiajs/vue3';


    const props= defineProps({
        materiels:Object,
    })

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title:'Matériels',
            href:route("materiel.index")
        }
    ]
</script>

<template>
    <ContainerLayout :breadcrumbs>
        <Table>
            <TableCaption>Tous les matériels</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Nom</TableHead>
                    <TableHead>Matricule</TableHead>
                    <TableHead>Quantité total</TableHead>
                    <TableHead>Disponibilité</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead>Catégorie</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="materiel in materiels">
                    <TableCell>{{ materiel.nom_materiel}}</TableCell>
                     <TableCell>{{ materiel?.matricule ?? '-' }}</TableCell>
                     <TableCell>{{ materiel?.quantite_totale ?? '1' }}</TableCell>
                     <TableCell>{{ materiel?.quantite_disponible ?? materiel?.etat }}</TableCell>
                    <TableCell>{{ materiel.Description}}</TableCell>
                    <TableCell>{{ materiel.categorie_materiel.nom_categorieMat}}</TableCell>
                    <TableCell>
                        <DropdownMenu>
                            <DropdownMenuTrigger>
                                <Button variant="outline">
                                    <EllipsisVertical></EllipsisVertical>
                                </Button>
                                <DropdownMenuContent>
                                    <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                    <DropdownMenuSeparator/>
                                    <DropdownMenuGroup>
                                        <DropdownMenuItem><Link :href="route('materiel.show',materiel.id)" class="w-full">Voir</Link></DropdownMenuItem>
                                        <DropdownMenuItem><Link :href="route('materiel.edit',materiel.id)">Modifier</Link></DropdownMenuItem>
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