<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Http\Requests\StoreChantierRequest;
use App\Http\Requests\UpdateChantierRequest;
use App\Models\Etape;
use App\Models\Materiel;
use App\Models\Ouvrier;
use App\Models\Tache;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Inertia\Inertia;

class ChantierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chantiers= Chantier::all();
        return Inertia::render('chantier/Index',['chantiers'=>$chantiers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('chantier/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChantierRequest $request)
    {
        $rules=[
                'nom_chantier'=>'string',
                'localisation'=>'string',
                'Etat'=>"string",
                'created_at'=>now(),
                'updated_at'=>now(),
        ];
        if($request->DateDebutPrevue){
            $rules['DateDebutPrevue']='date|after_or_equal:today|before:DateFinPrevue';
        }
        if($request->DateFinPrevue){
            $rules['DateFinPrevue']='date|after_or_equal:today|after:DateDebutPrevue';
        }
        $validatedData= $request->validate($rules);
        Chantier::create($validatedData);
        return to_route('chantier.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Chantier $chantier)
    {
        $taches= Tache::where('chantier_id',$chantier->id)->get()->map(function($tache){
            if($tache->DateDebutPrevue){
                $tache->date_debut_prevue_formated = date('d/m/Y',strtotime($tache->DateDebutPrevue));
            }
            if($tache->DateFinPrevue){
                $tache->date_fin_prevue_formated = date('d/m/Y',strtotime($tache->DateFinPrevue));
            }
            return $tache;
        });
 
        $ouvriers= Ouvrier::all();
        $etapes=Etape::with(['ouvriers' => function ($query) {
                    $query->where('EtatAffectation', 'affecté');
                }])->get();
        $materielsDisponibles=Materiel::where('etat','disponible')->orWhere('quantite_disponible','>', 0)->get();
        $materielsChantier=$chantier->materiels()->wherePivot('date_retour_effectif','=',null)->get()->map(function($materiel){
           $materiel->date_debut_affectation_formated=date('d/m/Y',strtotime($materiel->pivot->date_debut_affectation));
           $materiel->date_fin_affectation_formated=date('d/m/Y',strtotime($materiel->pivot->date_fin_affectation_prevue));
           $materiel->date_retour_effectif_formated=date('d/m/Y',strtotime($materiel->pivot->date_retour_effectif));
           return $materiel;
        });

        $materielsUtilisees=$chantier->materiels()->wherePivot('date_retour_effectif','!=',null)->get()->map(function($materiel){
           $materiel->date_debut_affectation_formated=date('d/m/Y',strtotime($materiel->pivot->date_debut_affectation));
           $materiel->date_fin_affectation_formated=date('d/m/Y',strtotime($materiel->pivot->date_fin_affectation_prevue));
           $materiel->date_retour_effectif_formated=date('d/m/Y',strtotime($materiel->pivot->date_retour_effectif));
           return $materiel;
        });
        return Inertia::render('chantier/Show',[
            'chantier'=> $chantier,
            'taches'=>$taches,
            'ouvriers'=>$ouvriers,
            'etapes'=>$etapes,
            'materielsDisponibles'=>$materielsDisponibles,
            'materielsChantier'=>$materielsChantier,
            'materielsUtilisees'=>$materielsUtilisees,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chantier $chantier)
    {
        return Inertia::render('chantier/Edit', ['chantier' => $chantier]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChantierRequest $request, Chantier $chantier)
    {
       
        if($request->Etat){
            if( $request->Etat=="en cours"){
                $chantier->update(['Etat'=>$request->Etat,'DateDebutReelle'=>now()]);
            }
            if( $request->Etat=="terminé"){
                if ($chantier->DateDebutReelle=="") {
                    return to_route('chantier.show',$chantier)->with('error','Le chantier n\'a pas été démaré');
                }
                $chantier->update(['Etat'=>$request->Etat,'DateFinReelle'=>now()]);
            }
             if( $request->Etat=="en attente"){
                $chantier->update(['Etat'=>$request->Etat,'DateDebutReelle'=>now()]);
            }
        }else{
              $rules=[
                'nom_chantier'=>'string',
                'localisation'=>'string',
                'Etat'=>"string",
                'updated_at'=>now(),
            ];
            if($request->DateDebutPrevue){
                $rules['DateDebutPrevue']='date|after_or_equal:today|before:DateFinPrevue';
            }
            if($request->DateFinPrevue){
                $rules['DateFinPrevue']='date|after_or_equal:today|after:DateDebutPrevue';
            }
            $validatedData= $request->validate($rules);
            
            $chantier->update($validatedData);
        }
      
      
        return to_route('chantier.show',$chantier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chantier $chantier)
    {
        //
    }
}
