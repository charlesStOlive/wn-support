<?php namespace Waka\Support\Listeners;

use Carbon\Carbon;
use Waka\Utils\Classes\Listeners\WorkflowListener;
use Waka\Support\Models\Settings;
use Backend\Models\User;

class WorkflowTicketWListener extends WorkflowListener
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($event)
    {
        //Evenement obligatoires
        $event->listen('workflow.ticket_w.guard', [$this, 'onGuard']);
        $event->listen('workflow.ticket_w.entered', [$this, 'onEntered']);
        $event->listen('workflow.ticket_w.enter', [$this, 'onEnter']);
        $event->listen('workflow.ticket_w.afterModelSaved', [$this, 'onAfterSavedFunction']);
        //Evenement optionels à déclarer ici.
        //$event->listen('workflow.ticket_w.leave', [$this, 'onLeave']);
        $event->listen('workflow.ticket_w.transition', [$this, 'recLogs']);
        //$event->listen('workflow.ticket_w.enter', [$this, 'onEnter']);
    }

    /**
     * Fonctions de Gard
     * Permet de bloquer ou pas une transition d'état
     * doit retourner true or false
     */
     public function isCreatorAsking($event, $args = null)
    {
        $blocked = false;
        $model = $event->getSubject();
        $actualUser = \BackendAuth::getUser();
        if($model->user != $actualUser) {
            $blocked = true;
        }
        return $blocked;
    }

    public function isAwakable($event, $args = null)
    {
        $model = $event->getSubject();
        $date = Carbon::now();
        if(!$model->awake_at) {
            return false;
        }
        if ($model->awake_at->lte($date)) {
            return false;
        }
        return true;
    }

    



    public function isClient($event, $args = null)
    {
        $blocked = false;
        $blocked = !Settings::isClientManager(); //Si pas manager
        return $blocked;
    }

    public function isSupport($event, $args = null)
    {
        $blocked = false;
        $blocked = !Settings::isSupportMember(); //Si pas équipe support
        return $blocked;
    }
    

    /**
     * FONCTIONS DE TRAITEMENT PEUVENT ETRE APPL DANS LES FONCTIONS CLASSIQUES
     */

    public function createChildTicket($event, $args = null)
    {
        //trace_log('createChildTicket');
        
        $model = $event->getSubject();
        //trace_log($model->name);
        $model->createChildTicket();
    }

    public function removeTicketGroup($event, $args = null)
    {
        $model = $event->getSubject();
        $model->ticket_group_id = null;
    }

    public function cleanAwake($event, $args = null) {
        $model = $event->getSubject();
        $ticketGroupId = \Waka\Support\Models\Settings::get('actual_ticket_group' ,null);
        $model->ticket_group_id  = $ticketGroupId;
        $model->awake_at = null;
    }

    /**
     * Fonctions de production de doc, pdf, etc.
     * passe par l'evenement afterModelSaved
     * 2 arguements $model et $arg
     * Ici les valeurs ne peuvent plus être modifié il faut passer par un traitement
     */

    public function askSleep($model) {
        //trace_log('fonction askSleep');
        $model->ticket_group_id = null;
        \Event::fire('waka.workflow.popup_afterSave', ['name' => 'sleep']);
    }

    public function sendNotification($model, $args = null)
    {
        if($model->silent_mode) return;
        $user = User::find($model->next_id);
        $url = \Backend::url('waka/support/tickets/update/'.$model->id);
        $ticket = $model->toArray();
        $messages = $model['ticket_messages'] ?? [];
        if(!count($messages) && post('_session_key')) {
            $ticket['ticket_messages'] = $model->ticket_messages()->withDeferred(post('_session_key'))->get()->toArray();
        }
        $vars = compact('ticket','user','url');
        \Mail::queue('waka.support::mail.new_ticket', $vars, function($message) use($user) {
            $message->to($user->email, 'Notilac: '.$user->login);
        });
    }

}