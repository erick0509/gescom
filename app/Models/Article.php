<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'idFournisseur',
        'designation',
        'quantitePack',
        'unite'
    ];
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'idArticle', 'id');
    }
    public function factures()
    {
        return $this->belongsToMany(FactureAchat::class, 'article_facture_achats', 'idArticle', 'idFacture');
    }
    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'stocks', 'idArticle', 'idDepot');
    }
    //
    public function tarifs()
    {
        return $this->hasMany(Tarif::class,  'idArticle', 'id');
    }
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'idFournisseur');
    }
}
