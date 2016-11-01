<?php
/**
 * Created by MPF Framework.
 * Date: 2016-10-04
 * Time: 11:48
 */

namespace mpf\modules\calendar\models;

use app\components\htmltools\Messages;
use app\components\htmltools\Page;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\helpers\FileHelper;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\fields\Markdown;

/**
 * Class Event
 * @package app\models
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string $details
 * @property string $icon
 * @property int $author_id
 * @property string $added_time
 * @property string $event_time
 * @property string $event_end_time
 * @property int $status
 * @property int $visibility
 * @property \app\models\EventCategory $category
 * @property \app\models\User $author
 * @property EventRequirement[] $requirements
 */
class Event extends DbModel
{

    public $iconLocationPath = APP_ROOT . '/../htdocs/uploads/events/';
    public $iconLocationUrl = 'uploads/events/';

    const STATUS_NEW = 0;
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;
    const STATUS_DELETED = 3;

    const VISIBILITY_ALL = 1;
    const VISIBILITY_GROUP = 2;
    const VISIBILITY_MODS = 3;
    const VISIBILITY_HIDDEN = 0;

    /**
     * @var Event[]
     */
    public static $cachedList = [];

    public static $statusesLabels = [
        self::STATUS_NEW => 'New',
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_DELETED => 'Deleted'
    ];

    public static $visibilitiesLabels = [
        self::VISIBILITY_ALL => 'Everyone',
        self::VISIBILITY_GROUP => 'Group',
        self::VISIBILITY_MODS => 'Mods',
        self::VISIBILITY_HIDDEN => 'Hidden'
    ];

    public $requirementsUpdates = [], $saveTemplate, $templateName;

    public static function getVisibilityOptions()
    {
        $l = [
            self::VISIBILITY_ALL => Page::get()->translate(self::$visibilitiesLabels[self::VISIBILITY_ALL]),
            self::VISIBILITY_GROUP => Page::get()->translate(self::$visibilitiesLabels[self::VISIBILITY_GROUP]),
        ];
        if (WebApp::get()->user()->hasRight('MODERATORS') || WebApp::get()->user()->hasRight('DEVELOPERS') || WebApp::get()->user()->hasRight('ADMINS')) {
            $l[self::VISIBILITY_MODS] = Page::get()->translate(self::$visibilitiesLabels[self::VISIBILITY_MODS]);
        }
        return $l;
    }

    /**
     * Get translated visibility
     * @return string
     */
    public function getVisibility()
    {
        return Page::get()->translate(self::$visibilitiesLabels[$this->visibility]);
    }

