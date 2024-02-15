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
                <h3>Data Pelanggan</h3>
                <p class="text-subtitle text-muted">For user to check they list</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pelanggan</li>
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
                        Data Seluruh Pelanggan
                    </div>
                    <div class="col text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inlineForm"><i class="iconly-boldAdd-User"></i> Tambah</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor Wa</th>
                            <th>RT</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($pelanggan as $key => $value) {
                                echo "<tr>
                                    <td width='30%'>".$value->name."</td>
                                    <td width='20%'>".$value->phone."</td>
                                    <td width='10%'>".$value->rt."</td>
                                    <td width='20%'>
                                        <a href='javascript:void(0);' onClick='editData(".$value->id.")' class='btn icon icon-left btn-primary'>
                                            <i class='bi bi-pencil-square'></i> Edit
                                        </a>
                                        <a href='pelanggan/delete/".$value->id."' class='btn icon icon-left btn-danger'>
                                            <i class='bi bi-trash'></i> Hapus
                                        </a>
                                    </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Tambahkan Pengguna</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form action="/pelanggan/store" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="user_id">

                        <label>Nama :</label>
                        <div class="form-group">
                            <input type="text" placeholder="Tulis Nama" name="name" class="form-control" required>
                        </div>
                        <label>Nomor Wa :</label>
                        <div class="form-group">
                            <input type="number" placeholder="Tulis Nomor Whatsapp" name="phone" class="form-control" required>
                        </div>
                        <label>RT :</label>
                        <div class="form-group">
                            <input type="number" placeholder="Pilih RT" name="rt" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary"
                            data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" class="btn btn-primary ml-1"
                            data-bs-dismiss="">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        $('#table1').DataTable();
    });

    function editData(id) {
        $.ajax({
            url: "<?php echo base_url(); ?>pelanggan/getData/"+id,
            dataType: 'json',
            success: function(result) {
                $('#inlineForm input[name="user_id"]').val(result.data.id);
                $('#inlineForm input[name="name"]').val(result.data.name);
                $('#inlineForm input[name="phone"]').val(result.data.phone);
                $('#inlineForm input[name="rt"]').val(result.data.rt);
                $('#myModalLabel33').text('Edit Pengguna');
                $('#inlineForm').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        })
    }

    $('#inlineForm').on('hidden.bs.modal', function (e) {
        $('#inlineForm input[name="user_id"]').val('');
        $('#inlineForm input[name="name"]').val('');
        $('#inlineForm input[name="phone"]').val('');
        $('#inlineForm input[name="rt"]').val('');
        $('#myModalLabel33').text('Tambahkan Pengguna');
    });
</script>
<?= $this->endSection() ?>
