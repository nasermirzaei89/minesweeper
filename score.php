<?php
function loaddb()
{
	$con = mysql_connect("localhost","USER","PASS");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	if(!mysql_select_db("gamerecords", $con))
		mysql_query("CREATE DATABASE gamerecords",$con);
	mysql_set_charset('utf8',$con); 
	return($con);
}

function unloaddb($con)
{
	mysql_close($con);
}

function submittime($name, $level, $time)
{
	$result = mysql_query("SELECT * FROM minesweeper WHERE name='".$name."' AND level=".$level."");
	if(mysql_num_rows($result) > 0)
	{
		$result2 = mysql_query("UPDATE minesweeper SET time=".$time.", datetime=NOW() 
		WHERE name='".$name."' AND level=".$level." AND time>".$time."");
		if(mysql_affected_rows() > 0)
			return '<p class="success">Your time updated!</p>';
		else
			return '<p class="info">Your new time isn\'t better than your last time! try more!</p>';
	}
	else
	{
		$result2 = mysql_query("INSERT INTO minesweeper (name, level, time, datetime) VALUES ('".$name."', ".$level.",".$time.",NOW())");
		if(mysql_affected_rows() > 0)
			return '<p class="success">Your time submited!</p>';
		else
			return '<p class="fail">Your time not submited!</p>';
	}
}

function gettime($level, $limit = 25, $page = 1)
{
	$offset = ($page-1)* $limit;
	$result = mysql_query("SELECT * FROM minesweeper WHERE level=".$level." ORDER BY time ASC, datetime ASC LIMIT ".$offset.", ".$limit."");
	return $result;
}

function generate_hash($input) {
    $hash = "00000000000000000000";
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
;
    $hash_index = 0;
    $sl = strlen($input);
    if ($sl > 0) {
        for ($t = 0; $t < $sl; $t++) {
            $c = ord(substr($input, $t, 1));
            $hidx = $hash_index;
            for ($i = 1; $i <= strlen($hash); $i++) {
                $c2 = strpos($chars, substr($hash, $hidx, 1)) + 1;
                $c2 = $c2 + $c;
                while ($c2 > strlen($chars)) {
                    $c2 -= strlen($chars);
                }
                $hash = substr($hash, 0, $hidx) . substr($chars, $c2 - 1, 1) . substr($hash, $hidx + 1);
                $hidx += 1;
                if ($hidx >= strlen($hash)) $hidx = 0;
                $c--;
                if ($c < 1) $c = 1;
            }
            $hash_index += 1;
            if ($hash_index >= strlen($hash)) $hash_index = 0;
        }
    }
    return $hash;
}

$con = loaddb();
$Message = '';
$Submited = false;
if(isset($_GET['submit']))
{
	$Name = $_GET['name'];
	$Level = $_GET['level'];
	$Time = $_GET['time'];
	$Code = $_GET['code'];
	
	if($Code == generate_hash($Name.$Level.$Time))
	{
		$Message = submittime($Name, $Level, $Time);
		$Submited = true;
	}
	else
	{
		$Message = '<p class="fail">Invalid token code recieved!</p>';
	}
}

$result0 = gettime(0);
$result1 = gettime(1);
$result2 = gettime(2);
unloaddb($con);
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Minesweeper Time Records Tables</head></title>
	<meta charset="UTF-8" />
	<style type="text/css">
	<!--
		body {background-color:white; color: #333; font-family: tahoma; font-size:12px;}
		#whole{width:780px; margin:0 auto;}
		#whole div{float: left; width: 260px; border:0;}
		#whole div table{border:1px solid;background-color:#eee;width:256px;margin:0 auto;}
		.th-number, td-number{width: 32px; text-align:right;}
		.th-name, .td-name{text-align:left;}
		.th-time, td-time{width: 48px; text-align:center;}
		#whole div table tr:hover{background-color:#eec;}
		#you{font-weight:bold;background-color:lime;}
		#footer{text-align:center;}
		.success{font-weight:bold;width:360px;padding:5px;background-color:#af9;}
		.info{font-weight:bold;width:360px;padding:5px;background-color:#aff;}
		.fail{font-weight:bold;width:360px;padding:5px;background-color:#f99;}
	-->
	</style>
</head>
<body>
	<div id="whole">
	<h1>Minesweeper Time Records Tables</h1>
	<?php echo $Message; ?>
	<br />
	<div id="begginer">
		<h2>Beginner</h2>
		<table>
			<tr>
				<th class="th-number">#</th>
				<th class="th-name">Name</th>
				<th class="th-time">Time</th>
			</tr>
			<?php
			$num = 0;
			while($row = mysql_fetch_array($result0))
			{
				$num++;
				$you = false;
				if(isset($_GET['submit']) && $Submited)
				{
					if($_GET['level']==$row['level'] && $_GET['name']==$row['name'])
						$you = true;
				}
				if($you)
					echo '<tr id="you" class="highlight">';
				else
					echo '<tr>';
					
				echo '<td class="td-number">' . $num . '</td>';
				echo '<td class="td-name">' . $row['name'] . '</td>';
				echo '<td class="td-time">' . $row['time'] . '</td>';
				echo '</tr>';
				$you = false;
			}
			?>
		</table>
	</div>
	
	<div id="intermediate">
		<h2>Intermediate</h2>
		<table>
			<tr>
				<th class="th-number">#</th>
				<th class="th-name">Name</th>
				<th class="th-time">Time</th>
			</tr>
			<?php
			$num = 0;
			while($row = mysql_fetch_array($result1))
			{
				$num++;
				$you = false;
				if(isset($_GET['submit']) && $Submited)
				{
					if($_GET['level']==$row['level'] && $_GET['name']==$row['name'])
						$you = true;
				}
				if($you)
					echo '<tr id="you" class="highlight">';
				else
					echo '<tr>';
					
				echo '<td class="td-number">' . $num . '</td>';
				echo '<td class="td-name">' . $row['name'] . '</td>';
				echo '<td class="td-time">' . $row['time'] . '</td>';
				echo '</tr>';
				$you = false;
			}
			?>
		</table>
	</div>
	
	<div id="advanced">
		<h2>Advanced</h2>
		<table>
			<tr>
				<th class="th-number">#</th>
				<th class="th-name">Name</th>
				<th class="th-time">Time</th>
			</tr>
			<?php
			$num = 0;
			while($row = mysql_fetch_array($result2))
			{
				$num++;
				$you = false;
				if(isset($_GET['submit']) && $Submited)
				{
					if($_GET['level']==$row['level'] && $_GET['name']==$row['name'])
						$you = true;
				}
				if($you)
					echo '<tr id="you" class="highlight">';
				else
					echo '<tr>';
					
				echo '<td class="td-number">' . $num . '</td>';
				echo '<td class="td-name">' . $row['name'] . '</td>';
				echo '<td class="td-time">' . $row['time'] . '</td>';
				echo '</tr>';
				$you = false;
			}
			?>
		</table>
	</div>
	<br style="clear:both" />
	<p id="footer"><a href="index.html">Play Minesweeper Online</a></p>
</div>
</body>
</html>
