<?php
class Authorisation
{
    public static function setAuthorisationTokenCustomer(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '5b2c303a-43a9-4abf-a51a-b2d109527c7d');
        $I->getClient()->getCookieJar()->set($cookie);
    }

    public static function setAuthorisationTokenEmployee(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '4e114242-a7ee-42d0-9c4e-fe25e89a5321');
        $I->getClient()->getCookieJar()->set($cookie);
    }

    public static function setAuthorisationTokenShipper(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '0ee1345a-2a0c-497b-8c6e-4b25507fb931');
        $I->getClient()->getCookieJar()->set($cookie);
    }

    public static function setAuthorisationTokenPublic(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '8940207e-eca3-479f-8231-6c7ddf038a78');
        $I->getClient()->getCookieJar()->set($cookie);
    }
}
