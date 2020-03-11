<?php

namespace ant\affiliate\models;

use Yii;
use yii\db\ActiveRecord;
use ant\order\models\Order;
use ant\affiliate\models\Referral;

/**
 * This is the model class for table "em_affiliate_referral_contribution".
 *
 * @property int $id
 * @property int $referral_id
 * @property int $order_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Order $order
 * @property AffiliateReferral $referral
 */
class ReferralContribution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%affiliate_referral_contribution}}';
    }

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\TimestampBehavior',
            ],
            [
                'class' => 'yii\behaviors\AttributeBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'commission_amount',
                ],
                'value' => function($event) {
                    return Yii::$app->affiliateManager->getCommissionForReferralContribution($event->sender);
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referral_id', 'order_id', 'status'], 'integer'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['referral_id'], 'exist', 'skipOnError' => true, 'targetClass' => Referral::className(), 'targetAttribute' => ['referral_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'referral_id' => 'Referral ID',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferral()
    {
        return $this->hasOne(Referral::className(), ['id' => 'referral_id']);
    }
	
	public static function find() {
		return new \ant\affiliate\models\query\ReferralContributionQuery(get_called_class());
	}
	
	public static function findRewardedByUserId($userId) {
		return self::find()
			->joinWith(['order order' => function($q) {
				$q->joinWith('invoice invoice');
			}])
			->joinWith('referral referral')
			->andWhere(['order.status' => 1]) // Rewarded
			->andWhere(['referral.user_id' => Yii::$app->user->id]);
	}
}
