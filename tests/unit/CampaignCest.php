<?php 
use ant\models\ModelClass;
use ant\affiliate\models\Campaign;
use ant\affiliate\models\CampaignContribution;

class CampaignCest
{
    public function _before(UnitTester $I)
    {
    }
	
    public function _fixtures()
    {
        return [
            'order' => [
                'class' => \tests\fixtures\OrderFixture::className(),
            ],
        ];
    }

    public function testEnsureCodeFor(UnitTester $I)
    {
		$campaignCode = 'testcode';
		
		$referrable = $this->createTestModel();
		Campaign::ensureCodeFor($campaignCode, $referrable);
		
		$campaign = Campaign::findOne([
			'model_class_id' => ModelClass::getClassId($referrable),
			'model_id' => $referrable->id,
			'code' => $campaignCode,
		]);
		
		$I->assertTrue(isset($campaign));
		$I->assertEquals($campaignCode, $campaign->code);
		$I->assertEquals(ModelClass::getClassId($referrable), $campaign->model_class_id);
		$I->assertEquals($referrable->id, $campaign->model_id);
	}
	
	public function testRecordContribution(UnitTester $I) {
		$campaignCode = 'testcode';
		
		$referrable = $this->createTestModel();
		$order = $I->grabFixture('order')->getModel(0);
		$campaign = Campaign::ensureCodeFor($campaignCode, $referrable);
		
		$campaign->recordContribution($order);
		
		$contribution = CampaignContribution::findOne(['campaign_id' => $campaign->id, 'order_id' => $order->id]);
		$I->assertTrue(isset($contribution));
	}
	
	protected function createTestModel($attibutes = []) {
		$model = new TestModel;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
	}
}

class TestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
}
