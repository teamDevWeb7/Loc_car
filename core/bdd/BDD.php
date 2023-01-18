<?php
namespace Core\bdd;
// pour pdo antishlash ou
// use PDO
// use PDOException
class BDD{
    // seule utilité de l'objet est de s assurer qu'il n y ai qu'une co à la bdd en même tps
    private string $host;
    private string $user;
    private string $dbname;
    private string $mdp;
    private string $char;

    // permettre une seule co par user au serveur
    // singleton ->objet qui s'assure tt le tps que lui même n'existe qu'une seule fois
    // si pas d'instance on en fait 1 sinon on renvoit celle qui existe

    // je mets un anti slash car endroit fichier a bouger a cause composer, donc antishlash pour ramener àà la racine de php ou se trouve PDO
    public \PDO $connection;

    public static BDD $instance;

    // on va mettre le construct en privé, seule la methode statique pourra creer une instance

    private function __construct(string $host, string $dbname, string $user, string $mdp, string $char){
        $this->host=$host;
        $this->dbname=$dbname;
        $this->user=$user;
        $this->mdp=$mdp;
        $this->char=$char;
        try {
            //connection appartient à l'instance
            $this->connection=new \PDO("mysql:host={$this->host};
                                        dbname={$this->dbname};
                                        charset={$this->char}", 
                                        $this->user, $this->mdp);
        }catch(\PDOException $e){
            echo "[ERREUR]=>{$e->getMessage()}";
            die;
        }
    }
    // static->appartient à l'objet
    // creation methode pr construct
    public static function getInstance(string $host, string $dbname, string $user, string $mdp, string $char='utf8'){
        // on verif qu'il n'y en ai pas et qu'elle soit vide : vide pck si jamais instanciée existe mais vide
        // avec self on fait mention à l'objet et non pas à l'instance->$this
        if(!isset(self::$connection) or empty(self::$connection)){
            self::$instance=new BDD($host, $dbname, $user, $mdp, $char);
        }
        // qu'il n'y en ai pas on crée sinon on revoit ce qu'y a deja
        return self::$instance;
    }
    // on donne à la fonction getInstance les infos de co, elle va les donner au constructor, construc privé on ne donne pas direct

    // design pattern singleton->methode static qui recup instance si existe sinon créé private constructor

}

?>