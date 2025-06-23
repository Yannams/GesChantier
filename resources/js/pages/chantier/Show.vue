<script setup >
    import {DropdownMenu,DropdownMenuContent,DropdownMenuItem,DropdownMenuLabel,DropdownMenuSeparator,DropdownMenuTrigger, DropdownMenuGroup} from '@/components/ui/dropdown-menu'
    import Card from '@/components/ui/card/Card.vue';
    import CardDescription from '@/components/ui/card/CardDescription.vue';
    import CardFooter from '@/components/ui/card/CardFooter.vue';
    import CardHeader from '@/components/ui/card/CardHeader.vue';
    import CardTitle from '@/components/ui/card/CardTitle.vue';
    import ContainerLayout from '@/layouts/app/ContainerLayout.vue';
    import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
    import { Link, useForm } from '@inertiajs/vue3';
    import Button from '@/components/ui/button/Button.vue';
    import { Check, LoaderCircle, ChevronsUpDown, Search, EllipsisVertical, Calendar, Edit, Trash, Pickaxe} from 'lucide-vue-next';
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
    import {Popover,PopoverContent,PopoverTrigger,} from '@/components/ui/popover';
    import {Command,CommandDialog,CommandEmpty,CommandGroup,CommandInput,CommandItem,CommandList,CommandSeparator  } from '@/components/ui/command'
    import Textarea from '@/components/ui/textarea/Textarea.vue';
    import {Collapsible,CollapsibleContent,CollapsibleTrigger,} from '@/components/ui/collapsible'
    import InputError from '@/components/InputError.vue';    
    import { Toaster } from '@/components/ui/sonner';
    import { toast } from 'vue-sonner'



    const props= defineProps({ 
        chantier: Object,
        taches:Object,
        ouvriers:Object,
        etapes: Object,
        materielsDisponibles: Object,
        materielsChantier:Object,
        materielsUtilisees:Object
    })
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
     
    const isOpen = ref(false)
    const errors=ref({})
    const formChantier = useForm({
        Etat: props.chantier?.Etat || 'en_cours',
    })

    

    const form = useForm({
        libelle: '',
        Description: '',
        DateDebutPrevue:'',
        DateFinPrevue:'',           
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
        errors.value={}
         try {
            const response = await axios.post('/etape', formEtape)
            etapes.value.push(response.data.etape) // On met à jour la liste
            formEtape.reset()

        } catch (error) {
           if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors
            } else {
                console.error("Erreur inconnue", error)
            } 
        }
    };

    const SubmitEditEtape = async (etape) => {
        try {
            const response = await axios.put(`/etape/${etape.id}`, formEtape)
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

    const materielSelected= ref()
    const open= ref(false)
    watch(materielSelected, (val) => {
        formMateriel.materiel_id = val?.id ?? ''
    })

     const formMateriel = useForm({
        pivot_id:"",
        materiel_id: "",
        date_fin_affectation_prevue:"",
        quantite:"",
        quantite_total:"",
        remarques:"",
        chantier_id:props.chantier.id,   
    });
    const materielsDisponibles = ref([...props.materielsDisponibles])
     const submitMateriel = async () => {
        errors.value={}
         try {
            const response = await axios.post('/materiel/add/', formMateriel)
            materielsChantier.value.push(response.data)
            const index= materielsDisponibles.value.findIndex(m=>m.id==response.data.id);
             if (index !== -1) {
                if(response.data.quantite_disponible==0)
                    materielsDisponibles.value.splice(index, 1)
                else
                    materielsDisponibles.value[index].quantite_disponible=response.data.quantite_disponible
            }
            materielSelected.value=""
            formMateriel.reset();
            console.log(materielsChantier.value, materielsDisponibles.value)
            
        } catch (error) {
           if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors
            } else {
                console.error("Erreur inconnue", error)
            }
        }
    };

    const deleteMateriel=(materielChantier)=>{
        formMateriel.materiel_id=materielChantier.pivot.materiel_id 
        formMateriel.post(route('RemoveMateriel'), {
            onFinish: () => formMateriel.reset('materiel_id'),
        })
    }
    const materielsChantier = ref([...props.materielsChantier])
    const materielsUtilisees= ref([...props.materielsUtilisees])
    async function materielState(materielChantier, nouvelleValeur) {
        errors.value={}
        try {
            const response = await axios.put(`/materiel/${materielChantier.id}`, {
                processing:'stateChanging',
                etat: nouvelleValeur,
                chantier_id: materielChantier.pivot.chantier_id,
                pivot_id:materielChantier.pivot.id
            })  
            const nouveauMateriel = response.data            
            materielsUtilisees.value.push(nouveauMateriel);
            const index = materielsChantier.value.findIndex(m => m.id === materielChantier.id && m.pivot?.id === materielChantier.pivot?.id)
            if (index !== -1) {
                materielsChantier.value.splice(index, 1)
                const materielEnDisponibilite={...response.data}
                delete materielEnDisponibilite.pivot
                materielsDisponibles.value.push(materielEnDisponibilite);
            }
            
        } catch (e) {
            errors.value=e.response.data.errors
            if (errors.value.global_probleme) {         
                toast.error(errors.value.global_probleme[0]);
            }
            
        }
        
    }

    const formRetour=useForm({
        type_retour:"",
        quantite:"",
        date_retour_effectif:"",
        chantier_id:props.chantier.id,
    })
     
    const submitRetour = async (materielChantier) => {
        errors.value={}
        try{
            formRetour.pivot_id=materielChantier.pivot.id
            const response = await axios.put(`/materiel/${materielChantier.id}`,formRetour)
            if(formRetour.type_retour==='partiel'){
                const donneeMateriel={...response.data}
                const index = materielsChantier.value.findIndex(m => m.id === materielChantier.id && m.pivot?.id === materielChantier.pivot?.id)
                if (index !== -1) {
                    if (donneeMateriel.chantierMateriel && donneeMateriel.chantierMaterielRetournee) {
                        materielsChantier.value[index].pivot.quantite=donneeMateriel.chantierMateriel.pivot.quantite
                        materielsUtilisees.value.push(donneeMateriel.chantierMaterielRetournee);
                    } else if (donneeMateriel.chantierMaterielRetournee && !donneeMateriel.chantierMateriel) {
                        materielsUtilisees.value.push(donneeMateriel.chantierMaterielRetournee);
                        materielsChantier.value.splice(index, 1)
                    }
                    const indexDispo= materielsDisponibles.value.findIndex(mD=>mD.id===donneeMateriel.materiel.id)
                    console.log( materielsDisponibles.value[indexDispo])
                    if (indexDispo === -1 && donneeMateriel.materiel) {
                        materielsDisponibles.value.push(donneeMateriel.materiel)
                    }else if(indexDispo !== -1 && donneeMateriel.materiel){
                        materielsDisponibles.value[indexDispo].quantite_disponible=donneeMateriel.materiel.quantite_disponible
                    }
                }else {
                    toast.error('un problème est survenu !')
                }
              
            }else if(formRetour.type_retour==='tout'){
                const donneeMateriel={...response.data}
                const index = materielsChantier.value.findIndex(m => m.id === materielChantier.id && m.pivot?.id === materielChantier.pivot?.id)
                if (index !== -1) {          
                    if (donneeMateriel.chantierMaterielRetournee && !donneeMateriel.chantierMateriel) {
                        materielsUtilisees.value.push(donneeMateriel.chantierMaterielRetournee);
                        materielsChantier.value.splice(index, 1)
                    }
                    const indexDispo= materielsDisponibles.value.findIndex(mD=>mD.id===donneeMateriel.materiel.id)
                    if (indexDispo === -1 && donneeMateriel.materiel) {
                        materielsDisponibles.value.push(donneeMateriel.materiel)
                    }else if(indexDispo !== -1 && donneeMateriel.materiel){
                        materielsDisponibles.value[indexDispo].quantite_disponible=donneeMateriel.materiel.quantite_disponible
                    }
                }
            }
        }catch(e){
            errors.value=e.response.data.errors
        }
    }
   async function toggleTerminee(etape, nouvelleValeur) {
       try {
            const response = await axios.put(`/etape/${etape.id}`, {
                Etat: nouvelleValeur,
                tache_id:etape.tache_id,
            })

            const nouvelleEtape = response.data.etape
            const index = etapes.value.findIndex(e => e.id === nouvelleEtape.id)
            
            if (index !== -1) {
            // supprime 1 élément à la position `index` et y insère `nouvelleEtape`
                etapes.value[index].Etat = nouvelleEtape.Etat
                etapes.value[index].DateDebutReelle = nouvelleEtape.DateDebutReelle
                etapes.value[index].DateFinReelle = nouvelleEtape.DateFinReelle

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
    formEtape.DateDebutPrevue=etape.DateDebutPrevue
    formEtape.DateFinPrevue=etape.DateFinPrevue
    formEtape.ouvrier_id=etape.ouvriers.map(o => o.id)
    formEtape.tache_id=etape.tache_id
    formEtape.chantier_id=props.chantier?.id
    
}
function statutTache(tacheId) {
  const etapesAssociees = etapes.value.filter(e => e.tache_id === tacheId)
  const etats = etapesAssociees.map(e => e.Etat)

  if (etats.length === 0) return 'En attente'

  if (etats.every(e => e === 'terminé')) {
    return 'Terminé'
  }

  if (etats.some(e => e === 'en cours' || e === 'terminé')) {
    return 'En cours'
  }

  return 'En attente'
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
            
            <div class="mx-5">
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Période prévue: </span> <span class="font-bold ml-2"> {{chantier?.DateDebutPrevue ? formatDate(chantier?.DateDebutPrevue) : ''}} - {{chantier?.DateFinPrevue  ? formatDate(chantier?.DateFinPrevue) : '' }}</span> </div>
                <div class="flex items-center"> <Calendar class="h-5 w-5 opacity-50 mr-1"/> <span class="text-muted-foreground"> Période réelle: </span> <span class="font-bold ml-2"> {{chantier?.DateDebutReelle ? formatDate(chantier?.DateDebutReelle) : ''}} - {{chantier?.DateFinReDateDebutReelle  ? formatDate(chantier?.DateFinReDateDebutReelle) : '' }} </span> </div>
            </div>

        </Card>

        <Card class="bg-sidebar">
            <CardHeader>
                <div class="grid grid-cols-3 gap-4">
                    <CardTitle>Tâches</CardTitle>
                </div>
            </CardHeader>
            <div class="mx-5">
                 <Sheet v-for="tache in taches" :key="tache.id" >
                    <SheetTrigger class="w-full">
                       <div class="rounded-md bg-white shadow-md text-left  border px-4 py-4 mb-4 flex justify-between items-center">
                           <span class="font-bold"> {{ tache.libelle }} </span>
                           <div>
                                <span class="border p-2 rounded text-sm text-muted-foreground mr-2">Periode prevue: {{tache.date_debut_prevue_formated }} - {{tache.date_fin_prevue_formated }} </span>
                                <span class="border p-2 rounded text-sm text-muted-foreground">{{statutTache(tache.id)}}</span> 
                           </div>
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
                                                    :disabled="!etape.DateDebutReelle "
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
                                                            Modifier l'étape liée à la tâche chantier
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
                                                                        <SelectValue placeholder="Selectionner un ouvrier"  />
                                                                    </SelectTrigger>
                                                                    <SelectContent>
                                                                        <SelectGroup>
                                                                            <SelectLabel>Ouvriers</SelectLabel>
                                                                            <SelectItem :value="ouvrier.id" v-for="ouvrier in ouvriers" :key="ouvrier.id">
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
                                    <Label for="Description"  class="text-right ">
                                        Description
                                    </Label>
                                    <Input id="Description"  class="col-span-3" v-model="form.Description" />
                                </div>
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="DateDebutPrevue" class="text-left ">
                                        Date début prévue
                                    </Label>
                                    <Input id="DateDebutPrevue" type="date" class="col-span-3" v-model="form.DateDebutPrevue" />
                                </div>
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="DateDebutFin" class="text-right ">
                                        Date fin prévue
                                    </Label>
                                    <Input id="DateDebutFin" type="date" class="col-span-3" v-model="form.DateFinPrevue"/>
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
            </div> 
           
        </Card>
        <Card class="bg-sidebar">
            <CardHeader class="flex items-center justify-between">
               <span class="font-bold"> Matériels</span>
                <Dialog>
                    <DialogTrigger>
                        <Button><Plus />Attribuer un matériel</Button>
                    </DialogTrigger>
                    <DialogContent>
                    <form @submit.prevent="submitMateriel">
                        <DialogHeader>
                        <DialogTitle>Ajouter un matériel au chantier </DialogTitle>
                        <DialogDescription> {{ chantier.nom_chantier }}</DialogDescription>
                        </DialogHeader>
                        <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="libelle" class="text-right">
                                Materiel
                            </Label>
                            <div class="col-span-3">
                                <Popover v-model:open="open">
                                    <PopoverTrigger class="border p-1 rounded w-full flex justify-between items-center">
                                        <span>{{ materielSelected ? materielSelected.matricule ? `${materielSelected.nom_materiel} (${materielSelected.matricule})`: materielSelected.nom_materiel: 'Sélectionner un materiel'}} </span>
                                         <ChevronsUpDown class="text-sm text-muted-foreground w-5 h-5"/>
                                    </PopoverTrigger>
                                    <PopoverContent>
                                         <Command>
                                            <CommandInput placeholder="Rechercher un matériel" />
                                            <CommandList>
                                                <CommandEmpty>Aucun matériel trouvé</CommandEmpty>
                                                <CommandGroup heading="Matériels">
                                                    <CommandItem 
                                                        v-for="materielDisponible in materielsDisponibles "
                                                        :key="materielDisponible.id"
                                                        :value="materielDisponible.id"
                                                        @select="() => {
                                                            materielSelected = materielDisponible
                                                            open = false
                                                        }"

                                                        class="flex justify-between"
                                                    >
                                            
                                                        <span>{{ materielDisponible.nom_materiel }}</span>
                                                        <span v-if="materielDisponible.matricule" class="text-muted-foreground text-sm">{{ materielDisponible.matricule }}</span> 
                                                        <Check v-if="materielDisponible.id===materielSelected?.id"/>
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>

                                <InputError :message="errors.materiel_id[0]" v-if="errors.materiel_id" />
                            </div>
                        </div>
                        <div v-if="!materielSelected?.gestion_par_unite && !materielSelected==''" class="grid grid-cols-4 items-center gap-4">
                           <Label for="quantite" class="text-right">
                                Quantite
                            </Label>
                            <div  class="col-span-3">
                                <Input
                                    id="quantite"
                                    type="number"
                                    v-model="formMateriel.quantite"
                                />
                                <div class="text-muted-foreground text-sm mt-2">{{ materielSelected?.quantite_disponible }} {{ materielSelected?.nom_materiel }}{{ materielSelected?.quantite_disponible > 1 ? 's':'' }} disponible{{ materielSelected?.quantite_disponible > 1 ? 's':'' }}</div>
                                <InputError :message="errors.quantite[0]" v-if="errors.quantite" class="col-span-4"/>
                            </div>
                            

                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="date_fin_affectation_prevue" class="text-right">
                                Date fin prévue
                            </Label>
                            <div class="col-span-3">
                                 <Input
                                    id="date_fin_affectation_prevue"
                                    type="date"
                                    v-model="formMateriel.date_fin_affectation_prevue"
                                />
                                <InputError :message="errors.date_fin_affectation_prevue[0]" v-if="errors.date_fin_affectation_prevue" />
                            </div>
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="remarques" class="text-right">
                                Remarques
                            </Label>
                            <div class="col-span-3">
                                <Textarea id="remarques" placeholder="Remarques"  v-model="formMateriel.remarques"/>
                                <InputError :message="errors.remarques[0]" v-if="errors.remarques"/>
                            </div>
                        </div>
                        </div>
                        <DialogFooter>
                            <Button type="submit">
                                <LoaderCircle v-if="formMateriel.processing" class="h-4 w-4 animate-spin" />
                                Ajouter
                            </Button>
                        </DialogFooter>
                    </form>
                    </DialogContent>
                </Dialog>
            </CardHeader>
            <div class="mx-5">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <ContextMenu v-for="materielChantier in materielsChantier">
                        <ContextMenuTrigger class="border p-2 rounded bg-white grid grid-cols-4 gap-4 items-center">
                            <span class="col-span-2 flex flex-col">
                                <span>{{ materielChantier.nom_materiel }} </span>
                                <span class="text-muted-foreground text-sm">{{ materielChantier.matricule }}</span>
                                <span class="text-muted-foreground text-sm" v-if="materielChantier.pivot.quantite">{{ materielChantier.pivot.quantite}} en utilisation</span>
                                <span class="text-muted-foreground text-sm">{{ materielChantier.date_debut_affectation_formated}} - {{ materielChantier.date_fin_affectation_formated }}</span> 
                                <span class="text-muted-foreground text-sm" v-if="materielChantier.pivot.date_retour_effectif"> retournée le {{ materielChantier.date_retour_effectif_formated }}</span>
                            </span> 
                            <div class="col-span-2 flex justify-end">
                                <ToggleGroup type="single" :model-value="materielChantier.etat" @update:model-value="val=>materielState(materielChantier, val)" v-if="materielChantier.gestion_par_unite">
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
                                        {{ errors.date_retour_effectif }}
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
                                <Link :href="route('materiel.show',materielChantier.id)" class="flex items-center"><Pickaxe class="mr-2"/> Aller au materiel</Link>
                            </ContextMenuItem>
                        </ContextMenuContent>
                    </ContextMenu>

                </div>
                            
                <Collapsible v-model:open="isOpen">
                    <CollapsibleTrigger class="flex items-center font-bold">Materiels utilisées   <ChevronsUpDown class="h-4 w-4 ml-4" /></CollapsibleTrigger>
                    <CollapsibleContent>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div v-for="materielUtilisee in materielsUtilisees" class="border p-2 rounded bg-white grid grid-cols-4 gap-4 items-center">
                                <span class="col-span-2 flex flex-col">
                                    <span>{{ materielUtilisee.nom_materiel }} </span>
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