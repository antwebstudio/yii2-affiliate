<?php
namespace ant\affiliate\behaviors;

use Yii;
use yii\db\ActiveRecord;
use ant\affiliate\models\Campaign;
use ant\affiliate\models\Referral;
use ant\affiliate\models\ReferralContribution;
use ant\affiliate\filters\ReferralFilter;
use ant\order\models\Order;

class ReferrableBehavior extends \yii\base\Behavior {
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => [$this, 'afterInsert'],
            Order::EVENT_AFTER_ORDER_COMPLETE => [$this, 'afterOrderConfirmed'],
        ];
    }

    public function afterInsert($event) {
		// Record contribution for referral
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
		
		// Record contribution for campaign
		$campaign = Campaign::findOne($this->getCampaignId());

        if (isset($campaign)) {
            $campaign->recordContribution($this->owner);
        }
    }
	
	protected function getCampaignId() {
        $campaignCode = Yii::$app->affiliateManager->getCampaignCode();
		if (trim($campaignCode) != '') {
			$campaign = Campaign::ensureCodeFor($campaignCode, get_class($this->owner->item), $this->owner->item->id);
			
			return $campaign->id;
		}
	}

    protected function getReferralId() {
        return Yii::$app->affiliateManager->getReferral();
    }
}