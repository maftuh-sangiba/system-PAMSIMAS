<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MeteranModel;
use App\Models\PelangganModel;
use App\Models\PembayaranModel;
use App\Models\PenggunaanModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    protected $pelangganModel, $meteranModel, $pembayaranModel, $penggunaanModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->meteranModel = new MeteranModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->penggunaanModel = new PenggunaanModel(); 
    }

    public function index()
    {
        $session = session();

        $allPelanggan = $this->pelangganModel->findAll();
        $allMeteran = $this->meteranModel->findAll();
        $lunas = $this->pembayaranModel->where('(status = "Lunas & Sisa" OR status = "Lunas")')->findAll();
        $unpaid = $this->pembayaranModel->where('status', 'Belum Dibayar')->findAll();
        $total = $this->penggunaanModel->getMonthlyBiayaCount();

        $data = array(
            'name' => $session->get('user_name'),
            'email' => $session->get('user_email'),
            'total_pelanggan' => count($allPelanggan),
            'total_meteran' => count($allMeteran),
            'lunas' => count($lunas),
            'unpaid' => count($unpaid),
            'total' => $total,
        ); 
            
        return view('dashboard', $data);
    }
}
