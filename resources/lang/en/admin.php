<?php

return [
    'title' => 'Auth Skin Slim',
    'nav' => 'Auth Skin Slim',
    'intro' => 'Options for the Auth API skin.slim field and compatibility with the Skin API plugin.',
    'relax_label' => 'Relax Skin API image dimension validation on upload',
    'relax_help' => 'When enabled, skin and cape PNG uploads through Skin API (site and API) use wide min/max dimensions instead of the exact values from Skin API settings. Use this if uploads fail with “invalid dimensions” (e.g. HD skins, 64×32, Alex/slim layouts). Does not change Skin API files.',
    'skin_api_disabled' => 'The Skin API plugin is disabled. This option only affects Skin API upload validation when Skin API is active.',
];
