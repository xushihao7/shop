<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
class ApiController extends Controller
{
    public  function  contact(){
        $url='http://vm.api.com/test.php?type=1';
        $client=new Client();
        $r=$client->request('GET',$url);
        $response_arr=$r->getBody();
        //var_dump($response_arr);
        $data=json_decode($response_arr,true);
         echo "<pre>";print_r($data);echo "<pre/>";
    }
}
