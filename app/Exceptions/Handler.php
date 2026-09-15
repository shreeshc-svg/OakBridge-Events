<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // an upload bigger than the server's post_max_size: send admins back to the form with a message
        $this->renderable(function (PostTooLargeException $e, $request) {
            if ($request->routeIs('hero.update')) {
                $limit = ini_get('post_max_size');

                return redirect()->route('hero.edit')->withErrors([
                    'hero_image' => 'That file is larger than this server accepts (' . $limit . '). '
                        . 'Use a smaller video, or raise the upload limit in Hostinger hPanel > PHP Configuration.',
                ]);
            }
        });
    }
}
