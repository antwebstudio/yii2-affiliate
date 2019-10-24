<?php

namespace ant\affiliate\models;

use Yii;
use ant\helpers\Currency;
use ant\user\models\User;
use ant\order\models\Order;

/**
 * This is the model class for table "em_affiliate_referral".
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property AffiliateReferralContribution[] $affiliateReferralContributions
 */
class Referral extends \yii\db\ActiveRecord
{
    const SCENARIO_CAMPAIGN = 'campaign';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%affiliate_referral}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'except' => self::SCENARIO_CAMPAIGN],
            [['user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            [['user_id'], 'unique', 'targetAttribute' => ['user_id', 'campaign_id'], 'message' => 'User is already added.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign()
    {
        return $this->hasOne(Campaign::className(), ['id' => 'campaign_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContributions()
    {
        return $this->hasMany(ReferralContribution::className(), ['referral_id' => 'id']);
    }

    public function getCompletedContributions() {
        return $this->hasMany(ReferralContribution::className(), ['referral_id' => 'id'])
			->alias('referralContribution')
			->orderBy('referralContribution.created_at DESC')
            ->joinWith(['order' => function($q) {
                $q->alias('order');
                $q->joinWith('invoice invoice');
            }])
            ->andWhere(['order.status' => Order::STATUS_COMPLETE]);
    }

    public function getTotalContributionAmount() {
        $sum = $this->getCompletedContributions()->sum('invoice.paid_amount');
		return isset($sum) ? Currency::rounding($sum) : 0;
    }
	
	public function getTotalCommission() {
        $sum = $this->getCompletedContributions()->sum('commission_amount');
		return isset($sum) ? Currency::rounding($sum) : 0;
	}
	
	public function recordContribution($order) {
		$contribution = new ReferralContribution;
		$contribution->referral_id = $this->id;
		$contribution->order_id = $order->id;
		$contribution->status = 0;
		
		if (isset($this->campaign)) {
			$contribution->commission_amount = Currency::rounding($this->calculateComission($order->subtotal, $this->campaign->commission_percent / 100));
		}

		if (!$contribution->save()) throw new \Exception(print_r($contribution->errors, 1));
		
		return $contribution;
	}
	
	public function updateContribution($order) {
		$contribution = ReferralContribution::findOne(['order_id' => $order->id]);
		
		if (isset($this->campaign)) {
			$contribution->commission_amount = Currency::rounding($this->calculateComission($order->subtotal, $this->campaign->commission_percent / 100));
		}
		
		if (!$contribution->save()) throw new \Exception(print_r($contribution->errors, 1));
		
		return $contribution;
	}
	
	protected function calculateComission($amount, $rate) {
		return $amount * $rate;
	}
}
