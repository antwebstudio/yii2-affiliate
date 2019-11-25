<?php

namespace ant\affiliate\models;

use Yii;
use ant\models\ModelClass;
use ant\affiliate\models\Referral;
use ant\affiliate\models\CampaignContribution;

/**
 * This is the model class for table "em_affiliate_campaign".
 *
 * @property int $id
 * @property int $model_class_id
 * @property int $model_id
 * @property int $commission_percent
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ModelClass $modelClass
 * @property AffiliateReferral[] $affiliateReferrals
 */
class Campaign extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVATED = 1;
    const STATUS_ACTIVATED = 0;
	
	const SCENARIO_JUST_CODE = 'justCode';

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\TimestampBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%affiliate_campaign}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commission_percent'], 'required', 'except' => self::SCENARIO_JUST_CODE],
            [['model_class_id', 'model_id', 'commission_percent', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['model_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelClass::className(), 'targetAttribute' => ['model_class_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_class_id' => 'Model Class ID',
            'model_id' => 'Model ID',
            'commission_percent' => 'Commission Percent',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	/*public static function recordContribution($campaignId, $referrable) {
		$campaign = self::findOne($campaignId);

        if (isset($campaign)) {
            $campaign->recordContribution($referrable);
        }
	}*/
	
	public static function ensureCodeFor($campaignCode, $model, $modelId = null) {
		if (trim($campaignCode) == '') throw new \Exception('Campaign code cannot be empty. ');
		
		if (is_object($model)) {
			$modelClass = get_class($model);
			$modelId = $model->primaryKey;
		} else {
			$modelClass = $model;
			if (!isset($modelId)) throw new \Exception('Model ID is required. ');
		}
		
		$campaign = self::findOne([
			'model_class_id' => ModelClass::getClassId($modelClass),
			'model_id' => $modelId,
			'code' => $campaignCode,
		]);
		
		if (!isset($campaign)) {
			$campaign = new self(['scenario' => Campaign::SCENARIO_JUST_CODE]);
			$campaign->model_class_id = ModelClass::getClassId($modelClass);
			$campaign->model_id = $modelId;
			$campaign->code = $campaignCode;
			
			if (!$campaign->save()) throw new \Exception('Not able to create campaign. '.print_r($campaign->errors, 1));
		}
		
		return $campaign;
	}
	
	public function recordContribution($referrable) {
		$contribution = new CampaignContribution;
		$contribution->campaign_id = $this->id;
		$contribution->order_id = $referrable->id;
		$contribution->status = $this->isActive ? CampaignContribution::STATUS_ACTIVATED : CampaignContribution::STATUS_NOT_ACTIVATED;
		
		if (!$contribution->save()) throw new \Exception(print_r($contribution->errors, 1));
		
		return $contribution;
	}

    public function getStatusText() {
        if ($this->status == self::STATUS_NOT_ACTIVATED) {
            return 'Paused';
        } else {
            return 'Active';
        }
    }

    public function getIsActive() {
        return $this->status == self::STATUS_ACTIVATED;
    }

    public function activate() {
        $this->status = self::STATUS_ACTIVATED;
        return $this;
    }

    public function deactivate() {
        $this->status = self::STATUS_NOT_ACTIVATED;
        return $this;
    }

    public function getName($titleAttribute = 'title') {
        if (isset($this->commission_percent)) {
            return $this->model->{$titleAttribute}.' - '.$this->commission_percent.'%';
        }
        return $this->model->{$titleAttribute};
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(ModelClass::getClassName($this->model_class_id), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferrals()
    {
        return $this->hasMany(Referral::className(), ['campaign_id' => 'id']);
    }
}
