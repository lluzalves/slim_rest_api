<?php

namespace App\Controllers;

class HomeController extends BaseController{

    public function index($request ,$response){

        return 'HomeController';
    }

    public function login($request, $response){
        return 'login';
    }

    public function logout($request, $response){

        return 'logout';
    }

    public function register($request, $response){
        return 'register';
    }
}