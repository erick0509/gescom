<?php return [
    'dompdf' => [
        'font_path' => base_path('public/fonts/'), // Chemin des polices
        'font' => 'Arial.ttf', // Nom de la police par défaut
        'options' => [
            'isHtml5ParserEnabled' => true, // Activer l'analyseur HTML5
            'isPhpEnabled' => true, // Activer l'exécution PHP dans les vues
            'isRemoteEnabled' => true, // Activer les ressources distantes
        ],
    ],
];
