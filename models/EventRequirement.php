<?php
/**
 * Created by MPF Framework.
 * Date: 2016-10-04
 * Time: 11:55
 */

namespace mpf\modules\calendar\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\tools\Validator;
use mpf\WebApp;

/**
 * Class EventRequirement
 * @package app\models
 * @property int $id
 * @property int $event_id
 * @property string $title_ro
 * @property string $title_en
 * @property int $min_number
 * @property int $max_number
 * @property int $recommended_number
 * @property \app\models\Event $event
 */
class EventRequirement extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "event_requirements";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels()
    {
        return [
            'id' => 'Id',
            'event_id' => 'Event',
            'title_ro' => 'Title Ro',
            'title_en' => 'Title En',
            'min_number' => 'Min Number',
            'max_number' => 'Max Number',
            'recommended_number' => 'Recommended Number'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'event' => [DbRelations::BELONGS_TO, '\app\models\Event', 'event_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, event_id, title_ro, title_en, min_number, max_number, recommended_number", "safe", "on" => "search"],
            ['title_ro, title_en, min_number, max_number, recommended_number', 'safe', 'on' => 'insert,update'],
            ['title_ro, title_en', 'required', 'on' => 'insert,update'],
            ['min_number, max_number, recommended_number', 'int', 'min' => 0, 'on' => 'insert,update'],
            ['max_number', function (Validator $validator, $field, $options, $label, $message) { // check if old password is correct
                if ($validator->getValue($field) >= $validator->getValue('min_number')) {
                    return true;
                }
                throw new \Exception($message ? $message : $validator->translate($label . ' must be bigger than the Min Number!'));
            }, 'on' => 'insert,update'],
            ['recommended_number', function (Validator $validator, $field, $options, $label, $message) { // check if old password is correct
                if ($validator->getValue($field) >= $validator->getValue('min_number') && $validator->getValue($field) <= $validator->getValue('max_number')) {
                    return true;
                }
                throw new \Exception($message ? $message : $validator->translate($label . ' must be placed between Min Number and Max Number!'));
            }, 'on' => 'insert,update']
        ];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $lang = WebApp::get()->request()->getLanguage();
        if ('ro' == $lang)
            return $this->title_ro;
        return $this->title_en;
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "event_id", "title_ro", "title_en", "min_number", "max_number", "recommended_number"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
