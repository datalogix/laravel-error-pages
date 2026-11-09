<?php

return [
    // Enable or disable the custom error pages entirely.
    'enabled' => true,

    // Restrict rendering to specific status codes, e.g. [403, 404, 500] or "403,404,500".
    // Leave as `null` to handle every HTTP exception.
    'codes' => null,

    // Show the exception message (e.g. abort(403, 'Upgrade your plan'))
    // instead of the translated description when it isn't empty.
    'show_exception_message' => false,
];
