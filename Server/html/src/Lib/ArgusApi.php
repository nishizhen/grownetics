<?php
namespace App\Lib;

use Cake\I18n\Time;

# This is a generic Argis API class that can be called be whoever needs to do so.
# Mainly used in ArgusShell for now.
class ArgusApi {

    # Update paramaters in DB
    public function updateParameters() {
        $params = $this->getParameter('','');
        
        $paramsArray = [];
    }

    # Get one parameter from Argus
    public function getParameter($parameterId, $requestParams = "?currentValues") {
        $username = "APIuser";
        $password = "API123";
        # ARGUS_URL example: localhost:5555 or 192.168.88.75:47840
        $remote_url = 'https://' . env('ARGUS_URL') . '/argusapi/v1/parameters/' . $parameterId . $requestParams;

        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            ),
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        );

        $context = stream_context_create($opts);


        $file = file_get_contents($remote_url, false, $context);

        $response = json_decode($file);
        return $response;
    }
}
