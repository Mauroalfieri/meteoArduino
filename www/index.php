<?php
$rows = file("dati.log");
$humy = array();
$temp = array();

$data = null;

$vanno    = null;
$vmese    = null;
$vgiorno  = null;
$vora     = null;
$vmin     = null;

foreach ($rows as $line) {
        $vals = explode(" ", $line);
	
        $data   = explode( "/", $vals[0]);
		$giorno = $data[0];
		$mese   = $data[1];
		$anno   = $data[2];
		       
        $time  = explode( ":", $vals[1]);
		$ora   = $time[0];
		$min   = $time[1];
		$sec   = $time[2];
		
        $humy[ $anno ][ $mese ][ $giorno ][ $ora ][ $min ] = $vals[2];
        $temp[ $anno ][ $mese ][ $giorno ][ $ora ][ $min ] = $vals[3];
        $hsuo[ $anno ][ $mese ][ $giorno ][ $ora ][ $min ] = $vals[4];
        $tsuo[ $anno ][ $mese ][ $giorno ][ $ora ][ $min ] = $vals[5];
}

$viewdata = ( !empty($_GET['data']) )? $_GET['data'] : $anno . $mese . $giorno;
$vhumy = getValData( $humy[substr($viewdata,0,4)], $viewdata );
$vtemp = getValData( $temp[substr($viewdata,0,4)], $viewdata );
$vhsuo = getValData( $hsuo[substr($viewdata,0,4)], $viewdata );
$vtsuo = getValData( $tsuo[substr($viewdata,0,4)], $viewdata );

function getValData( $arr = array(), $data = null ) {
	$vmese    = substr($data,4,2);
	$vgiorno  = substr($data,6,2);
	$vora     = substr($data,8,2);
	
	if ( !empty( $vmese ) ) { $arr = $arr[$vmese]; } 
	else {
		if ( count( $arr ) <= 0) return $arr;
		foreach ($arr as $m => $value) { $_arr[$m]= end($value); }
		$arr = $_arr;
	}
	if ( !empty( $vgiorno ) ) { $arr = $arr[$vgiorno]; } 
	else {
		if ( count( $arr ) <= 0) return $arr;
		foreach ($arr as $g => $value) { $_arr[$g]= end($value); }
		$arr = $_arr;
	}
	if ( !empty( $vora ) ) {
		if ( count( $arr[$vora] ) <= 0) return $arr;
		foreach ($arr[$vora] as $h => $value) { if (($h%2) == 0) $_arr[$h] = $value; } $arr = $_arr; } 
	else {
		if ( count( $arr ) <= 0) return $arr;
		foreach ($arr as $h => $value) { $_arr[$h] = end($value); }
		$arr = $_arr;
	}
	
	return $arr;
}

function getPrevVal( $data = null, $rows = array() ) {
	$vanno    = substr($data,0,4);
	$vmese    = substr($data,4,2);
	$vgiorno  = substr($data,6,2);
	$vora     = substr($data,8,2);
	
	if (!empty($vora) )    { $_formato = "YmdH"; $_data = date('YmdH', mktime ($vora-1,0,0,$vmese,$vgiorno,$vanno)); }
	else if (!empty($vgiorno) ) { $_formato = "Ymd";  $_data = date('Ymd', mktime (0,0,0,$vmese,$vgiorno-1,$vanno)); }
	else if (!empty($vmese) )   { $_formato = "Ym";   $_data = date('Ym', mktime ($vora,0,0,$vmese-1,$vanno)); }
	else                        { $_formato = "Y";    $_data = $data; }
	
	if ( $_data >= getFirstData( $rows, $_formato ) ) { $data = $_data; }
	return $data;
}
function getNextVal( $data = null, $rows = array() ) {
	$vanno    = substr($data,0,4);
	$vmese    = substr($data,4,2);
	$vgiorno  = substr($data,6,2);
	$vora     = substr($data,8,2);
	
	if (!empty($vora) )    		{ $_formato = "YmdH"; $_data = date('YmdH', mktime ($vora+1,0,0,$vmese,$vgiorno,$vanno)); }
	else if (!empty($vgiorno) ) { $_formato = "Ymd";  $_data = date('Ymd', mktime (0,0,0,$vmese,$vgiorno+1,$vanno)); }
	else if (!empty($vmese) )   { $_formato = "Ym";   $_data = date('Ym', mktime ($vora,0,0,$vmese+1,$vanno)); }
	else                        { $_formato = "Y";    $_data = $data; }
	
	if ( $_data <= getEndData( $rows, $_formato ) ) { $data = $_data; }
	return $data;
}

