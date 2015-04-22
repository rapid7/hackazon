<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 22.04.2015
 * Time: 13:56
  */



namespace Tests\App\Rest\Controller;


use App\Test\RESTWebTestCase;

class CategoryTest extends RESTWebTestCase
{
    public function testCategoryCollectionPageAndPerPage()
    {
        // Test correct parameters give correct response
        $result = $this->apiRequest('GET', '/api/category/?' . http_build_query(['page' => 1, 'per_page' => 10]));

        $this->assertInternalType('array', $result['data'], 'Invalid response');
        $this->assertGreaterThan(0, count($result['data']), 'The number of categories must be greater than zero.');

        // Test page parameter SQl injection
        $result = $this->apiRequest('GET', '/api/category/?' . http_build_query(['page' => '\"# \' asdf']));
        $response = $this->getResponse();

        $this->assertInternalType('array', $result, 'Invalid response data.');
        $this->assertGreaterThanOrEqual(400, $response->getStatus(), 'Response status code should be >= 400 (error)');
        $this->assertTrue(mb_strpos($result['message'], 'Database error', 0, 'utf-8') !== false, 'SQL error must have error details.');

        // Test per page SQL injection
        $result = $this->apiRequest('GET', '/api/category/?' . http_build_query(['per_page' => '4\"# \' asdf']));
        $response = $this->getResponse();

        $this->assertInternalType('array', $result, 'Invalid response data.');
        $this->assertGreaterThanOrEqual(400, $response->getStatus(), 'Response status code should be >= 400 (error)');
        $this->assertTrue(mb_strpos($result['message'], 'Database error', 0, 'utf-8') === false, 'SQL error must be blind and do not output error description.');
    }

    public function testCategoryPostActionXSS()
    {
        mt_srand();
        $vulnName = 'All Beauty <script>alert(' . mt_rand(0, 1000) . ');</script>';

        //Find several categories
        $categoryResponse = $this->apiRequest('GET', '/api/category/');

        $this->assertInternalType('array', $categoryResponse, 'Invalid response data.');
        $this->assertTrue(count($categoryResponse['data']) > 0, "Response should contain several categories.");

        $cat = $categoryResponse['data'][1];

        $cat['name'] = $vulnName;

        $categoryResponse = $this->apiRequest('PUT', '/api/category/' . $cat['categoryID'], $cat);
        $this->assertInternalType('array', $categoryResponse, 'Invalid response data.');

        $cat = $categoryResponse;
        $this->assertEquals($cat['name'], $vulnName, 'The name vulnerable to XSS should keep its name with script tag.');
    }
}