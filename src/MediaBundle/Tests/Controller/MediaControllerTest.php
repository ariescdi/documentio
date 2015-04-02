<?php

namespace MediaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MediaControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        
        // Create a new client to browse the application
        $client = static::createClient();
        
        // basic index test
        $url = '/';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET $url");
        
        // admin must redirect to login
        $url = '/admin/';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Status must be 302 (redirect) or GET $url when unlogged");
        
        // follow redirect & login
        $crawler = $client->followRedirect();
        $form = $crawler->selectButton('_submit')->form(array(
            '_username' => 'sylvain',
            '_password' => 'sgarcia',
        ));
                
        $crawler = $client->submit($form);
        $this->assertEquals($url . 'login_check' , $client->getRequest()->getPathInfo());
        
        $response = $client->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode(), "Unexpected HTTP status code for GET $url/login_check");
        
        $crawler = $client->followRedirect();
        $this->assertEquals($url, $client->getRequest()->getPathInfo());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET $url");
        $this->assertEquals($url, $client->getRequest()->getPathInfo());
        
        
        
        // Create new media
        //$url = '/admin/media/new';
        //$crawler = $client->request('GET', $url);
        //$this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET $url");
        /*
        // Create a new entry in the database
        $crawler = $client->request('GET', '/media/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /media/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'mediabundle_media[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'mediabundle_media[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
         
         */
    } 
}
