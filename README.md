# Symfony 7.3 + PHP 8.4 Minimal Demo: EventListener Response Rewrite & Modern Practices

This repository demonstrates a minimal Symfony 7.3 web application using PHP
8.4, focusing on:

- **Response rewriting with EventListener**: Dynamically modify HTTP responses
  using Symfony's event system.
- **Latest language and framework features**: Leverage PHP 8.4 and Symfony 7.3's
  newest syntax and attributes to reduce code and improve readability.
- **Code quality assurance**: Enforce high code quality with PHPStan and Rector.

## Purpose

The goal is to showcase how to build a clean, modern Symfony application with
the smallest possible codebase, while demonstrating best practices for
maintainability and static analysis.

## Key Features

- Uses an `EventListener` to rewrite the response content (e.g., inject a
  notification message based on request or environment).
- Makes use of constructor property promotion, attributes, and other PHP
  8.4/Symfony 7.3 features.
- Integrates PHPStan (static analysis) and Rector (automated refactoring) for
  code quality.
- Minimal dependencies and configuration for easy understanding and extension.

## Requirements

- PHP 8.4
- Symfony 7.3 (latest)

## Quick Start

1. **Install dependencies**

   ```bash
   composer install
   ```

2. **Run the Symfony server**

   ```bash
   symfony server:start
   ```
   Visit http://localhost:8000

3. **Run tests**

   ```bash
   php bin/phpunit
   ```

4. **Check code quality**

   ```bash
   vendor/bin/phpstan analyse src --level=max
   vendor/bin/rector process src
   ```

## Code Quality Tools

- **PHPStan**: Static analysis for catching bugs and enforcing strict types.
- **Rector**: Automated code upgrades and refactoring.

## GitHub Actions

A sample workflow is included for CI with PHPStan, PHPUnit, and PHP-CS-Fixer.

## About the Demo

- The main logic is in `src/EventListener/DynamicNotificationListener.php`.
- The listener rewrites the response to inject a notification message into a
  placeholder div, using the latest PHP and Symfony features.
- The codebase is intentionally minimal and readable, serving as a reference for
  modern Symfony development.

## Dynamic Notification Filtering

The demo's `DynamicNotificationListener` can filter when to show the
notification by inspecting several aspects of the HTTP request. This allows you
to control exactly when and to whom the notification appears.

### Sample Filter Methods

- **isAllowedIp(Request $request): bool**
  - Only show the notification for specific IP addresses (e.g., localhost for
    development/testing).
- **isAllowedUserAgent(Request $request): bool**
  - Hide or show the notification based on the visitor's User-Agent (e.g., hide
    for bots or CLI tools like curl).
- **isWithinDatePeriod(DateTimeImmutable $now): bool**
  - Only show the notification within a specific date/time window (e.g., for
    scheduled maintenance or campaigns).
- **isAllowedPath(Request $request): bool**
  - Exclude the notification from certain routes or pages (e.g., don't show on
    /login or /about).

You can easily extend or customize these filters to fit your needs, such as
filtering by user roles, cookies, headers, or any other request property.

## Integrating the Dynamic Notification in Your Templates

To display the dynamic notification injected by the
`DynamicNotificationListener`, you need to add a placeholder `<div>` to your
base Twig template (or any template you want the notification to appear in).

### 1. Add the Notification Placeholder

Insert the following line in your template, ideally just inside the `<body>` tag
or wherever you want the notification to appear:

```twig
<div id="dynamic_notification"></div>
```

- The listener will automatically inject the notification message into this div
  if the filters pass.
- If the div is empty, no notification will be shown and the CSS will not apply
  any styles.

**Example (base.html.twig):**

```twig
<body>
    <div id="dynamic_notification"></div>
    {# ...rest of your layout... #}
</body>
```

### 2. Customizing the CSS ID

If you want to use a different CSS id for the notification box (e.g.,
`my_custom_notification`), you need to:

1. **Change the div id in your template:**
   ```twig
   <div id="my_custom_notification"></div>
   ```
2. **Update the listener configuration:**
   - In `config/services.yaml`, set the `notification_div_id` argument for the
     listener:
     ```yaml
     App\\EventListener\\DynamicNotificationListener:
       arguments:
         $notificationDivId: "my_custom_notification"
     ```
3. **Update your CSS selector:**
   - In your CSS (e.g., `assets/styles/notification.css`), change the selector:
     ```css
     #my_custom_notification:not(:empty) {
       /* ...styles... */
     }
     ```

### 3. Notes

- The notification will only appear if the listener's filters allow it for the
  current request.
- You can safely include the placeholder div in all templates; it will remain
  empty if no notification is set.
- For advanced use, you can move or style the notification box as needed—just
  keep the id in sync between your template, listener config, and CSS.

---

Feel free to fork, adapt, or use as a reference for your own Symfony projects!
