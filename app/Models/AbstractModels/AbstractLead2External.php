<?php
/**
 * Model object generated by: Skipper (http://www.skipper18.com)
 * Do not modify this file manually.
 */

namespace App\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

/**
* Class AbstractLead2External
* @package App\Models\AbstractModels
*
* @property bigInteger $id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property bigInteger $lead_id
* @property \Carbon\Carbon $scheduled_time
* @property string $external_service
* @property boolean $processed
* @property json $response
* @property json $request
* @property \Carbon\Carbon $finished
* @property \App\Models\Lead $lead
*/ 
abstract class AbstractLead2External extends Model
{
    /**  
     * The model's default values for attributes.
     * 
     * @var array
     */
    protected $attributes = ['processed' => 0];
    
    /**  
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'lead_id' => 'integer',
        'scheduled_time' => 'datetime',
        'external_service' => 'string',
        'processed' => 'boolean',
        'response' => 'array',
        'request' => 'array',
        'finished' => 'datetime'
    ];
    
    /**  
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'lead_id',
        'scheduled_time',
        'external_service',
        'processed',
        'response',
        'request',
        'finished'
    ];
    
    public function lead()
    {
        return $this->belongsTo('\App\Models\Lead', 'lead_id', 'id');
    }
}
