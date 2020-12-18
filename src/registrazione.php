<!DOCTYPE html>
<html>
<head>
<title>Pendolari Fondani | Stazioni - Registrazione</title>
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
	$chiave = $_GET ['chiave'];
	
	$servername = "localhost";
	$username = "pendolarifondani";
	$password = "pendolarifondani";
	$dbname = "my_pendolarifondani";
	
	// Create connection
	$conn = new mysqli ( $servername, $username, $password, $dbname );
	// Check connection
	if ($conn->connect_error) {
		die ( "Connection failed: " . $conn->connect_error );
	}
	
	$tbPercorso = null;
	$tbData = null;
	$sql = "SELECT * FROM tb_segnalazioni sg WHERE sg.chiave='$chiave'";
	$result = $conn->query ( $sql );
	if ($result->num_rows > 0) {
		while ( $row = $result->fetch_assoc () ) {
			$tbPercorso = $row["percorso"];
			$tbData = $row["data"];
		}
	}
	$jsonData = json_decode ( $tbPercorso, true );

	// parsing
	$jsonTrain = $jsonData ['categoria'] . " " . $jsonData ['numeroTreno'];
	
	$jsonOrigStaz = $jsonData ['origineZero'];
	$jsonOrigOra = convert_date ( $jsonData ['orarioPartenzaZero'] );
	
	$jsonDestStaz = $jsonData ['destinazioneZero'];
	$jsonDestOra = convert_date ( $jsonData ['orarioArrivoZero'] );
	
	$jsonInterStaz = $jsonData ['stazioneUltimoRilevamento'];
	$jsonInterOra = convert_date ( $jsonData ['oraUltimoRilevamento'] );
	
	$jsonRitardo = $jsonData ['compRitardo'];
	?>

<div class="row">
	<div class="col-md-6">
		<table width="100%">
			<tr>
				<td width="30%"></td>
				<td width="40%"></td>
				<td width="30%"></td>
			</tr>
			<tr>
				<td colspan="3">
				<hr>
				Treno <?= $jsonTrain ?> del <?= date('d/m/Y', strtotime($tbData) ) ?><br>
				<hr>
				Partenza: <?= $jsonOrigOra ?> - <?= $jsonOrigStaz ?><br>
				Arrivo: <?= $jsonDestOra ?> - <?= $jsonDestStaz ?><br>
				<hr>
				Situazione: <?= $jsonInterOra ?> - <?= $jsonInterStaz ?> (<?= $jsonRitardo[0] ?>)<br>
				<hr>
				</td>
			</tr>
			<?php
	// analization
	$jsonFermate = $jsonData ['fermate'];
	$numStaz = sizeof ( $jsonFermate );
	
	for($index = 0; $index < $numStaz; $index ++) {
		$elemDef = $jsonFermate [$index];
		?>
		<tr>
				<td colspan="4" align="left"><?= $elemDef ['stazione'] ?></td>
			</tr>
			<tr>
				<td align="right">arrivo:</td>
				<td align="left"><?=  convert_date ( $elemDef ['arrivo_teorico'] ) ?> <img
					src="img/freccia.jpg"> <?=  convert_date ( $elemDef ['arrivoReale'] ) ?></td>
			</tr>
			<tr>
				<td align="right">partenza:</td>
				<td align="left"><?=  convert_date ( $elemDef ['partenza_teorica'] ) ?> <img
					src="img/freccia.jpg"> <?=  convert_date ( $elemDef ['partenzaReale'] ) ?></td>
			</tr>
		<?php
	}
	?>
	</table>
	</div>
	
	<?php
	// common function - convert_date
	function convert_date($dataUnix) {
		$convertito = '--';
		if ($dataUnix != null) {
			$date = date_create ();
			date_timestamp_set ( $date, $dataUnix / 1000 );
			$convertito = date_format ( $date, 'H:i' );
		}
		return $convertito;
	}
	
	// common function - get_web_page
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
	
	<div class="col-md-6">
		<form action="segnala.php" method="post">
			<table width="100%">
				
	<?php
	
	$sql = "SELECT DATE_FORMAT(sg.data, '%d/%m/%Y') data, sg.treno, es.* FROM tb_segnalazioni sg, tb_estratti es WHERE sg.chiave=es.chiave AND sg.chiave='$chiave'";
	$result = $conn->query ( $sql );	
	if ($result->num_rows > 0) {
		while ( $row = $result->fetch_assoc () ) {
	?>
	
				<tr>
					<td align="center" colspan="2">
						<hr>
						<u>Ultima Rilevazione Effettuata</u>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<ul style="margin-left: 100px">
						<li> Ore: <?= date( 'H:i', strtotime ($row["stop_time"] ) ) ?>
						<li> Stazione: <?= $row["stop_desc"] ?>
						<li> Ritardo: <?= $row["ritardo"] ?>
					</ul>
					</td>
				</tr>
				
	<?php
		}
		
		$sqlNum = "SELECT * FROM tb_note WHERE chiave='$chiave' order by hit_num desc";
		$resultNum = $conn->query ( $sqlNum );
		if ($resultNum->num_rows > 0) {

	?>

				<tr>
					<td align="center" colspan="2">
						<hr>
						<u>Note</u>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<ul style="margin-left: 100px">
				
	<?php
			while ( $row = $resultNum->fetch_assoc () ) {
	?>
	
						<li><?= date( 'H:i', strtotime ($row["in_time"] ) ) ?>) <?= $row["nota"] ?>
				
	<?php
			}
	?>
	
					</ul>
					</td>
				</tr>
	
	<?php
		}
		
	}
	?>

			</table>
		</form>
	</div>
</div>
	
<?php
include 'sector_03.php';
?>
	
</body>
</html>
