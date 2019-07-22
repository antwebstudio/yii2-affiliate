<?php
namespace ant\affiliate\controllers;

class DefaultController extends \yii\web\Controller {
    public function actionIndex() {
        return $this->render($this->action->id, [

        ]);
    }
}