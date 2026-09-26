<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PengaturanNota extends Model {
    protected $table = 'pengaturan_nota';
    protected $fillable = ['key', 'value'];
    
    public static function get($key, $default = '') {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value) {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
