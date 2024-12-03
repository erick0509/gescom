<?php

namespace App\Models;
use App\Models\Depot;
use App\Models\ArticleTransfert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $table = 'transferts'; // Nom de la table associée

    protected $fillable = [
        'primaryKey',
        'idDepotSource',
        'idDepotDestination',
        'dateTransfert',
        'statut',
        'commentaire',
    ];
    public static function generateCustomPrimaryKey($idDepot)
    {
        $prefixe = 'TR_' . Depot::find($idDepot)->prefixe; // Assurez-vous que le modèle Depot est correctement défini

        // Initialiser le compteur à 1
        $count = 1;

        // Construire la clé primaire personnalisée avec le préfixe et le numéro incrémenté
        $primaryKey = $prefixe . '_' . $count;

        // Vérifier si la clé primaire générée existe déjà
        while (Transfert::where('primaryKey', $primaryKey)->exists()) {
            // Si la clé existe, incrémenter le compteur et reconstruire la clé primaire
            $count++;
            $primaryKey = $prefixe . '_' . $count;
        }

        return $primaryKey;
    }
    // Relation avec le modèle Depot (source)
    public function depotSource()
    {
        return $this->belongsTo(Depot::class, 'idDepotSource');
    }

    // Relation avec le modèle Depot (destination)
    public function depotDestination()
    {
        return $this->belongsTo(Depot::class, 'idDepotDestination');
    }

    // Relation avec le modèle ArticleTransfert
    public function articlesTransferts()
    {
        return $this->hasMany(ArticleTransfert::class, 'idTransfert');
    }
}
