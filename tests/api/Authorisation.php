<?php
class Authorisation
{
    public static function setAuthorisationToken(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '7f38212946ddbd7aadba90192887c5538328bb77bf3756504a1e538226fa8f51');
        $I->getClient()->getCookieJar()->set($cookie);
    }
}
