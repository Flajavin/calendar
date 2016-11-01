<?php
/**
 * Created by MPF Framework.
 * Date: 2016-10-10
 * Time: 14:18
 */

namespace mpf\modules\calendar\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

/**
 * Class EventTemplate
 * @package app\models
 * @property int $id
 * @property string $title_ro
 * @property string $title_en
 * @property string $details
 * @property int $created_by
 * @property string $created_time
 * @property \app\models\User $author
 */
class EventTemplate extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "event_templates";
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
            'title_ro' => 'Title Ro',
            'title_en' => 'Title En',
            'details' => 'Details',
            'created_by' => 'Created By',
            'created_time' => 'Created Time'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'created_by']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, title_ro, title_en, details, created_by, created_time", "safe", "on" => "search"]
        ];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $lang= WebApp::get()->request()->getLanguage();
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

        foreach (["id", "title_ro", "title_en", "details", "created_by", "created_time"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public static function createFromDetails($name, $details)
    {
        $t = new EventTemplate();
        $t->title_en = $name;
        $t->title_ro = $name;
        $t->created_by = WebApp::get()->user()->id;
        $t->created_time = date('Y-m-d H:i:s');
        if (isset($details['requirementsUpdates'])) {
            $req = [];
            foreach ($details['requirements'] as $details) {
                $req['_' . count($req)] = $details;
            }
            $details['requirementsUpdates'] = $req;
        }

        $t->details = json_encode($details);
        return $t->save();
    }
}
