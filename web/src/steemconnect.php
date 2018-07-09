<?php
session_start();
class SteemConnect{
    public $url = "https://steemconnect.com/api/me";
    public $response = [];
    public $postFields = [];
    public $headers = array(
        "Content-Type: application/json",
        "cache-control: no-cache"
    );

    function __construct()
    {
        $this->sessionValidation();
        if ($this->hasToken()) $this->headers[] = "authorization: ".$_SESSION["code"];
    }
    public function vote($voteOptions){
        $this->postFields = $voteOptions;
    }
    public function HttpRequest($requestMethod, SteemConnect $request){
        if ($requestMethod == "POST" || $requestMethod == "post"):
            $curl = curl_init();
            curl_setopt_array($curl,array(
                CURLOPT_URL => $request->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request->postFields),
                CURLOPT_HTTPHEADER => $request->headers,
            ));
            $response = curl_exec($curl);
            $errors = curl_error($curl);
            curl_close($curl);
            if ($errors):
                return $errors;
            else: return $response;endif;
        endif;
    }
    public function HttpResponse(SteemConnect $steemConnect){
        if ($steemConnect->hasToken()){
            $result = $steemConnect->HttpRequest("POST",$steemConnect);
            $result = json_decode($result);
            return $result;
        }else{
            $steemConnect->endSession();
            $steemConnect->response["success"] = false;
            $steemConnect->response["message"] = "Expire user session";
            return false;
        }
    }
    public function getUser(SteemConnect $steemConnect){
        return $steemConnect->HttpResponse($steemConnect)->user;
    }
    private function hasToken(){
        if (!empty($_SESSION["code"])) return true;
        else return false;
    }
    private function sessionValidation(){
        if(!empty($_SESSION['expires'])):
            if($_SESSION['expires'] < time()):
                $this->endSession();
            endif;
        else: session_regenerate_id(true);endif;
    }
    private function endSession(){
        session_unset();
        session_regenerate_id(true);
    }
}
