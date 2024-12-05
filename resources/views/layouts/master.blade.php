<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <style>
        /* Styles pour cacher le reste de la page lors de l'impression */
        @media print {
            body * {
                visibility: hidden;
                /* Masquer tout le contenu */

            }

            #print-section-etat,
            #print-section-etat *,
            #print-section-etat-payement,
            #print-section-etat-payement *,
            #print-section-etat-client,
            #print-section-etat-client * {
                visibility: visible;
            }

            #print-section-etat-client,
            #print-section-etat-payement {
                width: 100%;
                margin: 0 auto;
                padding: 20px;
            }

            #print-section-etat {
                position: absolute;
                left: 0;
                top: 0;
                margin: 5mm;
            }

            .print-section,
            .print-section * {
                visibility: visible;
                /* Rendre visible uniquement le contenu de la section à imprimer */
            }


            /* Appliquer des styles spécifiques à l'impression */
            @page {
                /* Taille du papier, largeur de 80mm */
                margin: 5mm;


            }

            /* Aucune marge */


            /* Style de la section à imprimer */

            .print-section {
                font-size: 12px;
                width: 80mm;
                margin: 0 auto;
                margin-top: 5mm;
                background-color: white;
                padding: 0;
                box-sizing: border-box;
                /* Inclure les bordures et les marges dans la largeur et la hauteur */
                page-break-after: avoid;
                margin-bottom: 0;
            }

            .entete {
                display: block;
            }

            .print-section thead {
                display: table-header-group;
                /* Forcer l'affichage du thead sur chaque nouvelle page */
            }

            .print-section tbody {
                display: table-row-group;
            }

            .print-section tfoot {
                display: table-footer-group;
            }


            /* Style des éléments à l'intérieur de la section */
            .print-section h1,
            .print-section h2,
            .print-section p,
            .print-section table,
            .print-section th,
            .print-section td {
                margin: 0;
                /* Aucune marge */
                padding: 0;
                /* Aucun espacement interne */
            }

            /* Style spécifique pour le tableau */
            .print-section table {
                border-collapse: collapse;
                /* Fusionner les bordures */
                width: 100%;
                /* Largeur maximale du tableau */

            }

            .print-section th,
            .print-section td {
                border: 1px solid black;
                /* Bordure de 1px solide */
                padding: 5px;
                /* Espacement interne */
                page-break-inside: avoid;

            }

            .print-section tr {
                page-break-inside: avoid;
                /* Éviter les sauts de page à l'intérieur des lignes */

            }

            .print-section.grand-format {
                width: 148mm;
            }

            .print-section.ticket {
                width: 80mm;
            }

        }
    </style>

    <style>
        .hidden {
            display: none;
        }

        .visible {
            display: block;
        }
    </style>
    <title>GesCom Legacy</title>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
                // La page est chargée à partir de l'historique du navigateur
                window.location.reload();
            }
        });
    </script>
    <link rel="stylesheet" href="{{ asset('css/animation.css') }}">
</head>

<body>
    @include('partials.animation') 
    @yield("contenu")
    <script src="{{asset('js/bootstrap.bundle.min.js')}}">

    </script>
    <script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ asset('js/animation.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script>


    </script>
</body>

</html>