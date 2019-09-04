<?php
namespace ant\affiliate\behaviors;

use Yii;
use yii\db\ActiveRecord;
use ant\affiliate\models\Referral;
use ant\affiliate\models\ReferralContribution;
use ant\affiliate\filters\ReferralFilter;
use common\modules\order\models\Order;

class ReferrableBehavior extends \yii\base\Behavior {
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => [$this, 'afterInsert'],
            Order::EVENT_AFTER_ORDER_COMPLETE => [$this, 'afterOrderConfirmed'],
        ];
    }

    public function afterInsert($event) {
        $referralId = $this->getReferralId();
		$referral = Referral::findOne($referralId);

        if (isset($referral) && (!isset($referral->campaign) || $referral->campaign->isActive)) {
            $referral->recordContribution($this->owner);
        }
    }

    public function afterOrderConfirmed($event) {
        $referralId = $this->getReferralId();
		$referral = Referral::findOne($referralId);
		
		if (isset($referral)) {
			$referral->updateContribution($this->owner);
		}
    }

    protected function getReferralId() {
        return Yii::$app->affiliateManager->getReferral();
    }
}