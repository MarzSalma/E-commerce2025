<?php 
namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log; // Import the Log facade
class ShopController extends Controller
{
    public function store(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif', 
        'address' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string',
        'status' => 'required|string|in:actif,inactif',
        'seller_id' => 'required|exists:users,id',
    ]);
    // Upload logo if exists
    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $filename = $file->store('logos', 'public');
        $validated['logo'] = $filename;

        // Log the file details
        Log::info('Logo uploaded:', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'stored_as' => $filename,
        ]);
    }
    // Upload logo if exists
    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('logos', 'public');
    }

    try {
        // Create shop
        Shop::create($validated);

        return redirect()->route('shops.index')->with('success', 'Boutique ajoutée avec succès !');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Erreur lors de lajout de la boutique : ' . $e->getMessage()]);
    }
}

public function show()
{
    // Fetch all shops
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
        return view('Admin.Modifier_Boutique', compact('shop')); 
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
        $shops = Shop::all(); 
        return view('admin.Gerer_Boutique', compact('shops'));
    }

    // CRUD Produit
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

            $validated['shop_id'] = $category->shop_id; 

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
        return view('Admin.produits', compact('products'));
    }

    public function create()
    {
        $categories = Category::all(); 
        $shops = Shop::all();
        return view('Admin.Ajoute_Produit', compact('categories', 'shops')); 
    }

    public function read()
    {
        $products = Product::where('status', 'actif')->get();
        return view('Admin.Affiche_Produit', compact('products'));
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $shops = Shop::all();
        return view('Admin.Modifier_Produit', compact('product', 'categories', 'shops'));
    }

    public function updateProduct(Request $request, $id)
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
            $product = Product::findOrFail($id);
            $product->update($validated); 
            return redirect()->route('products.show', $product->shop_id)->with('success', 'Produit mis à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du produit : ' . $e->getMessage());
        }
    }

    public function destroyProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete(); 
            return redirect()->route('products.show', $product->shop_id)->with('success', 'Produit supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression du produit : ' . $e->getMessage());
        }
    }

    // CRUD Category
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
        $categories = Category::with('shop')->get(); 
        return view('Admin.Affiche_Categories', compact('categories'));
    }

    public function editCat($id)
    {
        $category = Category::findOrFail($id); 
        return view('Admin.Modifier_Categories', compact('category'));
    }

    public function updateCat(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:actif,inactif',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->withErrors(['error' => 'Catégorie introuvable']);
        }

        try {
            $category->update($request->only(['name', 'description', 'status']));

            return redirect()->route('categories.show')->with('success', 'Catégorie mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de la catégorie');
        }
    }

    public function destroyCategory($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete(); 
            return redirect()->route('categories.show')->with('success', 'Catégorie supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression de la catégorie');
        }
    }
}