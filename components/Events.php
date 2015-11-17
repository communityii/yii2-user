<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, communityii, 2014 - 2015
 * @package communityii/yii2-user
 * @version 1.0.0
 *
 * @author derekisbusy https://github.com/derekisbusy
 * @author kartik-v https://github.com/kartik-v
 */

namespace comyii\user\components;

/**
 * Class Events the events for the module.
 * 
 * @package comyii\user\components
 */
class Events
{
    // the list of events
    const EVENT_EXCEPTION = 'exception';
    const EVENT_BEFORE_ACTION = 'beforeAction';
    const EVENT_REGISTER_BEGIN = 'beforeRegister';
    const EVENT_REGISTER_COMPLETE = 'registerComplete';
    const EVENT_LOGIN_BEGIN = 'loginBegin';
    const EVENT_LOGIN_COMPLETE = 'loginComplete';
    const EVENT_LOGOUT = 'logout';
    const EVENT_PASSWORD_BEGIN = 'passwordBegin';
    const EVENT_PASSWORD_COMPLETE = 'passwordComplete';
    const EVENT_RECOVERY_BEGIN = 'recoveryBegin';
    const EVENT_RECOVERY_COMPLETE = 'recoveryComplete';
    const EVENT_RESET_BEGIN = 'resetBegin';
    const EVENT_RESET_COMPLETE = 'resetComplete';
    const EVENT_ACTIVATE_BEGIN = 'activateBegin';
    const EVENT_ACTIVATE_COMPLETE = 'activateComplete';
    const EVENT_AUTH_BEGIN = 'authBegin';
    const EVENT_AUTH_COMPLETE = 'authComplete';
    const EVENT_NEWEMAIL_BEGIN = 'newemailBegin';
    const EVENT_NEWEMAIL_COMPLETE = 'newemailComplete';
    const EVENT_PROFILE_INDEX = 'profileIndex';
    const EVENT_PROFILE_VIEW = 'profileView';
    const EVENT_PROFILE_UPDATE = 'profileUpdate';
    const EVENT_PROFILE_UPDATE_BEGIN = 'profileUpdateBegin';
    const EVENT_PROFILE_UPDATE_COMPLETE = 'profileUpdateComplete';
    const EVENT_PROFILE_DELETE_AVATAR_BEGIN = 'profileDeleteAvatarBegin';
    const EVENT_PROFILE_DELETE_AVATAR_COMPLETE = 'profileDeleteAvatarComplete';
    const EVENT_ADMIN_INDEX = 'adminIndex';
    const EVENT_ADMIN_VIEW = 'adminView';
    const EVENT_ADMIN_UPDATE_BEGIN = 'adminUpdateBegin';
    const EVENT_ADMIN_UPDATE_COMPLETE = 'adminUpdateComplete';
    const EVENT_ADMIN_BATCH_UPDATE_BEGIN = 'adminBatchBegin';
    const EVENT_ADMIN_BATCH_UPDATE_COMPLETE = 'adminBatchComplete';
    const EVENT_CREATE_USER_BEGIN = 'createBegin';
    const EVENT_CREATE_USER_COMPLETE = 'createComplete';
    const EVENT_EMAIL_FAILED = 'emailFailed';
}