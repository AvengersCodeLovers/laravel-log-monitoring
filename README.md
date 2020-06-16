## Documentation

To get started with **Monitoring Log Laravel**, use Composer to add the package to your project's dependencies:

```bash
   composer require avengers-code-lovers/laravel-log-monitoring
```

## Configuration

### Laravel 5.5+

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

### Laravel < 5.5:

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```php
'providers' => [
    // Other service providers...

    AvengersGroup\MonitoringServiceProvider::class

],
```

Set your session start to global middleware in app/Http/Kernel.php
```php
protected $middleware => [
    // Other global middleware...

    \Illuminate\Session\Middleware\StartSession::class,
];

protected $middlewareGroups => [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        //\Illuminate\Session\Middleware\StartSession::class,       // Comment this
        // ...
    ],
];
```

You will also need to add api_key chatwork bot and room sos for services your application utilizes. These credentials should be placed in your `config/services.php` configuration file, and use the key `chatwork`. For example:

```php
    'chatwork' => [
        'api_key' => env('CHATWORK_API_KEY'),
        'room_id_sos' => env('CHATWORK_API_ROOM_ID'),
        'role' => [
            'admin' => 'admin'
        ]
    ]
```
Add key in .env 

```
CHATWORK_API_KEY=xxxxx
CHATWORK_API_ROOM_ID=xxxxx
```

### Basic Usage Monitoring Log Laravel Application Error


Add Monitoring Log Laravel reporting to App/Exceptions/Handler.php:

```php
public function report(Exception $exception)
{
    app('monitoring')->sendExceptionToChatWork($exception);

    parent::report($exception);
}
```

### Basic Usage Monitoring Log Queue Error
Follow document in https://laravel.com/docs/master/queues#cleaning-up-after-failed-jobs

```php
/**
 * The job failed to process.
 *
 * @param  Exception  $exception
 * @return void
 */
public function failed(Exception $exception)
{
    app('monitoring')->sendExceptionToChatWork($exception);
}
```

### Advance Usage

Monitoring Log Laravel support reporting accept `$request` as param with supportable functions which come with a message with more infomation

Add Monitoring Log Laravel reporting to App/Exceptions/Handler.php:

```php
/**
 * Render an exception into an HTTP response.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Exception  $exception
 * @return \Illuminate\Http\Response
 */
public function render($request, Exception $exception)
{
    app('monitoring')->sendExceptionToChatWork($exception, $request);

    return parent::render($request, $exception);
}
```