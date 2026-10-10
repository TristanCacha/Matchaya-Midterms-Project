<?php

namespace App\Controllers;

use App\Models\SaleModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return $this->renderPage('dashboard/index', [
            'title' => 'Dashboard', 'activePage' => 'dashboard', 'metrics' => model(SaleModel::class)->dashboard(),
        ]);
    }
}
