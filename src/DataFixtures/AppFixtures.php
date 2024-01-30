<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use App\Entity\Categorie;
use App\Entity\Promotion;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\QuizPromotion;
use App\Entity\Reponse;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private string $ADMIN_EMAIL = 'admin@admin.fr';
    private string $FIRSTNAME_ADMIN = 'Admin';
    private string $LASTNAME_ADMIN = 'Admin';

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Catégories
        $categories = ['Développement Web', 'FiveM', 'Musique', 'Animal', 'Géographie'];
        foreach ($categories as $key => $cat) {
            $categorie = new Categorie();
            $categorie->setLibelle($cat);
            $manager->persist($categorie);

            // Enregistrer la référence
            $this->addReference('categorie_' . $key, $categorie);
        }

        // Promotions
        $promotions = ['IW 2024', 'AL 2024', 'MM 2024'];
        foreach ($promotions as $key => $promo) {
            $promotion = new Promotion();
            $promotion->setLibelle($promo);
            $manager->persist($promotion);

            // Enregistrer la référence
            $this->addReference('promotion_' . $key, $promotion);
        }

        // Quizz
        $quizzes = [
            [
                'categorie' => 'categorie_0', 
                'libelle' => 'Les basiques d\'HTML & CSS', 
                'duration' => 10,
                'promotion' => 'promotion_0',

                'questions' => [
                    [
                        'libelle' => 'Quel est l\'élément HTML pour le plus grand titre ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => '<h1>', 'bonne_reponse' => 1],
                            ['libelle' => '<h6>', 'bonne_reponse' => 0],
                            ['libelle' => '<head>', 'bonne_reponse' => 0],
                            ['libelle' => '<heading>', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est l\'élément HTML pour le plus petit titre ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => '<h1>', 'bonne_reponse' => 0],
                            ['libelle' => '<h6>', 'bonne_reponse' => 1],
                            ['libelle' => '<head>', 'bonne_reponse' => 0],
                            ['libelle' => '<heading>', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est l\'élément HTML pour insérer une ligne horizontale ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => '<break>', 'bonne_reponse' => 0],
                            ['libelle' => '<hr>', 'bonne_reponse' => 1],
                            ['libelle' => '<line>', 'bonne_reponse' => 0],
                            ['libelle' => '<tr>', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est l\'élément HTML pour insérer une ligne de tableau ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => '<break>', 'bonne_reponse' => 0],
                            ['libelle' => '<hr>', 'bonne_reponse' => 0],
                            ['libelle' => '<line>', 'bonne_reponse' => 0],
                            ['libelle' => '<tr>', 'bonne_reponse' => 1],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est l\'élément HTML pour insérer une cellule de tableau ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => '<td>', 'bonne_reponse' => 1],
                            ['libelle' => '<th>', 'bonne_reponse' => 0],
                            ['libelle' => '<tr>', 'bonne_reponse' => 0],
                            ['libelle' => '<table>', 'bonne_reponse' => 0],
                        ]
                    ],
                ]
            ],

            [
                'categorie' => 'categorie_1', // FiveM
                'libelle' => 'Introduction à FiveM',
                'duration' => 25,
                'promotion' => 'promotion_1',
                'questions' => [
                    [
                        'libelle' => 'Quel est le nom du serveur de test de FiveM ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'FiveM Test Server', 'bonne_reponse' => 0],
                            ['libelle' => 'FiveM Test', 'bonne_reponse' => 0],
                            ['libelle' => 'FiveM', 'bonne_reponse' => 0],
                            ['libelle' => 'FiveM Test Server', 'bonne_reponse' => 1],
                        ]
                    ],
                    [
                        'libelle' => 'Quelle est la native qui permet de récupérer le nom du joueur ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'GetPlayerName(source)', 'bonne_reponse' => 1],
                            ['libelle' => 'GetPlayerName(-1)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetName(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetName(-1)', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quelle est la native qui permet de récupérer l\'identifiant du joueur ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'GetPlayerIdentifier(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetPlayerIdentifier(-1)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetPlayerIdentifiers(source)', 'bonne_reponse' => 1],
                            ['libelle' => 'GetPlayerIdentifiers(-1)', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quelle est la native qui permet de récupérer l\'identifiant du ped du joueur (client) ?',
                        'qcm' => true,
                        'reponses' => [
                            ['libelle' => 'GetPlayerPed(-1)', 'bonne_reponse' => 1],
                            ['libelle' => 'GetPlayerPed(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'PlayerPedId()', 'bonne_reponse' => 1],
                            ['libelle' => 'GetPlayerPedId(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetPlayerPedId(-1)', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'FiveM utilise la librairie JQuery pour les UI ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'Vrai', 'bonne_reponse' => 1],
                            ['libelle' => 'Faux', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quelle est la native qui permet de récupérer la position du joueur ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'GetPlayerPosition(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetPlayerPosition(-1)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetPlayerCoords(source)', 'bonne_reponse' => 0],
                            ['libelle' => 'GetEntityCoords(playerPedId)', 'bonne_reponse' => 1],
                        ]
                    ],
                ]
            ],
            [
                'categorie' => 'categorie_2', // Musique
                'libelle' => 'Histoire de la Musique',
                'duration' => 20,
                'promotion' => 'promotion_0',
                'questions' => [
                    [
                        'libelle' => 'Quel est le nom du compositeur de la 5ème symphonie ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'Mozart', 'bonne_reponse' => 0],
                            ['libelle' => 'Beethoven', 'bonne_reponse' => 1],
                            ['libelle' => 'Bach', 'bonne_reponse' => 0],
                            ['libelle' => 'Chopin', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est le nom du compositeur de la 9ème symphonie ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'Mozart', 'bonne_reponse' => 0],
                            ['libelle' => 'Beethoven', 'bonne_reponse' => 1],
                            ['libelle' => 'Bach', 'bonne_reponse' => 0],
                            ['libelle' => 'Chopin', 'bonne_reponse' => 0],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est le nom du compositeur de la 3ème symphonie ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'Mozart', 'bonne_reponse' => 0],
                            ['libelle' => 'Beethoven', 'bonne_reponse' => 0],
                            ['libelle' => 'Bach', 'bonne_reponse' => 0],
                            ['libelle' => 'Chopin', 'bonne_reponse' => 1],
                        ]
                    ],
                    [
                        'libelle' => 'Quel est le nom du compositeur de la 1ère symphonie ?',
                        'qcm' => false,
                        'reponses' => [
                            ['libelle' => 'Mozart', 'bonne_reponse' => 0],
                            ['libelle' => 'Beethoven', 'bonne_reponse' => 0],
                            ['libelle' => 'Bach', 'bonne_reponse' => 1],
                            ['libelle' => 'Chopin', 'bonne_reponse' => 0],
                        ]
                    ],
                ]
            ],

            [
                'categorie' => 'categorie_3',
                'libelle' => 'Quiz sur les Animaux',
                'duration' => 15,
                'promotion' => 'promotion_2',
                'questions' => [
                        [
                            'libelle' => 'Quel est le plus grand mammifère terrestre ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Éléphant africain', 'bonne_reponse' => 1],
                                ['libelle' => 'Rhinocéros', 'bonne_reponse' => 0],
                                ['libelle' => 'Girafe', 'bonne_reponse' => 0],
                                ['libelle' => 'Hippopotame', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Combien de pattes a une araignée en général ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => '6', 'bonne_reponse' => 0],
                                ['libelle' => '8', 'bonne_reponse' => 1],
                                ['libelle' => '10', 'bonne_reponse' => 0],
                                ['libelle' => '12', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Quel est l\'oiseau national des États-Unis ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Aigle royal', 'bonne_reponse' => 1],
                                ['libelle' => 'Corbeau', 'bonne_reponse' => 0],
                                ['libelle' => 'Moineau', 'bonne_reponse' => 0],
                                ['libelle' => 'Pigeon', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Combien de doigts a un chat sur une patte avant ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => '4', 'bonne_reponse' => 1],
                                ['libelle' => '5', 'bonne_reponse' => 0],
                                ['libelle' => '6', 'bonne_reponse' => 0],
                                ['libelle' => '3', 'bonne_reponse' => 0],
                            ]
                        ],
                    ]
                ],

                [
                    'categorie' => 'categorie_4',
                    'libelle' => 'Quiz sur la Géographie',
                    'duration' => 30,
                    'promotion' => 'promotion_1',
                    'questions' => [
                        [
                            'libelle' => 'Quel est le plus grand continent du monde ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Europe', 'bonne_reponse' => 0],
                                ['libelle' => 'Amérique du Nord', 'bonne_reponse' => 0],
                                ['libelle' => 'Asie', 'bonne_reponse' => 1],
                                ['libelle' => 'Afrique', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Quelle est la plus longue rivière du monde ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Mississippi', 'bonne_reponse' => 0],
                                ['libelle' => 'Nil', 'bonne_reponse' => 1],
                                ['libelle' => 'Amazone', 'bonne_reponse' => 0],
                                ['libelle' => 'Yangtsé', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Quelle est la plus haute montagne du monde ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Mont Everest', 'bonne_reponse' => 1],
                                ['libelle' => 'Mont McKinley', 'bonne_reponse' => 0],
                                ['libelle' => 'Mont Kilimandjaro', 'bonne_reponse' => 0],
                                ['libelle' => 'Mont Blanc', 'bonne_reponse' => 0],
                            ]
                        ],
                        [
                            'libelle' => 'Quel pays est le plus vaste en superficie ?',
                            'qcm' => false,
                            'reponses' => [
                                ['libelle' => 'Russie', 'bonne_reponse' => 1],
                                ['libelle' => 'Canada', 'bonne_reponse' => 0],
                                ['libelle' => 'Chine', 'bonne_reponse' => 0],
                                ['libelle' => 'États-Unis', 'bonne_reponse' => 0],
                            ]
                        ],
                    ]
                ],
                
            
        ];


        foreach ($quizzes as $quizData) {
            $quiz = new Quiz();
            $quiz->setCategorie($this->getReference($quizData['categorie']));
            $quiz->setLibelle($quizData['libelle']);
            $quiz->setDuration($quizData['duration']);
            $manager->persist($quiz);

            // Questions
            foreach ($quizData['questions'] as $questionData) {
                $question = new Question();
                $question->setQuiz($quiz);
                $question->setLibelle($questionData['libelle']);
                $question->setQcm($questionData['qcm']);
                $manager->persist($question);

                // Réponses
                foreach ($questionData['reponses'] as $reponseData) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setLibelle($reponseData['libelle']);
                    $reponse->setBonneReponse($reponseData['bonne_reponse']);
                    $manager->persist($reponse);
                }
            }

            // Promotion
            $quizPromotion = new QuizPromotion();
            $quizPromotion->setQuiz($quiz);
            $quizPromotion->setPromotion($this->getReference($quizData['promotion']));
            $manager->persist($quizPromotion);
        }

        // Admin
        $user = new User();
        $user->setEmail($this->ADMIN_EMAIL);
        $user->setPrenom($this->FIRSTNAME_ADMIN);
        $user->setNom($this->LASTNAME_ADMIN);
        $user->setRoles(['ROLE_USER', 'ROLE_FORMATEUR', 'ROLE_ADMIN']);

        $randomPassword = bin2hex(random_bytes(5));
        $password = $this->hasher->hashPassword($user, $randomPassword);
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        echo "Admin Account : \n";
        echo "Email : " . $this->ADMIN_EMAIL . "\n";
        echo "Password : " . $randomPassword . "\n";
    }
}
