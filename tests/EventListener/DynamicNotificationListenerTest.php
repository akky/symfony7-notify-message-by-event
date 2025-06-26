<?php

namespace App\Tests\EventListener;

use App\EventListener\DynamicNotificationListener;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class DynamicNotificationListenerTest extends TestCase
{
    private function getListener(): DynamicNotificationListener
    {
        // Logger and Stopwatch are not used in the tested methods
        return new DynamicNotificationListener(
            $this->createMock(LoggerInterface::class),
            null
        );
    }

    public function testIsAllowedIp(): void
    {
        $listener = $this->getListener();
        $request = Request::create('/', Request::METHOD_GET, [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $this->assertTrue($listener->isAllowedIp($request));
        $request = Request::create('/', Request::METHOD_GET, [], [], [], ['REMOTE_ADDR' => '8.8.8.8']);
        $this->assertFalse($listener->isAllowedIp($request));
    }

    public function testIsAllowedUserAgent(): void
    {
        $listener = $this->getListener();
        $request = Request::create('/', Request::METHOD_GET, [], [], [], []);
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $this->assertTrue($listener->isAllowedUserAgent($request));
        $request->headers->set('User-Agent', 'curl/7.68.0');
        $this->assertFalse($listener->isAllowedUserAgent($request));
    }

    public function testIsWithinDatePeriod(): void
    {
        $listener = $this->getListener();
        $inPeriod = new DateTimeImmutable('2025-06-15');
        $before = new DateTimeImmutable('2025-05-31');
        $after = new DateTimeImmutable('2030-07-11');
        $this->assertTrue($listener->isWithinDatePeriod($inPeriod));
        $this->assertFalse($listener->isWithinDatePeriod($before));
        $this->assertFalse($listener->isWithinDatePeriod($after));
    }

    public function testIsAllowedPath(): void
    {
        $listener = $this->getListener();
        $request = Request::create('/home');
        $this->assertTrue($listener->isAllowedPath($request));
        $request = Request::create('/login');
        $this->assertFalse($listener->isAllowedPath($request));
        $request = Request::create('/about');
        $this->assertFalse($listener->isAllowedPath($request));
    }
}
