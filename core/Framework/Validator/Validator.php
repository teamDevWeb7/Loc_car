<?php
// valider données ex tel champs requis etc


// ce qu'on recup du form dans une var(data)-> $validator=new Validator (data)
// on crée une var erreur -> sur la var $validator on use sa méthode required
// ->required= a chaque clé du tab on verif qu'elle soit remplie
// si une clé pas remplie on use la méthode add Error qui avec le Validator Erreur genere un mess d'aerreur en string
// apres required on ->getErrors renvoie True ou False
// si False on continue le script
//si True on use la methode du Validator error pour afficher le champs manquant

namespace Core\Framework\Validator;

use Doctrine\ORM\EntityRepository;

class Validator{
    private array $data;
    private array $errors;
    /**
     * enregistre le tableau de données à valider
     *
     * @param array $data tableau de données(usually il s agit du tab récupéré par $request->getParsedBody)
     */
    public function __construct(array $data){
        $this->data=$data;
    }

    /**
     * reçois un nombre inconnu de var -> traité en tableau mais pas tableau
     * liste les index attendus et obligatoires ds le tab de données
     * @param string ...$keys liste de chaine de carac, "..."->précise attend un nbr indéfinit de values
     * comme ... je ne peux pas donner d autres var à la suite
     *
     * @return self
     */
    public function required(string ...$keys):self{
        foreach($keys as $key){
            // function attend string et tableau à manipuler -> renvoie true ou false -> check si key existe ou non ds tab
            // la condition check qu'aucun champs ne soit vide ou pas rempli
            if(!array_key_exists($key, $this->data) || $this->data[$key]==='' || $this->data[$key]===null){
                $this->addError($key, 'required');
            }
        }



        return $this;
    }

    /**
     * s assure que le mail est valide
     *
     * @param string $key
     * @return self
     */
    public function email(string $key):self{
        // filter_var fonction native permet de check la conformité d'une value en fonction d'un filtre(cf php manual)
        if(!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL)){
            // si rentre ds condition c pas un email
            $this->addError($key, 'email');
        }
        return $this;
    }

    /**
     * s assure que le nbr de caractère d'une string soit bien compris entre un $min et un $max
     *
     * @param string $key
     * @param integer $min
     * @param integer $max
     * @return self
     */
    public function strSize(string $key, int $min, int $max):self{
        if(!array_key_exists($key, $this->data)){
            return $this;
            // si cle existe pas ds tab on return this
        }
        $length=mb_strlen($this->data[$key]);
        if($length < $min){
            $this->addError($key, 'strMin');
        }
        if($length > $max){
            $this->addError($key, 'strMax');
        }
        return $this;
    }

    /**
     * s'assure que le champs saisit possède la même valeur que son champs de confirmation
     * si la value de $key est "mdp", le champs de confirmation doit absolument se nommer "mdp_confirme"
     *
     * @param string $key
     * @return self
     */
    public function confirme(string $key):self{
        $confirme=$key . '_confirme';
        if(!array_key_exists($key, $this->data)){
            return $this;
        }
        if(!array_key_exists($confirme, $this->data)){
            return $this;
        }
        if($this->data[$key] !== $this->data[$confirme]){
            $this->addError($key, 'confirme');
        }
        
        return $this;
    }

    /**
     * s assure qu'une value soit unique en BDD
     *
     * @param string $key index du tableau
     * @param EntityRepository $repo doctrine's repositorie de l element à check
     * @param string $field champs à check en BDD (default vaut nom)
     * @return self
     */
    public function isUnique(string $key, EntityRepository $repo, string $field='nom'):self{
        // recup entities du repo
        $all=$repo->findAll();
        // creer nom method usable pr recup la value(ex: si $field='model' alors $method='getModel')
        $method='get'.ucfirst($field);
        // on boucle sur tous les enregistrements de la bdd
        foreach($all as $item){
            // verif insensible a la casse
            // on check si la value saisit par le user correspond à une valeur existante en BDD sans tenir compte des accents
            // si existe on soulève une erreur
            if(strcasecmp($item->$method(), $this->data[$key])===0){
                $this->addError($key, 'unique');
                break;
            }
        }

        return $this;
    }

    /**
     * enregistre ds tab les erreurs, fonctionne avec le ValidatorError
     *
     * @param string $key
     * @param string $rule
     * @return void
     */
    private function addError(string $key, string $rule):void{
        if(!isset($this->errors[$key])){
            $this->errors[$key]=new ValidatorError($key, $rule);
        }
    }

    /**
     * soit on renvoit un tab rempli des erreurs soit on renvoit rien puisque pas d'erreurs
     * doit etre appelé apres les autres methodes
     *
     * @return array|null
     */
    public function getErrors():?array{
        return $this->errors ?? null;
    }
}