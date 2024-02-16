<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\SaldoModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Pembayaran extends BaseController
{
    use ResponseTrait;

    protected $modelPembayaran, $modelSaldo, $db;

    function __construct()
    {
        $this->modelPembayaran = new PembayaranModel();
        $this->modelSaldo = new SaldoModel();
        $this->db = db_connect();
    }
    public function check()
    {
        $nomorMeteran = $this->request->getVar('nomor_meteran');
        $currentYearMonth = date('Y-m');

        $data = $this->modelPembayaran->join('penggunaan', 'penggunaan.id = pembayaran.id_penggunaan')
            ->join('meteran', 'meteran.id = penggunaan.id_meteran')
            ->join('pelanggan', 'pelanggan.id = meteran.id_pelanggan')
            ->where('meteran.nomor_meteran', $nomorMeteran)
            ->where('DATE_FORMAT(penggunaan.bulan, "%Y-%m")', $currentYearMonth)
            ->where("(pembayaran.status = 'Belum Dibayar' OR pembayaran.status = 'Belum Lunas')")
            ->select('pembayaran.id as id_pembayaran, penggunaan.id as id_penggunaan, pelanggan.id as id_pelanggan, pembayaran.status, pembayaran.dibayarkan, penggunaan.biaya, meteran.nomor_meteran, pelanggan.name')
            ->first();

        if (!$data) {
            $result = array(
                'status' => 'failed',
                'data' => 'Data Not Found',
            );

            return $this->respond($result);
        }

        $result = array(
            'status' => 'success',
            'data' => $data,
        );

        return $this->respond($result);
    }

    public function pay()
    {
        $id = $this->request->getVar('id_pembayaran');
        $jumlah = $this->request->getVar('jumlah');

        $findPelanggan = $this->modelPembayaran->find($id);

        if (!$findPelanggan) {
            return $this->respond(
                array(
                    'status' => 'error',
                    'msg' => 'Data pembayaran tidak ditemukan atau sudah dibayarkan'
                )
            );
        }

        $idPelanggan = $this->modelPembayaran->getPelanggan($id);
        $biaya = $this->modelPembayaran->getPerluDibayar($id)->biaya;
        $dibayarkan = $this->modelPembayaran->getPerluDibayar($id)->dibayarkan;
        $checkSaldo = $this->modelSaldo->where('id_pelanggan', $idPelanggan)->first();

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

                    $result = $this->modelSaldo->update($checkSaldo['id'], $dataSaldo);

                    if (!$result) {
                        throw new Exception("Failed to update data into saldo");
                    }
                } else {
                    $dataSaldo = array(
                        'id_pelanggan' => $idPelanggan,
                        'saldo' => $sisa,
                    );

                    $result = $this->modelSaldo->insert($dataSaldo);

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

                $pembayaranResult = $this->modelPembayaran->update($id, $dataPembayaran);

                if (!$pembayaranResult) {
                    throw new Exception("Failed to insert data into pembayaranModel");
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new Exception("Transaction failed");
                }

                return $this->respond(
                    array(
                        'status' => 'success',
                        'msg' => 'Berhasil melakukan pembayaran'
                    )
                );
            } catch (Exception $e) {
                $this->db->transRollback();

                return $this->respond(
                    array(
                        'status' => 'error',
                        'msg' => 'Gagal melakukan pembayaran'
                    )
                );
            }
        } else {
            try {
                $this->db->transStart();
                if (isset($dibayarkan)) {
                    $jumlah = $jumlah + $dibayarkan;
                }

                if (isset($checkSaldo)) {
                    $sisaSaldo = $checkSaldo['saldo'] - $this->modelPembayaran->getPerluDibayar($id)->biaya;

                    if ($sisaSaldo <= 0) {
                        $sisaSaldo = 0;

                        $dataSaldo = array(
                            'saldo' => $sisaSaldo,
                        );

                        $result = $this->modelSaldo->update($checkSaldo['id'], $dataSaldo);
                    }

                    $jumlah = $jumlah + $checkSaldo['saldo'];
                }

                $dataPembayaran = array(
                    'status' => $status,
                    'dibayarkan' => $jumlah,
                );

                $pembayaranResult = $this->modelPembayaran->update($id, $dataPembayaran);

                if (!$pembayaranResult) {
                    return $this->respond(
                        array(
                            'status' => 'error',
                            'msg' => 'Gagal melakukan pembayaran'
                        )
                    );
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new Exception("Transaction failed");
                }

                return $this->respond(
                    array(
                        'status' => 'success',
                        'msg' => 'Berhasil melakukan pembayaran'
                    )
                );
            } catch (Exception $e) {
                $this->db->transRollback();

                return $this->respond(
                    array(
                        'status' => 'error',
                        'msg' => 'Gagal melakukan pembayaran'
                    )
                );
            }
        }
    }
}
