<!-- deux comportements -->
<!-- afficher page puis afficher avec modifs -->
<!-- doit savoir si arrive premiere fois sur page ou si soumet -> POST -->


<!-- pour recup imgs on fait exister superglob '$_FILES' va donner name, type, tmp name(localisation lors de l'execution seulement) size, code erreur->si 0 tout va bien
on doit donc recup l'image-->
<?php
// si peut recup var marque c est que le form a ete posté
if(isset($_POST['marque'])){
    // check ce qu'il y a dans post pr deter ce qu'on en fait
    // var_dump($_POST);
    // var_dump($_FILES);


    // recup de l'img
    $imgData= $_FILES['img'];//img pck input s'appelle img
    // on commenece par check si y a pas d'erreur
    if($imgData['error']==0){
        // on donne le chemin pr enregistrer
        $path=dirname(__DIR__)."/public/assets/imgs/";
        // on prend le chemin temporaire
        $temp=$imgData['tmp_name'];
        // enfin besoin name de l'img pr differenciation des autres+besoin pr chemin
        // on ne modif pas $name direct car on aura besoin du name correct pour la bdd
        $name=$imgData['name'];
        $path.=$name;
        // on peut donc demander a php de bouger le fichier->fonction deja existante de php
        // 1er para ou elle est, 2eme para, ou elle va
        move_uploaded_file($temp, $path);
        // on test + message ok tout va bien
        // echo 'image enregistrée';

        // apres les tests on fait une redirection
        header('Location: ?page=home');


    }else{
        echo 'une erreur s\'est produite';
    }

}else{
    // comme pas de reception de 'marque' on veut juste aller sur la page pour la premiere fois
    require dirname(__DIR__)."/view/addVehicule.php";
}