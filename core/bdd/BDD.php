<?php
class BDD{
    private string $host;
    private string $user;
    private string $dbname;
    private string $mdp;
    private string $char;

    // permettre une seule co par user au serveur
    // singleton ->objet qui s'assure tt le tps que lui même n'existe qu'une seule fois
    // si pas d'instance on en fait 1 sinon on renvoit celle qui existe

    public $connection;

    // on va mettre le construct en privé, seule la methode statique pourra creer une instance

    private function __construct(){

    }

    // creation methode pr construct
    public static function getInstance(){
        
    }

}

?>