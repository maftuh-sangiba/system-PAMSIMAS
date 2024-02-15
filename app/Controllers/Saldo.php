<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaldoModel;
use CodeIgniter\HTTP\ResponseInterface;

class Saldo extends BaseController
{
    protected $saldo;

    public function __construct()
    {
        $this->saldo = new SaldoModel();
    }

    public function index()
    {
        $saldoAll = $this->saldo->getJoined();
        $data = array( 
            'saldo' =>  $saldoAll,
        );

        return view('saldo/index', $data);
    }
}
