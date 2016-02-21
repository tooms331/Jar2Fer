<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';
 
API::useAPI(function(API $api){
    $layout=new LAYOUT($api); 
    
    $layout->writeHeader("Mon compte");
    
    if(!$api->estAuthentifier())
    {
        ?><p class="whitePanel">Vous de vez vous authentifiez pour accéder à cette page : <a href="Connection.php">Connection</a>.</p><?php
    }
    else
    {
        $compte = $api->compteConnecte();
        ?> 
            <h4>Type de Compte :</h4>
            <form class="whitePanel contentPanel" action="Connection.php" accept-charset="utf-8" enctype="multipart/form-data">

                <p>Votre statut actuel est : <b><?php $layout->safeWrite($compte->statut) ?></b></p>
                <p>Vous pouvez faire une demande de modification de statut :</p>
                <br />
                <div style="padding-left:2em;">
                    <label><input checked="checked" type="radio" name="demande_statut" value="Panier" /> <u><b>Panier : </b></u></label>
                    <p style="padding-left:2em;">
                         Vous vous engagez chaque semaine à prendre notre "panier de la semaine", vous pouvez à tout moment rompre cet engagement, la rupture seras effective dès la semaine suivante. 
                    </p>
                    <br />
                    <label><input type="radio" name="demande_statut" value="LibreService" /> <u><b>Libre Service : </b></u></label>
                    <p style="padding-left:2em;"> 
                        Chaque semaine vous pouvez effectuer une commande entre le mercredi et le vendredi. 
                    </p>
                    <br />
                    <label><input type="radio" name="demande_statut" value="Premium" /> <u><b>Engagement Libre Service : </b></u></label>
                    <p style="padding-left:2em;">
                        Vous vous engagez chaque semaine à faire une commande d'un certain montant. En contre parti, les commandes vous sont ouverte dés le lundi jusqu'au vendredi.<br />
                        <label><i>Montant de votre engagement : <input type="number" name="Engagement" min="0" step="1" value="10" /> €</i></label>
                    </p>
                </div>
                <br />
                <input type="hidden" name="type" value="STATUT" />
                <input type="submit" value="Demander un changement de statut" />
            </form>
        <?php
    }
        
    $layout->writeFooter();
});