<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR</title>
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <style>
        @media print {
            .card-footer {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-dark">

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-7 col-lg-3 cetak">
                <div class="card">
                    <div class="card-body p-5 text-center">
                        <h5 class="card-title"><?= $qrCode ?></h5>
                        <p class="card-text fw-bold h4 text-dark pt-3"><?= $nomorMeteran ?></p>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col text-center">
                                <button type="button" class="btn btn-warning" onclick="printContent()"><i class='bi bi-printer'></i> Print</button>
                                <button type="button" class="btn btn-primary" onclick="downloadAsImage()"><i class='bi bi-download'></i> Download</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url() ?>public/assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script>
        function printContent(){
            window.print();
        }

        function downloadAsImage(){
            var cardElement = document.querySelector('.cetak');
            var cardClone = cardElement.cloneNode(true);
            cardClone.querySelector('.card-footer').remove();
            document.body.appendChild(cardClone);

            html2canvas(cardClone).then(function(canvas) {
                var link = document.createElement("a");
                document.body.appendChild(link);
                link.download = "card_image.png";
                link.href = canvas.toDataURL("image/png");
                link.target = '_blank';
                link.click();
                document.body.removeChild(link);
                document.body.removeChild(cardClone);
            });
        }
    </script>
</body>

</html>