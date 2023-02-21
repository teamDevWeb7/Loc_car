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

class Validator{
    private array $data;
    private array $errors;
    public function __construct(array $data){
        $this->data=$data;
    }

    /**
     * reçois un nombre inconnu de var -> traité en tableau
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

    public function email(string $key):self{
        if(!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL)){
            // si rentre ds condition c pas un email
            $this->addError($key, 'email');
        }
        return $this;
    }

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

    // 
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
     *
     * @return array|null
     */
    public function getErrors():?array{
        return $this->errors ?? null;
    }
}