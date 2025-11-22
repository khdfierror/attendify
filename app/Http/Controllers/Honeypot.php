<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Honeypot extends Controller
{
    public function __invoke(string $path = '')
    {
        // Load the array of honeypot paths from the configuration.
        $honeypotPathsArray = config('honeypot.paths', []);

        // Turn the path array into a regex pattern.
        $honeypotPathsRegex = '/^(' . str_replace(['.', '/'], ['\.', '\/'], implode('|', $honeypotPathsArray)) . ')/i';

        $extension = Str::of($path)->afterLast('.');

        $blockedExtensions = ['php', 'html'];

        $containBlockedExtension = Str::of($extension)
            ->contains($blockedExtensions, ignoreCase: true);

        // If the user tries to access a honeypot path or a file with the php extension, fail with the teapot code.
        if (preg_match($honeypotPathsRegex, $path) || $containBlockedExtension) {
            abort(Response::HTTP_I_AM_A_TEAPOT);
        }

        // Otherwise just display our regular 404 page.
        abort(Response::HTTP_NOT_FOUND);
    }
}
