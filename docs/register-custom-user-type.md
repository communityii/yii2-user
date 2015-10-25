Register Custom User Type
=========================

[:back: guide](index.md#advanced-customization)

You can add roles and other information to user accounts upon registration by using events and user types.

```php
    'modules' => [
        'user' => [
            'class' => 'comyii\user\Module',
            // ... user types and other configurations
            'on beforeRegister' => ['\common\handlers\RegistrationHandler', 'beforeRegister'],
            'on registerComplete' => ['\common\handlers\RegistrationHandler', 'registerComplete']
        ],
    ],
```

Next, you can create the handler class `\common\handlers\RegistrationHandler`. Notice that we can attach events to the model class as well since it is passed in as a property of the event parameter.

```php
namespace common\handlers;

use Yii;
use yii\db\ActiveRecord;
use comyii\user\events\RegistrationEvent;

class RegistrationHandler extends \yii\base\Object
{
    public static $vendor;
    public static $vendorContact;
    public static $model;
    public static $event;

    /**
     * Pre registration event handler
     * @param RegistrationEvent $event
     */
    public static function beforeRegister($event)
    {
        self::$event = $event;
        if($event->type === 'vendor') {
            self::$vendor = new \common\models\Vendor;
            self::$vendorContact = new \common\models\VendorContact;
            $model = self::$event->model;
            $model->type = \common\models\User::TYPE_VENDOR;
            // attach event to User model in account controller
            $model->on(ActiveRecord::EVENT_BEFORE_VALIDATE,[self::className(),'beforeValidateVendor']);
            $model->on(ActiveRecord::EVENT_AFTER_INSERT,[self::className(),'afterInsertVendor']);
            self::$model = $model;
            $event->viewFile = '@frontend/views/register/vendor';
            $event->activate = true;
        }
    }
    
    /**
     * Pre insert/update vendor handler
     * @param RegistrationEvent $event
     */
    public static function beforeValidateVendor($event)
    {
        $valid = true;
        if(!self::$vendor->load(Yii::$app->request->post()) || !self::$vendor->validate()) {
            $valid=false;
        }
        if(!self::$vendorContact->load(Yii::$app->request->post()) || !self::$vendorContact->validate()) {
            $valid=false;
            // ... validate other models here
        }
        // tell the controller to stop
        if(!$valid) {
            self::$event->error = true;
            // you could also throw an exception to stop the script
            // throw new InvalidRequestException();
        }
    }
    
    /**
     * Post vendor insert handler
     * @param RegistrationEvent $event
     */
    public static function afterInsertVendor($event)
    {
        self::$vendor->user_id = $event->sender->id;
        if(!self::$vendor->save()) {
            self::$event->error = true;
        }
        self::$vendorContact->vendor_id = self::$vendor->id;
        if(!self::$vendorContact->save()) {
            self::$event->error = true;
            return;
        }
        self::$model->type = \common\models\User::TYPE_VENDOR;
        self::$model->save();
        Yii::$app->authManager->assign(Yii::$app->authManager->createRole('vendor'), self::$model->getId());
    }
    
    /**
     * Post registration handler
     * @param RegistrationEvent $event
     */
    public static function registerComplete($event)
    {
        if($event->type=='vendor') {
            // redirect to custom route
            $event->redirect = ['/vendor/account'];
        }
    }
}
```
[:back: top](#register-custom-user-type) | [:back: guide](index.md#advanced-customization)