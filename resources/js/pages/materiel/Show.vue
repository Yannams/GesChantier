<script setup >
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { ContextMenu, ContextMenuContent, ContextMenuItem, ContextMenuTrigger } from '@/components/ui/context-menu';
import { Dialog, DialogContent, DialogHeader, DialogTrigger } from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Building, ChevronsUpDown, Edit, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios'

const props= defineProps({
    materiel: Object,
    materielsChantierExploit:Object,
    categoriesMateriel:Object,
    materielsChantierFini:Object
})

const breadcrumbs = [
    {
        title: 'Matériel',
        href: route('materiel.index'),
    },

    {
        title: 'voir',
        href: route('materiel.show', props.materiel?.id),
    },
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

    function findCategorieName  () {
         const index= props.categoriesMateriel.findIndex(cM=> cM.id == props.materiel.categorie_materiel_id)
         if(index !== -1){
            const categorieName = props.categoriesMateriel[index].nom_categorieMat
            return categorieName;
         }
         return 'categorie inconnue'
    }
    const errors= ref({})
    const materielsChantierExploit = ref([...props.materielsChantierExploit])
    const materielsChantierFini = ref([...props.materielsChantierFini])

    async function materielState(materielChantier, nouvelleValeur) {
        errors.value={}
        try {
            const response = await axios.put(`/materiel/${materielChantier.pivot.materiel_id}`, {
                processing:'stateChanging',
                etat: nouvelleValeur,
                chantier_id: materielChantier.pivot.chantier_id,
                pivot_id:materielChantier.pivot.id
            })  
           
        } catch (e) {
            errors.value=e.response.data.errors
            if (errors.value.global_probleme) {         
                toast.error(errors.value.global_probleme[0]);
            }
            
        }
    }

</script>

<template>
    <ContainerLayout :breadcrumbs>
        <Card>
            <CardHeader class="flex items-center justify-between" >
                <CardTitle class="flex flex-col">
                    <span class="text-lg">{{ materiel?.nom_materiel }}</span>    
                    <span class="text-sm text-muted-foreground">{{ materiel?.matricule }}</span>
                </CardTitle>
                <Dialog>
                    <DialogTrigger>
                        <Button @click="remplirFormMateriel(materiel)">Modifier</Button>
                    </DialogTrigger>
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
            </CardHeader>
            <div class="grid gap-2 ml-5" >
               <div><span class="font-bold">Quantité totale: </span>  {{ materiel?.quantite_totale ?  materiel?.quantite_totale : '1' }}</div>
               <div><span class="font-bold">Disponibilité: </span>  {{ materiel?.quantite_disponible ? materiel?.quantite_disponible : materiel?.etat }}</div>
               <div><span class="font-bold">Description: </span>  {{ materiel?.Description }}</div>
               <div><span class="font-bold">categorie matériel: </span>  {{ findCategorieName() }}</div>
            </div>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="text-lg"> Chantiers</CardTitle>
            </CardHeader>
            <div class="ml-5">
                <CardTitle>Chantiers d'exploitations</CardTitle>
                 <div class="grid grid-cols-2 gap-4 my-4 ">
                    <ContextMenu v-for="materielChantier in materielsChantierExploit">
                        <ContextMenuTrigger class="border p-2 rounded bg-white grid grid-cols-4 gap-4 items-center">
                            <span class="col-span-2 flex flex-col">
                                <span>{{ materielChantier.nom_chantier }} </span>
                                <span class="text-muted-foreground text-sm">{{ materielChantier.localisation }}</span>
                                <span class="text-muted-foreground text-sm" v-if="materielChantier.pivot.quantite">{{ materielChantier.pivot.quantite}} en utilisation</span>
                                <span class="text-muted-foreground text-sm">{{ materielChantier.date_debut_affectation_formated}} - {{ materielChantier.date_fin_affectation_formated }}</span> 
                                <span class="text-muted-foreground text-sm" v-if="materielChantier.pivot.date_retour_effectif"> retournée le {{ materielChantier.date_retour_effectif_formated }}</span>
                            </span> 
                            <div class="col-span-2 flex justify-end">
                                <ToggleGroup type="single" :model-value="materielChantier.etat_affectation" @update:model-value="val=>materielState(materielChantier, val)" v-if="materielChantier.gestion_par_unite">
                                    <ToggleGroupItem value="utilisé" aria-label="Toggle utilisé"
                                        class="px-3 py-1 rounded-l-lg border
                                            data-[state=on]:bg-black
                                            data-[state=on]:text-white
                                            data-[state=on]:border-black
                                            transition-colors"
                                        >
                                        En utilisation
                                    </ToggleGroupItem>
                                    <ToggleGroupItem value="disponible" aria-label="Toggle disponible"
                                        class="px-3 py-1 border
                                            data-[state=on]:bg-black
                                            data-[state=on]:text-white
                                            data-[state=on]:border-black
                                            transition-colors"
                                        >
                                    Terminée
                                    </ToggleGroupItem>
                                </ToggleGroup>
                                <Dialog v-else>
                                    <DialogTrigger>
                                        <Button>Terminer</Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <form @submit.prevent="submitRetour(materielChantier)">
                                            <DialogHeader>
                                                <div class="font-bold">{{ materielChantier.nom_materiel }}</div>
                                            </DialogHeader>
                                            <div class="grid grid-cols-4 gap-4 mb-4">
                                                <Label for="type_retour">Type de retour</Label>
                                                <div class="col-span-3">
                                                    <Select v-model="formRetour.type_retour">
                                                        <SelectTrigger class="w-full" id="type_retour">
                                                            <SelectValue placeholder="choisir un type"/>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectGroup>
                                                            <SelectItem value="tout">
                                                                    Tous les materiels
                                                            </SelectItem>
                                                            <SelectItem value="partiel">
                                                                    Une partie
                                                                </SelectItem>
                                                            </SelectGroup>
                                                        </SelectContent>
                                                    </Select>
                                                    <InputError :message="errors.type_retour[0]" v-if="errors.type_retour"/>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-4 gap-4 mb-4" v-if="formRetour.type_retour==='partiel'">
                                                <Label for="quantite">
                                                    Quantite
                                                </Label>
                                                <div class="col-span-3">
                                                    <Input type="number" id="quantite" v-model="formRetour.quantite" />
                                                    <InputError :message="errors.quantite" v-if="errors.quantite"/>
                                                </div>
                                            </div>
                                             <div class="grid grid-cols-4 gap-4 mb-4" >
                                                <Label for="date_retour_effectif">
                                                   Date de retour
                                                </Label>
                                               <div class="col-span-3">
                                                    <Input type="date" id="date_retour_effectif" v-model="formRetour.date_retour_effectif" />
                                                    <InputError :message="errors.date_retour_effectif[0]" v-if="errors.date_retour_effectif"/>
                                                </div>  
                                            </div>
                                            <DialogFooter>
                                                <DialogClose>
                                                    <Button type="submit">Terminer</Button>
                                                </DialogClose>
                                            </DialogFooter>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </ContextMenuTrigger>
                        <ContextMenuContent>
                            <ContextMenuItem inset>
                                <Edit/> Modifier
                            </ContextMenuItem>
                             <ContextMenuItem inset>
                                <Trash/> Supprimer
                            </ContextMenuItem>
                            <ContextMenuItem inset>
                                <Link :href="route('chantier.show',materielChantier.id)" class="flex items-center"><Building class="mr-2"/> Aller au chantier</Link>
                            </ContextMenuItem>
                        </ContextMenuContent>
                    </ContextMenu>
                 </div>
                <Collapsible>
                    <CollapsibleTrigger class="flex items-center">
                        <CardTitle>Historique des chantiers</CardTitle>
                        <ChevronsUpDown class="ml-2 w-4 h-4"/>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <div class="grid grid-cols-2 gap-4 my-4 ">
                            <div v-for="materielUtilisee in materielsChantierFini" class="border p-2 rounded bg-white grid grid-cols-4 gap-4 items-center">
                                <span class="col-span-2 flex flex-col">
                                    <span>{{ materielUtilisee.nom_chantier}} </span>
                                    <span class="text-muted-foreground text-sm">{{ materielUtilisee.matricule }}</span>
                                    <span class="text-muted-foreground text-sm">{{ materielUtilisee.date_debut_affectation_formated}} - {{ materielUtilisee.date_fin_affectation_formated }}</span> 
                                    <span class="text-muted-foreground text-sm" v-if="materielUtilisee.pivot.quantite">{{ materielUtilisee.pivot.quantite }} retournés</span>
                                    <span class="text-muted-foreground text-sm" v-if="materielUtilisee.pivot.date_retour_effectif"> retournée le {{ materielUtilisee.date_retour_effectif_formated }}</span>
                                </span> 
                                <div class="col-span-2 flex justify-end">
                                    <div class="border bg-black text-white rounded-lg px-3 py-1">
                                        terminé
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CollapsibleContent>
                </Collapsible>
                 
            </div>
        </Card>
    </ContainerLayout>
</template>