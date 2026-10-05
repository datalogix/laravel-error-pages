<?php

return [
    'enabled' => true,

    // null = intercepts every HttpExceptionInterface; or restrict to
    // specific codes, e.g. [403, 404, 500].
    'codes' => null,

    // Show the exception message (e.g. abort(403, 'Upgrade your plan'))
    // instead of the translated description when it isn't empty.
    'show_exception_message' => false,
];
