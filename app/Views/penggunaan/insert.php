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
                <h3>Input Data Penggunaan Air</h3>
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

    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" action="<?= base_url() ?>penggunaan/store" method="post">
                                <div class="row">
                                    <div class="row">
                                        <h4>Bulan</h4>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="first-name-column">Pilih Bulan</label>
                                                <input id="bulan" class="form-control" name="bulan" width="250" type="month" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h4>Meteran</h4>
                                            <div class="form-group">
                                                <label for="first-name-column">Nomor Meteran</label>
                                                <select class="js-select-meteran form-select" style="width: 100%" name="nomor_meteran">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <h4>Pemakaian</h4>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="used-last">Bulan Lalu <span class="text-muted">(m<sup>3</sup>)</span></label>
                                                <input type="number" id="used-last" class="form-control" name="used-last" placeholder="0" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="used-this">Bulan Sekarang <span class="text-muted">(m<sup>3</sup>)</span></label>
                                                <input type="number" id="used-this" class="form-control" name="used-this" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <h4>Biaya</h4>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="pay-last">Bulan Lalu</label>
                                                <input type="number" id="pay-last" class="form-control" name="pay-last" placeholder="0" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="pay-this">Bulan Sekarang</label>
                                                <input type="number" id="pay-this" class="form-control" name="pay-this" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url() ?>public/assets/vendors/simple-datatables/style.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
        appearance:textfield;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url() ?>public/assets/vendors/simple-datatables/simple-datatables.js"></script>
<script src="<?= base_url() ?>public/assets/js/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $(".js-select-meteran").select2();

        getTodayMonth();
        let initBulan = $("#bulan").val();
        getData(initBulan);
    });

    $("#bulan").on('change', function(){
        let valBulan = $(this).val();
        getData(valBulan);
    });

    function getData(date){
        $.ajax({
            url: "<?= base_url() ?>penggunaan/getAllMeteran",
            type: "POST",
            data: {
                date: date,
            },
            dataType: 'json',
            success: function(result) {
                $(".js-select-meteran").empty();
                
                var defaultOption = new Option('Pilih Meteran', '');
                $(defaultOption).prop('disabled', true).prop('readonly', true).prop('selected', true);
                $(".js-select-meteran").append(defaultOption);

                $.each(result.data, function(index, value){
                    var addOption = new Option(value.nomor_meteran, value.id, false, false);
                    $(".js-select-meteran").append(addOption);
                })
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        })
    }

    function getTodayMonth(){
        var date = new Date();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        if (month < 10) month = "0" + month;
        var today = year + "-" + month;       
        $("#bulan").attr("value", today);
    }

    $("#used-this").on('change', function(){
        let valUsed = $(this).val();
        setPay(valUsed);       
    });

    function setPay(valUsed) {
        let pricePerMeter = 2500;
        let beban = 1500;
        let lastUsed = $("#used-last").val();
        let finalUsed = valUsed - lastUsed;

        let finalPay = finalUsed * pricePerMeter + beban; 
        $("#pay-this").val(finalPay);
    }
</script>
<?= $this->endSection() ?>
