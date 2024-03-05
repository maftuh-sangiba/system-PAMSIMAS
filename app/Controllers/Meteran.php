<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MeteranModel;
use App\Models\PenggunaanModel;
use SimpleSoftwareIO\QrCode\Generator;

class Meteran extends BaseController
{
    public function index()
    {
        $meteranModel = new MeteranModel();
        $data = $meteranModel->getJoinedData();

        return view('meteran/index', ['meteran' => $data]);
    }

    public function getPelanggan()
    {
        $meteranModel = new MeteranModel();
        $data = $meteranModel->get_pelanggan_not_in_meteran_data();

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
        $session = session();
        $meteranModel = new MeteranModel();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'pelanggan'      => 'required|numeric',
            'nomor_meteran'  => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $session->setFlashdata('msg', $errors);

            return redirect()->to('/meteran');
        }

        $pelanggan      = $this->request->getPost('pelanggan');
        $nomor_meteran  = $this->request->getPost('nomor_meteran');
        $id             = $this->request->getPost('id');

        $data = [
            'id_pelanggan'  => $pelanggan,
            'nomor_meteran' => $nomor_meteran,
        ];

        if (!empty($id)) {
            $result = $meteranModel->update($id, $data);
            if ($result) {
                $session->setFlashdata('success', 'Berhasil mengubah data meteran');
                return redirect()->to('/meteran');
            } else {
                $session->setFlashdata('msg', 'Gagal mengubah data meteran');
                return redirect()->to('/meteran');
            }
        } else {
            $result = $meteranModel->insert($data);
            if ($result) {
                $session->setFlashdata('success', 'Berhasil menambahkan data meteran');
                return redirect()->to('/meteran');
            } else {
                $session->setFlashdata('msg', 'Gagal menambahkan data meteran');
                return redirect()->to('/meteran');
            }
        }
    }

    public function delete($id)
    {
        $session = session();
        $meteranModel = new MeteranModel();
        $meteran = $meteranModel->find($id);

        if (!$meteran) {
            $session->setFlashdata('msg', 'Gagal menghapus data meteran');
            return redirect()->to('/meteran');
        }

        $meteranModel->delete($id);

        if ($meteranModel->affectedRows() < 0) {
            $penggunaanModel = new PenggunaanModel();
            $dataRelation = $penggunaanModel->where('id_meteran', $id)->first();

            if ($dataRelation) {
                $session->setFlashdata('msg', array('msg' => 'Meteran masih memiliki data penggunaan'));
                return redirect()->to('/meteran');
            }

            $session->setFlashdata('msg', array('msg' => 'Gagal menghapus data meteran'));
            return redirect()->to('/meteran');
        }

        $session->setFlashdata('success', 'Berhasil menghapus data meteran');
        return redirect()->to('/meteran');
    }

    public function getData($id)
    {
        $meteranModel = new MeteranModel();
        $meteran = $meteranModel->getPengguna($id);

        if (!$meteran) {
            $result = array(
                'status' => 'error',
                'message' => 'Meteran not found'
            );
        } else {
            $result = array(
                'status' => 'success',
                'data' => $meteran
            );
        }

        echo json_encode($result);
    }

    public function cetak($id)
    {
        $meteranModel = new MeteranModel();
        $meteran = $meteranModel->find($id);

        $qrcode = new Generator();
        $qrCodes = $qrcode->size(200)->generate($meteran['nomor_meteran']);

        return view('meteran/print', [
            'qrCode' => $qrCodes,
            'nomorMeteran' => $meteran['nomor_meteran'],
        ]);
    }
}
