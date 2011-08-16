<?php
	////////////////////////////////////////////
	////////// Configuration ///////////////////
	////////////////////////////////////////////
	$servername = "";
	//Name of your MC server!
	$host = "";
	//MySQL Host
	$user = "";
	// MySQL Username
	$pass = "";
	// MySQL Password
	$database = "";
	// Database name
	$polling_period = "5";
	//Polling Period
	$usepollmultiplier = false;
	//Make this true if you're having problems with Online players showing up as Disconnected.
	////////////////////////////////////////////
	
	// Open SQL connection
	mysql_connect($host,$user,$pass) OR die("Can't establish a connection with the MySQL server!");
	mysql_select_db($database) OR die("The Database could not be selected!");
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////        Connected Players        ////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////
	$playerquery = mysql_query("SELECT * FROM loginqueueplayertable WHERE connected = 1");
	$playerArray = array();
	//Define arrays.
	$disconnectArray = array();
	//Disconnect Checking
	$currentTime = time();
	//Get current time
	
	if ($usepollmultiplier == true){
		$polling_period = 1.5*$polling_period;
	}

	$compareTime = $currentTime - $polling_period;
	//This is the time we use to compare with the lastConnectPoll time.
	while ($playerdata = mysql_fetch_assoc($playerquery)){
		$playername = $playerdata[playername];
		$connectTime = $playerdata[lastConnectPoll];
		//Get lastConnectPoll time.
		$connectTime = round($connectTime/1000);
		//Convert Java's milliseconds -> php's seconds.
		
		if ($connectTime < $compareTime){
			//If his lastConnectPoll is less than the compareTime value, the player is disconnected.
			array_push($disconnectArray,$playername);
			//insert player into disconnected array
		} else{
			array_push($playerArray,$playername);
			//insert player into connected array
		}

	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////         Queued Players          ////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////
	$playerquery2 = mysql_query("SELECT * FROM loginqueueplayertable WHERE queued = 1");
	$queueArray = array();
	//Define array
	while ($playerdata = mysql_fetch_assoc($playerquery2)){
		$playername = $playerdata[playername];
		array_push($queueArray,$playername);
		//insert player into queued array
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////         Eligible Players          //////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////
	$playerquery3 = mysql_query("SELECT * FROM loginqueueplayertable WHERE eligible = 1");
	$eligibleArray = array();
	//Define array
	while ($playerdata = mysql_fetch_assoc($playerquery3)){
		$playername = $playerdata[playername];
		$eligibleTime = $playerdata[becameEligible];
		//Get becameEligible time.
		$eligibleTime = round($eligibleTime/1000);
		//Convert Java's milliseconds -> php's seconds.
		$remainingTime = $currentTime -$eligibleTime;
		//Find how much time eligible person has.
		
		if ($remainingTime < 30){
			$playername .= "(Releasing Slot)";
			//Notify users that this person will no longer be eligible in a bit
		}

		array_push($eligibleArray,$playername);
		//insert player into eligible array
	}

	?>
<style type="text/css">
<!--
.style1 {
	color: #003300;
	font-weight: bold;
	font-size: 36px;
}
.style2 {font-size: 16px}
-->
</style>
<p align="center" class="style1"><?php  echo $servername ?> Queue</p>
<p align="center" class="style2">Currently <b><?php  echo count($playerArray); ?></b> player(s) Online<br />
  </p>
<table width="500" align="center" cellspacing="0">
<tbody><tr>
<th colspan="3"><div align="center">Player List</div></th>
</tr>
<tr>
<td colspan="3"><div align="center">
<?php 
	$i = count($playerArray) - 1;
	
	if ($i >= 0){
		while ($i >= 0){
			echo $playerArray[$i];
			
			if ($i > 0){
				echo ", ";
			}

			$i --;
		}

	} else{
		echo "No one is Online! =(";
	}

	?>
</div></td></tr>
</tbody></table>
<p>&nbsp;</p>
<p align="center" class="style2">Currently <b><?php  echo count($disconnectArray); ?></b> player(s) Disconnected<br />
<table width="500" align="center" cellspacing="0">
  <tbody>
    <tr>
      <th colspan="3"><div align="center">Disconnected List</div></th>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <?php 
	$i = count($disconnectArray) - 1;
	
	if ($i >= 0){
		while ($i >= 0){
			echo $disconnectArray[$i];
			
			if ($i > 0){
				echo ", ";
			}

			$i --;
		}

	} else{
		echo "No one is disconnected!";
	}

	?>
      </div></td>
    </tr>
  </tbody>
</table>
<p>&nbsp;</p>
<p align="center" class="style2">Currently <b><?php  echo count($eligibleArray); ?></b> player(s) Eligible (Can Login)<br />
</p>
<table width="500" align="center" cellspacing="0">
  <tbody>
    <tr>
      <th colspan="3"><div align="center">Eligible List</div></th>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <?php 
	$i = count($queueArray) - 1;
	
	if ($i >= 0){
		while ($i >= 0){
			echo "- ".$queueArray[$i];
			
			if ($i > 0){
				echo "<br>";
			}

			$i --;
		}

	} else{
		echo "No one is Eligible!";
	}

	?>
      </div></td>
    </tr>
  </tbody>
</table>
<p>&nbsp;</p>
<p align="center" class="style2">Currently <b><?php  echo count($queueArray); ?></b> player(s) Queued<br />
</p>
<table width="500" align="center" cellspacing="0">
  <tbody>
    <tr>
      <th colspan="3"><div align="center">Queue List</div></th>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <?php 
	$i = count($queueArray) - 1;
	
	if ($i >= 0){
		while ($i >= 0){
			echo $queueArray[$i];
			
			if ($i > 0){
				echo ", ";
			}

			$i --;
		}

	} else{
		echo "No one is waiting in Queue!";
	}

	?>
      </div></td>
    </tr>
  </tbody>
</table>
<p>&nbsp;</p>