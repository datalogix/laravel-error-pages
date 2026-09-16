<?php

// Intentionally NOT registered via composer.json (no PSR-4/classmap entry).
// class_exists() with autoloading enabled would make Composer's autoloader
// permanently define this class for the rest of the PHP process the first
// time ANY test calls class_exists('TALLKit\TALLKitServiceProvider') —
// including fallback-branch tests, since PHP cannot "undefine" a class.
// Loading it must stay confined to require_once inside the one isolated
// (#[RunInSeparateProcess]) test that needs it.

namespace TALLKit;

class TALLKitServiceProvider {}
