<?php
/**
 * Armazena casas de cambios que aceitam trocar moedas
 */

namespace Atlassian\Models;

use Pedreiro\Models\Base;
use Telefonica\Models\Digital\Account;

class Token extends Base
{

    
    protected $organizationPerspective = false;

    public static $apresentationName = 'Tokens';

    protected $table = 'tokens';       

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'token',
        'obs',
        'scopes',
        'status'
    ];

     /**
      * The attributes that should be cast to native types.
      *
      * @var array
      */
    protected $casts = [
        'scopes' => 'json',
    ];

    protected $mappingProperties = array(

        'name' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
        'status' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
    );


    /**
     * Relation for the user that created this entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(
            Account::class,
            'account_id',
            'id'
        );
    }
}