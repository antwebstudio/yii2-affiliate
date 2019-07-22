<?php

namespace ant\affiliate\migrations\rbac;

use yii\db\Schema;
use common\rbac\Migration;
use common\rbac\Role;

class M190722145055_permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\affiliate\controllers\DefaultController::className() => [
				'index' => ['View affiliate index page', [Role::ROLE_USER]],
			],
		];
		
		parent::init();
	}
	
	public function up()
    {
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
