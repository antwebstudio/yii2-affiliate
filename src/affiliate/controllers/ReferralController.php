<?php
namespace ant\affiliate\controllers;

use Yii;
use ant\affiliate\models\Referral;

class ReferralController extends \yii\web\Controller {
    public function behaviors() {
        return [
            [
                'class' => 'yii\filters\VerbFilter',
                'actions'=> [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionDelete($id) {
        $model = Referral::findOne($id);

        try {
            if (isset($model) && $model->delete()) {
                Yii::$app->session->setFlash('success', 'Referral deleted successfully. ');
            }
        } catch (\yii\db\IntegrityException $ex) {
            Yii::$app->session->setFlash('error', 'Referral with contribution cannot be deleted. ');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}