<?php

namespace App\Helpers\PurifyRequest;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Stevebauman\Purify\Facades\Purify;
class PurifyRequestHelper
{
    private $arrDataRequests       = []; #se almacenan datos que llegan en el request
    private $arrPurifiedDataXSS    = []; #se almacenan datos purificados de XSS
    private $arrPossibleThreatsSql = []; #se almacenan las posibles amenazas SQL
    private $arrPossibleThreatsXss = []; #se almacenan las posibles amenazas XSS
    private $arrWhiteListInputs    = ['_token']; #se almacenan los inputs de los formularios que no pasan por las validaciones
    private $arrBlackListString    = ["/drop(\s)/i", "/insert(\s)/i", "/javascript/i", "/script/i", "/alert/i", "/select(\s)/i","/update(\s)/i", "/show(\s)/i", "/table(\s)/i",
                                      "/create(\s)/i","/--/","/union(\s)/i","/into(\s)/i","/\.\*/",
                                      "/((\"|')?[0-9](\"|')?(\s){0,})\=((\s){0,}(\"|')?[0-9](\"|')?(\s){0,})/"];

    /**
     * @author jefferson starling barrera cardona
     * @description -> metodo principal del trait que recibe el request
     * de la petición para sanearlo de XSS y posible injection SQL
     * @param array $request
     * @return void
    **/
    public function voidPurifyRequest($request){
        $response = array();
        try{
            $arrResponse = [];
            if(!empty($request)){
                $this->arrDataRequests = $request;
                $this->voidPurifyXss();
                $this->voidPurifySqlInjection();
                if(!empty($this->arrPossibleThreatsSql) || !empty($this->arrPossibleThreatsXss)){
                    $arrResponse = [
                        'request'          => $this->arrDataRequests,
                        'requestXSS'       => $this->arrPossibleThreatsXss,
                        'requestInjection' => $this->simplifyPossibleThreat($this->arrPossibleThreatsSql)
                    ];
                    $user = JWTAuth::user();
                    Log::warning('Intento de inyeccion SQL con el usuario: ' . $user->user . ', ip_client: ' .  request()->ip() .', Descripcion: '.json_encode($arrResponse));
                    $response = array('valid' => false, 'type' => 'inyection', 'data' => $arrResponse);
                }else{
                    $response = array('valid' => true);
                }
            } else{
                $response = array('valid' => true);
            }
        }catch (\Throwable $th){
            Log::error($th->getMessage() . ' -> function voidPurifyRequest() PurifyTrait');
            $response = array('valid' => false, 'type' => 'error');
        }

        return $response;
    }

    /**
     * @author jefferson starling barrera cardona
     * @description -> metodo que procesa el request  verifica
     * por medio de la libreria Purify  la existencia de algun string relacionado con XSS
     * @param null
     * @return boolean
    **/
    private function voidPurifyXss(){
        foreach($this->arrDataRequests as $arrDataRequestKey => $arrDataRequestValue){
            if(!in_array($arrDataRequestKey,$this->arrWhiteListInputs)){
                if(is_null($arrDataRequestValue)){ $arrDataRequestValue = ""; }
                if(is_int($arrDataRequestValue)){ $arrDataRequestValue = (string)$arrDataRequestValue.""; }
                if($arrDataRequestValue !== Purify::clean($arrDataRequestValue) && !is_numeric($arrDataRequestValue)){
                    $this->arrPossibleThreatsXss[$arrDataRequestKey][] = $arrDataRequestValue;
                }
                $this->arrPurifiedDataXSS[$arrDataRequestKey] = Purify::clean($arrDataRequestValue);
            }
        }
        return true;
    }

    /**
     * @author jefferson starling barrera cardona
     * @description -> metodo que procesa el request ya limpio de posible XSS y verifica
     * por medio de expresiones regulares la existencia de algun string relacionado con SQL
     * @param null
     * @return boolean
    **/
    private function voidPurifySqlInjection(){
        foreach($this->arrPurifiedDataXSS as $arrPurifiedDataXSSKey => $arrPurifiedDataXSSValue){
            if(!in_array($arrPurifiedDataXSSKey, $this->arrWhiteListInputs)){
                foreach($this->arrBlackListString as $arrBlackListStringValue){
                    if(is_null($arrPurifiedDataXSSValue)){ $arrPurifiedDataXSSValue = ""; }
                    if(preg_match_all($arrBlackListStringValue, $arrPurifiedDataXSSValue, $arrThreats) && !is_numeric($arrPurifiedDataXSSValue)){
                         $this->arrPossibleThreatsSql[$arrPurifiedDataXSSKey][] = $arrThreats;
                    }

                }
            }
        }
        return true;
    }


    /**
     * @author jefferson starling barrera cardona
     * @description -> metodo simplifica la respusta de las expresiones regulares para su
     * facil utilización en el frontend
     * @param array -> array con posibles amenazas de SQL injection
     * @return array
    **/
    private function simplifyPossibleThreat(array $arrPossibleThreatsSql){
        $arrSimplified = [];
        foreach( $arrPossibleThreatsSql as $possibleThreatsKeyLevelOne => $possibleThreatsValueLevelOne){
            foreach($possibleThreatsValueLevelOne as $possibleThreatsValueLevelTwo){
                foreach($possibleThreatsValueLevelTwo as $possibleThreatsValueLevelThree){
                    if(!empty(trim($possibleThreatsValueLevelThree['0']))){
                        $arrSimplified[$possibleThreatsKeyLevelOne][]=$possibleThreatsValueLevelThree['0'];
                    }
                }
            }
        }
        return $arrSimplified;
    }
}
