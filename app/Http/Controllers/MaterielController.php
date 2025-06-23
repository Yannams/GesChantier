<?php

namespace App\Http\Controllers;

use App\Models\CategorieMateriel;
use App\Models\Materiel;
use App\Http\Requests\StoreMaterielRequest;
use App\Http\Requests\UpdateMaterielRequest;
use App\Models\Chantier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class MaterielController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiels=Materiel::with('categorieMateriel')->get();
        $categoriesMateriel=CategorieMateriel::all();
        return Inertia::render('materiel/Index',[
            'materiels'=>$materiels,
            'categoriesMateriel'=>$categoriesMateriel,
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
            'matricule'=>'unique:materiels,matricule|nullable',
            'quantite_totale'=>'nullable|integer',
            'gestion_par_unite'=>'boolean',
            'Description'=>'string',
            'etat'=>'required',
            'categorie_materiel_id'=>'required'
        ]);
        if($validatedData['gestion_par_unite']===true){
            $validatedData['quantite_totale']= null;
            $validatedData['quantite_disponible']=null;
        }else{
            $validatedData['matricule']=null;
            $validatedData['etat']=null;
        }
        $validatedData['quantite_disponible']=$validatedData['quantite_totale'];
        Materiel::create($validatedData);
        return to_route('materiel.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materiel $materiel)
    {
        $materielsChantierExploit=$materiel->chantiers()->wherePivotNull('date_retour_effectif')->get()->map(function ($chantier) use ($materiel)  {
            $chantier->date_debut_affectation_formated=date('d/m/Y',strtotime($chantier->pivot->date_debut_affectation));
            if($chantier->pivot->date_fin_affectation_prevue){
                $chantier->date_fin_affectation_formated=date('d/m/Y',strtotime($chantier->pivot->date_fin_affectation_prevue));
            }
            $chantier->gestion_par_unite=$materiel->gestion_par_unite;
            $chantier->etat_affectation=$materiel->etat;
            return $chantier;
        });

        $materielsChantierFini=$materiel->chantiers()->wherePivotNotNull('date_retour_effectif')->get()->map(function ($chantier) use ($materiel)  {
            $chantier->date_debut_affectation_formated=date('d/m/Y',strtotime($chantier->pivot->date_debut_affectation));
            if($chantier->pivot->date_fin_affectation_prevue){
                $chantier->date_fin_affectation_formated=date('d/m/Y',strtotime($chantier->pivot->date_fin_affectation_prevue));
            }
            if($chantier->pivot->date_retour_effectif){
                $chantier->date_retour_effectif_formated=date('d/m/Y',strtotime($chantier->pivot->date_retour_effectif));
            }
            $chantier->gestion_par_unite=$materiel->gestion_par_unite;
            $chantier->etat_affectation=$materiel->etat;
            return $chantier;
        });

        $categoriesMateriel=CategorieMateriel::all();
        return Inertia::render('materiel/Show',[
            'materiel'=>$materiel,
            'materielsChantierExploit'=>$materielsChantierExploit,
            'materielsChantierFini'=>$materielsChantierFini,
            'categoriesMateriel'=>$categoriesMateriel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materiel $materiel)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterielRequest $request, Materiel $materiel)
    {
       
       if ($request->processing==='stateChanging') {
            $validateData= Validator::make($request->all(),[
                'chantier_id'=>'required|exists:chantiers,id',
                'pivot_id'=>'required|exists:chantier_materiel,id',
            ]);
            if ($validateData->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => [
                        'global_probleme' => ['Un probleme est survenu!']
                    ]
                ], 422);
            }
        $chantier= Chantier::find($request->chantier_id);
            $materielsChantiers=$chantier->materiels()->wherePivot('id',$request->pivot_id)->get();
            if (!$materielsChantiers->isEmpty()) {
                $materiel->update(['etat'=>$request->etat]); 
                DB::table('chantier_materiel')->where('id',$request->pivot_id)->update(['date_retour_effectif'=>now()]);
                $materielChantier= $materielsChantiers->first();
                if($materielChantier){
                    $materielChantier->date_debut_affectation_formated=date('d/m/Y',strtotime($materielChantier->pivot->date_debut_affectation));
                    $materielChantier->date_fin_affectation_formated=date('d/m/Y',strtotime($materielChantier->pivot->date_fin_affectation_prevue));
                    $materielChantier->date_retour_effectif_formated=date('d/m/Y',strtotime($materielChantier->pivot->date_retour_effectif));
                }
                return response()->json($materielChantier);
            }else {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => [
                        'global_probleme' => ['Un probleme est survenu!']
                    ]
                ], 422);
            }
           
       }elseif ($request->type_retour) {
            $validateData=$request->validate([
                'type_retour'=>'in:partiel,tout',
                'quantite'=>'nullable',
                'date_retour_effectif'=>'before_or_equal:today',
                'chantier_id'=>'required|exists:chantiers,id',
                'pivot_id'=>'required|exists:chantier_materiel,id'
            ]);
             $chantier=Chantier::find($request->chantier_id);
            if($request->type_retour==='partiel'){

                $materielChantierInDB= DB::table('chantier_materiel')->where('id',$request->pivot_id)->first();
                if ($request->quantite < $materielChantierInDB->quantite) {
                   DB::transaction(function () use ($request, $materielChantierInDB, $materiel) {
                    $materiel->chantiers()->attach($request->chantier_id,[
                        'date_debut_affectation'=> $materielChantierInDB->date_debut_affectation,
                        'date_fin_affectation_prevue'=>$materielChantierInDB->date_fin_affectation_prevue,
                        'remarques'=>$materielChantierInDB->remarques,
                        'quantite'=>$request->quantite, 
                        'date_retour_effectif'=>$request->date_retour_effectif
                    ]);
                    DB::table('chantier_materiel')->where('id',$request->pivot_id)->update(['quantite'=>$materielChantierInDB->quantite-$request->quantite]);
                });
               
                $chantierMateriel= $chantier->materiels()
                    ->where('materiel_id', $materiel->id)
                    ->wherePivot('date_retour_effectif',null)
                    ->first();
                    if ($chantierMateriel) {
                        $chantierMateriel->date_debut_affectation_formated = date('d/m/Y', strtotime($chantierMateriel->pivot->date_debut_affectation));
                        $chantierMateriel->date_fin_affectation_formated = date('d/m/Y', strtotime($chantierMateriel->pivot->date_fin_affectation_prevue));
                        $chantierMateriel->date_retour_effectif_formated = $chantierMateriel->pivot->date_retour_effectif
                            ? date('d/m/Y', strtotime($chantierMateriel->pivot->date_retour_effectif))
                            : null;
                    }
                    $date_retour_effectif=Carbon::parse($request->date_retour_effectif);
                    $chantierMaterielRetournee= $chantier->materiels()
                        ->where('materiel_id', $materiel->id)
                        ->wherePivotBetween('date_retour_effectif',[$date_retour_effectif->copy()->startOfDay(),$date_retour_effectif->copy()->endOfDay()])
                        ->wherePivot('quantite',$request->quantite)
                        ->orderByPivot('created_at','desc')
                        ->first();
                        if ($chantierMaterielRetournee) {
                        $chantierMaterielRetournee->date_debut_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_debut_affectation));
                        $chantierMaterielRetournee->date_fin_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_fin_affectation_prevue));
                        $chantierMaterielRetournee->date_retour_effectif_formated = $chantierMaterielRetournee->pivot->date_retour_effectif
                            ? date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_retour_effectif))
                            : null;
                    }
                    $materiel->update(['quantite_disponible'=>$materiel->quantite_disponible + $request->quantite]);
                    return response()->json([   
                        'chantierMateriel'=>$chantierMateriel,
                        'materiel'=>$materiel,
                        'chantierMaterielRetournee'=>$chantierMaterielRetournee
                    ]);
                }elseif($request->quantite == $materielChantierInDB->quantite){
                    DB::table('chantier_materiel')->where('id',$request->pivot_id)->update(['date_retour_effectif'=>$request->date_retour_effectif]);
                    $date_retour_effectif=Carbon::parse($request->date_retour_effectif);
                    $chantierMaterielRetournee= $chantier->materiels()
                        ->where('materiel_id', $materiel->id)
                        ->wherePivotBetween('date_retour_effectif',[$date_retour_effectif->copy()->startOfDay(),$date_retour_effectif->copy()->endOfDay()])
                        ->wherePivot('quantite',$request->quantite)
                        ->orderByPivot('updated_at','desc')
                        ->first();
                    if ($chantierMaterielRetournee) {
                        $chantierMaterielRetournee->date_debut_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_debut_affectation));
                        $chantierMaterielRetournee->date_fin_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_fin_affectation_prevue));
                        $chantierMaterielRetournee->date_retour_effectif_formated = $chantierMaterielRetournee->pivot->date_retour_effectif
                            ? date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_retour_effectif))
                            : null;
                    }
                    $materiel->update(['quantite_disponible'=>$materiel->quantite_disponible + $request->quantite]);
                     return response()->json([   
                        'chantierMaterielRetournee'=>$chantierMaterielRetournee,
                        'materiel'=>$materiel
                    ]);
                }elseif ($request->quantite > $materielChantierInDB->quantite) {
                    return response()->json([ 
                        'message' => 'Validation failed.',
                        'errors' => [
                            'quantite' =>"la quantite de materiel est supérieur à celle à votre disposition ($materielChantierInDB->quantite)."
                        ]
                    ], 422);
                }
            }elseif ($request->type_retour==='tout') {
               DB::table('chantier_materiel')->where('id',$request->pivot_id)->update(['date_retour_effectif'=>$request->date_retour_effectif]);
            
                $date_retour_effectif=Carbon::parse($request->date_retour_effectif);
                $chantierMaterielRetournee= $chantier->materiels()
                    ->where('materiel_id', $materiel->id)
                    ->wherePivotBetween('date_retour_effectif',[$date_retour_effectif->copy()->startOfDay(),$date_retour_effectif->copy()->endOfDay()])
                    ->orderByPivot('updated_at','desc')
                    ->first();
                if ($chantierMaterielRetournee) {
                    $chantierMaterielRetournee->date_debut_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_debut_affectation));
                    $chantierMaterielRetournee->date_fin_affectation_formated = date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_fin_affectation_prevue));
                    $chantierMaterielRetournee->date_retour_effectif_formated = $chantierMaterielRetournee->pivot->date_retour_effectif
                        ? date('d/m/Y', strtotime($chantierMaterielRetournee->pivot->date_retour_effectif))
                        : null;
                }
                $materiel->update(['quantite_disponible'=> $materiel->quantite_disponible+$chantierMaterielRetournee->pivot->quantite]);
                return response()->json([
                    'chantierMaterielRetournee'=>$chantierMaterielRetournee,
                    'materiel'=>$materiel
                ]);
                    
            }
       }elseif ($request->nom_materiel) {
    
            $validatedData=$request->validate([
                'nom_materiel'=>'required',
                'matricule'=>'unique:materiels,matricule,'.$materiel->id.',id|nullable',
                'quantite_totale'=>'nullable|integer',
                'gestion_par_unite'=>'boolean',
                'Description'=>'string',
                'categorie_materiel_id'=>'required'
            ]);

            if($validatedData['gestion_par_unite']==true){
                $validatedData['quantite_totale']= null;
                $validatedData['quantite_disponible']=null;
            }else{
                $validatedData['matricule']=null;
                $validatedData['etat']=null;
            }
        
            $materiel->update($validatedData);
            return to_route('materiel.show',$materiel->id);
       }else {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => [
                    'global_probleme' => ['Un probleme est survenu!']
                ]
            ], 422);
       }

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
            "date_fin_affectation_prevue"=>'date|after:today',
            'quantite'=>'nullable|integer|min:1',
            "remarques"=>'string'
        ]);
       
        $materiel=Materiel::find($validatedData['materiel_id']);
       
        if (isset($validatedData['quantite']) && $validatedData['quantite'] > $materiel->quantite_disponible) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => [
                        'quantite' => ['La quantité demandée dépasse le stock disponible.']
                    ]
                ], 422);
            }
        if($materiel->etat == 'utilisé'){        
           return to_route('chantier.show',$request->chantier_id)->with('error','Le matériel sélectionné est indisponiible');
        }
        $chantier=Chantier::find($validatedData['chantier_id']);
        
        if ( $materiel->gestion_par_unite) {
            $materiel->update(['etat'=>'utilisé']);
            $materiel->chantiers()->attach($validatedData['chantier_id'], ['date_debut_affectation'=>now(),'date_fin_affectation_prevue'=>$validatedData['date_fin_affectation_prevue'],'remarques'=>$validatedData['remarques']]);
        }else {
            $materiel->update(['quantite_disponible'=>$materiel->quantite_disponible-$validatedData['quantite']]);
            $materiel->chantiers()->attach($validatedData['chantier_id'], ['date_debut_affectation'=>now(),'date_fin_affectation_prevue'=>$validatedData['date_fin_affectation_prevue'],'remarques'=>$validatedData['remarques'],'quantite'=>$validatedData['quantite']]);
        }
        $materielChantiers = $chantier->materiels()
            ->where('materiel_id', $validatedData['materiel_id'])
            ->get();

        // Prendre la dernière ligne pivot (la plus récente) – triée par date_debut_affectation
        $materielChantier = $materielChantiers->sortByDesc(function ($item) {
            return $item->pivot->date_debut_affectation;
        })->first();

        if ($materielChantier) {
            $materielChantier->date_debut_affectation_formated = date('d/m/Y', strtotime($materielChantier->pivot->date_debut_affectation));
            $materielChantier->date_fin_affectation_formated = date('d/m/Y', strtotime($materielChantier->pivot->date_fin_affectation_prevue));
            $materielChantier->date_retour_effectif_formated = $materielChantier->pivot->date_retour_effectif
                ? date('d/m/Y', strtotime($materielChantier->pivot->date_retour_effectif))
                : null;
        }
        return response()->json($materielChantier);
    }
    public function RemoveMateriel(Request $request ) {    
        $chantier=Chantier::find($request->chantier_id);
        $chantier->materiels()->detach($request->materiel_id);
    }
}
