<?php
namespace ant\affiliate\filters;

use Yii;
use ant\affiliate\models\Referral;
use ant\affiliate\components\AffiliateManager;

class ReferralFilter extends \yii\base\ActionFilter {
    const SESSION_NAME = AffiliateManager::SESSION_NAME;

    public function beforeAction($action) {
        Yii::$app->affiliateManager->handleReferralRequest();
		Yii::$app->affiliateManager->handleCampaignReferralRequest();
        return parent::beforeAction($action);
    }
}