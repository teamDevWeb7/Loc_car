<?php
// valider données ex tel champs requis etc

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