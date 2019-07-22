<?php
namespace ant\affiliate\behaviors;

use Yii;
use yii\db\ActiveRecord;
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

        if (isset($referralId)) {
            $contribution = new ReferralContribution;
            $contribution->referral_id = $referralId;
            $contribution->order_id = $this->owner->id;
            $contribution->status = 0;

            if (!$contribution->save()) throw new \Exception(print_r($contribution->errors, 1));
        }
    }

    public function afterOrderConfirmed($event) {

    }

    protected function getReferralId() {
        return Yii::$app->session->get(ReferralFilter::SESSION_NAME);
    }
}