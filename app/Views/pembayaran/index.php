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
                Data Seluruh Penggunaan Air
            </div>
            <div class="card-body">
                <div class="col-3 my-3">
                    <label for="first-name-column">Data Bulan</label>
                    <input id="bulan" class="form-control" name="bulan" width="250" type="month" required/>
                </div>
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>No Meteran</th>
                            <th width="14%">Perlu Dibayar</th>
                            <th width="10%">Sudah Dibayar</th>
                            <th width="10%">Kurang Bayar</th>
                            <th width="10%">Status</th>
                            <th width="35%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section>
        <div class="modal fade text-left" id="modal-bayar" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-label">Pembayaran</h4>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form action="<?= base_url() ?>pembayaran/bayar" method="post">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="user_id">
                            <label>Bayar :</label>
                            <div class="form-group">
                                <input type="number" name="jumlah" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary"
                                data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Batal</span>
                            </button>
                            <button type="submit" class="btn btn-primary ml-1"
                                data-bs-dismiss="">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Bayar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- <link rel="stylesheet" href="/assets/vendors/simple-datatables/style.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- <script src="/assets/vendors/simple-datatables/simple-datatables.js"></script> -->
<script src="<?= base_url() ?>public/assets/js/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(async function() {
        await getTodayMonth();

        let selectedDate = $("#bulan").val();

        $('#table1').DataTable({
            "processing": true,
            "ajax": {
                "url": "<?= base_url() ?>pembayaran/getDataFiltered",
                "type": "POST",
                "data": function(d){
                    d.date = selectedDate;
                }
            },
            "columns": [
                {"data": "nomor_meteran"},
                {"data": "biaya"},
                {"data": "dibayarkan"},
                {"data": "dibayarkan", 
                    render: function(data, type, row) {
                        if (row['status'] === 'Belum Dibayar' || row['status'] === 'Belum Lunas') {
                            return row['biaya'] - data;
                        }
                        return 0;
                    }
                },
                {"data": "status",
                    render: function(data, type, row) {
                        switch (data) {
                            case 'Belum Dibayar':
                                return "<span class='badge bg-danger'>"+data+"</span>";
                            break;
                            case 'Lunas & Sisa':
                                return "<span class='badge bg-success'>"+data+"</span>";
                            break;
                            case 'Belum Lunas':
                                return "<span class='badge bg-warning'>"+data+"</span>";
                            break;
                            default:
                                return "<span class='badge bg-primary'>"+data+"</span>";
                            break;
                        }
                    }
                },
                {"data": "id",
                    render: function(data, type, row) {
                        if (row['status'] === 'Belum Dibayar' || row['status'] === 'Belum Lunas') {
                            let html = `
                            <a href='javascript:void(0);' onClick='bayar(`+data+`)' class='btn icon incon-left btn-primary'><i class='bi bi-cash'></i> Bayar</a>
                            <a href='<?= base_url() ?>pembayaran/cetak/`+data+`' class='btn btn-secondary' target='_blank'><i class='bi bi-printer'></i> Cetak</a>
                            <a href='javascript:void(0);' onClick='kirim(`+data+`)' class='btn btn-success'><i class='bi bi-whatsapp'></i> Kirim</a>
                            `
                            return html;
                        } else {
                            let html = `
                            <a href='<?= base_url() ?>pembayaran/cetak/`+data+`' class='btn btn-secondary' target='_blank'><i class='bi bi-printer'></i> Cetak</a>
                            <a href='javascript:void(0);' onClick='kirim(`+data+`)' class='btn btn-success'><i class='bi bi-whatsapp'></i> Kirim</a>
                            `
                            return html;
                        }
                    }
                },
            ],
            "bDestroy": true,
        });
    });

    function getTodayMonth() {
        var date = new Date();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        if (month < 10) month = "0" + month;
        var today = year + "-" + month;       
        $("#bulan").attr("value", today);
    }

    $("#bulan").on('change', function(){
        let selectedDate = $(this).val();

        $('#table1').DataTable().clear();
        $('#table1').DataTable().destroy();

        $('#table1').DataTable({
            "processing": true,
            "ajax": {
                "url": "<?= base_url() ?>pembayaran/getDataFiltered",
                "type": "POST",
                "data": function(d){
                    d.date = selectedDate;
                }
            },
            "columns": [
                {"data": "nomor_meteran"},
                {"data": "biaya"},
                {"data": "dibayarkan"},
                {"data": "dibayarkan", 
                    render: function(data, type, row) {
                        if (row['status'] === 'Belum Dibayar' || row['status'] === 'Belum Lunas') {
                            return row['biaya'] - data;
                        }
                        return 0;
                    }
                },
                {"data": "status",
                    render: function(data, type, row) {
                        switch (data) {
                            case 'Belum Dibayar':
                                return "<span class='badge bg-danger'>"+data+"</span>";
                            break;
                            case 'Lunas & Sisa':
                                return "<span class='badge bg-success'>"+data+"</span>";
                            break;
                            case 'Belum Lunas':
                                return "<span class='badge bg-warning'>"+data+"</span>";
                            break;
                            default:
                                return "<span class='badge bg-primary'>"+data+"</span>";
                            break;
                        }
                    }
                },
                {"data": "id",
                    render: function(data, type, row) {
                        if (row['status'] === 'Belum Dibayar' || row['status'] === 'Belum Lunas') {
                            let html = `
                            <a href='javascript:void(0);' onClick='bayar(`+data+`)' class='btn icon incon-left btn-primary'><i class='bi bi-cash'></i> Bayar</a>
                            <a href='<?= base_url() ?>pembayaran/cetak/`+data+`' class='btn btn-secondary' target='_blank'><i class='bi bi-printer'></i> Cetak</a>
                            <a href='javascript:void(0);' onClick='kirim(`+data+`)' class='btn btn-success'><i class='bi bi-whatsapp'></i> Kirim</a>
                            `
                            return html;
                        } else {
                            let html = `
                            <a href='<?= base_url() ?>pembayaran/cetak/`+data+`' class='btn btn-secondary' target='_blank'><i class='bi bi-printer'></i> Cetak</a>
                            <a href='javascript:void(0);' onClick='kirim(`+data+`)' class='btn btn-success'><i class='bi bi-whatsapp'></i> Kirim</a>
                            `
                            return html;
                        }
                    }
                },
            ],
            "bDestroy": true,
        });

        $('#table1').DataTable().draw();
    });

    function bayar(id){
        console.log(id);
        $("#user_id").val(id);
        $("#modal-bayar").modal("show");
    }

    function kirim(id){
        console.log(id);
    }
</script>
<?= $this->endSection() ?>
