<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;


class ShopController extends Controller
{
    public function store(Request $request)
{
    // Validation des données de la boutique
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048', 
        'address' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string',
        'status' => 'required|string|in:actif,inactif',
        'seller_id' => 'required|exists:users,id',  
    ]);
    
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;
    }

    try {

        $shop = Shop::create($validated + [
            'description' => $request->input('description', null),
            'logo' => $request->input('logo', null),
            'phone' => $request->input('phone', null),
            'seller_id' => $request->input('seller_id'),
        ]);

        return redirect()->route('shop.show')->with('success', 'Boutique ajoutée avec succès !');

    } catch (\Exception $e) {

        return back()->withErrors(['error' => 'Erreur lors de l\'ajout de la boutique : ' . $e->getMessage()]);
    }
}

public function show()
{
    // Récupérer la boutique par ID
    $shops = Shop::all();

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
    $shop = Shop::findOrFail($id); 
    return view('Admin.Modifier_Boutique', compact('shop')); //AVEC DONNEES
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
        $shop->update($validated); 
        return redirect()->route('shop.show')->with('success', 'Boutique modifiée avec succès');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la modification');
    }
}
public function index()
    {
        // Récupérer les boutiques  la base 
        $shops = Shop::all(); 
        return view('admin.Gerer_Boutique', compact('shops'));
    }





    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:1',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:actif,inactif',
        ]);
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }
    
        try {
            $category = Category::find($request->category_id);
    
            if (!$category || !$category->shop_id) {
                return redirect()->back()->withErrors(['category_id' => 'La catégorie sélectionnée n\'est pas valide ou n\'a pas de boutique associée.']);
            }
    
            $validated['shop_id'] = $category->shop_id; // Ajout cruciale
    
            Product::create($validated);
    
            return redirect()->route('products.create')->with('success', 'Produit ajouté avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout du produit : ' . $e->getMessage());
        }
    }
    public function showProducts($shopId)
{
    $shop = Shop::findOrFail($shopId);
     $products = Product::where('shop_id', $shop->id)->get();
    return view('Admin.produits',compact('products'));
}

public function create()
{
    $categories = Category::all(); 
    $shops = Shop::all();
    return view('Admin.Ajoute_Produit', compact('categories', 'shops')); 
}







public function storeCategory(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:actif,inactif',
            'shop_id' => 'required|exists:shops,id',  
        ]);

        try {
            
            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'shop_id' => $request->shop_id,
            ]);

    
            return redirect()->route('categories.create')->with('success', 'Catégorie ajoutée avec succès !');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Erreur lors de l\'ajout de la catégorie. Veuillez réessayer plus tard.');
        }


    }
    public function createCategory()
    {
        $shops = Shop::all();
         return view('Admin.Ajouter_Categorie', compact('shops'));
        }
        public function showCategories()
        {
            $categories = Category::with('shop')->get(); // Charge les catégories avec les boutiques associées
            return view('Admin.Affiche_Categories', compact('categories'));
        }

        public function editCat($id)
{
    $category = Category::findOrFail($id); // Trouver la catégorie par son ID
    return view('Admin.Modifier_Categories', compact('category'));
}
public function updateCat(Request $request, $id)
{
    // Valider les données envoyées
    $request->validate([
        'name' => 'required|unique:categories,name,' . $id,
        'description' => 'nullable|string',
        'status' => 'required|in:actif,inactif',
    ]);

    // Trouver la catégorie par son ID
    $category = Category::find($id);

    if (!$category) {
        return redirect()->route('categories.update')->with('error', 'Catégorie introuvable');
    }

    // Mettre à jour les informations de la catégorie
    $category->name = $request->input('name');
    $category->description = $request->input('description');
    $category->status = $request->input('status');
    $category->save();

    // Retourner à la page de l'affichage des catégories avec un message de succès
    return redirect()->route('categories.show')->with('success', 'Catégorie modifiée avec succès');

}






public function destroyCategory($id)
{
    try {
        $category = Category::findOrFail($id); // Récupère la catégorie par son ID

        // Vérifie si la catégorie contient des produits
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }

        // Supprime la catégorie si elle ne contient aucun produit
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès !');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
}





}