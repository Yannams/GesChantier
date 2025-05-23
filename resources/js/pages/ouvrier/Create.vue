<script setup lang="ts">
import FormContainerLayout from '@/layouts/app/FormContainerLayout.vue';
import InputError from '@/components/InputError.vue';;
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
const form = useForm({
    nom: '',
    Prenom: '',
    specialisation: '',
    Contact: '',
    Adresse: '',
});

const submit = () => {
    form.post(route('ouvrier.store'), {   
        onFinish: () => form.reset('nom', 'Prenom', 'specialisation', 'Contact', 'Adresse'),
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ouvriers',
        href: route('ouvrier.index'),
    },

    {
        title: 'Nouveau',
        href: route('ouvrier.create'),
    },

];



</script>

<template>
    <FormContainerLayout :title="'Créer ouvrier'" :description="'Ajouter un nouvel ouvrier'" :breadcrumbs>
        <form @submit.prevent="submit"  class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="nom">Nom</Label>
                    <Input id="nom" type="text" required autofocus :tabindex="1" autocomplete="nom" v-model="form.nom" placeholder="Nom de famille de l'ouvrier" />
                    <InputError :message="form.errors.nom" />
                </div>
                 <div class="grid gap-2">
                    <Label for="Prenom">Prénom</Label>
                    <Input id="Prenom" type="text" required autofocus :tabindex="1" autocomplete="Prenom" v-model="form.Prenom" placeholder="Prénom de l'ouvrier" />
                    <InputError :message="form.errors.Prenom" />
                </div>
                 
                <div class="grid gap-2">
                    <Label for="specialisation">Spécialisation</Label>
                    <Input id="specialisation" type="text" required autofocus :tabindex="1" autocomplete="specialisation" v-model="form.specialisation" placeholder="ex: maçonnerie, plomberie..." />
                    <InputError :message="form.errors.specialisation" />
                </div>
                 <div class="grid gap-2">
                    <Label for="Contact">Contact</Label>
                    <Input id="Contact" type="text" required autofocus :tabindex="1" autocomplete="Contact" v-model="form.Contact" placeholder="+2290199XXXXXX" />
                    <InputError :message="form.errors.Contact" />
                </div>
                 <div class="grid gap-2">
                    <Label for="Adresse">Adresse</Label>
                    <Input id="Adresse" type="text" required autofocus :tabindex="1" autocomplete="Adresse" v-model="form.Adresse" placeholder="ex: fidjrossè" />
                    <InputError :message="form.errors.Adresse" />
                </div>
                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Ajouter
                </Button>

            </div>
        </form>
    </FormContainerLayout>
</template>