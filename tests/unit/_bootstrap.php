<?php
require_once dirname(dirname(__DIR__)).'/vendor/autoload.php';
require_once dirname(dirname(__DIR__)).'/vendor/yiisoft/yii2/Yii.php';

function env($name) {
	if ($name == 'ROBOT_EMAIL' || $name == 'DEVELOPER_EMAIL') {
		return 'test@example.com';
	}
}

Yii::setAlias('@common', dirname(dirname(__DIR__)).'/src/common');
Yii::setAlias('@tests', dirname(__DIR__));

$config = require dirname(__DIR__).'/config/unit.php';
new yii\web\Application($config);
