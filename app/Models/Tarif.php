<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $fillable = [
        'idArticle',  // Clé étrangère vers Article
        'idDepot',    // Clé étrangère vers Depot
        'quantite_min', 
        'quantite_max', 
        'prix'
    ];

    // Relation avec l'article
    public function article()
    {
        return $this->belongsTo(Article::class, 'idArticle');
    }

    // Relation avec le dépôt
    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
    
}
