<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 14.10.2016
 * Time: 12:45
 */

namespace mpf\modules\calendar\controllers;


use app\components\SqlCrudController;
use mpf\modules\calendar\models\Event;

class Events extends SqlCrudController
{
    public $modelName = '\app\models\Event';

    public function actionActions()
    {
        $events = Event::findAllByPk($_POST['Event']);
        switch ($_POST['action']) {
            case 'validate':
                foreach ($events as $event) {
                    $event->status = Event::STATUS_OPEN;
                    $event->save(false);
                }
                break;
            case 'close':
                foreach ($events as $event) {
                    $event->status = Event::STATUS_CLOSED;
                    $event->save(false);
                }
                break;
            default:
                break;
        }
        $this->goBack();


    }
}