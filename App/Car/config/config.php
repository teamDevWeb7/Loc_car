<?php

return [
    CarModule::class =>\DI\autowire(),
    'img.basePath'=>dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'public'. DIRECTORY_SEPARATOR. 'assets' . DIRECTORY_SEPARATOR.'imgs'. DIRECTORY_SEPARATOR
];




?>