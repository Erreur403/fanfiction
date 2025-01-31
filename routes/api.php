<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\AbonneController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ChapitreController;
use App\Http\Controllers\HistoireController;
use App\Http\Controllers\CategorieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route: Gaël Test
Route::get('/categories', [TestController::class, 'index']); // Afficher toutes les catégories
Route::post('/categories', [TestController::class, 'store']); // Ajouter une catégorie
Route::post('/upload-image', [ChapitreController::class, 'uploadImage']);
Route::post('/chapitres', [ChapitreController::class, 'storeChapitre']);
Route::get('/chapitres/{id}', [ChapitreController::class, 'getChapitre']);
Route::get('/histoire', [HistoireController::class, 'edit']);
Route::put('/histoire/{id}', [HistoireController::class, 'updateStory']);
Route::get('/histoire/categories/{id}', [CategorieController::class, 'getCategoriesForStory']);
Route::put('/categories/{id}', [CategorieController::class, 'updateCategory']);
//fin : route Gaël Test

//Début : Nom 
/* mes routes en clair, ne pas utiliser resources pour plus de visibilité... */
//Fin: Nom