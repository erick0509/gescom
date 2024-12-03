<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'idDepot',
        'type',
        'date_operation',
        'montant',
        'commentaire'
    ];
    /**
     * Relation avec le modèle Depot.
     * Une opération appartient à un dépôt.
     */
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
}
