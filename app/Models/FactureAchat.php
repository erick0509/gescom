<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureAchat extends Model
{
    use HasFactory;
    protected $fillable = [
        'primaryKey',
        'dateAchat',
        'montantTotal',
        'ReferenceFactureAchat',
        'idFournisseur',
        'idDepot',
        'statut',
        'sommePayee',
        'montantTotal'
    ];
    public static function generateCustomPrimaryKey($idDepot)
    {
        $prefixe = 'FA_' . Depot::find($idDepot)->prefixe; // Assurez-vous que le modèle Depot est correctement défini

        // Initialiser le compteur à 1
        $count = 1;

        // Construire la clé primaire personnalisée avec le préfixe et le numéro incrémenté
        $primaryKey = $prefixe . '_' . $count;

        // Vérifier si la clé primaire générée existe déjà
        while (FactureAchat::where('primaryKey', $primaryKey)->exists()) {
            // Si la clé existe, incrémenter le compteur et reconstruire la clé primaire
            $count++;
            $primaryKey = $prefixe . '_' . $count;
        }

        return $primaryKey;
    }
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_facture_achats', 'idFacture', 'idArticle');
    }
    public function articlesAchat()
    {
        return $this->hasMany(ArticleFactureAchat::class, 'idFacture');
    }
    public function paiements()
    {
        return $this->hasMany(PayementAchat::class, 'idFacture');
    }
    public function depots()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'idFournisseur');
    }
}
