<?php

namespace Waka\Support\Models;

use Backend\Models\User;
use Model;
use Config;

/**
 * Class Settings
 * @package Renatio\Support\Models
 */
class Settings extends Model
{

    /**
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * @var string
     */
    public $settingsCode = 'waka_support_settings';

    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';


    public static function getSupportUsers() {
        $clientUsers = self::get('client_manage_team');
        return array_column($clientUsers, 'user');
    }
    public static function getClientManagers() {
        $teamUsers = self::get('support_team');
        return array_column($teamUsers, 'user');

    }
    //
    public static function isSupportMember() {
        $userId = \BackendAuth::getUser()->id;
        $clientUsers = self::getSupportUsers();
        if(in_array($userId, $clientUsers)) {
            return true;
        } else {
            return false;
        }
    }
    public static function isClientManager() {
        $userId = \BackendAuth::getUser()->id;
        $clientManagers = self::getClientManagers();
        if(in_array($userId, $clientManagers)) {
            return true;
        } else {
            return false;
        }
    }
    public function listUsers()
    {
        $backendUser = User::get(['first_name', 'last_name', 'id']);
        $backendUser = $backendUser->keyBy('id');
        $backendUser->transform(function ($item, $key) {
            return $item['first_name'] . ' ' . $item['last_name'];
        });
        return $backendUser->toArray();
    }

    

}
