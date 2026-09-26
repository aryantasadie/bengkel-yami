<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriJasa extends Model {
    use HasFactory;
    protected $table = 'kategori_jasa';
    protected $fillable = ['nama_kategori'];

    public function jasa() {
        return $this->hasMany(Jasa::class, 'kategori_jasa_id');
    }
}
