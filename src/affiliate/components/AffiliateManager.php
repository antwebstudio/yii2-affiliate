<?php
namespace ant\affiliate\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use ant\affiliate\models\Referral;

class AffiliateManager extends \yii\base\Component {
    const SESSION_NAME = 'referral';
    const COOKIES_NAME = 'referral';

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CookieCollection
     */
    protected $cookies;

    protected $requestCookies;

    public $cookiesExpire = 0; // seconds
    public $overrideMethods = [];
    public $referralQueryParamName = 'ref';

    public function init()
    {
        $this->session = Yii::$app->session;
        $this->cookies = Yii::$app->response->cookies;
        $this->requestCookies = Yii::$app->request->cookies;
    }

    public function createReferralUrl($route) {
      $referral = Referral::findOne(['user_id' => Yii::$app->user->id]);
      return Url::to(ArrayHelper::merge($route, [$this->referralQueryParamName => $referral->name]), true);
    }

    public function handleReferralRequest() {
      $referral = Yii::$app->request->get($this->referralQueryParamName);
        
      if (isset($referral)) {
          $referral = Referral::findOne(['name' => $referral]);
          if (isset($referral)) {
              $this->setReferral($referral);
          }
      }
    }

    public function setReferral($referral) {
      $referral = $referral instanceof Referral ? $referral->id : $referral;
      $this->session->set(self::SESSION_NAME, $referral);
      $this->cookies->add(new \yii\web\Cookie([
          'name' => self::COOKIES_NAME,
          'value' => $referral,
          'expire' => $this->cookiesExpire == 0 ? $this->cookiesExpire : time() + $this->cookiesExpire,
      ]));
    }

    public function getReferral() {
      return $this->session->get(self::SESSION_NAME, $this->requestCookies->getValue(self::COOKIES_NAME));
    }
    
    public function getCommissionForReferralContribution($contribution) {
      if (isset($this->overrideMethods['getCommissionForReferralContribution']) && is_callable($this->overrideMethods['getCommissionForReferralContribution'])) {
        return call_user_func_array($this->overrideMethods['getCommissionForReferralContribution'], [$contribution]);
      }
    }

    public function clear() {
      $this->cookies->remove(self::COOKIES_NAME);
      $this->session->remove(self::SESSION_NAME);
    }
}