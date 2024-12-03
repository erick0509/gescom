<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'prefixe',
        'type',
        'principal',
        'adresse',
        'code_acces',
        // autres attributs
    ];

    // Relation avec la table Stock (association Depot-Article)
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'idDepot', 'id');
    }

    // Relation avec la table Tarif (association Depot-Article)
    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'idDepot', 'id');
    }

    // Relation avec Article à travers Stock
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'stocks', 'idDepot', 'idArticle');
    }
    public function caisse()
    {
        return $this->hasOne(Caisse::class, 'idDepot');
    }
    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class, 'idDepot');
    }
}
