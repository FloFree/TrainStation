<!DOCTYPE html>
<html>
<head>
<title>Pendolari Fondani | Stazioni - Consulta</title>
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
$opera = $_POST ['opera'];
$numTreno = $_POST ['numTreno'];
$dateFrom = $_POST ['dateFrom'];
$dateTo = $_POST ['dateTo'];
$lateFrom = $_POST ['lateFrom'];
$lateTo = $_POST ['lateTo'];
$inLine = $_POST ['inLine'];
$today = date ( "d/m/Y" );

if ($opera!=null && trim($opera)!="") {

	//echo "opera: ", $opera,"<br>";
	//echo "dateFrom: ", $dateFrom,"<br>";
	//echo "dateTo: ", $dateTo,"<br>";
	
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

	$andNumTreno = "";
	if ($numTreno!=null){
		$andNumTreno = " and treno='".$numTreno."'";
	}
	
	$andDateFrom = "";
	if ($dateFrom!=null){
		$andDateFrom = " and data>=STR_TO_DATE('".$dateFrom."', '%d/%m/%Y')";
	}
	
	$andDateTo = "";
	if ($dateTo!=null){
		$andDateTo = " and data<=STR_TO_DATE('".$dateTo."', '%d/%m/%Y')";
	}
	
	$andLateFrom = "";
	if ($lateFrom!=null){
		$andLateFrom = " and ritardo>='".$lateFrom."'";
	}
	
	$andLateTo = "";
	if ($lateTo!=null){
		$andLateTo = " and ritardo<='".$lateTo."'";
	}
	
	$andInLine = "";
	if ($inLine!=null){
		$andInLine = " and in_line='si'";
	}
	
	$sqlNum = "SELECT DATE_FORMAT(sg.data, '%d/%m/%Y') data, sg.treno, sg.in_line, es.* FROM tb_segnalazioni sg, tb_estratti es WHERE sg.chiave=es.chiave"
			. $andNumTreno . $andDateFrom . $andDateTo . $andLateFrom . $andLateTo . $andInLine
			. " order by sg.data desc, es.stop_time desc";
	//echo "query: " . $sqlNum . "<br>";
	$resultNum = $conn->query ( $sqlNum );
?>
	<div align="center">
		<table width="80%">
		
			<tr>
				<td align="center" colspan="7">
					<h3>Consulta Segnalazioni</h3>
				</td>
			</tr>
		
			<tr>
				<td align="center" colspan="7">
				<hr>
				Data da <?= $dateFrom ?> a <?= $dateTo ?><br>
				Numero treno <?= $numTreno ?><br>
				Ritardo da <?= $lateFrom ?> a <?= $lateTo ?><br>
				<hr>
				</td>
			</tr>
			
			<tr>
				<td><b>Data</b></td>
				<td><b>Treno</b></td>
				<td><b>Partenza</b></td>
				<td><b>Arrivo</b></td>
				<td><b>Fermata</b></td>
				<td><b>Ritardo</b></td>
				<td></td>
			</tr>

<?php
	if ($resultNum->num_rows > 0) {
		while ( $row = $resultNum->fetch_assoc () ) {
			//echo "treno #" .$row["treno"]. " del " .$row["data"]. " (" .$row["orig_desc"]. " ---> " .$row["dest_desc"]. ") #" . $row["ritardo"]. " min. ritardo<br>";
?>
			<tr 
					<?php
					if ($row["in_line"]=="no"){echo "style='color: grey;'";}
					?>
			>
				<td><?= $row["data"] ?></td>
				<td><a href="registrazione.php?chiave=<?= $row["chiave"] ?>"><?= $row["treno"] ?></a></td>
				<td><?= $row["orig_desc"] ?></td>
				<td><?= $row["dest_desc"] ?></td>
				<td><?= $row["stop_desc"] ?></td>
				<td><?= $row["ritardo"] ?></td>
				<td>
					<?php
					if ($row["in_line"]=="no"){echo "out line";}
					?>
				</td>
			</tr>
<?php
		}
	} else {
    	echo "<td align=\"center\" colspan=\"5\">0 results</td>";
	}
	
?>
		</table>
	</div>
<?php
		
} else {
?>

	<div align="center">
		<form method="post">
			<table>
				<tr>
					<td align="center" colspan="2">
						<h3>Consulta Segnalazioni</h3>
					</td>
				</tr>
				
				<tr>
					<td align="right"><div class="form-group">Data da: </div></td>
					<td>

<div class="form-group"> 
	<input class="form-control" id="dateFrom" name="dateFrom" placeholder="DD/MM/YYY" type="text"/>
</div>
<script>
    $(document).ready(function(){
      var date_input=$('input[name="dateFrom"]'); 
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: 'dd/mm/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);
    })
</script>						
					
					</td>
				</tr>
				
				<tr>
					<td align="right"><div class="form-group">a: </div></td>
					<td>
					
<div class="form-group"> 
	<input class="form-control" id="dateTo" name="dateTo" placeholder="DD/MM/YYY" type="text"/>
</div>
<script>
    $(document).ready(function(){
      var date_input=$('input[name="dateTo"]');
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: 'dd/mm/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);
    })
</script>					

					</td>
				</tr>

				<tr>
					<td align="right"><div class="form-group">Numero treno: </div></td>
					<td><div class="form-group"><input type="text" name="numTreno" value="" class="form-control" /></div></td>
				</tr>

				<tr>
					<td align="right"><div class="form-group">Ritardo da: </div></td>
					<td><div class="form-group"><input type="text" name="lateFrom" value="" class="form-control" /></div></td>
				</tr>
				
				<tr>
					<td align="right"><div class="form-group">a: </div></td>
					<td><div class="form-group"><input type="text" name="lateTo" value="" class="form-control" /></div></td>
				</tr>

				<tr>
					<td align="right"><div class="form-group">in line: </div></td>
					<td><div class="form-group"><input type="checkbox" name="inLine" value="inLine" class="form-control" checked /></div></td>
				</tr>
				
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="&nbsp&nbsp visualizza rapporti &nbsp&nbsp" /><br />
						<input type="hidden" id="opera" name="opera" value="yes" />
						<input type="hidden" id="today" name="today" value="<?= $today ?>" />
						<br />
						<br />
					</td>
				</tr>
			</table>
		</form>
	</div>

<?php
}
?>

<?php
include 'sector_03.php';
?>

</body>
</html>
