<?php namespace Waka\Support\Models;

use Model;
use Carbon\Carbon;
use Backend\Models\User;

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
        'awake_at',
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
       'ticket_group' => ['Waka\Support\Models\TicketGroup'],
       'user' => ['Backend\Models\User'],
       'next' => ['Backend\Models\User'],
       'support_user' => ['Backend\Models\User'],
       'support_client' => ['Backend\Models\User'],
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
    public function beforeCreate() {
            $id = $this->getNextStringId(5);
            $this->code = 'EM_'.$id;  
    }
    public function afterCreate() 
    {
        $content = $this->getOriginalPurgeValue('first_message');
        $this->ticket_messages()->create([
            'content' =>$content,
        ]);
    }

    public function beforeSave() 
    {
        $this->next_id = $this->getNextUserId();
        
        if(!$this->code) {
            $this->code = 'EM_'.str_pad( $this->id, 5, "0", STR_PAD_LEFT );
        }
        
        
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
        // $user = \BackendAuth::getUser();
        // if($user->hasAccess('waka.support.admin.super')) {
        //     return TicketType::lists('name', 'id');
        // } else {
        //     return TicketType::where('is_for_super_user', null)->lists('name', 'id');
        // }
        return TicketType::lists('name', 'id');
        
    }
    public function listSupportUser() {
        $users =  Settings::getSupportUsers();
        $users = User::whereIn('id', $users)->get();
        return $this->collectionConcatId($users);
    }
    public function ListClientTeam() {
        $users =   Settings::getClientManagers();
        $users = User::whereIn('id', $users)->get();
        return $this->collectionConcatId($users);
    }


    /**
     * GETTERS
     **/
    public function getNextUserId() {
        if($this->state == "draft") {
            return $this->user_id;
        }
        else if(in_array($this->state, ['wait_support', 'validated'])) {
            return $this->support_user_id ? $this->support_user_id : Settings::getSupportUsers()[0] ?? null;
        }
        else if(in_array($this->state, ['wait_managment', 'wait_validation'])) {
            return $this->support_client_id ? $this->support_client_id : Settings::getClientManagers()[0] ?? null;   
        } 
        else {
            return null;
        }
        
    }

    public function getBaseAwakeAttribute() {
        //trace_log('yo');
        return Carbon::now()->addWeek();
    }

    /**
     * SCOPES
     */
    public function scopeClosed($query)
    {
        $query->whereIn('state', ['abdn', 'archived']);
    }
    public function scopeActive($query) {
        $query->whereIn('state', ['draft', 'wait_support', 'wait_managment', 'wait_validation', 'validated', ''])
        ->orWhereNull('state');

    }
    public function scopeNoGroup($query) {
        $query->whereNull('ticket_group_id');
    }
    public function scopeIsFacturable($query) {
        $query->where('temps','>', 0);
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

    public function getMessagesAsTxt()
    {
        $messages = $this->ticket_messages()->get(['content'])->pluck('content')->toArray();
        $messagesTxt = implode("\n", $messages);
        $messagesTxt = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n", $messagesTxt), ENT_QUOTES, 'UTF-8');
        $messagesTxt = strip_tags($messagesTxt);
        return $messagesTxt;
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

    public static function getMenucounter()
    {
        $userId = \BackendAuth::getUser()->id;
        return Ticket::where('next_id', $userId)->count();

        // if(Settings::isClientManager()) {
        //     return Ticket::whereIn('state', ['wait_managment', 'wait_response'])->count();
        // } 
        //  if(Settings::isSupportMember()) {
        //     return Ticket::whereIn('state', ['wait_support'])->count();
        // }     
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
