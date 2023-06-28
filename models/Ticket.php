<?php

namespace Waka\Support\Models;

use Model;
use Carbon\Carbon;
use Backend\Models\User;

/**
 * ticket Model
 */

class Ticket extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\SimpleTree;
    use \Winter\Storm\Database\Traits\Sortable;
    use \Waka\Utils\Classes\Traits\DataSourceHelpers;
    use \Waka\Utils\Classes\Traits\WakaWorkflowTrait;
    use \Waka\Utils\Classes\Traits\DbUtils;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_support_tickets';
    public $defaultWorkflowName = "ticket_w";

    public $implement = [
        'October.Rain.Database.Behaviors.Purgeable',
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
    ];

    public $customMessages = [];

    /**
     * @var array attributes send to datasource for creating document
     */
    public $attributesToDs = [];


    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

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
     * @var array Spécifié le type d'export à utiliser pour chaque champs
     */
    public $importExportConfig = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'ticket_messages' => [
            'Waka\Support\Models\TicketMessage',
            'delete' => true
        ],
    ];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'ticket_type' => ['Waka\Support\Models\TicketType'],
        'ticket_group' => ['Waka\Support\Models\TicketGroup'],
        'user' => ['Backend\Models\User'],
        'next' => ['Backend\Models\User'],
        'support_user' => ['Backend\Models\User'],
        'support_client' => ['Backend\Models\User'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
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
    public function beforeValidate() {
        //trace_log(post());
        if(!$this->ticket_messages()->withDeferred(post('_session_key'))->count()) {
            throw new \ValidationException(['ticket_messages' => \Lang::get('waka.support::ticket.e.ticket_messages_missing')]);
        }

    }
    public function beforeCreate()
    {
        $id = $this->getNextStringId(5);
        if (!$this->temps) $this->temps = 0.0;
        $this->code = 'EM_' . $id;
        if (!$this->user) {
            $this->user = \BackendAuth::getUser();
        }
    }

    public function beforeSave()
    {
        //trace_log($this->toArray());
        $this->next_id = $this->getNextUserId();
        if (!$this->code) {
            $this->code = 'EM_' . str_pad($this->id, 5, "0", STR_PAD_LEFT);
        }
    }


    /**
     * LISTS
     **/
    public function listTicketTypes()
    {
        return TicketType::lists('name', 'id');
    }
    public function listSupportUser()
    {
        $users =  Settings::getSupportUsers();
        $users = User::whereIn('id', $users)->get();
        return $this->collectionConcatId($users);
    }
    public function ListClientTeam()
    {
        $users =   Settings::getClientManagers();
        $users = User::whereIn('id', $users)->get();
        return $this->collectionConcatId($users);
    }


    /**
     * GETTERS
     **/
    public function getNextUserId()
    {
        if ($this->state == "draft") {
            return $this->user_id;
        } else if (in_array($this->state, ['wait_support', 'running', 'validated',])) {
            return $this->support_user_id ? $this->support_user_id : Settings::getSupportUsers()[0] ?? null;
        } else if (in_array($this->state, ['wait_managment',])) {
            return $this->support_client_id ? $this->support_client_id : Settings::getClientManagers()[0] ?? null;
        } else {
            return $this->next_id;
        }
    }

    public function getBaseAwakeAttribute()
    {
        return Carbon::now()->addWeek();
    }
    public function getDefaultSupportNotilacAttribute()
    {
        $actualUserId = \BackendAuth::getUser()->id;
        $supports = Settings::getSupportUsers();
        if(in_array($actualUserId, $supports)) {
            return $actualUserId;
        } else {
            return $supports[0];
        }
    }
    public function getDefaultSupportClientAttribute()
    {
        $actualUserId = \BackendAuth::getUser()->id;
        $supports = Settings::getClientManagers();
        //trace_log($actualUserId);
        //trace_log($supports);
        if(in_array($actualUserId, $supports)) {
            return $actualUserId;
        } else {
            return null;
        }
    }
    public function getDefaultTicketGroupAttribute()
    {
        return \Waka\Support\Models\Settings::get('actual_ticket_group' ,null);
    }

    /**
     * SCOPES
     */
    public function scopeClosed($query)
    {
        $query->whereIn('state', $this->getWfScope('closed'));
    }
    public function scopeActive($query)
    {
        $query->whereIn('state', $this->getWfScope('running'))
            ->orWhereNull('state');
    }
    public function scopeIsNotSleeping($query)
    {
        $query->whereNotIn('state', ['sleep']);
    }
    public function scopeNoGroup($query)
    {
        $query->whereNull('ticket_group_id');
    }
    public function scopeIsFacturable($query)
    {
        $query->where('temps', '>', 0);
    }
    public function scopeNextUser($query)
    {
        $query->where('next_id', \BackendAuth::getUser()->id);
    }
    public function scopeUserCounter($query)
    {
        $query->where('next_id', \BackendAuth::getUser()->id)->active()->isNotSleeping();
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
        //trace_log("filterFields");
       if(isset($fields->temps)) {
            if(!\BackendAuth::getUser()->isSuperUser()) {
                $fields->temps->readOnly = true;
            }
        }
    }


    /**
     * OTHERS
     */

    public function createChildTicket()
    {
        $sessionKey = post('_session_key');
        $modelCloned = $this->replicate();

        $modelCloned->created_at = Carbon::now();
        $modelCloned->temps = 0;
        $modelCloned->name = $this->name . ' (reprise)';
        $modelCloned->parent_id = $this->id;
        $modelCloned->ticket_group = null;
        $modelCloned->ticket_messages()->create([
            'content' => "Reprise du ticket : " . $this->name
        ], $sessionKey);
        if ($this->state == 'archived') {
            $modelCloned->state = 'draft';
        }
        //trace_log($modelCloned->toArray());
        $modelCloned->save();
        $modelCloned->commitDeferred($sessionKey);
        return $modelCloned->id;
    }

    /**
     * @return mixed
     */
    public function getOpenedCount()
    {
        return $this->opened()->count();
    }

    public function getMessages()
    {
        return  $this->ticket_messages()->withDeferred(post('_session_key'))->get(['content'])->pluck('content')->toArray();
    }

    public function getMessagesAsTxt()
    {
        $messages = $this->ticket_messages()->get(['content'])->pluck('content');
        $firstTxtMessage = \Soundasleep\Html2Text::convert($messages->first(), ['ignore_errors' => true]);
        $lastTxtMessage = '';
        if ($messages->last()) {
            $lastTxtMessage = \Soundasleep\Html2Text::convert($messages->last(), ['ignore_errors' => true]);
        }
        $messagesConcatended = "---PREMIER MESSAGE: \n  " . $firstTxtMessage . "\n ---DERNIER MESSAGE: \n  " . $lastTxtMessage;
        //trace_log($messagesConcatended);
        return $messagesConcatended;
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

    // /**
    //  * @return void
    //  */
    // public function setDefaults()
    // {
    //     $this->user = $this->model->user ?: BackendAuth::getUser()->id;
    // }

    //endKeep/
}
