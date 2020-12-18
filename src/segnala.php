<!DOCTYPE html>
<html>
<head>
<title>Pendolari Fondani | Stazioni - Segnala</title>
<link href="css/style-diva.css" rel="stylesheet" type="text/css" />
<?php
include 'sector_01.php';
?>

</head>
<body>

<?php
include 'sector_02.php';
?>

<div align="center">
	<p>ESITO OPERAZIONE</p>
	<hr>
<p>

<?php
date_default_timezone_set ( 'GMT+0100' );
$today = date ( "Ydm" );
$stationID = $_POST ['staid'];
$trainID = $_POST ['traid'];
$inLine = $_POST ['inlin'];
$nota = $_POST ['nota'];
$chiave = $today . "-" . $stationID . "-" . $trainID;
$operation = "";

// echo "cerca chiave: ", $chiave;
// echo "<br>";

if ($stationID != null && $trainID != null) {
	
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
	
	// Disable autocommit
	$conn->autocommit ( FALSE );
	
	$sqlNum = "SELECT * FROM tb_segnalazioni WHERE chiave = '$chiave'";
	$resultNum = $conn->query ( $sqlNum );
	$numHit = 0;
	if ($resultNum->num_rows > 0) {
		while ( $rowNum = $resultNum->fetch_assoc () ) {
			$numHit = $rowNum ["hit_tot"];
		}
	}
	$numHit = $numHit + 1;
	
	$urlTreno = "http://www.viaggiatreno.it/viaggiatrenonew/resteasy/viaggiatreno/andamentoTreno/";
	$defTreno = $stationID . "/" . $trainID;
	$urlComposition = $urlTreno . $defTreno;
	
	$jsonResponse = get_web_page ( $urlComposition );
	$jsonData = json_decode ( $jsonResponse, true );
	
	$extOrigCod = $jsonData ['idOrigine'];
	$extOrigDesc = $jsonData ['origine'];
	$extDestCod = $jsonData ['idDestinazione'];
	$extDestDesc = $jsonData ['destinazione'];
	$extRitardo = $jsonData ['ritardo'];
	
	$extOrigOra = $jsonData ['orarioPartenzaZero'] / 1000;
	$extDestOra = $jsonData ['orarioArrivoZero'] / 1000;
	$extInterStaz = $jsonData ['stazioneUltimoRilevamento'];
	$extInterOra = $jsonData ['oraUltimoRilevamento'] / 1000;
	
	$sqlSegna = "";
	$sqlEstra = "";
	
	if ($numHit > 1) {
		// esegui update
		// echo "esegui update";
		// echo "<br>";
		$operation = "up";
		
		$sqlSegna = "UPDATE tb_segnalazioni SET hit_tot = '$numHit', up_time = NOW(), percorso = '$jsonResponse'
		WHERE chiave = '$chiave'";
		
		$sqlEstra = "UPDATE tb_estratti SET ritardo = '$extRitardo', stop_desc = '$extInterStaz', stop_time = FROM_UNIXTIME('$extInterOra')
		WHERE chiave = '$chiave'";
	} else {
		// esegui insert
		// echo "esegui insert";
		// echo "<br>";
		$operation = "in";
		
		$sqlSegna = "INSERT INTO tb_segnalazioni (chiave, data, origine, treno, hit_tot, in_time, percorso, in_line)
		VALUES ('$chiave', NOW(), '$stationID', '$trainID', '$numHit', NOW(), '$jsonResponse', '$inLine')";
		
		$sqlEstra = "INSERT INTO tb_estratti (chiave, orig_cod, orig_desc, orig_time, dest_cod, dest_desc, dest_time, ritardo, stop_desc, stop_time)
		VALUES ('$chiave', '$extOrigCod', '$extOrigDesc', FROM_UNIXTIME('$extOrigOra'), '$extDestCod', '$extDestDesc', FROM_UNIXTIME('$extDestOra'), '$extRitardo', '$extInterStaz', FROM_UNIXTIME('$extInterOra'))";
	}
	
	$execute = "OK";
	if ($conn->query ( $sqlSegna ) === TRUE) {
		
		if ($conn->query ( $sqlEstra ) === TRUE) {
			
			if ($nota != null) {
				
				$sqlNote = "INSERT INTO tb_note (chiave, hit_num, nota, in_time)
				VALUES ('$chiave', '$numHit', '$nota', NOW())";
				
				if ($conn->query ( $sqlNote ) === TRUE) {
					// echo "OK";
					// echo "<br>";
				} else {
					// echo "ERROR:<br>" . $conn->error;
					// echo "<br>";
					$execute = "KO";
				}
			} else {
				// echo "OK";
				// echo "<br>";
			}
		} else {
			// echo "ERROR:<br>" . $conn->error;
			// echo "<br>";
			$execute = "KO";
		}
	} else {
		// echo "ERROR:<br>" . $conn->error;
		// echo "<br>";
		$execute = "KO";
	}
	
	if ($execute == "OK") {
		// Commit transition
		$conn->commit ();
		// echo "Commit<br>";
		if ($operation == "in") {
			echo "Operazione effettuata: dati inseriti correttamente";
		} else if ($operation == "up") {
			echo "Operazione effettuata: dati aggiornati correttamente";
		} else {
			echo "Operazione non effettuata: anomalia sul sistema";
		}
		
	} else {
		// Rollback transition
		$conn->rollback ();
		// echo "Rollback<br>";
		echo "Operazione non effettuata: si è verificato un errore";
		
	}
	
	
	$conn->close ();
} else {
	
	echo "Operazione non effettuata: parametri stazione/treno incorretti";
	
}

?> 

</p>
</div>

<?php
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

<?php
include 'sector_03.php';
?>

</body>
</html>
