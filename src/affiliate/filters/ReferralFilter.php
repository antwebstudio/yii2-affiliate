<?php
namespace ant\affiliate\filters;

use ant\affiliate\models\Referral;

use Yii;

class ReferralFilter extends \yii\base\ActionFilter {
    const SESSION_NAME = 'referral';

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CookieCollection
     */
    protected $cookies;

    public function init()
    {
        $this->session = Yii::$app->session;
        $this->cookies = Yii::$app->response->cookies;
    }

    public function beforeAction($action) {
        $referral = Yii::$app->request->get('referral');
        
        if (isset($referral)) {
            $referral = Referral::findOne(['name' => $referral]);
            if (isset($referral)) {
                $this->session->set(self::SESSION_NAME, $referral->id);
            }
        }
        return parent::beforeAction($action);
    }
}