function getFirstData( $date, $formato ) {
	$lastdate = explode(" ", reset( $date ));
	
    $data   = explode( "/", $lastdate[0]);
	$giorno = $data[0];
	$mese   = $data[1];
	$anno   = $data[2];
	       
    $time  = explode( ":", $lastdate[1]);
	$ora   = $time[0];
	
	if ( $formato == "YmdH" ) { $_date = $anno . $mese . $giorno . $ora; }
	if ( $formato == "Ymd" )  { $_date = $anno . $mese . $giorno; }
	if ( $formato == "Ym" )   { $_date = $anno . $mese; }
	
	return $_date;
}
function getEndData( $date, $formato ) {
	$lastdate = explode(" ", end( $date ));
	
    $data   = explode( "/", $lastdate[0]);
	$giorno = $data[0];
	$mese   = $data[1];
	$anno   = $data[2];
	       
    $time  = explode( ":", $lastdate[1]);
	$ora   = $time[0];
	
	if ( $formato == "YmdH" ) { $_date = $anno . $mese . $giorno . $ora; }
	if ( $formato == "Ymd" )  { $_date = $anno . $mese . $giorno; }
	if ( $formato == "Ym" )   { $_date = $anno . $mese; }
	
	return $_date;
}

function formatData( $data = null ) {
	$anno    = substr($data,0,4);
	$mese    = substr($data,4,2);
	$giorno  = substr($data,6,2);
	$ora     = substr($data,8,2);
	
	$data = (!empty($giorno))? $giorno . "/" . $mese . "/" . $anno : $mese . "/" . $anno;
	if (!empty($ora)) { $data .= " h: " . $ora;  }
	
	return $data;
}

