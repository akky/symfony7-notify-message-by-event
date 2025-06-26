<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        self::assertResponseIsSuccessful();
    }
}
