<?php 
require_once './private/api.php';
header('Content-Type: application/javascript; charset:utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: 0');
header('Pragma: no-cache');
header('Access-Control-Allow-Origin: *');
?>

var apiClient =(function createApiClient() { 
    function call_core(commande,params)
    {
        var defered = $.Deferred();
        var postprom = $.post(
            "endpoint.php",
            {
                commande: commande,
                params: params
            }
        );
        postprom.done(function (result) {
            if(result.status == 'success') 
            {
                defered.resolve(result.value);
            }
            else
            {
                defered.reject(result.value);
            }
        });
        postprom.fail(function(err){
            try
            {
                var result =JSON.parse(err.responseText.trim());
                if(result.status == 'success') 
                {
                    defered.resolve(result.value);
                }
                else
                {
                    defered.reject(result.value);
                }
            }
            catch(ex)
            {
                defered.reject('erreur non gérée');
            }
        });
	
        return defered.promise();
    }
    return {<?php 
$APIRFLX = new ReflectionClass("API");
$APIMethods = $APIRFLX->getMethods(ReflectionMethod::IS_PUBLIC);
foreach($APIMethods as $APIMethod)
{   
    $mname=$APIMethod->getName();
    if(mb_strpos($mname,"API_")===0)
    {
        $mname= mb_substr($mname,4);
        $parametersList = implode(', ', array_map(function($param){return $param->getName();}, $APIMethod->getParameters()));
        echo "
        $mname : function($parametersList) {
            return call_core('$mname', arguments);
        },";
    }
}
?>

    };
})();