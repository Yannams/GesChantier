<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Http\Requests\StoreTacheRequest;
use App\Http\Requests\UpdateTacheRequest;

class TacheController extends Controller
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
    public function store(StoreTacheRequest $request)
    {
        $rules=[
            'libelle'=>'string',
            'Description'=>'string',
            'statut'=>'string',
            'DateDebutPrevue'=>'nullable|before_or_equal:DateFinPrevue|after:today',
            'DateFinPrevue'=>'nullable|after_or_equal:DateDebutPrevue',
            'created_at'=>now(),
            'updated_at'=>now(),
        ];
        $validatedData= $request->validate($rules);
        $validatedData['chantier_id']=$request->chantier_id;
        Tache::create($validatedData);
        return to_route('chantier.show',$request->chantier_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tache $tache)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tache $tache)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTacheRequest $request, Tache $tache)
    {
        $rules=[
            'libelle'=>'string',
            'Description'=>'string',
            'statut'=>'string',
            'DateDebutPrevue'=>'nullable|before_or_equal:DateFinPrevue|after:today',
            'DateFinPrevue'=>'nullable|after_or_equal:DateDebutPrevue',
            'created_at'=>now(),
            'updated_at'=>now(),
        ];
        $validatedData=$request->validate($rules);
        $tache->update($validatedData);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tache $tache)
    {
        //
    }
}
