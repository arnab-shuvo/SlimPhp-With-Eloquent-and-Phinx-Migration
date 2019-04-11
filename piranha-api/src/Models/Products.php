<?php
/**
 * Created by PhpStorm.
 * User: arnab
 * Date: 4/1/19
 * Time: 3:23 PM
 */

namespace App\Models;
use App\Models\User;


use Illuminate\Database\Eloquent\Model;


class Products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['name ', 'created_by'];
    public function created_by()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}