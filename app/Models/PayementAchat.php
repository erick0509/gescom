<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayementAchat extends Model
{
    use HasFactory;
    protected $table = 'paiements_achat';

    protected $fillable = [
        'datePayement',
        'idFacture',
        'idFournisseur',
        'somme',
        'reste',
        'mode',
        'reference',
        
    ];

    public function facture()
    {
        return $this->belongsTo(FactureAchat::class, 'idFacture');
    }
}
