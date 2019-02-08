<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 08/02/2019
 * Time: 16:10
 */

namespace App\Models;


class PeriodicUrl
{
    public $timestamps = false;
    public $page;


    public function isUrlTokenIsStillValid($token)
    {
        $page = PeriodicUrl::where('url_token', '=', $token)->take(1)->get();

        $this->page = $page[0];

        if (empty($this->page)) {
            return false;
        }
        return ($page[0]->exists() && $this->isTokenStillValid($page)) ? true : false;
    }

    public function isTokenStillValid($page)
    {
        $date = new DateTime($page->url_token);

        return $date->format('Y-m-d') == date('Y-m-d');
    }


}