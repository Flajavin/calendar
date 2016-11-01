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

/**
 * Class EventOrganizer
 * @package app\models
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property int $type
 * @property \app\models\Event $event
 * @property \app\models\User $user
 */
class EventOrganizer extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "event_organisers";
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
            'user_id' => 'User',
            'type' => 'Type'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'event' => [DbRelations::BELONGS_TO, '\mpf\modules\calendar\models\Event', 'event_id'],
            'user' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, event_id, user_id, type", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "event_id", "user_id", "type"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
