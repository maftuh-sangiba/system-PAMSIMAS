<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MeteranModel;
use App\Models\PembayaranModel;
use App\Models\PenggunaanModel;
use CodeIgniter\API\ResponseTrait;
use Exception;

class Penggunaan extends BaseController
{
    use ResponseTrait;

    protected $modelPenggunaan, $modelSaldo, $db, $modelPembayaran, $modelMeteran;

    function __construct()
    {
        $this->modelPenggunaan = new PenggunaanModel();
        $this->modelPembayaran = new PembayaranModel();
        $this->modelMeteran = new MeteranModel();
        $this->db = db_connect();
    }

    public function store()
    {
        $dateFinal = date('Y-m-d');
        $pricePerMeter = 2500;
        $beban = 1500;
        $pemakaian = $this->request->getVar('pemakaian');
        $biaya = $pemakaian * $pricePerMeter + $beban;
        $nomorMeteran = $this->request->getVar('nomor_meteran');
        $idMeteran = $this->modelMeteran->where('nomor_meteran', $nomorMeteran)->first();
        $currentYearMonth = date('Y-m');
        $hasData = $this->modelPenggunaan->where('id_meteran', $idMeteran['id'])->like('bulan', $currentYearMonth)->countAllResults();

        if ($hasData > 0) {
            return $this->respond(
                array(
                    'status' => 'error',
                    'msg' => 'Gagal menambahkan penggunaan, penggunaan sudah ada'
                )
            );
        }

        $data = [
            'bulan' => $dateFinal,
            'id_meteran' => $idMeteran['id'],
            'pemakaian' => $pemakaian,
            'biaya' => $biaya,
        ];

        try {
            $this->db->transStart();
            $result = $this->modelPenggunaan->insert($data);
            $penggunaan_id = $this->modelPenggunaan->getInsertID();

            if (!$result || !$penggunaan_id) {
                throw new Exception("Failed to insert data into penggunaanModel");
            }

            $pembayaranData = [
                'id_penggunaan' => $penggunaan_id,
                'status' => 'Belum Dibayar',
                'dibayarkan' => 0,
            ];

            $pembayaranResult = $this->modelPembayaran->insert($pembayaranData);

            if (!$pembayaranResult) {
                throw new Exception("Failed to insert data into penggunaan");
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new Exception("Transaction failed");
            }

            return $this->respond(
                array(
                    'status' => 'success',
                    'msg' => 'Berhasil menambahkan penggunaan'
                )
            );
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->respond(
                array(
                    'status' => 'error',
                    'msg' => 'Gagal menambahkan penggunaan'
                )
            );
        }
    }
}
