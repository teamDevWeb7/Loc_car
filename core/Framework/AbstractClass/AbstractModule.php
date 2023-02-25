<?php
namespace core\Framework\AbstractClass;


/**
 * un moduel represente un ensemble de pages qui sont chargées d'une responsabilité particulière
 * ex: CarModule est chargé à tout ce qui touche au vehicule: crud etc
 * each module que l on souhaite charger ds l application doit être declarée dans $modules dans /publi/index.php
 */
abstract class AbstractModule{

    /**
     * chemin du fichier de configuration destiné à PHP DI
     */
    public const DEFINITIONS=null;



}







?>