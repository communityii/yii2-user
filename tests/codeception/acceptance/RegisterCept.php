<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('perform registration tests');
$I->amOnPage('/');
$I->see('Home');