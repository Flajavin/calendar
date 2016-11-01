<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 01.11.2016
 * Time: 11:19
 */

namespace mpf\modules\calendar\components;


class SqlCrudController extends Controller
{
    /**
     * Action to redirect after edit;
     * @var string
     */
    public $afterEditRedirect = 'index';

    /**
     * Name of the model class to be used.
     * To limit actions change the rights for controller and hide actions from datatable
     *
     * @var string
     */
    public $modelName;

    /**
     * List of models using datatable widget;
     */
    public function actionIndex()
    {
        $class = $this->modelName;
        $model = $class::model();
        $shortClass = explode('\\', $class);
        $shortClass = $shortClass[count($shortClass) - 1];
        $this->assign('model', $model->setAttributes(isset($_GET[$shortClass]) ? $_GET[$shortClass] : []));
    }

    public function actionAdd()
    {
        $class = $this->modelName;
        $model = new $class();
        $shortClass = explode('\\', $class);
        $shortClass = $shortClass[count($shortClass) - 1];
        if (isset($_POST[$shortClass])) {
            if ($model->setAttributes($_POST[$shortClass])->save()) {
                $this->request->goToPage(null, $this->afterEditRedirect);
            }
        }
        $this->assign('model', $model);
    }

    public function actionEdit($id)
    {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        $shortClass = explode('\\', $class);
        $shortClass = $shortClass[count($shortClass) - 1];
        if (isset($_POST[$shortClass])) {
            if ($model->setAttributes($_POST[$shortClass])->save()) {
                $this->request->goToPage(null, $this->afterEditRedirect);
            }
        }
        $this->assign('model', $model);
    }

    public function actionDelete()
    {
        $class = $this->modelName;
        $shortClass = explode('\\', $class);
        $shortClass = $shortClass[count($shortClass) - 1];
        $models = $class::findAllByPk($_POST[$shortClass]);
        foreach ($models as $model) {
            $model->delete();
        }
        $this->request->goToPage(null, $this->afterEditRedirect);
    }

    public function actionDuplicate($id)
    {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        if (isset($_POST['duplicate'])) {
            if ($model->crudDuplicate()) {
                $this->request->goToPage(null, $this->afterEditRedirect);
            }
        }
    }

    public function actionView($id)
    {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        $this->assign('model', $model);
    }


}