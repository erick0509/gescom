<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use App\Models\FactureVente;

class ArticleFactureVente extends Model
{
    use HasFactory;
    protected $fillable = ['idFacture', 'idArticle', 'quantite','quantiteAffichee','remise', 'prixUnitaire','prixAchat'];//(MILA AMPIANA MONTANT)
    public function factureVente()
    {
        return $this->belongsTo(factureVente::class, 'idFacture');
    }
    public function article()
    {
        return $this->belongsTo(Article::class,  'idArticle', 'id');
    }
}
