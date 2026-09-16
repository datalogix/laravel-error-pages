<?php

return [
    'back_home' => 'Back to home',

    400 => [
        'title' => "That request didn't quite work",
        'description' => "Something about it wasn't understood. Mind going back and trying again?",
    ],

    401 => [
        'title' => 'Please sign in to continue',
        'description' => "You'll need to sign in before you can see this page.",
    ],

    403 => [
        'title' => "This page isn't available to you",
        'description' => "You don't have permission to view this. If that doesn't sound right, let us know.",
    ],

    419 => [
        'title' => 'Your session has expired',
        'description' => 'For your security we signed you out after a while. Please refresh the page and try again?',
    ],

    429 => [
        'title' => 'Slow down a little',
        'description' => "You've made too many requests in a short time. Give it a moment and try again.",
    ],

    500 => [
        'title' => 'Something went wrong on our end',
        'description' => "We've already been notified and we're looking into it. Please try again in a bit.",
    ],

    502 => [
        'title' => "We're having trouble connecting",
        'description' => "We couldn't reach our servers just now. Please try again shortly.",
    ],

    503 => [
        'title' => "We'll be right back",
        'description' => "We're doing a bit of maintenance. Please check back soon.",
    ],

    504 => [
        'title' => 'That took too long',
        'description' => "The server didn't respond in time. Please try again.",
    ],

    'default' => [
        'title' => "We couldn't find that page",
        'description' => 'It may have been moved, renamed, or the link might be outdated.',
    ],
];
