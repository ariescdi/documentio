<?php

namespace MediaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MediaControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        //ON CREER UN CLIENT POUR VISUALISE L'APPLICATION
        $client = static::createClient();

        //ON AUTORISE LES REDIRECTIONS
        $client->followRedirects();

        //ON ARRIVE SUR LE SITE
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /");

        //ON VA SUR MEDIA
        $crawler = $client->click($crawler->selectLink('Médias')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /media");

        //ON VISUALISE LE DOCUMENT KDKDK
        $crawler = $client->click($crawler->selectLink('kdkdk')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /kdkdk");
        //$crawler = $client->click($crawler->selectLink('Télécharger kdkdk')->link());


        //ON SE REND SUR LA PAGE DE CONNEXION
        $crawler = $client->click($crawler->selectLink('Admin')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /login");

        //ON REMPLI LE FORMULAIRE DE CONNEXION
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => 'admin',
            '_password'  => 'admin',
        ));

        $client->submit($form);

        //ON SE REND SUR LA PAGE D'ADMINISTRATION
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin");

        //VERIFICATION DE L'AFFICHAGE DES STATS
        $this->assertGreaterThan(0, $crawler->filter('td:contains("user")')->count(), 'Missing element td:contains("user")');

        //ON SE REND SUR LA LISTE DES MEDIAS (ADMIN)
        $crawler = $client->click($crawler->selectLink('Liste des médias')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/media/");

        //ON VERIFIE QUE LE DOCUMENT "USER DOC" EST PRESENT
        $this->assertGreaterThan(0, $crawler->filter('td:contains("user doc")')->count(), 'Missing element td:contains("user doc")');

        //ON SE REND SUR LA PAGE D'AJOUT D'UN MEDIA
        $crawler = $client->click($crawler->selectLink('Ajouter un média')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/media/new");

        //ON REMPLI LE FORMULAIRE
        $form = $crawler->selectButton('mediabundle_media[submit]')->form(array(
            'mediabundle_media[name]'  => 'Test',
            'mediabundle_media[comment]' => 'Test',
        ));

        $form['mediabundle_media[category]']->select(3);
        $form['mediabundle_media[type]']->select(2);
        $form['mediabundle_media[isPublished]']->tick();
        $form['mediabundle_media[file]']->upload('/URL/twitter.png');

        $client->submit($form);

        // A VERIFIER POURQUOI APPELER LA PAGE /!\ /!\
        $crawler = $client->request('GET', '/admin/media/12');

        //ON VERIFIE QUE LE DOCUMENT EST PRESENT SUR LE SITE
        $crawler = $client->click($crawler->selectLink('Voir le document sur le site')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /media/Test");

        //ON EDITE CE NOUVEAU DOCUMENT
        $crawler = $client->click($crawler->selectLink('Editer')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET admin/media/{{id}}/edit");


        $form = $crawler->selectButton('mediabundle_media[submit]')->form(array(
            'mediabundle_media[name]'  => 'Test modification',
        ));

        $form['mediabundle_media[category]']->select(1);
        $form['mediabundle_media[type]']->select(1);
        $form['mediabundle_media[isPublished]']->tick();

        $client->submit($form);


        //SUPPRESSION DE CE NOUVEAU DOCUMENT
        $crawler = $client->click($crawler->selectLink('Supprimer')->link());
        $crawler = $client->request('GET', '/media/Test');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /media/Test");

    }
}
