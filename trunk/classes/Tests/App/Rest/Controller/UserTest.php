<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 22.04.2015
 * Time: 11:45
  */



namespace Tests\App\Rest\Controller;


use App\Test\RESTWebTestCase;

class UserTest extends RESTWebTestCase
{
    public function testPutActionForXMLExternalEntity()
    {
        $this->client->setHeader('Content-Type', 'application/xml');
        $result = $this->apiRequest('PUT', '/api/user/1', [], [], [], $this->getXMLExternalEntityPayload());
        $secretData = $result['invalidFields']['goodies']['goodies'];

        $this->assertTrue(mb_strpos($secretData, $this->secretContent, 0, 'utf-8') !== false,
            'There must be the following content in the response: "' .$this->secretContent . '"');
    }

    public function testPutActionForSqlInjection()
    {
        $userData = $this->fetchTestUser();
        $userData['first_name'] = '# test_user \' \"';
        $result = $this->apiRequest('PUT', '/api/user/' . $userData['id'], $userData);
        $response = $this->getResponse();

        $this->assertInternalType('array', $result, 'Invalid response data.');
        $this->assertGreaterThanOrEqual(400, $response->getStatus(), 'Response status code should be >= 400 (error)');
        $this->assertEquals($result['message'], 'Error', 'SQL error must be blind and do not output error description.');
    }

    protected function fetchTestUser()
    {
        //Find test user by username
        $users = $this->apiRequest('GET', '/api/user/?' . http_build_query(['username' => $this->username]));

        $this->assertTrue(count($users['data']) == 1, "Response should contain one user with username " . $this->username);

        $userData = $users['data'][0];

        $this->assertEquals($this->username, $userData['username'], 'Invalid user');

        return $userData;
        // Generate XML for user
        //$putData = XML::asXML($userData, 'user');
    }
}