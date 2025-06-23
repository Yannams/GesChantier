<script setup>
    import Table from '@/components/ui/table/Table.vue';
    import TableBody from '@/components/ui/table/TableBody.vue';
    import TableCaption from '@/components/ui/table/TableCaption.vue';
    import TableCell from '@/components/ui/table/TableCell.vue';
    import TableHead from '@/components/ui/table/TableHead.vue';
    import TableHeader from '@/components/ui/table/TableHeader.vue';
    import TableRow from '@/components/ui/table/TableRow.vue';
    import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
    import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger,DropdownMenuGroup} from '@/components/ui/dropdown-menu'
    import Button from '@/components/ui/button/Button.vue';
    import { Edit, EllipsisVertical, Eye, LoaderCircle } from 'lucide-vue-next';
    import { Link,useForm } from '@inertiajs/vue3';
    import {DialogTrigger, Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
    import Label from '@/components/ui/label/Label.vue';
    import Input from '@/components/ui/input/Input.vue';
    import InputError from '@/components/InputError.vue';
    import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';



    const props= defineProps({
        materiels:Object,
        categoriesMateriel:Object,
    })

    const breadcrumbs = [
        {
            title:'Matériels',
            href:route("materiel.index")
        }
    ]

    
    const form = useForm({
        matricule: '',
        nom_materiel: '',
        Description: '',
        etat:'',
        categorie_materiel_id:'',
        quantite_totale:'',
        gestion_par_unite: '',
    });

    const remplirFormMateriel=(materiel)=>{
        form.nom_materiel=materiel.nom_materiel
        form.Description=materiel.Description
        form.matricule=materiel.matricule
        form.etat=materiel.etat
        form.categorie_materiel_id=materiel.categorie_materiel_id
        form.quantite_totale=materiel.quantite_totale
        form.gestion_par_unite=materiel.gestion_par_unite
    }

    const SubmitEditMateriel = (materiel) => {
        form.put(route('materiel.update', materiel.id),{
            onFinish: ()=>form.reset()
        })
    }

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
                        <Dialog>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <Button variant="outline">
                                        <EllipsisVertical></EllipsisVertical>
                                    </Button>
                                    <DropdownMenuContent>
                                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                        <DropdownMenuSeparator/>
                                        <DropdownMenuGroup>
                                            <DropdownMenuItem><Link :href="route('materiel.show',materiel.id)" class="w-full flex items-center"><Eye class="mr-2"/> Voir</Link></DropdownMenuItem>
                                            <DialogTrigger asChild>
                                                <DropdownMenuItem @click="remplirFormMateriel(materiel)">
                                                    <Edit/> Modifier
                                                </DropdownMenuItem>
                                            </DialogTrigger>
                                        </DropdownMenuGroup>
                                    </DropdownMenuContent>
                                </DropdownMenuTrigger>
                            </DropdownMenu>
                            <DialogContent>
                                <DialogHeader class="font-bold">Modifier {{ materiel.nom_materiel }}</DialogHeader>
                                    <form @submit.prevent="SubmitEditMateriel(materiel)">
                                        <div class="grid gap-6">
                                            <div class="grid gap-2">
                                                <Label for="nom_materiel">Nom du matériel</Label>
                                                <Input id="nom_materiel" type="text" required autofocus :tabindex="1" autocomplete="nom_materiel" v-model="form.nom_materiel" placeholder="Nom du matériel" />
                                                <InputError :message="form.errors.nom_materiel" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="Description">Description</Label>
                                                <Input id="Description" type="text" required autofocus :tabindex="1" autocomplete="Description" v-model="form.Description" placeholder=" Lorem ipsum dolor sit..." />
                                                <InputError :message="form.errors.Description" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="gestion_par_unité">type de gestion</Label>
                                                <Select id="gestion_par_unité" v-model="form.gestion_par_unite">
                                                    <SelectTrigger class="w-full">
                                                        <SelectValue placeholder="Selectionner un type de gestion"/>
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectGroup>
                                                            <SelectLabel>type de gestion</SelectLabel>
                                                            <SelectItem :value="true" >
                                                                Gestion par unité
                                                            </SelectItem>
                                                            <SelectItem :value="false"  >
                                                                Gestion multiple
                                                            </SelectItem>
                                                            
                                                        </SelectGroup> 
                                                    </SelectContent>
                                                </Select>
                                                <InputError :message="form.errors.gestion_par_unite" />
                                            </div>
                                            <div class="grid gap-2" v-if="form.gestion_par_unite">
                                                <Label for="matricule">Matricule</Label>
                                                <Input id="matricule" type="text" required autofocus :tabindex="1" autocomplete="matricule" v-model="form.matricule" placeholder="matcricule..." />
                                                <InputError :message="form.errors.matricule" />
                                            </div>
                                            <div class="grid gap-2" v-else>
                                                <Label for="quantite_totale">Quantité</Label>
                                                <Input id="quantite_totale" type="number" required autofocus :tabindex="1" autocomplete="quantite_totale" v-model="form.quantite_totale" placeholder="Quantité totale..." />
                                                <InputError :message="form.errors.quantite_totale" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="categorie_materiel_id">Categorie du materiel</Label>
                                                <Select id="categorie_materiel_id" v-model="form.categorie_materiel_id">
                                                    <SelectTrigger class="w-full">
                                                        <SelectValue placeholder="Selectionner une catégorie"/>
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectGroup>
                                                            <SelectLabel>Catégorie matériel</SelectLabel>
                                                            <SelectItem :value="categorieMateriel.id" v-for="categorieMateriel in categoriesMateriel" :key="categorieMateriel.id">
                                                                {{ categorieMateriel.nom_categorieMat }}
                                                            </SelectItem>
                                                        </SelectGroup>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                                            
                                            <Button type="submit" class="mt-2 w-full" tabindex="5" >
                                                Ajouter
                                            </Button>
                                        </div>
                                    </form>
                            </DialogContent>
                        </Dialog>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </ContainerLayout>
</template>