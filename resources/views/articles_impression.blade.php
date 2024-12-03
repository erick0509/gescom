<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression des Articles</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .texte-long {
            text-align: left;
            padding-left: 10px;
        }

        .tarif-col {
            width: 80px;
        }

        /* Styles spécifiques à l'impression */
        @media print {
            @page {
                margin: 20mm;
            }
            
            /* Répéter l'en-tête sur chaque page */
            thead {
                display: table-header-group;
            }

            /* Garder le pied de page en bas */
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>
    <div id="sectionAImprimer">
        <!-- Tableau principal avec en-tête répétée sur chaque page -->
        <table>
            <thead>
                                <!-- Ligne d'information (dépôt et date) qui sera répétée -->
                <tr>
                    <td colspan="11" style="text-align: left; border: none;">
                        <b>{{ session('depotValue') }}</b> - Tirage le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </td>
                </tr>
                <!-- En-têtes des colonnes -->
                <tr>
                    <th rowspan="2">Désignation</th>
                    <th rowspan="2">Stock</th>
                    <th rowspan="2">Prix d'achat</th>
                    <th colspan="3">Tarif 1</th>
                    <th colspan="3">Tarif 2</th>
                    <th colspan="3">Tarif 3</th>
                </tr>
                <tr class="text-secondary">
                    <th class="tarif-col">Marge/ Prix d'achat</th>
                    <th class="prix-col">Prix</th>
                    <th class="tarif-col">Qt</th>
                    <th class="tarif-col">Marge/ Prix d'achat</th>
                    <th class="prix-col">Prix</th>
                    <th class="tarif-col">Qt</th>
                    <th class="tarif-col">Marge/ Prix d'achat</th>
                    <th class="prix-col">Prix</th>
                    <th class="tarif-col">Qt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                <tr>
                    <th class="texte-long">{{ $article->designation }}</th>
                    <td><b>{{ $article->stocks->first()->quantiteDepot ?? 'Non disponible' }}</b></td>
                    <td>@if($codeValide)
                          <b>{{ number_format($article->stocks->first()->prixMoyenAchat, 1, ',', ' ') }}</b>
                      @endif</td>

                    @foreach(range(0, 2) as $index)
                        @if(isset($article->tarifs[$index]))
                            @php $tarif = $article->tarifs[$index]; @endphp
                            <td class="tarif-col">@if($codeValide)
                                    <b>{{ number_format(max($tarif->prix - ($article->stocks->first()->prixMoyenAchat ?? 0),0), 1, ',', ' ') }}</b>
                                @endif
                                </td>
                            <td class="prix-col">{{ number_format($tarif->prix, 1, ',', ' ') }}</td>
                            <td class="tarif-col">[{{ $tarif->quantite_min }} - {{ $tarif->quantite_max === 9999999 ? 'infini' : $tarif->quantite_max }}]</td>
                        @else
                            <td class="tarif-col"></td>
                            <td class="prix-col"></td>
                            <td class="tarif-col"></td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        // Imprimer automatiquement
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html>
