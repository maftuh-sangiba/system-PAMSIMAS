<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $table_penggunaan = 'penggunaan';
    protected $table_meteran = 'meteran';
    protected $table_pelanggan = 'pelanggan';

    protected $allowedFields = ['id_penggunaan', 'status', 'dibayarkan'];

    public function getJoinedData($data)
    {
        $builder = $this->db->table($this->table);
        $builder->join($this->table_penggunaan, 'penggunaan.id = pembayaran.id_penggunaan');
        $builder->join($this->table_meteran, 'meteran.id = penggunaan.id_meteran');
        $builder->select('pembayaran.*, meteran.nomor_meteran, penggunaan.biaya');
        $builder->where("DATE_FORMAT(penggunaan.bulan,'%Y-%m')", $data);

        return $builder->get()->getResult();
    }

    public function getPerluDibayar($id)
    {
        $result = $this->db->table($this->table);
        $result->join($this->table_penggunaan, 'penggunaan.id = pembayaran.id_penggunaan');
        $result->select('penggunaan.biaya, pembayaran.dibayarkan');
        $result->where('pembayaran.id', $id);

        return $result->get()->getFirstRow();
    }

    public function getPelanggan($id)
    {
        $result = $this->db->table($this->table);
        $result->join($this->table_penggunaan, 'penggunaan.id = pembayaran.id_penggunaan');
        $result->join($this->table_meteran, 'meteran.id = penggunaan.id_meteran');
        $result->join($this->table_pelanggan, 'pelanggan.id = meteran.id_pelanggan');
        $result->select('pelanggan.id');
        $result->where('pembayaran.id', $id);

        return $result->get()->getFirstRow()->id;
    }
}