<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MeteranModel;
use App\Models\PelangganModel;
use CodeIgniter\HTTP\ResponseInterface;

class Pelanggan extends BaseController
{
    public function index()
    {
        $pelanggan = new PelangganModel();
        $data['pelanggan'] = $pelanggan->asObject()->findAll();

        return view('pelanggan/index', $data);
    }

    public function store()
    {
        $session = session();
        $pelangganModel = new PelangganModel();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'       => 'required',
            'phone'      => 'required|numeric|max_length[13]',
            'rt'         => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $session->setFlashdata('msg', $errors);

            return redirect()->to('/pelanggan');
        }

        $name       = $this->request->getPost('name');
        $phone      = $this->request->getPost('phone');
        $rt         = $this->request->getPost('rt');
        $id         = $this->request->getPost('user_id');

        $data = [
            'name'       => $name,
            'phone'      => $phone,
            'rt'         => $rt,
        ];

        if (!empty($id)) {
            $result = $pelangganModel->update($id, $data);
            if ($result) {
                $session->setFlashdata('success', 'Berhasil mengubah data pelanggan');
                return redirect()->to('/pelanggan');
            } else {
                $session->setFlashdata('msg', array('msg' => 'Gagal mengubah data pelanggan'));
                return redirect()->to('/pelanggan');
            }
        } else {
            $result = $pelangganModel->insert($data);
            if ($result) {
                $session->setFlashdata('success', 'Berhasil menambahkan data pelanggan');
                return redirect()->to('/pelanggan');
            } else {
                $session->setFlashdata('msg', array('msg' => 'Gagal menambahkan data pelanggan'));
                return redirect()->to('/pelanggan');
            }
        }

    }

    public function delete($id)
    {
        $session = session();
        $pelangganModel = new PelangganModel();
        $pelanggan = $pelangganModel->find($id);

        if (!$pelanggan) {
            $session->setFlashdata('msg', array('msg' => 'Data pelanggan tidak ditemukan'));
            return redirect()->to('/pelanggan');
        }

        $pelangganModel->delete($id);

        if ($pelangganModel->affectedRows() < 0) {
            $meteranModel = new MeteranModel();
            $dataRelation = $meteranModel->where('id_pelanggan', $id)->first();

            if ($dataRelation) {
                $session->setFlashdata('msg', array('msg' => 'Pelanggan masih memiliki meteran'));
                return redirect()->to('/pelanggan');
            }

            $session->setFlashdata('msg', array('msg' => 'Gagal menghapus data pelanggan'));
            return redirect()->to('/pelanggan');
        }

        $session->setFlashdata('success', 'Berhasil menghapus data pelanggan');
        return redirect()->to('/pelanggan');
    }

    public function getData($id)
    {
        $pelangganModel = new PelangganModel();
        $pelanggan = $pelangganModel->find($id);

        if (!$pelanggan) {
            $result = array(
                'status' => 'error',
                'message' => 'Pelanggan not found'
            );
        } else {
            $result = array(
                'status' => 'success',
                'data' => $pelanggan
            );
        }

        echo json_encode($result);
    }
}
