<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;

    // Définir la table associée
    protected $table = 'caisses';

    // Champs qui peuvent être remplis
    protected $fillable = ['idDepot', 'montant'];

    // Relation avec le modèle Depot
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
}
