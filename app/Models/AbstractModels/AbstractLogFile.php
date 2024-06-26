<?php
/**
 * Model object generated by: Skipper (http://www.skipper18.com)
 * Do not modify this file manually.
 */

namespace App\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

/**
* Class AbstractLogFile
* @package App\Models\AbstractModels
*
* @property bigInteger $id
* @property string $file_path
* @property boolean $processed
* @property boolean $is_processing
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property array $data
* @property integer $total_leads
* @property boolean $leads_exported
* @property \Illuminate\Database\Eloquent\Collection $leads
*/ 
abstract class AbstractLogFile extends Model
{
    /**  
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'log_files';
    
    /**  
     * The model's default values for attributes.
     * 
     * @var array
     */
    protected $attributes = [
        'processed' => 0,
        'is_processing' => 0,
        'total_leads' => 0,
        'leads_exported' => 0
    ];
    
    /**  
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'file_path' => 'string',
        'processed' => 'boolean',
        'is_processing' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data' => 'array',
        'total_leads' => 'integer',
        'leads_exported' => 'boolean'
    ];
    
    public function leads()
    {
        return $this->hasMany('\App\Models\Lead', 'log_file_id', 'id');
    }
}
