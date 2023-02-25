<?php
namespace Core\toaster;

use Core\toaster\Toast;
use Core\Session\SessionInterface;

// gere toast en s'appuant sur session
// toast parce qu'on veut qu'il na'apparaisse qu'une seule fois
// si session apparait toujours, on aurait pu mettre des conditions et tout mais ça aurait surchargé l'objet alors on en a fait un autre pr ces cas particuliers

// creer ext twig pr l'afficher sans que ce soit chiant

class Toaster{
    private const SESSION_KEY='toast';
    const ERROR=0;
    const SUCCESS=2;
    const WARNING=1;
    private SessionInterface $session;
    private Toast $toast;

    public function __construct(SessionInterface $session){
        $this->session=$session;
        $this->toast=new Toast();
    }

    // enregistre le toast
    public function makeToast(string $message, int $etat): void{
        switch($etat){
            case 0:
                $this->session->setArray(self::SESSION_KEY, $this->toast->error($message));
                break;
            case 1:
                $this->session->setArray(self::SESSION_KEY, $this->toast->warning($message));
                break;
            case 2:
                $this->session->setArray(self::SESSION_KEY, $this->toast->success($message));
                break;
        }
    }

    /**
     * retourne les toasts si en a
     *
     * @return array|null
     */
    public function renderToast():?array{
        // recup ts les toast enregistrés en session et on les stocke ds une var
        $toast=$this->session->get(self::SESSION_KEY);
        // on supprime les toasts de la session mais on les conserve ds la varv $toast pr que toast apparait qu'une fois et que si je recherge c est supprimé
        $this->session->delete(self::SESSION_KEY);
        return $toast;
    }

    /**
     * check si il y a toasts à afficher, retourne bool
     *
     * @return boolean
     */
    public function hasToast():bool{
        if($this->session->has(self::SESSION_KEY)&& sizeof($this->session->get(self::SESSION_KEY))>0){
            return true;
        }else{
            return false;
        }
    }
}





?>