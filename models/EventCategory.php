<?php
/**
 * Created by MPF Framework.
 * Date: 2016-10-04
 * Time: 11:47
 */

namespace mpf\modules\calendar\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

/**
 * Class EventCategory
 * @package app\models
 * @property int $id
 * @property string $name_ro
 * @property string $name_en
 * @property string $html_class_suffix
 */
class EventCategory extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "event_categories";
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
            'name_ro' => 'Name Ro',
            'name_en' => 'Name En',
            'html_class_suffix' => 'HTML Class Suffix'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [

        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, name_ro, name_en, html_class_suffix", "safe", "on" => "search"]
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        $lang = WebApp::get()->request()->getLanguage();
        if ('ro' == $lang)
            return $this->name_ro;
        return $this->name_en;
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name_ro", "name_en", "html_class_suffix"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
