<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\SaldoModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Pembayaran extends BaseController
{
    protected $session, $validation, $pembayaranModel, $db, $saldo;

    public function __construct()
    {
        $this->session = session();
        $this->validation = \Config\Services::validation();
        $this->pembayaranModel = new PembayaranModel();
        $this->saldo = new SaldoModel();
        $this->db = db_connect();
    }

    public function index()
    {
        return view('pembayaran/index');
    }

    public function getDataFiltered()
    {
        $date = $this->request->getPost('date');
        $data = $this->pembayaranModel->getJoinedData($date);

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

    public function bayar()
    {
        $this->validation->setRules([
            'jumlah'      => 'required|numeric',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            $errors = $this->validation->getErrors();
            $this->session->setFlashdata('msg', $errors);

            return redirect()->to('/pembayaran');
        }

        $id = $this->request->getPost('user_id');
        $jumlah = $this->request->getPost('jumlah');

        $idPelanggan = $this->pembayaranModel->getPelanggan($id);
        $biaya = $this->pembayaranModel->getPerluDibayar($id)->biaya;
        $dibayarkan = $this->pembayaranModel->getPerluDibayar($id)->dibayarkan;
        $checkSaldo = $this->saldo->where('id_pelanggan', $idPelanggan)->first();

        if (isset($checkSaldo)) {
            $biaya = $biaya - $checkSaldo['saldo'];
        }

        if (isset($dibayarkan)) {
            $biaya = $biaya - $dibayarkan;
        }

        if ($jumlah < $biaya) {
            $status = 'Belum Lunas';
        } elseif ($jumlah > $biaya) {
            $status = 'Lunas & Sisa';
            $sisa = $jumlah - $biaya;
        } elseif ($jumlah = $biaya) {
            $status = 'Lunas';
        }

        if (isset($sisa)) {
            try {
                $this->db->transStart();
                if (isset($checkSaldo)) {
                    $dataSaldo = array(
                        'saldo' => $sisa,
                    );

                    $result = $this->saldo->update($checkSaldo['id'], $dataSaldo);

                    if (!$result) {
                        throw new Exception("Failed to update data into saldo");
                    }
                } else {
                    $dataSaldo = array(
                        'id_pelanggan' => $idPelanggan,
                        'saldo' => $sisa,
                    );

                    $result = $this->saldo->insert($dataSaldo);

                    if (!$result) {
                        throw new Exception("Failed to insert data into saldo");
                    }
                }

                if (isset($dibayarkan)) {
                    $jumlah = $jumlah + $dibayarkan;
                }

                $dataPembayaran = array(
                    'status' => $status,
                    'dibayarkan' => $jumlah,
                );

                $pembayaranResult = $this->pembayaranModel->update($id, $dataPembayaran);

                if (!$pembayaranResult) {
                    throw new Exception("Failed to insert data into pembayaranModel");
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new Exception("Transaction failed");
                }

                $this->session->setFlashdata('success', 'Berhasil melakukan pembayaran');
                return redirect()->to('/pembayaran');
            } catch (Exception $e) {
                $this->db->transRollback();

                $this->session->setFlashdata('msg', array('data' => 'Gagal melakukan pembayaran'));
                return redirect()->to('/pembayaran');
            }
        } else {
            try {
                $this->db->transStart();
                if (isset($dibayarkan)) {
                    $jumlah = $jumlah + $dibayarkan;
                }

                if (isset($checkSaldo)) {
                    $sisaSaldo = $checkSaldo['saldo'] - $this->pembayaranModel->getPerluDibayar($id)->biaya;

                    if ($sisaSaldo <= 0) {
                        $sisaSaldo = 0;

                        $dataSaldo = array(
                            'saldo' => $sisaSaldo,
                        );

                        $result = $this->saldo->update($checkSaldo['id'], $dataSaldo);
                    }

                    $jumlah = $jumlah + $checkSaldo['saldo'];
                }

                $dataPembayaran = array(
                    'status' => $status,
                    'dibayarkan' => $jumlah,
                );

                $pembayaranResult = $this->pembayaranModel->update($id, $dataPembayaran);

                if (!$pembayaranResult) {
                    $this->session->setFlashdata('msg', array('data' => 'Gagal melakukan pembayaran'));
                    return redirect()->to('/pembayaran');
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new Exception("Transaction failed");
                }

                $this->session->setFlashdata('success', 'Berhasil melakukan pembayaran');
                return redirect()->to('/pembayaran');
            } catch (Exception $e) {
                $this->db->transRollback();

                $this->session->setFlashdata('msg', array('data' => 'Gagal melakukan pembayaran'));
                return redirect()->to('/pembayaran');
            }
        }
    }

    public function cetak($id)
    {
        $dataPembayaran = $this->pembayaranModel->getSinglePembayaran($id);
        return view('pembayaran/cetak', ['pembayaran' => $dataPembayaran]);
    }
}
