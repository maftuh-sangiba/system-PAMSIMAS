<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div class="row">
        <div class="col-8">
            <h3>Selamat datang <span class="text-primary"><?= $name ?></span></h3>
        </div>
        <div class="col text-end">
            <a href="/logout" class="btn btn-danger"><i class="bi bi-power"></i><span> Logout</span></a>
        </div>
    </div>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Pelanggan</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_pelanggan ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="iconly-boldSetting"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Meteran</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_meteran ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon green">
                                        <i class="iconly-boldTick-Square"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Lunas</h6>
                                    <h6 class="font-extrabold mb-0"><?= $lunas ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon red">
                                        <i class="iconly-boldClose-Square"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Unpaid</h6>
                                    <h6 class="font-extrabold mb-0"><?= $unpaid ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl">
                            <img src="/assets/images/faces/1.jpg" alt="Face 1">
                        </div>
                        <div class="ms-3 name">
                            <h5 class="font-bold"><?= $name ?></h5>
                            <h6 class="text-muted mb-0"><?= $email ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Total Biaya Bulanan</h4>
                </div>
                <div class="card-body">
                    <div id="chart-dashboard"></div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="/assets/vendors/apexcharts/apexcharts.js"></script>
<!-- <script src="/assets/js/pages/dashboard.js"></script> -->
<script>
    var monthlyBiayaCount = <?= json_encode($total) ?>;

    var months = [];
    var biayaCounts = [];
    var month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    monthlyBiayaCount.forEach(function(item) {
        months.push(item.month);
        biayaCounts.push(item.biaya_count);
    });

    var options = {
        annotations: {
		    position: 'back'
        },
        dataLabels: {
            enabled:false
        },
        chart: {
            type: 'bar',
            height: 300
        },
        fill: {
            opacity:1
        },
        colors: '#435ebe',
        series: [{
            name: 'Total Biaya',
            data: biayaCounts
        }],
        xaxis: {
            categories: months.map(function(month) {
                return month_names[month];
            })
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart-dashboard"), options);
    chart.render();
</script>
<?= $this->endSection() ?>