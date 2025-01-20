<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function store(Request $request)
{
    // Validation des données de la boutique
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        'address' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string',
        'status' => 'required|string|in:actif,inactif',
        'seller_id' => 'required|exists:users,id',  // Assurez-vous que cette table existe
    ]);
    
    if ($request->hasFile('logo')) {
        // Upload du fichier logo
        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;
    }

    try {
        // Création de la boutique
        $shop = Shop::create($validated + [
            'description' => $request->input('description', null),
            'logo' => $request->input('logo', null),
            'phone' => $request->input('phone', null),
            'seller_id' => $request->input('seller_id'),
        ]);

        // Réponse en cas de succès
        return redirect()->route('shop.show')->with('success', 'Boutique ajoutée avec succès !');

    } catch (\Exception $e) {
        // Gestion des erreurs
        return back()->withErrors(['error' => 'Erreur lors de l\'ajout de la boutique : ' . $e->getMessage()]);
    }
}

public function show()
{
    // Récupérer la boutique par ID
    $shops = Shop::all();

    // Retourner la vue avec les données de la boutique
    return view('Admin.Affiche_Boutique', compact('shops'));
}

public function destroy($id)
{
    $shop = Shop::findOrFail($id);
    $shop->delete();

    return redirect('/Affiche-Boutique')->with('success', 'Boutique supprimée avec succès');
}





public function edit($id)
{
    $shop = Shop::findOrFail($id); // Trouve la boutique par ID
    return view('admin.Modifier_Boutique', compact('shop')); // Retourne la vue avec les données
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|string',
        'address' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string',
        'status' => 'required|string|in:actif,inactif',
        'seller_id' => 'required|exists:users,id',
    ]);

    try {
        $shop = Shop::findOrFail($id);
        $shop->update($validated); // Met à jour les données
        return redirect()->route('shop.show')->with('success', 'Boutique modifiée avec succès');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la modification');
    }
}


public function index()
    {
        // Récupérer toutes les boutiques depuis la base de données
        $shops = Shop::all(); 

        // Retourner une vue avec les boutiques
        return view('admin.Gerer_Boutique', compact('shops'));
    }


    public function showProducts($shopId)
{
    $shop = Shop::findOrFail($shopId);

     $products = Product::where('shop_id', $shop->id)->get();
    
    return view('admin.produits',compact('products'));
}}