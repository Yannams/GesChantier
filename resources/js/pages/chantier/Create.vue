<script setup lang="ts">
    import InputError from '@/components/InputError.vue';;
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { LoaderCircle } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import FormContainerLayout from '@/layouts/app/FormContainerLayout.vue';
import { type BreadcrumbItem } from '@/types';
const form = useForm({
    nom_chantier: '',
    localisation: '',
    DateDebutPrevue: '',
    DateFinPrevue: '',
    DateDebutReelle: '',
    DateFinReelle: '',
    Etat:'en attente',
});

const submit = () => {
    form.post(route('chantier.store'), {   
        onFinish: () => form.reset('nom_chantier', 'localisation', 'DateDebutPrevue', 'DateFinPrevue'),
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Chantiers',
        href: '/chantier',
    },

    {
        title: 'Nouveau',
        href: '/chantier/create',
    },

];

</script>

<template>
    <FormContainerLayout :title="'Créer chantier'" :description="'Ajouter un nouveau chantier'" :breadcrumbs>
  
        <form @submit.prevent="submit"  class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="nom_chantier">Nom chantier</Label>
                    <Input id="nom_chantier" type="text" required autofocus :tabindex="1" autocomplete="nom_chantier" v-model="form.nom_chantier" placeholder="Nom chantier" />
                    <InputError :message="form.errors.nom_chantier" />
                </div>
                 <div class="grid gap-2">
                    <Label for="localisation">Localisation</Label>
                    <Input id="localisation" type="text" required autofocus :tabindex="1" autocomplete="localisation" v-model="form.localisation" placeholder="ex: quartier, lot, ..." />
                    <InputError :message="form.errors.localisation" />
                </div>
                 
                <div class="grid gap-2">
                    <Label for="DateDebutPrevue">Date début prévue</Label>
                    <Input id="DateDebutPrevue" type="date" required autofocus :tabindex="1" autocomplete="DateDebutPrevue" v-model="form.DateDebutPrevue" placeholder="Nom chantier" />
                    <InputError :message="form.errors.DateDebutPrevue" />
                </div>
                 <div class="grid gap-2">
                    <Label for="DateFinPrevue">Date fin prévue</Label>
                    <Input id="DateFinPrevue" type="date" required autofocus :tabindex="1" autocomplete="DateFinPrevue" v-model="form.DateFinPrevue" placeholder="Nom chantier" />
                    <InputError :message="form.errors.DateFinPrevue" />
                </div>
                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Ajouter
                </Button>

            </div>
        </form>
    </FormContainerLayout>
</template>