/* *
echo $viewdata  . " - " . $viewora . "<br>" . $vanno . "/" . $vmese . "/" . $vgiorno . "<br> ";
echo $vora . ":" . $vmin . "<br>";
echo "<pre>";
print_r( $vtemp );
print_r( $vhumy );
echo "</pre>";
die(); 
/* */
?>
	<head>
		<title>Line Chart</title>
		<script src="jss/chart-min.js"></script>
		<script src="jss/jquery-min.js"></script>
		<script>
	    window.onload = function () {   
			var lineChartDataAir = {
				labels : [<?php foreach ($vhumy as $k => $v) { echo "\"" . $k . "\","; } ?>],
				datasets : [
					{
						fillColor : "rgba(255,255,255,0)", // sfondo
						strokeColor : "rgba(31,107,193,1)", // linea
						pointColor : "rgba(19,77,187,1)",  // puntini
						pointStrokeColor : "#fff",
						data : [<?php foreach ($vhumy as $k => $h) { echo  $h . ","; } ?>]
					},
					{
						fillColor : "rgba(255,255,255,0)",
						strokeColor : "rgba(0,128,51,1)",
						pointColor : "rgba(0,128,51,1)",
						pointStrokeColor : "#fff",
						data : [<?php foreach ($vhsuo as $k => $t) { echo  $t . ","; } ?>]
					}
				]	
			}
			
			var lineChartDataTer = {
				labels : [<?php foreach ($vtemp as $k => $v) { echo "\"" . $k . "\","; } ?>],
				datasets : [
					{
						fillColor : "rgba(128,255,90,0)",
						strokeColor : "rgba(128,255,90,1)",
						pointColor : "rgba(128,255,90,1)",
						pointStrokeColor : "#fff",
						data : [<?php foreach ($vtemp as $k => $h) { echo  $h . ","; } ?>]
					},
					{
						fillColor : "rgba(255,51,51,0)",
						strokeColor : "rgba(255,51,51,1)",
						pointColor : "rgba(255,51,51,1)",
						pointStrokeColor : "#fff",
						data : [<?php foreach ($vtsuo as $k => $t) { echo  $t . ","; } ?>]
					}
				]	
			}
		    
		    function respondCanvas(){ 
		    	var c = $('#canvas_humy');
		    	var container = $(c).parent();
		    	c.attr('width', $(container).width() );
		        c.attr('height', (($(container).height()/2)-45) );
				var c = $('#canvas_temp');
		    	c.attr('width', $(container).width() );
		        c.attr('height', (($(container).height()/2)-45) );
				/* *
		        var c = $('#select');
		    	c.attr('width', $(container).width() );
		        var c = $('#detail');
		    	c.attr('width', $(container).width() );
		        /* */
		        var myLine = new Chart(document.getElementById("canvas_humy").getContext("2d")).Line(lineChartDataAir);
		        var myLine = new Chart(document.getElementById("canvas_temp").getContext("2d")).Line(lineChartDataTer);
		    }
		    
		    //$(window).resize( respondCanvas );
		    window.onorientationchange = respondCanvas;
			respondCanvas();
	    }
		</script>
		<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
		<meta http-equiv="refresh" content="60; url=index.php">
		<style>
			canvas { }
			#detail { text-align: center; font-size: 25px; font-family: Arial, Helvetica, sans-serif; margin-bottom: 10px; }
			#select { font-style: italic; font-size: 18px; font-family: Arial, Helvetica, sans-serif; margin: 5px 0; text-align: center; }
			#break { clear: both; }
			#prev { text-align: left; padding: 0px 5px; }
			#data1 { text-align: center; font-style: italic; font-size: 20px; font-family: Arial, Helvetica, sans-serif; vertical-align: center; }
			#next { text-align: right; padding: 0px 5px; }
			#label { font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 10px 30px; }
			#pulsante { width:280px; margin: 5px 0px; outline: none; cursor: pointer; text-align: center; text-decoration: none; font: bold 18px Arial, Helvetica, sans-serif;
			            color: #fff; padding: 10px 20px; border: solid 1px #0076a3; background: #0095cd; background: -webkit-gradient(linear, left top, left bottom, from(#00adee), to(#0078a5));
			            background: -webkit-linear-gradient(top,  #00adee,  #0078a5); background: -moz-linear-gradient(top,  #00adee,  #0078a5); background: -ms-linear-gradient(top,  #00adee,  #0078a5);
			            background: -o-linear-gradient(top,  #00adee,  #0078a5); background: linear-gradient(top,  #00adee,  #0078a5); -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px;
			            -moz-box-shadow: 0 1px 3px rgba(0,0,0,0.5); -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.5); box-shadow: 0 1px 3px rgba(0,0,0,0.5);
			}
			#pulsante:hover { background: #0095cd; background: -webkit-gradient(linear, left top, left bottom, from(#0078a5), to(#00adee)); 
			                  background: -webkit-linear-gradient(top,  #0078a5,  #00adee); background: -moz-linear-gradient(top,  #0078a5,  #00adee);
			                  background: -ms-linear-gradient(top,  #0078a5,  #00adee); background: -o-linear-gradient(top,  #0078a5,  #00adee);
			                  background: linear-gradient(top,  #0078a5,  #00adee);
			                }
			#pulsante:active { position: relative; top: 1px; }
		 </style>
	</head>
	<body>	
		<div id="detail">
			<button id="pulsante" onclick="window.location.assign('?data=<?php echo getEndData( $rows, "Ym" ); ?>');">Mensile</button>
			<button id="pulsante" onclick="window.location.assign('?data=<?php echo getEndData( $rows, "Ymd" ); ?>');">Giornaliero</button>
			<button id="pulsante" onclick="window.location.assign('?data=<?php echo getEndData( $rows, "YmdH" ); ?>');">Orario</button>
			<div id="select">
				<span id="prev">
					<?php if (getPrevVal( $viewdata, $rows ) < $viewdata) { ?><a href="?data=<?php echo getPrevVal( $viewdata, $rows ); ?>"><img src="images/left-arrow-icon.jpg" width="45" height="45" border="0" /></a><?php } else { ?><img src="images/left-arrow-icon-dis.jpg" width="45" height="45" border="0" /><?php } ?>
				</span><?php echo formatData( $viewdata ); ?>
				<!--span id="data"><?php echo formatData( $viewdata ); ?></span-->
				<span id="next">
					<?php if (getNextVal( $viewdata, $rows ) > $viewdata) { ?><a href="?data=<?php echo getNextVal( $viewdata, $rows ); ?>"><img src="images/right-arrow-icon.jpg" width="45" height="45" border="0" /></a><?php } else { ?><img src="images/right-arrow-icon-dis.jpg" width="45" height="45" border="0" /><?php } ?>
				</span>
			</div>
		</div>
		
		<div id="break"></div>
		<span id="label">Humidity %<br></span><canvas id="canvas_humy"></canvas>
		<img src="images/leg-humy.jpg" width="320" height="25"> <br> <br>
		<span id="label">Temperature &deg;C<br></span><canvas id="canvas_temp"></canvas>
		<img src="images/leg-temp.jpg" width="320" height="25">
	</body>
</html>
