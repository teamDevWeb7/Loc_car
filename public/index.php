<?php
// dirname pr chemin 
// __dir__ pr dossier actuel
require dirname(__DIR__)."/view/header.php";

// aller chercher router
// pour pas erreur chargement require once
require_once dirname(__DIR__)."/core/router.php";
// pour tester le router : http://localhost/loc_car/public/?page=addVehicule


?>



<?php
require dirname(__DIR__)."/view/footer.php";

?>
