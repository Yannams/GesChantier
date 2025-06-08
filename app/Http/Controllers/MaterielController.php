<?php

namespace App\Http\Controllers;

use App\Models\CategorieMateriel;
use App\Models\Materiel;
use App\Http\Requests\StoreMaterielRequest;
use App\Http\Requests\UpdateMaterielRequest;
use App\Models\Chantier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterielController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiels=Materiel::with('categorieMateriel')->get();
        return Inertia::render('materiel/Index',[
            'materiels'=>$materiels
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoriesMateriel=CategorieMateriel::all();
        return Inertia::render('materiel/Create',[
            'categoriesMateriel'=>$categoriesMateriel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaterielRequest $request)
    {
        $validatedData=$request->validate([
            'nom_materiel'=>'required',
            'matricule'=>'unique:materiels,matricule',
            'quantite_totale'=>'nullable|integer',
            'gestion_par_unite'=>'boolean',
            'Description'=>'string',
            'etat'=>'required',
            'categorie_materiel_id'=>'required'
        ]);
        $validatedData['quantite_disponible']=$validatedData['quantite_totale'];
        // dd($validatedData);
        Materiel::create($validatedData);
        return to_route('materiel.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materiel $materiel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materiel $materiel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterielRequest $request, Materiel $materiel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materiel $materiel)
    {
        //
    }

    public function AddMateriel(Request $request ) {    
        $validatedData=$request->validate([
            "materiel_id"=>'required',
            "chantier_id"=>'required',
            "date_debut_affectation"=>'date|after_or_equal:today|before:date_fin_affectation',
            "date_fin_affectation"=>'date|after_or_equal:today|after:date_debut_affectation',
        ]);

        $materiel=Materiel::find($validatedData['materiel_id']);
        if($materiel->etat == 'utilisé'){        
           return to_route('chantier.show',$request->chantier_id)->with('error','Le matériel sélectionné est indisponiible');
        }
        $materiel->chantiers()->attach($validatedData['chantier_id'], ['']);
        $materiel->update(['etat'=>'utilisé']);
        return to_route('chantier.show',$request->chantier_id);
    }
     public function RemoveMateriel(Request $request ) {    
        $chantier=Chantier::find($request->chantier_id);
        $chantier->materiels()->detach($request->materiel_id);
    }
}
