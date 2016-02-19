<?php
require_once './private/config.php';
require_once './private/layout.php';
require_once './private/api.php';

API::useAPI(function(API $api){
    $layout=new LAYOUT($api);
    $erreurauth="";
    $erreurcrea="";
    
    if(!$api->estAuthentifier())
    {   
        $email = (string)$_REQUEST['email'];
        $motdepasse = (string)$_REQUEST['motdepasse'];
        $type = (string)$_REQUEST['type'];
        
        
        if(!empty($email) && !empty($motdepasse))
        {
            if($type=='CREATION')
            {
                $motdepasse_confirmation = (string)$_REQUEST['motdepasse_confirmation'];
                if($motdepasse_confirmation===$motdepasse)
                {
                    try
                    {
                        $api->API_compte_creer($email, $motdepasse);
                        header('location:MonCompte.php');
                        return;
                        
                    }
                    catch(Exception $ex)
                    {
                        $erreurauth= $ex->getMessage();
                    }
                }
                else
                {
                    $erreurcrea= 'Les mots de passe ne correspondent pas!';
                }
            }
            else
            {
                try
                {
                    $api->API_compte_authentifier($email, $motdepasse);
                }
                catch(Exception $ex)
                {
                    $erreurauth= $ex->getMessage();
                }
            }
        }
    }
    
    if(!$api->estAuthentifier())
    {
        $layout->writeHeader("Connection");
        
        ?>
            <h4>Connection à mon compte</h4>
            <form class="whitePanel center" action="Connection.php" accept-charset="utf-8" enctype="multipart/form-data">
                <?php if($erreurauth) { ?>
                    <p><?php $layout->safeWrite($erreurauth); ?></p>
                <?php }?> 
                <ul class="infosdetail">
                    <li>
                        <label for="email">Email :</label>
                        <input id="email" name="email" type="email" />
                    </li>
                    <li>
                        <label for="motdepasse">Mot de passe :</label>
                        <input id="motdepasse" name="motdepasse" type="password" />
                    </li>
                </ul>
                <br /><br />
                <input type="hidden" id="type" name="type" value="AUTHENTIFICATION" />
                <input type="submit" value="Se connecter" />
            </form>
            <h4>Connection à mon compte</h4>
            <form class="whitePanel center" action="Connection.php" accept-charset="utf-8" enctype="multipart/form-data">
                <?php if($erreurcrea) { ?>
                    <p><?php $layout->safeWrite($erreurcrea); ?></p>
                <?php }?> 
                <ul class="infosdetail">
                    <li>
                        <label for="email">Email :</label>
                        <input id="email" name="email" type="email" />
                    </li>
                    <li>
                        <label for="motdepasse">Mot de passe :</label>
                        <input id="motdepasse" name="motdepasse" type="password" />
                    </li>
                    <li>
                        <label for="motdepasse_confirmation">Mot de passe (confirmation) :</label>
                        <input id="motdepasse_confirmation" name="motdepasse_confirmation" type="password" />
                    </li>
                </ul>
                <br /><br />
                <input type="hidden" id="type" name="type" value="CREATION" />
                <input type="submit" value="Se connecter" />
            </form>
        <?php    
        $layout->writeFooter();
    }
    else
    {
        $layout->writeHeader("Connection");
    
        ?> <p class="whitePanel">Vous êtes maintenant authentifier.</p><?php
        
        $layout->writeFooter();
    }
});