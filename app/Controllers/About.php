<?php

namespace App\Controllers;

use Config\Shop;

class About extends BaseController
{
    public function index(): string
    {
        return $this->renderPage('about/index', ['title' => 'About', 'activePage' => 'about', 'developer' => Shop::DEVELOPER]);
    }
}
