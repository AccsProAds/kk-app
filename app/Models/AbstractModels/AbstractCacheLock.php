<?php
/**
 * Model object generated by: Skipper (http://www.skipper18.com)
 * Do not modify this file manually.
 */

namespace App\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

/**
* Class AbstractCacheLock
* @package App\Models\AbstractModels
*
* @property string $key
* @property string $owner
* @property integer $expiration
*/ 
abstract class AbstractCacheLock extends Model
{
    /**  
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cache_locks';
    
    /**  
     * Primary key name.
     * 
     * @var string
     */
    public $primaryKey = 'key';
    
    /**  
     * Primary key type.
     * 
     * @var string
     */
    protected $keyType = 'string';
    
    /**  
     * Primary key is non-autoincrementing.
     * 
     * @var bool
     */
    public $incrementing = false;
    
    /**  
     * Do not automatically manage timestamps by Eloquent
     * 
     * @var bool
     */
    public $timestamps = false;
    
    /**  
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'key' => 'string',
        'owner' => 'string',
        'expiration' => 'integer'
    ];
}
