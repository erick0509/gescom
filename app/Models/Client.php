<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'intituleClient', 'contactClient', 'adresseClient', 'solde', 'idDepot'
    ];

    /**
     * Relation avec les payements
     * Un client peut avoir plusieurs payements
     */
    public function payements()
    {
        return $this->hasMany(Payement::class, 'idClient');
    }

    /**
     * Relation avec les factures de vente
     * Un client peut avoir plusieurs factures
     */
    public function factures()
    {
        return $this->hasMany(FactureVente::class, 'idClient');
    }

    /**
     * Relation avec le dépôt
     * Un client est lié à un dépôt
     */
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
}
