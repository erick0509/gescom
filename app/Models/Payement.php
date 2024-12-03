<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{
    use HasFactory;

    protected $fillable = [
        'primaryKey',
        'datePayement',
        'idFacture',
        'dejaUtilise',
        'avancement',
        'somme',
        'reste',
        'mode',
        'reference',
        'idClient' // Ajout de la colonne idClient pour la relation avec Client
    ];

    /**
     * Relation avec la facture de vente
     * Un paiement est lié à une facture
     */
    public static function generateCustomPrimaryKey($idDepot)
    {
        $prefixe = 'PA_' . Depot::find($idDepot)->prefixe; // Assurez-vous que le modèle Depot a un attribut 'prefixe'

        // Initialiser le compteur à 1
        $count = 1;

        // Construire la clé primaire personnalisée avec le préfixe et le numéro incrémenté
        $primaryKey = $prefixe . '_' . $count;

        // Vérifier si la clé primaire générée existe déjà
        while (Payement::where('primaryKey', $primaryKey)->exists()) {
            // Si la clé existe, incrémenter le compteur et reconstruire la clé primaire
            $count++;
            $primaryKey = $prefixe . '_' . $count;
        }

        return $primaryKey;
    }

    public function facture()
    {
        return $this->belongsTo(FactureVente::class, 'idFacture');
    }

    /**
     * Relation avec le client
     * Un paiement appartient à un client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'idClient');
    }
}
