<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\AbonneController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ChapitreController;
use App\Http\Controllers\HistoireController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\AuthController;

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

//
Route::post('/upload-image', [ChapitreController::class, 'uploadImage']);  //permet d'insérer des images dans le storage/public

//
Route::middleware('auth:sanctum')->post('/commentaires', [CommentaireController::class, 'store']);
Route::middleware('auth:sanctum')->put('/commentaires/{id}', [CommentaireController::class, 'update']);
Route::middleware('auth:sanctum')->post('/delete/commentaires/{id}', [CommentaireController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/chapitres/{chapitreId}/commentaires', [CommentaireController::class, 'getCommentairesByChapitre']);


//
//Route::get('/categories', [CategorieController::class, 'getCategoriesIndex']); // Afficher toutes les catégories
Route::middleware('auth:sanctum')->get('/categories', [CategorieController::class, 'getCategoriesIndex']); // Afficher toutes les catégories
Route::middleware('auth:sanctum')->post('/categories', [CategorieController::class, 'storeCategoriesHistoire']); // sauvegarder les catégories d'une histoire
Route::middleware('auth:sanctum')->post('/categories/preferees', [CategorieController::class, 'storeCategoriesPreferee']); // sauvegarder les catégories d'une histoire
//
Route::middleware('auth:sanctum')->post('/chapitres', [ChapitreController::class, 'storeChapitre']); //enregistrer un chapitre
Route::middleware('auth:sanctum')->post('/update/chapitre', [ChapitreController::class, 'updateChapitre']); 
Route::middleware('auth:sanctum')->get('/chapitres/{id}', [ChapitreController::class, 'getChapitre']);
Route::middleware('auth:sanctum')->get('/lecture/chapitre/{id}', [ChapitreController::class, 'lectureChapitre']); //lecture
Route::middleware('auth:sanctum')->post('/delete/chapitre/{id}', [ChapitreController::class, 'destroy']);
//
Route::middleware('auth:sanctum')->get('/histoire/{id}', [HistoireController::class, 'edit']); //afficher les informations d'une histoire pour modification
Route::middleware('auth:sanctum')->post('/histoire', [HistoireController::class, 'storeStory']); //enregistrer une histoire
Route::middleware('auth:sanctum')->put('/histoire/{id}', [HistoireController::class, 'updateStory']);
Route::middleware('auth:sanctum')->get('/meshistoires', [HistoireController::class, 'mesHistoires']);
Route::middleware('auth:sanctum')->post('/delete/histoire/{id}', [HistoireController::class, 'deleteStory']);
Route::middleware('auth:sanctum')->get('/lecture/other-chapitres/{id}', [HistoireController::class, 'getOtherChapterWithStory']); //récupère l'histoire pour la page de lecture et les différents chapitres
Route::middleware('auth:sanctum')->get('/histoire/latest', [HistoireController::class, 'getLatestStory']); //afficher la dernière histoire crée, elle apparait sur la page home


//
Route::middleware('auth:sanctum')->get('/histoire/categories/{id}', [CategorieController::class, 'getCategoriesForStory']); // récupère toutes les catégories et targue les catégories de l'histoire qui à l'id
Route::middleware('auth:sanctum')->put('/categories/{id}', [CategorieController::class, 'updateCategory']); // mets à jour les catégories d'une histoire
//routes pour authentification
Route::post('/register', [AuthController::class, 'register']);
Route::get('/test', [TestController::class, 'firstRoot']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//fin : route Gaël Test

//Début : Nom 
/* mes routes en clair, ne pas utiliser resources pour plus de visibilité... */
//Fin: Nom