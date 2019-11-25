<?php

namespace ant\affiliate\models;

use Yii;
use ant\order\models\Order;

/**
 * This is the model class for table "em_affiliate_campaign_contribution".
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $order_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AffiliateCampaign $campaign
 * @property Order $order
 */
class CampaignContribution extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVATED = 1;
    const STATUS_ACTIVATED = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%affiliate_campaign_contribution}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campaign_id', 'order_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::className(), 'targetAttribute' => ['campaign_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'campaign_id' => 'Campaign ID',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
