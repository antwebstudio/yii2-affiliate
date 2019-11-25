<?php
use yii\helpers\Html;
use yii\grid\GridView;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
		'order.id',
		[	
			'format' => 'html',
			'attribute' => 'order.formattedId',
			'value' => function($model) {
				return Html::a($model->order->formattedId, $model->order->url, ['target' => '_blank']);
			},
		],
		'order.netTotal',
	],
]) ?>
