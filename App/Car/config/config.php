<?php

// each fichier de config doit retourner un tableau associatif et peut autant déclarer des way d instancier une class que simplement declarer des value à enregister

// si on ne dit rien par defaut il fait autowire
return [
    // declare way d instancier
    CarModule::class =>\DI\autowire(),
    // value a garder en memoire
    'img.basePath'=>dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'public'. DIRECTORY_SEPARATOR. 'assets' . DIRECTORY_SEPARATOR.'imgs'. DIRECTORY_SEPARATOR
];




?>