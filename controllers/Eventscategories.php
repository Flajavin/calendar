<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 14.10.2016
 * Time: 12:45
 */

namespace mpf\modules\calendar\controllers;


use app\components\SqlCrudController;

class Eventscategories extends SqlCrudController
{

    public $modelName = '\mpf\modules\calendar\models\EventCategory';
}