<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 01.11.2016
 * Time: 10:52
 */

namespace mpf\modules\calendar\controllers;


use mpf\modules\calendar\components\Controller;
use app\components\htmltools\Messages;
use mpf\helpers\ArrayHelper;
use mpf\helpers\Calendar;
use mpf\helpers\CalendarDay;
use mpf\modules\calendar\models\Event;
use mpf\modules\calendar\models\EventCategory;
use mpf\modules\calendar\models\EventTemplate;
use mpf\WebApp;


class Home extends Controller
{
    public function actionIndex($month = null, $year = null)
    {
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        Event::cacheListForCalendar($month, $year);
        $this->assign('calendar', Calendar::get($month, $year, [
            'events' => function (CalendarDay $day) {
                return Event::getListForDay($day->date);
            }
        ]));
        $this->assign('currentDate', ['month' => $month, 'year' => $year]);
    }

    public function actionView($id)
    {
        $event = Event::findByPk($id);

        $this->assign('event', $event);
    }

    public function actionCreate($template = null)
    {
        $categories = EventCategory::findAll();
        if (!$categories) {
            Messages::get()->error("Can't create events: No categories found!");
            $this->goToPage('events');
        }
        $this->assign('categories', $categories);
        $m = new Event();
        if ($template) {
            $template = EventTemplate::findByPk($template);
            if ($template) {
                $m->setAttributes(json_decode($template->details, true));
            } else {
                Messages::get()->warning("Template not found!");
            }
        }
        $m->author_id = WebApp::get()->user()->id;
        $m->added_time = date('Y-m-d H:i:s');
        $m->icon = 'default.png';
        $m->status = Event::STATUS_NEW;
        if (isset($_POST['Event']) && $m->setAttributes($_POST['Event'])->save()) {
            if ($m->saveRequirements()) {
                $m->saveIcon();
                Messages::get()->success("Event saved!");
                $this->goToPage('home', 'view', ['id' => $m->id]);
                if ($m->saveTemplate) {
                    if (EventTemplate::createFromDetails($m->templateName, $_POST['Event'])) {
                        Messages::get()->success("Template saved!");
                    }
                }
            } else {
                $m->delete();
            }
        }
        var_dump($m->getErrors());
        $this->assign('model', $m);
        $this->assign('templates', ArrayHelper::get()->transform(EventTemplate::findAll(), ['id' => 'title_en']));
    }

    public function actionEdit($id)
    {
        $categories = EventCategory::findAll();
        if (!$categories) {
            Messages::get()->error("Can't create events: No categories found!");
            $this->goToPage('events');
        }
        $this->assign('categories', $categories);
        $m = Event::findByPk($id);
        if (!$m) {
            Messages::get()->error("Event not found!");
            $this->goToPage('home', 'index');
        }
        $m->prepareRequirementsForEdit();
        if (isset($_POST['Event']) && $m->setAttributes($_POST['Event'])->save()) {
            $m->saveIcon();
            if ($m->saveRequirements()) {
                Messages::get()->success("Event saved!");
                $this->goToPage('home', 'view', ['id' => $m->id]);
            }
        }
        $this->assign('model', $m);
    }

}