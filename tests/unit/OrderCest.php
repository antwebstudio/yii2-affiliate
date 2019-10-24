<?php 

use ant\cart\models\Cart;
use ant\cart\models\CartItem;
use ant\order\models\Order;
use ant\user\models\User;
use ant\affiliate\models\Referral;
use ant\affiliate\models\ReferralContribution;
use ant\affiliate\filters\ReferralFilter;

class OrderCest
{
    public function _before(UnitTester $I)
    {
		\Yii::configure(\Yii::$app, [
			'components' => [
				'cart' => [
					'class' => 'ant\cart\components\CartManager',
                ],
                'affiliateManager' => [
                    'class' => 'ant\affiliate\components\AffiliateManager',
                    'overrideMethods' => [
                        'getCommissionForReferralContribution' => function($contribution) {
                            return \ant\helpers\Currency::rounding($contribution->order->netTotal * 0.10);
                        }
                    ],
                ],
			],
		]);
    }

    // tests
    public function testNoReferral(UnitTester $I)
    {
		$cart = new Cart();
        
        if (!$cart->save()) throw new \Exception(print_r($cart->errors, 1));

        $order = new Order;
        if (!$order->save()) throw new \Exception(print_r($order->errors, 1));

        $contribution = ReferralContribution::findOne(['order_id' => $order->id]);

        $I->assertFalse(isset($contribution));
    }

    public function test(UnitTester $I)
    {
        $price = 100;

		$cart = new Cart(['type' => 'default']);
        if (!$cart->save()) throw new \Exception(print_r($cart->errors, 1));

        $cartItem = new CartItem(['unique_hash_id' => uniqid()]);
        $cartItem->attributes = [
            'name' => 'test item',
            'quantity' => 1,
            'unit_price' => $price,
        ];
        if (!$cartItem->save()) throw new \Exception(print_r($cartItem->errors, 1));
        $cart->link('cartItems', $cartItem);

        $referral = $this->createReferral();

        Yii::$app->session->set(ReferralFilter::SESSION_NAME, $referral->id);

        $order = new Order(['cart_id' => $cart->id]);
        if (!$order->save()) throw new \Exception(print_r($order->errors, 1));

        $contribution = ReferralContribution::findOne(['order_id' => $order->id]);

        $I->assertTrue(isset($contribution));
        $I->assertEquals($referral->id, $contribution->referral_id);
        $I->assertTrue(isset($contribution->referral));
        $I->assertEquals(0, $contribution->referral->getTotalContributionAmount());

        $user = $this->createUser();

        $order->billTo($user);
        $order->invoice->payManually($price);

        $order->markAsConfirmed();

        $I->assertEquals($price, $contribution->referral->getTotalContributionAmount());
        $I->assertEquals($price * 0.1, $contribution->commission_amount);
    }

    protected function createUser() {
        $model = new User;
        $model->registered_ip = '::1';
        $model->attributes = [
            'username' => 'test_user',
            'email' => 'test_user@example.com',
        ];
        $model->generateAuthKey();
        $model->setPassword('123456');
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
    }

    protected function createReferral() {
        $model = new Referral;
        $model->name = 'test';
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        return $model;
    }
}
