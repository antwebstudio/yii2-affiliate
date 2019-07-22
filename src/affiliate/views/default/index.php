<?php
use ant\affiliate\models\Referral;
use ant\affiliate\models\ReferralContribution;
use yii\data\ActiveDataProvider;

$referral = Referral::findOne(['user_id' => Yii::$app->user->id]);
if (!isset($referral)) throw new \yii\web\NotFoundHttpException('You don\'t have referral account. ');
?>

<div class="row">
	<div class="col-md-4 col-xs-6">
		<div style="background-color: #dddddd; padding: 10px 15px; ">
			<p>Total Contributed: </p>
			<h1><?= Yii::$app->formatter->asCurrency($referral->getTotalContributionAmount()) ?></h1>
		</div>
	</div>

	<div class="col-md-4 col-xs-6">
		<div style="background-color: #dddddd; padding: 10px 15px; ">
			<p>Total Commission: </p>
			<h1><?= Yii::$app->formatter->asCurrency($referral->getTotalCommission()) ?></p></h1>
		</div>
	</div>
</div>

<h2>Details</h2>
<?= \yii\grid\GridView::widget([
	'dataProvider' => new ActiveDataProvider([
		'query' => $referral->getCompletedContributions(),
		
	]),
	'columns' => [
		[
			'label' => 'Amount',
			'format' => 'currency',
			'attribute' => 'order.invoice.paid_amount',
		],
		[
			'label' => 'Commission',
			'format' => 'currency',
			'attribute' => 'commission_amount',
		],
		[
			'label' => 'Date',
			'attribute' => 'created_at',
		],
	],
]) ?>