    /**
     * Get translated status
     * @return string
     */
    public function getStatus()
    {
        return Page::get()->translate(self::$statusesLabels[$this->status]);
    }

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "events";
    }

    /**
     * @param string $date
     * @return Event[]
     */
    public static function getListForDay($date)
    {
        $l = [];
        foreach (self::$cachedList as $event) {
            $s = substr($event->event_time, 0, 10);
            $e = substr($event->event_end_time, 0, 10);
            if ($s <= $date && $date <= $e) {
                $l[] = $event;
            }
        }
        return $l;
    }

    /**
     * @param int $limit
     * @return Event[]
     */
    public static function getUpcoming($limit = 5)
    {
        return Event::findAllByAttributes([
            'status' => [self::STATUS_CLOSED, self::STATUS_OPEN],
            'visibility' => [self::VISIBILITY_ALL]
        ], [
            'condition' => 'event_end_time > NOW()',
            'limit' => $limit,
            'order' => 'event_time ASC'
        ]);
    }

    public static function cacheListForCalendar($month, $year)
    {
        if ($month < 10)
            $month = '0' . ltrim($month, '0');
        $start = strtotime('-1 month', strtotime("$year-$month-20"));
        $end = strtotime('+1 month', strtotime("$year-$month-10"));
        self::$cachedList = Event::model()->findAll(":start <= event_time AND event_time <= :end", [':start' => date('Y-m-d H:i:s', $start), ':end' => date('Y-m-d H:i:s', $end)]);
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
            'category_id' => 'Category',
            'title' => 'Title',
            'details' => 'Details',
            'icon' => 'Icon',
            'author_id' => 'Author',
            'added_time' => 'Added Time',
            'event_time' => 'Event Time',
            'event_end_time' => 'Event End Time',
            'status' => 'Status',
            'visibility' => 'Visibility',
            'requirementsUpdates' => 'Roles Requirements',
            'saveTemplate' => 'Save Template',
            'templateName' => 'Template Name'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'category' => [DbRelations::BELONGS_TO, '\app\models\EventCategory', 'category_id'],
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'author_id'],
            'requirements' => [DbRelations::HAS_MANY, EventRequirement::className(), 'event_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, category_id, title, details, icon, author_id, added_time, event_time, event_end_time, status, visibility", "safe", "on" => "search"],
            ['category_id, title, details, icon, event_time, event_end_time, status, visibility, requirementsUpdates, saveTemplate, templateName', 'safe', 'on' => 'insert,update'],
            ['category_id, author_id, title, details', 'required', 'on' => 'insert,update'],
            ['status, visibility, category_id, author_id', 'int']
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "category_id", "title", "details", "icon", "author_id", "added_time", "event_time", "event_end_time", "status", "visibility"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * Validate requirements
     * @return bool
     */
    public function beforeSave()
    {
        if (!count($this->requirementsUpdates)) {
            return parent::beforeSave();
        }
        if (1 == count($this->requirementsUpdates) && isset($this->requirementsUpdates['_0']) && !trim($this->requirementsUpdates['_0']['title_ro'])) {
            return parent::beforeSave();
        }
        $e = new EventRequirement();
        $e->event_id = 1; // temp id
        $valid = true;
        $errors = [];
        foreach ($this->requirementsUpdates as $k => $update) {
            if (!$e->setAttributes($update)->validate()) {
                $valid = false;
                $errors[$k] = $e->getErrors();
            }
        }
        if ($errors)
            $this->setError('requirementsUpdates', $errors);
        return $valid;
    }

    public function getDetailsLink($calendarDate, $htmlOptions = [])
    {
        $categoryClass = 'event-link-' . $this->category->html_class_suffix;
        $htmlOptions['class'] = isset($htmlOptions['class']) ? ($htmlOptions['class'] . ' ' . $categoryClass) : $categoryClass;
        $s = substr($this->event_time, 0, 10);
        $e = substr($this->event_end_time, 0, 10);
        if ($calendarDate > $s)
            $htmlOptions['class'] .= ' event-started-before';
        if ($calendarDate < $e)
            $htmlOptions['class'] .= ' event-ends-after';
        return Html::get()->link(['events', 'view', ['id' => $this->id]], $this->title, $htmlOptions);
    }

    /**
     * Save / update requirements
     * @return bool
     */
    public function saveRequirements()
    {
        if (!count($this->requirementsUpdates))
            return true;
        $ok = true;
        $existent = EventRequirement::findAllByAttributes(['event_id' => $this->id]);
        foreach ($existent as $ex) {
            if (!isset($this->requirementsUpdates[$ex->id])) {
                $ex->delete(); // delete those that  are no longer used;
            }
        }
        foreach ($this->requirementsUpdates as $k => $requirement) {
            if (!(trim($requirement['title_ro']) && trim($requirement['title_en']))) {
                continue;
            }
            if ('_' == substr($k, 0, 1)) {
                $r = new EventRequirement();
                $r->event_id = $this->id;
            } else {
                $r = EventRequirement::findByAttributes(['id' => $k, 'event_id' => $this->id]);
                if (!$r) {
                    Messages::get()->error('Requirements have been updated by someone else, so the updates weren\'t saved for some of them!');
                    continue;
                }
            }
            if (!$r->setAttributes($requirement)->save()) {
                $ok = false;
            }
        }
        return $ok;
    }

    public function prepareRequirementsForEdit()
    {
        $this->requirementsUpdates = [];
        $safeFields = explode(', ', 'title_ro, title_en, min_number, max_number, recommended_number');
        foreach ($this->requirements as $requirement) {
            $p = [];
            foreach ($safeFields as $field) {
                $p[$field] = $requirement->$field;
            }
            $this->requirementsUpdates[$requirement->id] = $p;
        }
    }

    public function saveIcon()
    {
        if (!isset($_FILES['icon']) || !$_FILES['icon']['tmp_name']) {
            return null;
        }
        if (!FileHelper::get()->isImage($_FILES['icon']['tmp_name'])) {
            return false;
        }
        $name = $this->id . substr($_FILES['icon']['name'], -30);
        FileHelper::get()->upload('icon', $this->iconLocationPath . $name);
        $old = $this->icon;
        if ($old && 'default.png' != $old && $name != $old) {
            @unlink($old);
        }
        $this->icon = $name;
        return $this->save();
    }

    /**
     * @return string
     */
    public function getIconURL()
    {
        return $this->iconLocationUrl . ($this->icon ?: 'default.png');
    }

    public function getDetails()
    {
        return Markdown::processText($this->details);
    }

    public function canIEditIt()
    {
        return WebApp::get()->user()->isConnected() ? (WebApp::get()->user()->hasRight('ADMINS') || WebApp::get()->user()->hasRight('MODERATORS') || (WebApp::get()->user()->id == $this->id)) : false;
    }
}
