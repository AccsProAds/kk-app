<?php
/**
 * Model object generated by: Skipper (http://www.skipper18.com)
 * Do not modify this file manually.
 */

namespace App\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

/**
* Class AbstractJobBatch
* @package App\Models\AbstractModels
*
* @property string $id
* @property string $name
* @property integer $total_jobs
* @property integer $pending_jobs
* @property integer $failed_jobs
* @property longText $failed_job_ids
* @property mediumText $options
* @property integer $cancelled_at
* @property integer $created_at
* @property integer $finished_at
*/ 
abstract class AbstractJobBatch extends Model
{
    /**  
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'job_batches';
    
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
        'id' => 'string',
        'name' => 'string',
        'total_jobs' => 'integer',
        'pending_jobs' => 'integer',
        'failed_jobs' => 'integer',
        'failed_job_ids' => 'string',
        'options' => 'string',
        'cancelled_at' => 'integer',
        'created_at' => 'integer',
        'finished_at' => 'integer'
    ];
}
