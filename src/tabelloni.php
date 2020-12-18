<!DOCTYPE html>
<html>
<head>
<title>Pendolari Fondani | Stazioni - Tabelloni</title>
<link href="css/style-diva.css" rel="stylesheet" type="text/css" />
<?php
include 'sector_01.php';
?>

</head>
<body>

<?php
include 'sector_02.php';
?>

<?php
$stazioniLista = array (
		"",
		"ROMA TERMINI|S08409",
		"TORRICOLA|S08600",
		"POMEZIA - S.PALOMBA|S08601",
		"CAMPOLEONE|S08602",
		"CISTERNA DI LATINA|S08603",
		"LATINA|S08608",
		"SEZZE ROMANO|S08609",
		"PRIVERNO FOSSANOVA|S08604",
		"MONTE S.BIAGIO|S08605",
		"FONDI - SPERLONGA|S08606",
		"ITRI|S08607",
		"FORMIA - GAETA|S08640",
		"MINTURNO|S09150" 
);

$stazioneCercata = $_GET ['staz'];
// echo "Stazione Cercata NUM: ", $stazioneCercata, "<br>";
// echo "Stazione Cercata VAL: ", $stazioniLista [$stazioneCercata], "<br>";
// echo "<br>";

$stazioneDef = explode ( "|", $stazioniLista [$stazioneCercata] );
// echo "Stazione Cercata NOME: ", $stazioneDef [0], "<br>";
// echo "Stazione Cercata ID: ", $stazioneDef [1], "<br>";
// echo "<br>";

$urlArrivi = "http://www.viaggiatreno.it/viaggiatrenonew/resteasy/viaggiatreno/arrivi";
$urlPartenze = "http://www.viaggiatreno.it/viaggiatrenonew/resteasy/viaggiatreno/partenze";
date_default_timezone_set ( 'GMT+0100' );
$parTime = date ( "D M d Y H:i:s" ) . " GMT+0100 (ora solare Europa occidentale)";
$urlArriviVal = $urlArrivi . '/' . $stazioneDef [1] . '/' . rawurlencode ( $parTime );
$urlPartenzeVal = $urlPartenze . '/' . $stazioneDef [1] . '/' . rawurlencode ( $parTime );

// echo $urlArriviVal;
// echo '<hr>';
// echo $urlPartenzeVal;
// echo '<hr>';

$jsonArriviResponse = get_web_page ( $urlArriviVal );
$jsonArriviData = json_decode ( $jsonArriviResponse, true );

$jsonPartenzeResponse = get_web_page ( $urlPartenzeVal );
$jsonPartenzeData = json_decode ( $jsonPartenzeResponse, true );
?>

	<table border="0" width="100%">
		<tr>
			<td align="center" colspan="2">
				<h3>Guarda i Tabelloni</h3>
			</td>
		</tr>
		<tr>
			<td width="20%" valign="top">
				<!-- sector link / start-->

				<div class="gadget">
					<div class="clr"></div>
					<ul class="sb_menu">
<?php
$numStaz = sizeof ( $stazioniLista );
for($index = 1; $index < $numStaz; $index ++) {
	$elemDef = $stazioniLista [$index];
	$elemExp = explode ( "|", $elemDef );
	?>

				<li><a href="tabelloni.php?staz=<?= $index ?>"><?= $elemExp[0] ?></a></li>

<?php
}
?>
				</ul>
				</div> <!-- sector link / end -->
			</td>

			<td width="80%" valign="top">
				<!-- sector tabella / start-->
			
<?php
if (! empty ( $stazioneDef [0] )) {
	?>
			
				<table border="0" width="100%">
					<tr>
						<td colspan="2" align="center"><?= $stazioneDef [0] ?></td>
					</tr>

					<tr>
						<td width="50%" valign="top" align="center">
<?php
	echo "<table border=\"1\" width=\"100%\">";
	echo "<tr><td colspan=\"5\" align=\"center\">ARRIVI</td></tr>";
	echo "<tr>";
	echo "<td>Treno</td>";
	echo "<td>Origine</td>";
	echo "<td>Arrivo</td>";
	echo "<td>Binario</td>";
	echo "<td>Ritardo</td>";
	echo "</tr>";
	foreach ( $jsonArriviData as $train_name => $train ) {
		echo "<tr>";
		echo "<td><a href='percorso.php?staid=", $train ['codOrigine'], "&traid=", $train ['numeroTreno'], "' title='Guarda Percorso Treno'>", $train ['compNumeroTreno'], "</a></td>";
		echo "<td>", $train ['origine'], "</td>";
		echo "<td>", $train ['compOrarioArrivo'], "</td>";
		echo "<td>", $train ['binarioEffettivoArrivoDescrizione'], " / ", $train ['binarioProgrammatoArrivoDescrizione'], "</td>";
		echo "<td>", $train ['ritardo'], "</td>";
		echo "</tr>";
	}
	echo "</table>";
	?>
						</td>
						<td width="50%" valign="top" align="center">
<?php
	echo "<table border=\"1\" width=\"100%\">";
	echo "<tr><td colspan=\"5\" align=\"center\">PARTENZE</td></tr>";
	echo "<tr>";
	echo "<td>Treno</td>";
	echo "<td>Destinazione</td>";
	echo "<td>Partenza</td>";
	echo "<td>Binario</td>";
	echo "<td>Ritardo</td>";
	echo "</tr>";
	foreach ( $jsonPartenzeData as $train_name => $train ) {
		echo "<tr>";
		echo "<td><a href='percorso.php?staid=", $train ['codOrigine'], "&traid=", $train ['numeroTreno'], "' title='Guarda Percorso Treno'>", $train ['compNumeroTreno'], "</a></td>";
		echo "<td>", $train ['destinazione'], "</td>";
		echo "<td>", $train ['compOrarioPartenza'], "</td>";
		echo "<td>", $train ['binarioEffettivoPartenzaDescrizione'], " / ", $train ['binarioProgrammatoPartenzaDescrizione'], "</td>";
		echo "<td>", $train ['ritardo'], "</td>";
		echo "</tr>";
	}
	echo "</table>";
	?>
						</td>
					</tr>

				</table>

<?php
}
?>
			<!-- sector tabella / end-->
			</td>
		</tr>
	</table>

<?php
function get_web_page($url) {
	$options = array (
			CURLOPT_RETURNTRANSFER => true, // return web page
			CURLOPT_HEADER => false, // don't return headers
			CURLOPT_FOLLOWLOCATION => true, // follow redirects
			CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
			CURLOPT_ENCODING => "", // handle compressed
			CURLOPT_USERAGENT => "test", // name of client
			CURLOPT_AUTOREFERER => true, // set referrer on redirect
			CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
			CURLOPT_TIMEOUT => 120  // time-out on response
		);
	
	$ch = curl_init ( $url );
	curl_setopt_array ( $ch, $options );
	
	$content = curl_exec ( $ch );
	
	curl_close ( $ch );
	
	return $content;
}
?>

<?php
include 'sector_03.php';
?>

</body>
</html>
