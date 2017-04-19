<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/messages');

        $this->assertContains('messages', $client->getResponse()->getContent());
    }
    
    public function testCreate()
    {
        $client = static::createClient();
        
        $crawler = $client->request('POST',
                '/messages',
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                '{"message":{"title":"new title", "content":"new content"}}'
        );
        $response = $client->getResponse();
        $this->assertSame(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['message']['title'], 'new title');
        $this->assertSame($data['message']['content'], 'new content');
    }
    
    
    public function testCreateAndUpdate()
    {
        $client = static::createClient();
        
        $crawler = $client->request('POST',
                '/messages',
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                '{"message":{"title":"new title", "content":"new content"}}'
        );
        $response = $client->getResponse();
        
        $data = json_decode($response->getContent(), true);
        
        
        $id = $data['message']['id'];
        $crawler = $client->request('PUT',
                '/messages/'.$id,
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                '{"message":{"title":"updated title", "content":"updated content"}}'
        );
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['message']['title'], 'updated title');
        $this->assertSame($data['message']['content'], 'updated content');
        
    }
    
    
}
