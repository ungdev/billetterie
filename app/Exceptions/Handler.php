<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $this->sendErrorToSlack($exception);
        parent::report($exception);
    }

    public function sendErrorToSlack(Exception $e)
    {
        $url = Config::get('services.slack.exception_webhook');
        if ($url) {
            $parsedUrl = parse_url($url);

            $this->client = new \GuzzleHttp\Client([
                'base_uri' => $parsedUrl['scheme'] . '://' . $parsedUrl['host'],
            ]);

            $payload = json_encode(
                [
                    'text' => get_class($e) . ': ' . $e->getMessage() . ' (' . $e->getCode() . ')',
                    'username' => 'Exception Billetterie',
                    'icon_emoji' => ':rotating_light:',
                    'attachments' => [
                        [
                            'title' => 'File',
                            'text' => $e->getFile() . ':' . $e->getLine(),
                            'color' => '#d80012',
                        ],
                        [
                            'title' => 'Trace',
                            'text' => $e->getTraceAsString(),
                            'color' => '#d80012',
                        ],
                    ],
                ]);
            $response = $this->client->post($parsedUrl['path'], ['body' => $payload]);
            return $response;
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
