<script setup >
import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger, DropdownMenuGroup} from '@/components/ui/dropdown-menu'
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
import { useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { Check, LoaderCircle, ChevronsUpDown, Search, EllipsisVertical, Calendar, Edit, Trash} from 'lucide-vue-next';
import { Plus } from 'lucide-vue-next';
import {Dialog,DialogContent,DialogDescription,DialogHeader,DialogTitle,DialogTrigger,DialogFooter} from '@/components/ui/dialog'
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import dayjs from 'dayjs'
import {Sheet,SheetContent,SheetDescription,SheetHeader,SheetTitle,SheetTrigger,} from '@/components/ui/sheet'  
import { ref, watch } from 'vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {Select,SelectContent,SelectGroup,SelectItem,SelectLabel,SelectTrigger,SelectValue,} from '@/components/ui/select'
import {ContextMenu,ContextMenuCheckboxItem, ContextMenuContent,ContextMenuItem,ContextMenuLabel,ContextMenuRadioGroup,ContextMenuRadioItem,ContextMenuSeparator,ContextMenuShortcut,ContextMenuSub,ContextMenuSubContent,ContextMenuSubTrigger,ContextMenuTrigger,} from '@/components/ui/context-menu'
import axios from 'axios'
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';

const props= defineProps({ 
    chantier: Object,
    taches:Object,
    ouvriers:Object,
    etapes: Object,
})


const formChantier = useForm({
  Etat: props.chantier?.Etat || 'en_cours',
});

    const breadcrumbs = [
        {
            title: 'Chantiers',
            href: '/chantier',
        },
        {
            title: props.chantier?.nom_chantier,
            href: route('chantier.show', props.chantier?.id),
        },
    ];

   const form = useForm({
        libelle: '',
        Description: '',
        chantier_id:props.chantier?.id,
    });

    const submit = () => {
        form.post(route('taches.store'), {   
            onFinish: () => form.reset('libelle', 'Description', 'DateDebutPrevue', 'DateFinPrevue'),
        });
    };

    const formEtape = useForm({
        libelle: '',
        Description: '',
        Etat:'en attente',
        DateDebutPrevue: '',
        DateFinPrevue: '',
        ouvrier_id: [],
        tache_id:'',
        chantier_id:props.chantier?.id,
    });

   
        
    const submitEtape = async () => {
         try {
            const response = await axios.post('/etape', formEtape)
            etapes.value.push(response.data.etape) // On met à jour la liste
            formEtape.reset()
            dialogOpen.value = false // ou isOpen = false selon ton modal
        } catch (error) {
            console.error(error)
        }
    };

    const SubmitEditEtape = async (etape) => {
        try {
            const response = await axios.put(`/etape/${etape.id}`, formEtape)
            // Mettre à jour l'étape dans la liste locale
            const index = etapes.value.findIndex(e => e.id === etape.id)
            if (index !== -1) {
                etapes.value[index] = response.data.etape
            }
            formEtape.reset()
            dialogOpen.value = false
        } catch (e) {
            console.error(e)
        }
    };
    const etapes = ref([...props.etapes])

   const supprimerEtape = async (id) => {
        try {
            await axios.delete(`/etape/${id}`)
            etapes.value = etapes.value.filter(e => e.id !== id)
            
        } catch (error) {
            console.log(error)
            alert('Erreur lors de la suppression.')
        }
    }

   async function toggleTerminee(etape, nouvelleValeur) {
    console.log(nouvelleValeur);
    
       try {
            const response = await axios.put(`/etape/${etape.id}`, {
                Etat: nouvelleValeur,
                tache_id:etape.tache_id,
            })

            const nouvelleEtape = response.data
            console.log(response.data)
            const index = etapes.value.findIndex(e => e.id === nouvelleEtape.id)
            if (index !== -1) {
            // supprime 1 élément à la position `index` et y insère `nouvelleEtape`
                etapes.value.splice(index, 1, nouvelleEtape)
            }

        } catch (e) {
            console.error(e)
        }
        
    }
  const etatLocal = ref(props.chantier?.Etat || 'en attente');

    // Synchroniser la prop chantier si elle change
   watch(
    () => props.chantier.Etat,
    (newEtat) => {
        formChantier.Etat = newEtat;
        etatLocal.value = newEtat;
    }
    );


    // Méthode appelée à chaque changement du ToggleGroup
   function handleEtatChange(nouveauEtat) {
        etatLocal.value = nouveauEtat;
        formChantier.Etat = nouveauEtat;

        formChantier.put(
            route('chantier.update', props.chantier.id),
            { preserveScroll: true }
        );
    }
        
const remplirFormulairEtape=(etape)=>{
    formEtape.libelle=etape.libelle
    formEtape.Description=etape.Description
    formEtape.ouvrier_id=etape.ouvriers.map(o => o.id)
    formEtape.tache_id=etape.tache_id
    formEtape.chantier_id=props.chantier?.id
    
}

const formatDate=(date)=>{

     return dayjs(date).format('DD/MM/YYYY');
}
</script>

<template>
    
    <ContainerLayout :breadcrumbs>
        <Card class="bg-sidebar">
            <CardHeader class="flex justify-between">
                <div class="flex flex-col">
                    <CardTitle class="mb-2">{{ chantier?.nom_chantier }}</CardTitle>
                    <CardDescription>informations supplémentaire</CardDescription>
                </div>
               
                <div class="flex items-center">
                     <ToggleGroup type="single"  :modelValue="etatLocal" @update:modelValue="handleEtatChange">
                        <ToggleGroupItem value="en attente" aria-label="Toggle En attente"
                            class="px-3 py-1 rounded-l-lg border
                                data-[state=on]:bg-black
                                data-[state=on]:text-white
                                data-[state=on]:border-black
                                transition-colors"
                            >
                        En attente
                        </ToggleGroupItem>
                        <ToggleGroupItem value="en cours" aria-label="Toggle En cours"
                            class="px-3 py-1  border
                                data-[state=on]:bg-black
                                data-[state=on]:text-white
                                data-[state=on]:border-black
                                transition-colors"
                            >
                        Démarrer
                        </ToggleGroupItem>
                        <ToggleGroupItem value="terminé" aria-label="Toggle cloturé"
                                class="px-3 py-1 rounded-r-lg border
                                data-[state=on]:bg-black
                                data-[state=on]:text-white
                                data-[state=on]:border-black
                                transition-colors"
                            >
                        Terminer
                        </ToggleGroupItem>
                    </ToggleGroup>
                </div>
               
            </CardHeader>
            
            <CardContent>
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Date début prévue: </span> <span class="font-bold ml-2"> {{chantier?.DateDebutPrevue ? formatDate(chantier?.DateDebutPrevue) : ''}}</span> </div>
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Date fin prévue: </span> <span class="font-bold ml-2"> {{chantier?.DateFinPrevue  ? formatDate(chantier?.DateFinPrevue) : '' }}</span> </div>
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Date Début réelle: </span> <span class="font-bold ml-2"> {{chantier?.DateDebutReelle ? formatDate(chantier?.DateDebutReelle) : ""}}</span> </div>
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Date fin réelle: </span> <span class="font-bold ml-2"> {{chantier?.DateFinReelle ? formatDate(chantier?.DateFinReelle) : ""}}</span> </div>
            </CardContent>

        </Card>

        <Card class="bg-sidebar">
            <CardHeader>
                <div class="grid grid-cols-3 gap-4">
                    <CardTitle>Tâches</CardTitle>
                </div>
            </CardHeader>
            <CardContent>
                 <Sheet v-for="tache in taches" :key="tache.id" >
                    <SheetTrigger class="w-full">
                       <div class="rounded-md bg-white shadow-md text-left font-bold border px-4 py-4 mb-4 flex flex-col">
                            {{ tache.libelle }}
                        </div>
                    </SheetTrigger>
                    <SheetContent >
                    <SheetHeader>
                        <SheetTitle>
                            <Input type="text" className="border-0 bg-transparent focus:ring-0 outline-none shadow-none" v-model="tache.libelle"/>
                        </SheetTitle>
                        <ScrollArea class="h-120">
                            <ContextMenu v-for="etape in etapes" :key="etape.id">
                                <ContextMenuTrigger>
                                    <div class="rounded-md bg-sidebar shadow-md text-left font-bold border px-4 py-4 mt-4 flex flex-col" v-if="etape.tache_id==tache.id">  
                                        <!-- <div class="h- flex items-center mr-3"><Checkbox :id="`${etape.id}`"  :model-value="etape.Etat==='terminé' ? true : false" @update:modelValue="val => toggleTerminee(etape, val)"/></div> -->
                                        <Label :for="`${etape.id}`" :class="etape.Etat==='terminé' ? 'line-through' : '' " class="mb-3" >
                                            <div class="flex flex-col">
                                                <span >{{ etape.libelle }} </span>
                                                <span class="text-muted-foreground text-sm mt-1 ">
                                                    <template v-for="ouvrier in etape.ouvriers">
                                                        {{ ouvrier.nom }} {{ ouvrier.Prenom }},
                                                    </template>
                                                </span>
                                            </div>
                                        </Label>
                                        <ToggleGroup type="single"  :modelValue="etape.Etat" @update:modelValue="val => toggleTerminee(etape, val)">
                                            <ToggleGroupItem value="en attente" aria-label="Toggle En attente"
                                                class="px-3 py-1 rounded-l-lg border
                                                    data-[state=on]:bg-black
                                                    data-[state=on]:text-white
                                                    data-[state=on]:border-black
                                                    transition-colors"
                                                >
                                            En attente
                                            </ToggleGroupItem>
                                            <ToggleGroupItem value="en cours" aria-label="Toggle En cours"
                                                class="px-3 py-1  border
                                                    data-[state=on]:bg-black
                                                    data-[state=on]:text-white
                                                    data-[state=on]:border-black
                                                    transition-colors"
                                                >
                                            Démarrer
                                            </ToggleGroupItem>
                                            <ToggleGroupItem value="terminé" aria-label="Toggle terminé"
                                                    class="px-3 py-1 rounded-r-lg border
                                                    data-[state=on]:bg-black
                                                    data-[state=on]:text-white
                                                    data-[state=on]:border-black
                                                    transition-colors"
                                                    :disabled="!etape.DateDebutReelle || etape.DateDebutReelle.trim()"
                                                >
                                            Terminer
                                            </ToggleGroupItem>
                                        </ToggleGroup>
                                    </div>
                                </ContextMenuTrigger>
                                <ContextMenuContent>
                                    <ContextMenuItem>
                                        <Dialog>
                                            <DialogTrigger @click.stop="remplirFormulairEtape(etape)" class="w-full flex">
                                                <Edit class="mr-2"/> Modifier
                                            </DialogTrigger>
                                            <DialogContent>
                                                <form @submit.prevent="SubmitEditEtape(etape)">
                                                    <DialogHeader>
                                                        <DialogTitle>Modifier étape</DialogTitle>
                                                        <DialogDescription>
                                                            Créer une étape liée à la tâche chantier
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <div class="grid gap-4 py-4">
                                                        <div class="grid grid-cols-4 items-center gap-4">
                                                            <Label for="libelle" class="text-right">
                                                                Titre
                                                            </Label>
                                                            <Input id="libelle" type="text"  class="col-span-3" v-model="formEtape.libelle"/>
                                                        </div>
                                                        <div class="grid grid-cols-4 items-center gap-4">
                                                            <Label for="Description" type="text" class="text-right ">
                                                                Description
                                                            </Label>
                                                            <Input id="Description"  class="col-span-3" v-model="formEtape.Description" />
                                                        </div>    
                                                            <div class="grid grid-cols-4 items-center gap-4">
                                                            <Label for="Description" type="text" class="text-right ">
                                                                Ouvrier
                                                            </Label>
                                                            <div class="col-span-3">
                                                                <Select multiple v-model="formEtape.ouvrier_id">
                                                                    <SelectTrigger class="w-full">
                                                                        <SelectValue placeholder="Selection un ouvrier"  />
                                                                    </SelectTrigger>
                                                                    <SelectContent>
                                                                        <SelectGroup>
                                                                            <SelectLabel>Ouvriers</SelectLabel>
                                                                            <SelectItem :value="ouvrier.id" v-for="ouvrier in ouvriers">
                                                                                {{ ouvrier.nom }} {{ ouvrier.Prenom }}
                                                                            </SelectItem>
                                                                        </SelectGroup>
                                                                    </SelectContent>
                                                                </Select>
                                                            </div>
                                                            <Input type="hidden"  v-model="formEtape.tache_id"/>
                                                        </div>                                     
                                                    </div>
                                                    <DialogFooter>
                                                        <DialogClose>
                                                            <Button type="submit">
                                                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                                                Modifier
                                                            </Button>
                                                        </DialogClose>
                                                    </DialogFooter>
                                                </form>
                                                
                                            </DialogContent>

                                        </Dialog>
                                    </ContextMenuItem>
                                    <ContextMenuItem @click="supprimerEtape(etape.id)"><Trash/> Suprimer</ContextMenuItem>
                                </ContextMenuContent>
                            </ContextMenu>
                        </ScrollArea>
                        <Dialog class="mt-2">
                            <DialogTrigger  @click="formEtape.tache_id = tache.id"  class="mt-3 text-left">
                                <Button><Plus/> Ajouter une étape</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <form @submit.prevent="submitEtape">
                                    <DialogHeader>
                                        <DialogTitle>Ajouter une étape</DialogTitle>
                                        <DialogDescription>
                                            Créer une étape liée à la tâche chantier
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <Label for="libelle" class="text-right">
                                                Titre
                                            </Label>
                                            <Input id="libelle" type="text"  class="col-span-3" v-model="formEtape.libelle"/>
                                        </div>
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <Label for="Description" type="text" class="text-right ">
                                                Description
                                            </Label>
                                            <Input id="Description"  class="col-span-3" v-model="formEtape.Description" />
                                        </div> 
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <Label for="DateDebutPrevue" class="text-left ">
                                                Date début prévue
                                            </Label>
                                            <Input id="DateDebutPrevue" type="date" class="col-span-3" v-model="formEtape.DateDebutPrevue" />
                                        </div>
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <Label for="DateDebutFin" class="text-right ">
                                                Date fin prévue
                                            </Label>
                                            <Input id="DateDebutFin" type="date" class="col-span-3" v-model="formEtape.DateFinPrevue"/>
                                        </div>   
                                            <div class="grid grid-cols-4 items-center gap-4">
                                            <Label for="Description" type="text" class="text-right ">
                                                Ouvrier
                                            </Label>
                                            <div class="col-span-3">
                                                <Select multiple v-model="formEtape.ouvrier_id">
                                                    <SelectTrigger class="w-full">
                                                        <SelectValue placeholder="Selection un ouvrier"  />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectGroup>
                                                            <SelectLabel>Ouvriers</SelectLabel>
                                                            <SelectItem :value="ouvrier.id" v-for="ouvrier in ouvriers">
                                                                {{ ouvrier.nom }} {{ ouvrier.Prenom }}
                                                            </SelectItem>
                                                        </SelectGroup>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <Input type="hidden"  v-model="formEtape.tache_id"/>
                                        </div>                                      
                                    </div>
                                    <DialogFooter>
                                        <DialogClose>
                                            <Button type="submit">
                                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                                Ajouter
                                            </Button>
                                        </DialogClose>
                                    </DialogFooter>
                                </form>
                                
                            </DialogContent>
                        </Dialog>
                    </SheetHeader>
                    </SheetContent>
                </Sheet>
                <Dialog>
                    <DialogTrigger>
                        <Button><Plus/> Ajouter une Tâche</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <form @submit.prevent="submit">
                           
                            <DialogHeader>
                                <DialogTitle>Ajouter une tâche</DialogTitle>
                                <DialogDescription>
                                    Créer une tache liée au chantier
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="libelle" class="text-right">
                                        Titre
                                    </Label>
                                    <Input id="libelle" type="text"  class="col-span-3" v-model="form.libelle"/>
                                </div>
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="Description" type="text" class="text-right ">
                                        Description
                                    </Label>
                                    <Input id="Description"  class="col-span-3" v-model="form.Description" />
                                </div>

                        
                            </div>
                            <DialogFooter>
                                <DialogClose>
                                     <Button type="submit">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        Ajouter
                                    </Button>
                                </DialogClose>
                            </DialogFooter>
                        </form>
                        
                    </DialogContent>
                </Dialog>
            </CardContent>
        </Card>
    </ContainerLayout>
</template>