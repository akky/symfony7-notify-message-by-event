<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Demo Home');
    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/about');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'About');
    }
}
