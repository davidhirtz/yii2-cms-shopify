<?php

/**
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\tests\functional;

use davidhirtz\yii2\cms\shopify\tests\support\FunctionalTester;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\modules\admin\data\ProductActiveDataProvider;
use davidhirtz\yii2\shopify\modules\admin\widgets\grids\ProductGridView;
use davidhirtz\yii2\skeleton\codeception\fixtures\UserFixtureTrait;
use davidhirtz\yii2\skeleton\codeception\functional\BaseCest;
use davidhirtz\yii2\skeleton\models\User;
use davidhirtz\yii2\skeleton\modules\admin\widgets\forms\LoginActiveForm;
use Yii;

class AuthCest extends BaseCest
{
    use UserFixtureTrait;

    public function checkIndexAsGuest(FunctionalTester $I): void
    {
        $I->amOnPage('/admin/product/index');

        $widget = Yii::createObject(LoginActiveForm::class);
        $I->seeElement("#$widget->id");
    }

    public function checkIndexWithoutPermission(FunctionalTester $I): void
    {
        $this->getLoggedInUser();

        $I->amOnPage('/admin/product/index');
        $I->seeResponseCodeIs(403);
    }

    public function checkIndexWithPermission(FunctionalTester $I): void
    {
        $user = $this->getLoggedInUser();
        $auth = Yii::$app->getAuthManager()->getPermission(Product::AUTH_PRODUCT_UPDATE);
        Yii::$app->getAuthManager()->assign($auth, $user->id);

        $I->amOnPage('/admin/product/index');

        $widget = Yii::$container->get(ProductGridView::class, [], [
            'dataProvider' => Yii::createObject(ProductActiveDataProvider::class),
            'searchUrl' => '/',
        ]);

        $I->seeElement("#$widget->id");
    }

    protected function getLoggedInUser(): User
    {
        $user = User::find()->one();

        $webuser = Yii::$app->getUser();
        $webuser->loginType = 'test';
        $webuser->login($user);

        return $user;
    }
}
