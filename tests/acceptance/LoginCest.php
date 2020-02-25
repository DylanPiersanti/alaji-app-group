<?php namespace App\Tests;
use App\Tests\AcceptanceTester;

class LoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToLoginUserSuccess(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimonet.alaji@gmail.com');
        $I->fillField('password', 'alaji');
        $I->click('Valider');
        $I->click('Mon profil');
        $I->see('Julien Simonet');
    }

    public function tryToLoginAdminSuccess(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@alaji.fr');
        $I->fillField('password', 'admin');
        $I->click('Valider');
        $I->see('ADMIN');
    }

    public function tryToLoginWrongEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jssimonet.alaji@gmail.com');
        $I->fillField('password', 'alaji');
        $I->click('Valider');
        $I->see('Email could not be found.');
    }

    public function tryToLoginWrongPassword(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimonet.alaji@gmail.com');
        $I->fillField('password', 'aalaji');
        $I->click('Valider');
        $I->see('Invalid credentials.');
    }
}
