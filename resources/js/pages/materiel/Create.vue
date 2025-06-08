<script setup lang="ts">
import FormContainerLayout from '@/layouts/app/FormContainerLayout.vue';
import { BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';;
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import Select from '@/components/ui/select/Select.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import { ref } from 'vue';

defineProps({
    categoriesMateriel:Object
})

const form = useForm({
    matricule: '',
    nom_materiel: '',
    Description: '',
    etat:'Disponible',
    categorie_materiel_id:'',
    quantite_totale:'',
    gestion_par_unite: true,
});

const submit = () => {
    form.post(route('materiel.store'), {   
        onFinish: () => form.reset('matricule','nom_materiel', 'Description','categorie_materiel_id'),
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Matériel',
        href: route('materiel.index'),
    },

    {
        title: 'Nouveau',
        href: route('materiel.create'),
    },

];
</script>

<template>
    <FormContainerLayout :breadcrumbs>
           <form @submit.prevent="submit"  class="flex flex-col gap-6">
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
                {{ form.errors }}
                                
                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Ajouter
                </Button>

            </div>
        </form>
    </FormContainerLayout>
</template>