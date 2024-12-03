<?php

namespace App\Models;
use App\Models\Article;
use App\Models\Transfert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTransfert extends Model
{
    use HasFactory;

    // Définir la table associée à ce modèle
    protected $table = 'article_transferts';

    // Les champs pouvant être assignés en masse
    protected $fillable = [
        'idTransfert',
        'idArticle',
        'quantite',
        'quantiteAffichee',
    ];

    // Définir les relations

    /**
     * Relation avec le modèle Transfert
     */
    public function transfert()
    {
        return $this->belongsTo(Transfert::class, 'idTransfert');
    }

    /**
     * Relation avec le modèle Article
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'idArticle');
    }
}
