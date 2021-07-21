<?php namespace Waka\Support\Models;

use Model;
use Carbon\Carbon;

/**
 * ticket Model
 */

class Ticket extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\Sortable;
    use \Waka\Utils\Classes\Traits\DataSourceHelpers;
    use \Waka\Utils\Classes\Traits\WakaWorkflowTrait;
    use \Waka\Utils\Classes\Traits\DbUtils;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_support_tickets';

    public $implement = [
        'October.Rain.Database.Behaviors.Purgeable',
    ];
    public $purgeable = [
        'first_message',
        'next_message',
    ];

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
        'ticket_type' => 'required',
        'user' => 'required',
    ];

    public $customMessages = [
    ];

    /**
     * @var array attributes send to datasource for creating document
     */
    public $attributesToDs = [
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
        'ticket_messages' => [
            'Waka\Support\Models\TicketMessage',
            'delete' => true
        ],
    ];
    public $hasOneThrough = [
    ];
    public $hasManyThrough = [
    ];
    public $belongsTo = [
       'ticket_type' => ['Waka\Support\Models\TicketType'],
       'user' => ['Backend\Models\User'],
    ];
    public $belongsToMany = [
    ];        
    public $morphTo = [];
    public $morphOne = [
    ];
    public $morphMany = [
    ];
    public $attachOne = [
    ];
    public $attachMany = [
        'attachments' => [
            'System\Models\File',
            'public' => false,
            'delete' => true
        ],
    ];

    //startKeep/

    /**
     *EVENTS
     **/
    public function afterCreate() 
    {
        $content = $this->getOriginalPurgeValue('first_message');
        $this->ticket_messages()->create([
            'content' =>$content,
        ]);
    }

    public function beforeSave() 
    {
        if($this->id) {
            $content = $this->getOriginalPurgeValue('next_message');
            if(!$content) {
                return;
            }
            TicketMessage::create([
                'content' =>$content,
                'ticket_id' => $this->id,
            ]);
        } 
       
        

    }

    public function beforeValidate() 
    {
        if(!$this->user) {
            $this->user = \BackendAuth::getUser();
        }

    }


    /**
     * LISTS
     **/
    public function listTicketTypes()
    {
        $user = \BackendAuth::getUser();
        if($user->hasAccess('waka.support.admin.super')) {
            return TicketType::lists('name', 'id');
        } else {
            return TicketType::where('is_for_super_user', null)->lists('name', 'id');
        }
        
    }

    /**
     * GETTERS
     **/

    /**
     * SCOPES
     */
    public function scopeClosed($query)
    {
        $query->whereIn('state', ['abdn', 'archived']);
    }

    /**
     * @param $query
     */
    public function scopeOpened($query)
    {
        $query->whereNotIn('state', ['abdn', 'archived']);
    }

    /**
     * SETTERS
     */
 
    /**
     * FILTER FIELDS
     */
    public function filterFields($fields, $context = null)
    {
        if (!isset($fields->name)) {
            return;
        }
    }


    /**
     * OTHERS
     */
    


    /**
     * @return mixed
     */
    public function getOpenedCount()
    {
        return $this->opened()->count();
    }

    /**
     * @return mixed
     */
    public function getClosedCount()
    {
        return $this->closed()->count();
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return Backend::url('waka/support/tickets/update/' . $this->id);
    }

    /**
     * Delete ticket attachments
     */
    public function deleteAttachments()
    {
        foreach ($this->attachments as $file) {
            $file->delete();
        }
    }

    /**
     * @return void
     */
    public function setDefaults()
    {
        $this->user = $this->model->user ?: BackendAuth::getUser()->id;
    }

    

//endKeep/
}