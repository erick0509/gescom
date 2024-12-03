<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    // Nom de la table associée (si différent du pluriel du nom du modèle)
    protected $table = 'fournisseurs';

    // Les champs qui peuvent être remplis via un formulaire (mass assignable)
    protected $fillable = [
        'idDepot',
        'intitule',
        'contact',
        'adresse'
    ];

    /**
     * Relations
     */

    // Relation avec FactureAchat : Un fournisseur peut avoir plusieurs factures d'achat
    public function facturesAchat()
    {
        return $this->hasMany(FactureAchat::class, 'idFournisseur');
    }

    // Relation avec PaiementAchat : Un fournisseur peut avoir plusieurs paiements
    public function paiementsAchat()
    {
        return $this->hasMany(PayementAchat::class, 'idFournisseur');
    }
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
}
