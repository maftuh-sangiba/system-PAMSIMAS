<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <?php if(session()->getFlashdata('msg')): ?>
        <?php foreach(session()->getFlashdata('msg') as $error): ?>
            <div class="alert alert-danger" id="notif"><?= esc($error) ?></div>
        <?php endforeach; ?>
    <?php endif;?>

    <?php if(session()->getFlashdata('success')):?>
        <div class="alert alert-success" id="notif"><?= session()->getFlashdata('success') ?></div>
    <?php endif;?>

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Penggunaan Air</h3>
                <p class="text-subtitle text-muted">For user to check they list</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Penggunaan Air</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        Data Seluruh Penggunaan Air
                    </div>
                    <div class="col text-end">
                        <a href="/penggunaan/insert">
                            <button type="button" class="btn btn-primary"><i class="iconly-boldAdd-User"></i> Tambah</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-3 my-3">
                    <label for="first-name-column">Data Bulan</label>
                    <input id="bulan" class="form-control" name="bulan" width="250" type="month" required/>
                </div>
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Nomor Meteran</th>
                            <th>Pemakaian Bulan Ini</th>
                            <th>Biaya Bulan Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($penggunaan as $key => $value) {
                                echo "<tr>
                                    <td width='20%'>".$value->nomor_meteran."</td>
                                    <td width='20%'>".$value->pemakaian."<span> m<sup>3</sup></span></td>
                                    <td width='20%'>".$value->biaya."</td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="/assets/js/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {
        getTodayMonth();
        $('#table1').DataTable();
    });

    function getTodayMonth(){
        var date = new Date();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        if (month < 10) month = "0" + month;
        var today = year + "-" + month;       
        $("#bulan").attr("value", today);
    }

    $("#bulan").on('change', function(){
        let selectedDate = $(this).val();
        console.log(selectedDate);

        $('#table1').DataTable().clear();
        $('#table1').DataTable().destroy();

        $('#table1').DataTable({
            "processing": true,
            "ajax": {
                "url": "<?php echo base_url(); ?>/penggunaan/getDataFiltered",
                "type": "POST",
                "data": function(d){
                    d.date = selectedDate;
                }
            },
            "columns": [
                {"data": "nomor_meteran"},
                {"data": "pemakaian"},
                {"data": "biaya"},
            ],
            "bDestroy": true,
        });

        $('#table1').DataTable().draw();
    })
</script>
<?= $this->endSection() ?>
