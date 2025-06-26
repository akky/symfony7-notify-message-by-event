<?php

// This file has been renamed to DynamicNotificationListener.php. The old class name and logic have been migrated.

namespace App\EventListener;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class DynamicNotificationListener.
 *
 * This listener injects a notification message into the response content.
 * It looks for a specific HTML element in the response and replaces it with a notification message.
 */
#[AsEventListener(event: KernelEvents::RESPONSE)]
final readonly class DynamicNotificationListener
{
    /**
     * DynamicNotificationListener constructor.
     *
     * @param string $targetElementId The ID of the HTML element where the notification message will be inserted
     */
    public function __construct(
        private LoggerInterface $logger,
        private ?Stopwatch $stopwatch = null,
        #[Autowire('%dynamic_notification_listener.target_element_id%')]
        private string $targetElementId = 'dynamic_notification',
    ) {
    }

    /**
     * Handles the response event to inject a notification message.
     *
     * @param ResponseEvent $event The response event
     */
    public function __invoke(ResponseEvent $event): void
    {
        if ($this->stopwatch instanceof Stopwatch) {
            $this->stopwatch->start(
                name: 'dynamic_notification_listener_process',
                category: 'notification_listener'
            );
        }

        try {
            // Skip if it's not the main request (e.g., internal forwards)
            if (!$event->isMainRequest()) {
                return;
            }

            $request = $event->getRequest();
            $now = new DateTimeImmutable();

            if (
                !$this->isAllowedIp($request)
                || !$this->isAllowedUserAgent($request)
                || !$this->isWithinDatePeriod($now)
                || !$this->isAllowedPath($request)
            ) {
                return;
            }

            $this->logger->info('DynamicNotificationListener: onResponseEvent called');

            $response = $event->getResponse();
            $content = $response->getContent();
            $placeholder = '<div id="'.$this->targetElementId.'"></div>';
            if (!is_string($content) || !str_contains($content, $placeholder)) {
                // some pages can have no notification message placeholder, so it is not an error
                // $this->logger->debug("no $targetElementId found in response content");
                return;
            }

            // Notification message: show class name dynamically
            $message = sprintf(
                'This is a notification message inserted by %s',
                self::class
            );

            $messageHtml = sprintf(
                '<div id="%s">%s</div>',
                $this->targetElementId,
                htmlspecialchars($message)
            );

            $content = str_replace(
                search: $placeholder,
                replace: $messageHtml,
                subject: $content
            );

            $response->setContent($content);
        } finally {
            // stop Stopwatch when available
            $this->stopwatch?->stop(name: 'dynamic_notification_listener_process');
        }
    }

    public function isAllowedIp(Request $request): bool
    {
        $allowedIps = ['127.0.0.1', '::1'];

        return in_array($request->getClientIp(), $allowedIps, true);
    }

    public function isAllowedUserAgent(Request $request): bool
    {
        $ua = $request->headers->get('User-Agent', '');

        return false === stripos((string) $ua, 'curl');
    }

    public function isWithinDatePeriod(DateTimeImmutable $now): bool
    {
        $start = new DateTimeImmutable('2025-06-01');
        $end = new DateTimeImmutable('2025-07-10 23:59:59');

        return $now >= $start && $now <= $end;
    }

    public function isAllowedPath(Request $request): bool
    {
        $excludedPaths = ['/login', '/about'];

        return !in_array($request->getPathInfo(), $excludedPaths, true);
    }
}
