<?php

$url = Yii::$app->affiliateManager->createReferralUrl($model->route);
?>
<div style="background-color: #dddddd; padding: 10px 15px; margin-bottom:10px; ">
    <h4><?= $model->title ?></h4>

    <div class="referral-url clearfix">
        <div class="url">
            <div class="url-span" >
                <?= $url ?>
            </div>
        </div>
        <a class="btn-primary btn-xs copy-btn" data-url="<?= $url ?>" href="javascript:;" onclick="copyLinkIn()">Copy</a>
    </div>
</div>