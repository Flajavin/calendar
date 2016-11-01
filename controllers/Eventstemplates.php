<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 14.10.2016
 * Time: 12:46
 */

namespace mpf\modules\calendar\controllers;


use mpf\modules\calendar\components\SqlCrudController;

class Eventstemplates extends SqlCrudController
{
    public $modelName = '\mpf\modules\calendar\models\EventTemplate';
}