<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Histoire;
use App\Models\Chapitre;
use App\Models\Category;
use App\Models\CategorieHistoire;
use App\Models\Commentaire;
use App\Models\UserAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Liste des utilisateurs avec leurs profils
        $users = [
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => bcrypt('password'),
                'bio' => 'Amoureuse des histoires fantastiques et des mondes imaginaires.',
                'avatar' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739114257/images/pepkbl17fovwnfks8mpw.jpg',
               
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
                'bio' => 'Passionné d\'aventures et de voyages à travers les livres.',
                'avatar' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739118178/images/gcpxa3kmqaeywxf79fyy.jpg',
               
            ],
            [
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'password' => bcrypt('password'),
                'bio' => 'Fan de romances et de drames émouvants.',
                'avatar' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739061691/samples/people/boy-snow-hoodie.jpg',
               
            ],
        ];

        // Insertion des utilisateurs et récupération de leurs IDs
        $userIds = [];
        foreach ($users as $user) {
            $userId = DB::table('users')->insertGetId([
                'name' => $user['name'],
                'email' => $user['email'],
                'biographie' => $user['bio'] ,
                'profile' => $user['avatar'],
                'password' => $user['password'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $userIds[] = $userId;

            // Insertion du profil utilisateur
        /*    DB::table('profiles')->insert([
                'user_id' => $userId,
                'bio' => $user['profile']['bio'],
                'avatar' => $user['profile']['avatar'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);*/
        }

        // Liste des histoires avec leurs titres, résumés et couvertures
        $stories = [
            [
                'titre' => 'Le Royaume Oublié',
                'resume' => 'Une quête épique pour retrouver un royaume perdu depuis des siècles.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739118178/images/gcpxa3kmqaeywxf79fyy.jpg',
            ],
            [
                'titre' => 'Amour en Automne',
                'resume' => 'Une romance bouleversante qui se déroule dans un petit village pittoresque.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739102947/images/iink110epjitrabo0fsd.jpg',
            ],
            [
                'titre' => 'Les Ombres du Passé',
                'resume' => 'Un thriller psychologique où les secrets familiaux refont surface.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739102947/images/iink110epjitrabo0fsd.jpg',
            ],
            [
                'titre' => 'La Guerre des Étoiles',
                'resume' => 'Une épopée spatiale entre des factions rivales pour le contrôle de la galaxie.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739100906/images/vn91fncwzbt06jni4j0j.jpg',
            ],
            [
                'titre' => 'Le Jardin Secret',
                'resume' => 'Une histoire mystérieuse autour d\'un jardin caché et de ses pouvoirs magiques.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739102947/images/iink110epjitrabo0fsd.jpg',
            ],
            [
                'titre' => 'Le Chant des Sirènes',
                'resume' => 'Une aventure maritime où les sirènes ne sont pas ce qu\'elles semblent être.',
                'couverture' => 'https://res.cloudinary.com/dkowocn8y/image/upload/v1739102947/images/iink110epjitrabo0fsd.jpg',
            ],
        ];

        // Insertion des histoires et récupération de leurs IDs
        $storyIds = [];
        foreach ($stories as $index => $story) {
            $storyId = DB::table('histoires')->insertGetId([
                'titre' => $story['titre'],
                'resume' => $story['resume'],
                'couverture' => $story['couverture'],
                'statut' => 'Publier',
                'progression' => 'EnCours',
                'restriction_age' => rand(12, 18),
                'mot_cles' => 'aventure, mystère, romance',
                'user_id' => $userIds[$index % count($userIds)], // Associe les histoires aux utilisateurs
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $storyIds[] = $storyId;
        }

        // Liste des chapitres avec leurs titres et contenu
        $chapters = [
            [
                'titre' => 'Chapitre 1: Le Début',
                'content' => json_encode([
                    ["insert" => "Il était une fois...\n\n"],
                    ["insert" => "Dans un monde lointain, une quête commença.\n\n"],
                    ["insert" => ["image" => "https://res.cloudinary.com/dkowocn8y/image/upload/v1739115646/images/fctc6ftoceyq9u1j64fq.jpg"]],
                    ["insert" => "\n\n"],
                ]),
            ],
            [
                'titre' => 'Chapitre 2: La Rencontre',
                'content' => json_encode([
                    ["insert" => "Les héros se rencontrèrent dans une auberge.\n\n"],
                    ["insert" => "Leur destin était scellé.\n\n"],
                    ["insert" => ["image" => "https://res.cloudinary.com/dkowocn8y/image/upload/v1739126950/images/ok7mk62vwbmvcjejxbe9.jpg"]],
                    ["insert" => "\n\n"],
                ]),
            ],
            [
                'titre' => 'Chapitre 3: Le Combat',
                'content' => json_encode([
                    ["insert" => "Le combat fut long et épuisant.\n\n"],
                    ["insert" => "Mais la victoire était à portée de main.\n\n"],
                    ["insert" => ["image" => "https://res.cloudinary.com/dkowocn8y/image/upload/v1739115646/images/fctc6ftoceyq9u1j64fq.jpg"]],
                    ["insert" => "\n\n"],
                ]),
            ],
            [
                'titre' => 'Chapitre 4: La Conclusion',
                'content' => json_encode([
                    ["insert" => "Et ainsi, l\'histoire trouva sa fin.\n\n"],
                    ["insert" => "Mais une nouvelle aventure commença.\n\n"],
                    ["insert" => ["image" => "https://res.cloudinary.com/dkowocn8y/image/upload/v1739118295/images/jzjm9asrikvx39u8qdca.jpg"]],
                    ["insert" => "\n\n"],
                ]),
            ],
        ];

        // Insertion des chapitres
        foreach ($storyIds as $storyId) {
            foreach ($chapters as $chapter) {
                DB::table('chapitres')->insert([
                    'histoire_id' => $storyId,
                    'statut' => 'Publier',
                    'numero' => rand(1, 10),
                    'titre' => $chapter['titre'],
                    'content' => $chapter['content'],
                    'vues' => rand(0, 100),
                    'likes' => rand(0, 50),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Récupération des IDs des chapitres
        $chapterIds = DB::table('chapitres')->pluck('id');

        // Création des catégories
        $categories = [
            ['nom' => 'Aventure', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Romance', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Fantastique', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($categories);

        // Récupération des IDs des catégories
        $categoryIds = DB::table('categories')->pluck('id');

        // Association des catégories aux histoires
        foreach ($storyIds as $storyId) {
            DB::table('categorie_histoires')->insert([
                'categorie_id' => $categoryIds->random(),
                'histoire_id' => $storyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Création des commentaires
        foreach ($chapterIds as $chapterId) {
            if (rand(0, 1)) { // 50% de chance de créer un commentaire
                DB::table('commentaires')->insert([
                    'content' => 'Super chapitre !',
                    'chapitre_id' => $chapterId,
                    'user_id' => $userIds[array_rand($userIds)],
                    'commentaire_parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Création des actions utilisateur
        foreach ($chapterIds as $chapterId) {
            DB::table('user_actions')->insert([
                'user_id' => $userIds[array_rand($userIds)],
                'chapitre_id' => $chapterId,
                'action' => rand(0, 1) ? 'lecture' : 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
}
