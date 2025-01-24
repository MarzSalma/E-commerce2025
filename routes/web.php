<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;

Route::get('/', function () {
    return view('welcome');
});


//CRUD Boutique
//Create
Route::get('/Ajouter_Boutique', function () {
    return view('admin.Ajouter_Boutique');
});
Route::post('/Ajouter_Boutique', [ShopController::class, 'store']);
//Read 
Route::get('/Affiche-Boutique', [ShopController::class, 'show'])->name('shop.show');
//Delete
Route::delete('/shops/{id}', [ShopController::class, 'destroy'])->name('shops.destroy');
//Update
Route::get('/Modifier_Boutique/{id}', [ShopController::class, 'edit'])->name('shops.edit');
Route::put('/Modifier_Boutique/{id}', [ShopController::class, 'update'])->name('shops.update');
//Affiche sous forme de box
Route::get('/Gerer_Boutique', [ShopController::class, 'index'])->name('gerer.boutique');

//affiche produits
Route::get('/boutique/{id}', [ShopController::class, 'showProducts'])->name('boutique.products');


//CRUD Produit
//Create
Route::post('/Ajouter_Produit', [ShopController::class, 'storeProduct'])->name('products.store'); 
Route::get('/Ajouter_Produit', [ShopController::class, 'create'])->name('products.create');


//CRUD Cat
//Create
Route::get('/Ajouter_Categorie', [ShopController::class, 'createCategory'])->name('categories.create');
Route::post('/Ajouter_Categorie', [ShopController::class, 'storeCategory'])->name('categories.store');
//Read
Route::get('/Affiche_Categories', [ShopController::class, 'showCategories'])->name('categories.show');
//Update
Route::get('/Modifier_Categories/{id}', [ShopController::class, 'editCat'])->name('categories.edit');
Route::put('/Modifier_Categories/{id}', [ShopController::class, 'updateCat'])->name('categories.update');
//Delete
Route::delete('/categories/{id}', [ShopController::class, 'destroyCategory'])->name('categories.destroy');



Route::get('/dashboard', function () {
    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
