<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Girik</title>
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendors/bootstrap-icons/bootstrap-icons.css">
</head>

<body>
    <div class="container text-center p-5">
        <div class="col col-sm-12 col-lg-6 mx-auto">
            <table class="table table-bordered border-dark">
                <thead>
                    <tr>
                        <th class="text-start p-4 text-dark">
                            <div class="row align-items-center">
                                <div class="col-3 col-sm-12 col-md-2 col-lg-2 align-self-center text-center">
                                    <img src="<?= base_url('public/assets/images/logo/logo-air.png') ?>" alt="logo" width="50">
                                </div>
                                <div class="col-9 col-sm-12 col-md-10 col-lg-10 text-sm-center text-lg-start mt-lg-0 mt-sm-3">
                                    <div class="row">
                                        <span class="p-0">PENYEDIA AIR MINUM & SANITASI BERBASIS MASYARAKAT</span>
                                    </div>
                                    <div class="row">
                                        <span class="h2 p-0 m-0 text-dark">PAMSIMAS</span>
                                    </div>
                                    <div class="row">
                                        <span class="p-0">Desa. Nganu, Kec. Nganu, Kab. Nganu, Jawa Tengah</span>
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-4 text-dark">
                            <p class="fs-4 fw-bold">
                                TAGIHAN PEMBAYARAN
                            </p>
                            <table class="text-start mx-auto table table-responsive table-borderless text-dark border border-dark">
                                <tr>
                                    <td>Bulan</td>
                                    <td width="20" class="text-center"> : </td>
                                    <td><?= $pembayaran->bulan ?></td>
                                </tr>
                                <tr>
                                    <td>Nomor Meteran</td>
                                    <td class="text-center"> : </td>
                                    <td><?= $pembayaran->nomor_meteran ?></td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td class="text-center"> : </td>
                                    <td><?= $pembayaran->name ?></td>
                                </tr>
                                <tr>
                                    <td>Pemakaian</td>
                                    <td class="text-center"> : </td>
                                    <td><?= $pembayaran->pemakaian ?><span> m<sup>3</sup></span></td>
                                </tr>
                                <tr>
                                    <td>Tagihan</td>
                                    <td class="text-center"> : </td>
                                    <td>Rp. <?= $pembayaran->biaya ?></td>
                                </tr>
                                <tr>
                                    <td>Status Pembayaran</td>
                                    <td class="text-center"> : </td>
                                    <td><?= $pembayaran->status ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="<?= base_url() ?>public/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>