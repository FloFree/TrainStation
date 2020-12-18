<!DOCTYPE html>
<html>
<head>
<title>Pendolari Fondani | Stazioni - Itinerario</title>
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
	$treno = $_GET ['treno'];
	$searchPage = "";
	?>
		
	<div align="center">
		<form>
			<table>
				<tr>
					<td align="center" colspan="2">
						<h3>Itinerario Treno</h3>
					</td>
				</tr>
				<tr>
					<td align="right">
						<div class="form-group">Numero Treno: </div>
					</td>
					<td>
						<div class="form-group"><input type="text" name="treno" value="<?= $treno ?>" class="form-control" /></div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2"><input type="submit" value="&nbsp&nbsp cerca treno &nbsp&nbsp" /></td>
				</tr>
			</table>
		</form>
	</div>
	
	<div align="center">
	<?php
	if ($treno != null) {
		
		$urlTreno = "http://www.viaggiatreno.it/viaggiatrenonew/resteasy/viaggiatreno/cercaNumeroTrenoTrenoAutocomplete/";
		$urlComposition = $urlTreno . $treno;
		
		// echo $urlComposition;
		// echo '<hr>';
		// echo "Treno Inserito: ", $treno, "<br>";
		// echo "<br>\n";
		
		$jsonResponse = get_web_page ( $urlComposition );
		// var_dump ( $jsonResponse );
		// echo "<br>\n";
		
		// analizza stazione - start
		if ($jsonResponse != null) {
			$jsonList = explode ( "\n", $jsonResponse );
			$numStaz = sizeof ( $jsonList );
			//echo "Num: ", $numStaz, "<br>";
			if ($numStaz > 2) {
				echo "Scegliere Stazione<br>";
				for($index = 0; $index < ($numStaz - 1); $index ++) {
					// echo $index, ") ", $jsonList [$index], "<br>";
					$stazione = extactCode($jsonList [$index]);
					$percorso = extactName($jsonList [$index]);
					echo "<a href='percorso.php?staid=", $stazione, "&traid=", $treno, "' title='Guarda Percorso Treno'>Percorso da ",$percorso,"</a><br>";
				}
			} else {
				//echo "StazioneTrovata: ", $jsonList [0], "<br>";
				$stazione = extactCode($jsonList [0]);
				$percorso = extactName($jsonList [0]);
				echo "<a href='percorso.php?staid=", $stazione, "&traid=", $treno, "' title='Guarda Percorso Treno'>Percorso da ",$percorso,"</a><br>";
				$searchPage = "percorso.php?staid=". $stazione. "&traid=". $treno;
			}
		} else {
			echo "Treno Cercato Non Esistente<br>\n";
		}
		// analizza stazione - end
	}
	?>
	</div>
	
	<?php
	// common function - get_web_page
	function extactCode($string) {
		$stepX = explode ( "|", trim ( $string ) );
		$stepY = explode ( "-", $stepX [1] );
		return $stepY [1];
	}

	function extactName($string) {
		$stepX = explode ( "|", trim ( $string ) );
		$stepY = explode ( "-", $stepX [0] );
		return $stepY [1];
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

<?php
if ($searchPage != null) {
	echo "<script language='javascript'>";
	echo 'self.location="'.$searchPage.'"';
	echo "</script>";
}
?>

<?php
include 'sector_03.php';
?>

</body>
</html>
