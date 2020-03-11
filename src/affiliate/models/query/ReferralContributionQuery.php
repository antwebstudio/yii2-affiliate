<?php
namespace ant\affiliate\models\query;

class ReferralContributionQuery extends \yii\db\ActiveQuery {
	public function getTotalCommission() {
		return $this->sum('commission_amount');
	}
	
	public function getTotalContributionAmount() {
		return $this->sum('invoice.paid_amount');
	}
}