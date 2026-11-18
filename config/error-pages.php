<?php

return [
    // Enable or disable the custom error pages entirely.
    'enabled' => true,

    // Restrict rendering to specific status codes, e.g. [403, 404, 500] or "403,404,500".
    // Leave as `null` to handle every HTTP exception.
    'codes' => null,

    // Show the exception message (e.g. abort(403, 'Upgrade your plan'))
    // instead of the translated description when it isn't empty.
    // Use `true` or `false` (also `1` or `0`) or specific status codes, e.g. [403] or "402,403".
    'show_exception_message' => false,

    // Show the message given to a policy or gate denial (e.g. Response::deny('...'))
    // instead of the translated description. Plain `false` denials keep the translated copy.
    // Use `true` or `false` (also `1` or `0`) or specific status codes, e.g. [403] or "403,404".
    'show_authorization_message' => true,
];
