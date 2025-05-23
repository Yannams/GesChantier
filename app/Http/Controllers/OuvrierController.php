<?php

namespace App\Http\Controllers;

use App\Models\Ouvrier;
use App\Http\Requests\StoreOuvrierRequest;
use App\Http\Requests\UpdateOuvrierRequest;
use Inertia\Inertia;

class OuvrierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ouvriers=Ouvrier::all();   
        return Inertia::render('ouvrier/Index',['ouvriers'=>$ouvriers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ouvrier/Create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOuvrierRequest $request)
    {
        $validatedData=$request->validate([
            "nom"=>"string",
            "Prenom"=>"string",
            "specialisation"=>"string",
            "Contact"=>"regex:/^\+?[0-9 ]{1,20}$/|unique:ouvriers,Contact",
            "Adresse"=>"string",
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
      
        Ouvrier::create($validatedData);
        return to_route('ouvrier.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ouvrier $ouvrier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ouvrier $ouvrier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOuvrierRequest $request, Ouvrier $ouvrier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ouvrier $ouvrier)
    {
        //
    }
}
