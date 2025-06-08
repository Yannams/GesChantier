<?php

namespace App\Http\Controllers;

use App\Models\Etape;
use App\Http\Requests\StoreEtapeRequest;
use App\Http\Requests\UpdateEtapeRequest;
use App\Models\HistoriqueEtatEtape;

class EtapeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEtapeRequest $request)
    {
        
       $rules=[
            'libelle'=>'string',
            'Description'=>'string',
            'Etat'=>'string',
            'created_at'=>now(),
            'updated_at'=>now(),
            'tache_id'=>'required'
        ];
          if($request->DateDebutPrevue){
            $rules['DateDebutPrevue']='date|after_or_equal:today|before:DateFinPrevue';
        }
        if($request->DateFinPrevue){
            $rules['DateFinPrevue']='date|after_or_equal:today|after:DateDebutPrevue';
        }
        $validatedData= $request->validate($rules);
        $etape=Etape::create($validatedData);
        
        if($request->ouvrier_id){
            $ouvriers=$request->validate(['ouvrier_id'=>'required|array|min:1']);
            foreach ($ouvriers as $ouvrier_id) {
               $etape->ouvriers()->attach($ouvrier_id, ['EtatAffectation'=>'affecté', 'created_at'=>now(), 'updated_at'=>now()]);
            } 
        }
         $etape->load(['ouvriers' => function ($query) {
            $query->where('EtatAffectation', 'affecté');
        }]);
        HistoriqueEtatEtape::create([
            'etape_id'=>$etape->id,
            'Etat'=>$etape->Etat,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
        return response()->json(['etape' => $etape]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Etape $etape)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etape $etape)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEtapeRequest $request, Etape $etape)
    {
       
        $rules=[
            'libelle'=>'string',
            'Description'=>'string',
            'created_at'=>now(),
            'updated_at'=>now(),
            'tache_id'=>'required'
        ];
          if($request->DateDebutPrevue){
            $rules['DateDebutPrevue']='date|after_or_equal:today|before:DateFinPrevue';
        }
        if($request->DateFinPrevue){
            $rules['DateFinPrevue']='date|after_or_equal:today|after:DateDebutPrevue';
        }
        $validatedData= $request->validate($rules);
        if( $request->Etat=="en cours"){
               $validatedData['DateDebutReelle']=now()->toDateString();
               $validatedData['Etat']="en cours";
            }
            if( $request->Etat=="terminé"){
                if ($etape->DateDebutReelle=="") {
                   
                }else {
                    $validatedData['DateFinReelle']=now()->toDateString();
                    $validatedData['Etat']="terminé";
                }
            }
             if( $request->Etat=="en attente"){
                $validatedData['DateDebutReelle']=null; 
                $validatedData['DateFinReelle']=null;
                $validatedData['Etat']="en attente";
            }
            $etape->update($validatedData);
        
        if($request->ouvrier_id){
            $anciensIds = $etape->ouvriers()->wherePivot('EtatAffectation', 'affecté')->pluck('ouvrier_id')->toArray();
            $nouveauxIds = $request->ouvrier_id;
            $idsARetirer = array_diff($anciensIds, $nouveauxIds); // Désaffectés
            $idsAGarder  = array_intersect($anciensIds, $nouveauxIds); // Déjà affectés
            $idsAAjouter = array_diff($nouveauxIds, $anciensIds);
            foreach ($idsARetirer as $id) {
                $etape->ouvriers()->updateExistingPivot($id, ['EtatAffectation' => 'désaffecté']);
            }

            // 2. Garder comme 'affecté' (déjà le cas, mais on peut rafraîchir)
            foreach ($idsAGarder as $id) {
                $etape->ouvriers()->updateExistingPivot($id, ['EtatAffectation' => 'affecté']);
            }

            // 3. Affecter les nouveaux
            foreach ($idsAAjouter as $id) {
                $etape->ouvriers()->attach($id, ['EtatAffectation' => 'affecté']);
            }
        }
    
         $etape->load(['ouvriers' => function ($query) {
            $query->where('EtatAffectation', 'affecté');
        }]);
          HistoriqueEtatEtape::create([
            'etape_id'=>$etape->id,
            'Etat'=>$etape->Etat,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
        return response()->json(['etape' => $etape]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etape $etape)
    {
        $etape->ouvriers()->detach();
        $etape->delete();
        return response()->json(['message' => 'Étape supprimée.']);
    }
}
