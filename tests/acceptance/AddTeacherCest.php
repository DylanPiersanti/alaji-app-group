<?php namespace App\Tests;
use App\Tests\AcceptanceTester;

class AddTeacherCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToAddTeacherSuccess(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@alaji.fr');
        $I->fillField('password', 'admin');
        $I->click('Valider');
        $I->click('Ajouter un examinateur');
        $I->fillField('Email', 'jsimonet.alaji@gmail.com');
        $I->fillField('Password', 'alaji');
        $I->click('Submit');
        $I->see('Julien Simonet');
    }

    public function tryToAddTeacherWrongEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@alaji.fr');
        $I->fillField('password', 'admin');
        $I->click('Valider');
        $I->click('Ajouter un examinateur');
        $I->fillField('Email', 'jssimonet.alaji@gmail.com');
        $I->fillField('Password', 'alaji');
        $I->click('Submit');
        $I->see("L'email n'existe pas dans moodle.");
    }

    public function tryToAddNotTeacher(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@alaji.fr');
        $I->fillField('password', 'admin');
        $I->click('Valider');
        $I->click('Ajouter un examinateur');
        $I->fillField('Email', 'tonyblard55@gmail.com');
        $I->fillField('Password', 'alaji');
        $I->click('Submit');
        $I->see("L'email ne correspond pas à un examinateur.");
    }
}
