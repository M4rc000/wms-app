<?php
	$Date = date('d F Y', strtotime($delivery[0]['Delivery_date']));
	$logo_path = FCPATH . 'assets/img/CKC.png';
	$logo_data = base64_encode(file_get_contents($logo_path));
	$logo_type = pathinfo($logo_path, PATHINFO_EXTENSION);
	$logo_src = 'data:image/' . $logo_type . ';base64,' . $logo_data;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SURAT JALAN</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }

    .header {
      width: 100%;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .header-left {
      float: left;
      width: 65%;
      text-align: center;
    }

    .header-right {
      float: right;
      width: 35%;
      font-size: 12px;
      text-align: right;
      line-height: 1.6;
    }

    .header-left img {
      width: 110px;
    }

    .header-left .company-name {
      font-weight: bold;
      font-size: 16px;
      margin-top: 5px;
    }

    table {
      width: 100%;
      margin-top: 40px;
      margin-bottom: 10px;
			border-collapse: collapse;
    }

    th.letter, td.letter {
      border: 1px solid black;
      padding: 6px;
      text-align: center;
      vertical-align: middle;
      word-break: break-word;
    }

    th[colspan="6"] {
      font-size: 18px;
    }

    .signature-row td {
      height: 70px;
    }
  </style>
</head>
<body>

<table style="width: 100%; margin-bottom: 5px;">
  <tr>
    <!-- Logo kiri -->
    <td style="width: 10%; text-align: left;">
      <img src="<?= $logo_src ?>" alt="Logo" style="width: 100px;">
    </td>

    <!-- Nama perusahaan tengah -->
    <td style="width: 60%; text-align: center;">
      <span style="font-weight: bold; font-size: 17px; letter-spacing: 1.5px;">PT CAHAYA KAROMAH CEMERLANG</span>
    </td>

    <!-- Info surat jalan kanan -->
    <td style="width: 30%; text-align: right; font-size: 10px;">
      <?= htmlspecialchars($delivery[0]['Client_name']) ?><br>
      NO SJ : <?= htmlspecialchars($delivery[0]['No_SJ']) ?><br>
      DATE : <?= $Date ?><br>
      NO. PO : <?= htmlspecialchars($delivery[0]['No_PO']) ?>
  </td>
  </tr>
</table>

<table class="letter">
  <thead>
    <tr>
      <th class="letter" colspan="6">SURAT JALAN</th>
    </tr>
    <tr>
      <th class="letter" style="width: 7%;">NO</th>
      <th class="letter" style="width: 20%;">PART NUMBER</th>
      <th class="letter" style="width: 35%;">PART NAME</th>
      <th class="letter" style="width: 13%;">QTY</th>
      <th class="letter" style="width: 13%;">UNIT</th>
      <th class="letter" style="width: 20%;">REMAKS</th>
    </tr>
  </thead>
  <tbody>
		<?php $number = 0; foreach($delivery as $dl): $number++;?>
    <tr>
      <td class="letter"><?=$number;?></td>
      <td class="letter"><?=$dl['Product_no'];?></td>
      <td class="letter"><?=$dl['Product_name'];?></td>
      <td class="letter"><?=$dl['Qty'];?></td>
      <td class="letter"><?=$dl['Unit'];?></td>
      <td class="letter"></td>
    </tr>
		<?php endforeach; ?>
    <!-- Baris kosong sampai 5 -->
    <?php for ($i = 2; $i <= 4; $i++): ?>
    <tr>
      <td class="letter"><?= $i ?></td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
    </tr>
    <?php endfor; ?>
    <tr>
      <td class="letter" colspan="2">DIBUAT</td>
      <td class="letter">DIKETAHUI</td>
      <td class="letter">DISETUJUI</td>
      <td class="letter">SECURITY</td>
      <td class="letter">DITERIMA</td>
    </tr>
    <tr class="signature-row">
      <td class="letter" colspan="2"></td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
    </tr>
    <tr>
      <td class="letter" colspan="2">PPIC</td>
      <td class="letter">PPIC</td>
      <td class="letter"></td>
      <td class="letter"></td>
      <td class="letter"></td>
    </tr>
  </tbody>
</table>
</body>
</html>
