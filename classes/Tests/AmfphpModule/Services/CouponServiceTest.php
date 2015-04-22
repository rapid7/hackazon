<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 21.04.2015
 * Time: 20:16
  */



namespace Tests\AmfphpModule\Services;


use App\Test\WebTestCase;
use Symfony\Component\BrowserKit\Response;

class CouponServiceTest extends WebTestCase
{
    public function testFetchesValidCoupon()
    {
        $couponName = "MONDAY";

        $this->ensureLoggedIn();
        $this->requestJsonAmf("CouponService", "useCoupon", [$couponName]);
        /** @var Response $response */
        $response = $this->client->getResponse();

        $this->assertEquals('application/json', $response->getHeader('Content-Type'), "Incorrect content type.");

        $content = json_decode(''.$response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid response data.');
        $this->assertEquals($couponName, $content['coupon'], 'Invalid coupon name.');
    }

    public function testSQLInjection()
    {
        $couponName = "# SUNDAY \" '";

        $this->ensureLoggedIn();
        $this->requestJsonAmf("CouponService", "useCoupon", [$couponName]);
        /** @var Response $response */
        $response = $this->client->getResponse();

        $this->assertTrue(strpos($response->getHeader('Content-Type'), 'application/json') !== false, "Incorrect content type (should contain 'application/json').");

        $content = json_decode(''.$response->getContent(), true);
        $this->assertInternalType('array', $content, 'Invalid response data.');
        $this->assertGreaterThanOrEqual(400, $response->getStatus(), 'Response status code should be >= 400 (error)');
        $this->assertTrue($content['error'], 'Should contain a error mark in the response.');
        $this->assertEquals($content['message'], '', 'SQL error must be blind and do not output error description.');
    }
}