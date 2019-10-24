<?php
use ant\affiliate\models\Referral;
use ant\affiliate\models\ReferralContribution;
use yii\data\ActiveDataProvider;
use ant\helpers\StringHelper;
use ant\event\models\Event;

$this->context->layout = '//left-sidenav';

$isLinkPage = Yii::$app->request->get('link', 0);
$this->params['sideNav']['items'] = [
	[
		'label' => 'Dashboard',
		'url' => ['/affiliate'],
		'active' => !$isLinkPage,
	],
	[
		'label' => 'Referral Links',
		'url' => ['/affiliate', 'link' => 1],
		'active' => $isLinkPage,
	],
];
$referral = Referral::find()->andWhere(['user_id' => Yii::$app->user->id]);
if ($campaignId = Yii::$app->request->get('campaign')) {
	$referral->andWhere(['campaign_id' => $campaignId]);
}
$referral = $referral->one();
if (!isset($referral)) throw new \yii\web\NotFoundHttpException('You don\'t have referral account. ');
?>
<style>
.referral-url {
	width: 500px; position:relative; display: table;
}
.referral-url .url {
	display: table-cell; position:relative; background-color: #ffffff; 
	padding: 5px 3px; margin-bottom: 10px; overflow-x: hidden; white-space: nowrap; 
}
.referral-url .url .url-span {
	width: 440px; overflow: hidden; text-overflow: ellipsis;
}
.referral-url .copy-btn {
	display: table-cell; min-width: 60px; border-radius: 0px; padding: 6px 15px; position:relative;
}

@media (max-width: 579px) {	
	.referral-url {
		width: 250px;
	}
	.referral-url .url .url-span {
		width: 175px;
	}
}
</style>

<?php if ($isLinkPage): ?>
	<?= \yii\widgets\ListView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => Event::find()->onGoing()->andWhere(['id' => [190]])
		]),
		'itemView' => '_link',
		'viewParams' => ['referral' => $referral],
	]) ?>
<?php else: ?>
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
				'attribute' => 'id',
				'visible' => YII_DEBUG,
			],
			[
				'attribute' => 'order.formattedId',
				'visible' => YII_DEBUG,
			],
			[
				'label' => 'Buyer',
				'value' => function($model) {
					$identity = $model->order->billTo->email;
					return StringHelper::censor($identity, 3, strpos($identity, '@'));
				}
			],
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
<?php endif ?>

<?php \ant\widgets\JsBlock::begin() ?>
<script>
	function copyLinkIn() {
		var buttons = document.querySelectorAll('.copy-btn');
		for (var i in buttons) {
			buttons[i].text = 'Copy';
		}
		event.target.text = 'Copied';
		copyLink(event.target.getAttribute('data-url'));
	}

	function copyLink(link){
		//var tooltip = document.getElementById("popup-tooltip");
		var currentLink = document.createElement('input');
		currentLink.class = "copytext";
		document.body.appendChild(currentLink);
		// var currentLink = document.getElementById("copylink")
		currentLink.value = link;
		currentLink.select();
		document.execCommand("copy");
		//tooltip.innerHTML = "Link Copied";
		document.body.removeChild(currentLink);
	}
</script>
<?php \ant\widgets\JsBlock::end() ?>
