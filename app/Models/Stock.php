<?php

namespace App\Models;
use App\Models\Article;
use App\Models\Depot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['idDepot', 'idArticle','quantiteDepot','prixMoyenAchat','prixAchat'];

    public function depot()
    {
        return $this->belongsTo(Depot::class, 'idDepot');
    }
    public function article()
    {
        return $this->belongsTo(Article::class,  'idArticle', 'id');
    }
    // Relation avec le modèle Tarif (ajoutée)
    
}
