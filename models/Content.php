<?php namespace DasRoteQuadrat\BetterContentEditor\Models;

use Model;
use BackendAuth;
use October\Rain\Database\Traits\Revisionable;

class Content extends Model
{
    use Revisionable;

    protected $revisionable = ['item', 'content'];
    protected $fillable = ['item', 'content'];
    public $table = 'dasrotequadrat_content_history';
    public $revisionableLimit = 3;

    public $morphMany = [
        'revision_history' => ['System\Models\Revision', 'name' => 'revisionable']
    ];

    public function getRevisionableUser()
    {
        return BackendAuth::getUser()->id;
    }

}
