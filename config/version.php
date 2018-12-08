<?php

return [
    'number' => @file_get_contents(base_path('VERSION')) ?: '0.0.0',
    'commit' => trim(`git rev-parse --short HEAD`),
    'timestamp' => (int) `git show -s --format=%ct HEAD`,
];
