<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>PURCHASE ORDER</title>
	<style>
		@page {
			margin-top: 150px;
			/* reserve enough space for header */
			margin-bottom: 80px;
			/* reserve enough space for footer */
			margin-left: 25px;
			margin-right: 25px;
		}

		header {
			position: fixed;
			top: -130px;
			/* must match @page margin-top */
			left: 0;
			right: 0;
			height: 150px;
			/* same as margin-top */
			text-align: center;
		}

		footer {
			position: fixed;
			bottom: -80px;
			/* must match @page margin-bottom */
			left: 0;
			right: 0;
			height: 80px;
			/* same as margin-bottom */
			text-align: center;
			font-size: 10px;
		}

		main {
			margin-top: 0;
			/* remove extra margin */
		}

		body {
			font-size: 12px;
			font-family: DejaVu Sans, sans-serif;
		}

		.flex {
			display: flex;
			justify-content: space-between;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
			page-break-inside: auto;
		}

		.fordelivery th,
		.fordelivery td {
			border: 1px solid #000;
			padding: 5px;
		}

		.fordelivery tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}

		.mt-10 {
			margin-top: 40px;
		}

		.signature {
			width: 45%;
			border-top: 1px solid #000;
			text-align: center;
			padding-top: 5px;
		}
	</style>
</head>

<body>

	<!-- Header (repeated on every page) -->
	<header>
		<img src="<?= base_url('public/images/imgHeader.png') ?>" height="120">
	</header>

	<main>
		<table border="0" style="width:100%; ">
			<!-- <tr>
				<td style="text-align:right;"></td>
				<td></td>
				<td style="text-align:left;color: rgb(77,102,117);font-size: 16px;font-weight: 600;"><strong>DR #:</strong> <?= $dr_number ?></td>
			</tr> -->


			<tr>
				<td style="text-align:left;">
					<p><strong>SUPPLIER:</strong> <?= $supplier ?></p>
					<p><strong>ATTENTION:</strong> <?= $attention ?></p>
				</td>
				<td></td>
				<td style="text-align:left;">
					<p><strong>ORDER DATE:</strong> <?= $orderDate ?></p>
					<p><strong>PO REF. NO:</strong> <?= $ref_po ?></p>
				</td>
			</tr>

			
		</table>

		<h2 class="text-center">PURCHASE ORDER</h2>

		<!-- Items Table -->
		<table class="fordelivery">
			<thead>
				<tr>
					<th>ITEM</th>
					<th>DESCRIPTION</th>
					<th>P.O QTY</th>
					<th>U.M</th>
					<th>UNIT COST</th>
					<th>AMOUNT</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cart as $item): ?>
					<tr>
						<td><?= $item['name'] ?></td>
						<td><?= $item['description'] ?></td>
						<td class="text-right"><?= $item['quantity'] ?></td>
						<td class="text-center"><?= $item['unit'] ?></td>
						<td class="text-right">&#8369; <?= number_format($item['price'], 2) ?></td>
						<td class="text-right">&#8369; <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
					</tr>
				<?php endforeach; ?>

				<tr>
					<td colspan="5" class="text-right"><strong>SUBTOTAL:</strong></td>
					<td class="text-right"><strong>&#8369; <?= number_format($totalAmount, 2) ?></strong></td>
				</tr>

				<?php if (!empty($discountComputation) && $discountComputation > 0): ?>
					<tr>
						<td colspan="5" class="text-right"><strong><?=$discount?>% DISCOUNT:</strong></td>
						<td class="text-right"><strong>&#8369; <?= number_format($discountComputation, 2) ?></strong></td>
					</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
					<td class="text-right"><strong>&#8369; <?= number_format($grandTotalAmount, 2) ?></strong></td>
				</tr>
			</tfoot>
		</table>


		<table border="0" class="mt-10" style="width:100%; ">
			<tr>
				<td style="text-align:left;">
					<div class="signature">PREPARED BY</div>
				</td>
				<td style="text-align:right;">
					<div class="signature">APPROVED BY</div>
				</td>
			</tr>


		</table>

		
	</main>

</body>

</html>