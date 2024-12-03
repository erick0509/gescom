<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureVente extends Model
{
    use HasFactory;

    protected $fillable = [
        'primaryKey',
        'idDepot',
        'idClient', // Clé étrangère pour la relation avec Client
        'statut',
        'sommePayee',
        'remise',
        'montantTotal',
        'dateEcheance',
        'dateVente',
    ];

    /**
     * Générer une clé primaire personnalisée basée sur le dépôt
     */
    public static function generateCustomPrimaryKey($idDepot)
    {
        $prefixe = 'FV_' . Depot::find($idDepot)->prefixe; // Assurez-vous que le modèle Depot a un attribut 'prefixe'

        // Initialiser le compteur à 1
        $count = 1;

        // Construire la clé primaire personnalisée avec le préfixe et le numéro incrémenté
        $primaryKey = $prefixe . '_' . $count;

        // Vérifier si la clé primaire générée existe déjà
        while (FactureVente::where('primaryKey', $primaryKey)->exists()) {
            // Si la clé existe, incrémenter le compteur et reconstruire la clé primaire
            $count++;
            $primaryKey = $prefixe . '_' . $count;
        }

        return $primaryKey;
    }

    /**
     * Relation avec les articles (Many-to-Many)
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_facture_ventes', 'idFacture', 'idArticle');
    }

    /**
     * Relation avec les articles de vente (One-to-Many)
     */
    public function articlesVente()
    {
        return $this->hasMany(ArticleFactureVente::class, 'idFacture');
    }

    /**
     * Relation avec les paiements (One-to-Many)
     */
    public function paiements()
    {
        return $this->hasMany(Payement::class, 'idFacture');
    }

    /**
     * Relation avec le dépôt (Many-to-One)
     */
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }

    /**
     * Relation avec le client (Many-to-One)
     * Une facture appartient à un client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'idClient');
    }
}
