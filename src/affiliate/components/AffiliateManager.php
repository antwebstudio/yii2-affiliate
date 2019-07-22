<?php
namespace ant\affiliate\components;

class AffiliateManager extends \yii\base\Component {
    public $overrideMethods = [];
    
    public function getCommissionForReferralContribution($contribution) {
		if (isset($this->overrideMethods['getCommissionForReferralContribution']) && is_callable($this->overrideMethods['getCommissionForReferralContribution'])) {
			return call_user_func_array($this->overrideMethods['getCommissionForReferralContribution'], [$contribution]);
		}
    }
}