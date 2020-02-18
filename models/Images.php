<?php namespace DasRoteQuadrat\BetterContentEditor\Models;

use Model;

class Images extends Model
{
    protected $fillable = ['item', 'url'];
    public $table = 'dasrotequadrat_imageuploader';
}
