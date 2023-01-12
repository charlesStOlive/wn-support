<?php namespace Waka\Support\Models;

use Model;
use Carbon\Carbon;

/**
 * ticketGroup Model
 */

class TicketGroup extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Waka\Utils\Classes\Traits\DbUtils;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_support_ticket_groups';


    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

    /**
     * @var array Fillable fields
     */
    //protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
    ];

    public $customMessages = [
    ];

    /**
     * @var array attributes send to datasource for creating document
     */
    public $attributesToDs = [
        'qty',
        'heures',
        'total',
    ];


    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [
    ];
    public $hasMany = [
        'tickets' => [
            'Waka\Support\Models\Ticket',
            'delete' => true
        ],
    ];
    public $hasOneThrough = [
    ];
    public $hasManyThrough = [
    ];
    public $belongsTo = [
    ];
    public $belongsToMany = [
    ];        
    public $morphTo = [
    ];
    public $morphOne = [
    ];
    public $morphMany = [
    ];
    public $attachOne = [
    ];
    public $attachMany = [
    ];

    //startKeep/

    /**
     *EVENTS
     **/

    /**
     * LISTS
     **/

    /**
     * GETTERS
     **/
    public function getTotalAttribute() {
        return $this->tickets->sum('temps') * 80;
    }
    public function getQtyAttribute() {
        return $this->tickets->count();
        
    }
    public function getQtyOpenedAttribute() {
        return $this->tickets()->whereNotIn('state', ['archived', 'abdn', 'sleep'])->count();
    }
    
    public function getHeuresAttribute() {
        return $this->tickets->sum('temps');
    }
    

    /**
     * SCOPES
     */
    public function scopeOpened($query) {
        $query->where('is_factured', false);
    }

    /**
     * SETTERS
     */
 
    /**
     * FILTER FIELDS
     */

    /**
     * OTHERS
     */

//endKeep/
}