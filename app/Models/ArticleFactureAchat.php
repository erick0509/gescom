<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use App\Models\factureAchat;

class ArticleFactureAchat extends Model
{
    use HasFactory;
    protected $fillable = ['idFacture', 'idArticle', 'quantite', 'prixUnitaire'];

    public function factureAchat()
    {
        return $this->belongsTo(factureAchat::class, 'idFacture');
    }
    public function article()
    {
        return $this->belongsTo(Article::class,  'idArticle', 'id');
    }
}
