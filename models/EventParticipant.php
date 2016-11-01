<?php
/**
 * Created by MPF Framework.
 * Date: 2016-10-04
 * Time: 11:57
 */

namespace mpf\modules\calendar\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class EventParticipant
 * @package app\models
 * @property int $id
 * @property int $event_id
 * @property int $event_requirement_id
 * @property int $user_id
 * @property string $register_time
 * @property int $status
 * @property \app\models\Event $event
 * @property \app\models\EventRequirement $requirement
 * @property \app\models\User $user
 */
class EventParticipant extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "event_participants";
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
            'event_requirement_id' => 'Event Requirement',
            'user_id' => 'User',
            'register_time' => 'Register Time',
            'status' => 'Status'
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
            'requirement' => [DbRelations::BELONGS_TO, '\mpf\modules\calendar\models\EventRequirement', 'event_requirement_id'],
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
            ["id, event_id, event_requirement_id, user_id, register_time, status", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "event_id", "event_requirement_id", "user_id", "register_time", "status"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
