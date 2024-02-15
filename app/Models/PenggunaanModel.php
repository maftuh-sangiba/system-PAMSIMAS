<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaanModel extends Model
{
    protected $table = 'penggunaan';
    protected $table_meteran = 'meteran';
    protected $allowedFields = ['id_meteran', 'bulan', 'pemakaian', 'biaya'];

    public function getJoinedData($data)
    {
        $builder = $this->db->table($this->table);
        $builder->join($this->table_meteran, 'meteran.id = penggunaan.id_meteran');
        $builder->select('penggunaan.*, meteran.nomor_meteran');
        $builder->where("DATE_FORMAT(penggunaan.bulan,'%Y-%m')", $data);

        return $builder->get()->getResult();
    }

    public function getMonthlyBiayaCount()
    {
        $thisYear = date('Y');
        $query = $this->select('MONTH(bulan) as month, SUM(biaya) as biaya_count')
                      ->where('YEAR(bulan)', $thisYear)
                      ->groupBy('MONTH(bulan)')
                      ->orderBy('MONTH(bulan)')
                      ->findAll();
        
        return $query;
    }
}