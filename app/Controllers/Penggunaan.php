<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MeteranModel;
use App\Models\PembayaranModel;
use App\Models\PenggunaanModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Penggunaan extends BaseController
{
    protected $session, $penggunaanModel, $db, $meteranModel, $validation, $pembayaranModel;
    
    public function __construct()
    {
        $this->db = db_connect();
        $this->session = session();
        $this->penggunaanModel = new PenggunaanModel();
        $this->meteranModel = new MeteranModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $dateNow = date('Y-m');
        $data = $this->penggunaanModel->getJoinedData($dateNow);

        return view('penggunaan/index', ['penggunaan' => $data]);
    }

    public function insert()
    {
        return view('penggunaan/insert');
    }

    public function getAllMeteran()
    {
        $date = $this->request->getPost('date');
        $data = $this->meteranModel->getDataWhereMonth($date);

        if (!$data) {
            $result = array(
                'status' => 'error',
                'message' => 'Data not found'
            );
        } else {
            $result = array(
                'status' => 'success',
                'data' => $data
            );
        }

        echo json_encode($result);
    }

    public function store()
    {
        $this->validation->setRules([
            'bulan'      => 'required',
            'nomor_meteran'  => 'required|numeric',
            'used-this'  => 'required|numeric',
            'pay-this'  => 'required|numeric',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            $errors = $this->validation->getErrors();
            $this->session->setFlashdata('msg', $errors);

            return redirect()->to('/penggunaan/insert');
        }
        
        $bulan = $this->request->getVar('bulan');

        $dateArray = date_parse($bulan);
        $dateFinal = date('Y-m-d', mktime($dateArray['hour'], null, null, $dateArray['month'], $dateArray['day'], $dateArray['year']));

        $data = [
            'bulan' => $dateFinal,
            'id_meteran' => $this->request->getVar('nomor_meteran'),
            'pemakaian' => $this->request->getVar('used-this'),
            'biaya' => $this->request->getVar('pay-this'),
        ];

        if (!empty($id)) {
            $result = $this->penggunaanModel->update($id, $data);
        } else {
            try {
                $this->db->transStart();
                $result = $this->penggunaanModel->insert($data);
                $penggunaan_id = $this->penggunaanModel->getInsertID();

                if (!$result || !$penggunaan_id) {
                    throw new Exception("Failed to insert data into penggunaanModel");
                }

                $pembayaranData = [
                    'id_penggunaan' => $penggunaan_id,
                    'status' => 'Belum Dibayar',
                    'dibayarkan' => 0,
                ];
                
                $pembayaranResult = $this->pembayaranModel->insert($pembayaranData);

                if (!$pembayaranResult) {
                    throw new Exception("Failed to insert data into pembayaranModel");
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new Exception("Transaction failed");
                }
            } catch (Exception $e) {
                $this->db->transRollback();
            }
        }

        if ($result) {
            $this->session->setFlashdata('success', 'Berhasil menambahkan data penggunaan');
            return redirect()->to('/penggunaan');
        } else {
            $this->session->setFlashdata('msg', 'Gagal menambahkan data penggunaan');
            return redirect()->to('/penggunaan');
        }
    }

    public function getDataFiltered()
    {
        $date = $this->request->getPost('date');

        $penggunaanModel = new PenggunaanModel();
        $data = $penggunaanModel->getJoinedData($date);

        if (!$data) {
            $result = array(
                'status' => 'error',
                'data' => []
            );
        } else {
            $result = array(
                'status' => 'success',
                'data' => $data
            );
        }

        echo json_encode($result);
    }
}
