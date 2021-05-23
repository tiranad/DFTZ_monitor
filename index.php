<?php
include 'config.php';

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => $rpc_port,
  CURLOPT_URL => $rpc_url . ":" . $rpc_port,
  CURLOPT_USERPWD => $rpc_user . ":" . $rpc_password,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS => "{\n\"jsonrpc\": \"1.0\",\n\"id\":\"curltest\",\n\"method\": \"getnetworkinfo\"\n}",
));
$getnetworkinfo = curl_exec($curl);
$getnetworkinfo = json_decode($getnetworkinfo);
$networkinfo = $getnetworkinfo->{'result'};
$version = $networkinfo->{'subversion'};
$ipv4 = $networkinfo->{'localaddresses'}[0]->{'address'};
curl_close($curl);



$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => $rpc_port,
  CURLOPT_URL => $rpc_url . ":" . $rpc_port,
  CURLOPT_USERPWD => $rpc_user . ":" . $rpc_password,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS => "{\n\"jsonrpc\": \"1.0\",\n\"id\":\"curltest\",\n\"method\": \"masternode status\"\n}",
));
$getmasternodestatus = curl_exec($curl);
$getmasternodestatus = json_decode($getmasternodestatus);
$masternodestatus = $getmasternodestatus->{'result'};
$mnaddress = $masternodestatus->{'addr'};
curl_close($curl);



$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => $rpc_port,
  CURLOPT_URL => $rpc_url . ":" . $rpc_port,
  CURLOPT_USERPWD => $rpc_user . ":" . $rpc_password,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS => "{\n\"jsonrpc\": \"1.0\",\n\"id\":\"curltest\",\n\"method\": \"masternodelist\"\n}",
));
$listmasternodes = curl_exec($curl);
$listmasternodes = json_decode($listmasternodes);
$mnlist = $listmasternodes->{'result'};
curl_close($curl);


  for ($i = 0; $i < count($mnlist); $i++) {
      if($mnlist[$i]->{'addr'} == $mnaddress)
      {
        $mnstatus = $mnlist[$i]->{'status'};
        $mnnetwork = $mnlist[$i]->{'network'};

        if($mnlist[$i]->{'lastseen'} == 0){
          $mnlastseen = "Not yet";
        }
        else{
          $mnlastseen = date($date_format, $mnlist[$i]->{'lastseen'});
        }

        if($mnlist[$i]->{'lastpaid'} == 0){
          $mnlastpaid = "Not yet";
        }
        else{
          $lastpaid = $mnlist[$i]->{'lastpaid'};
          $interval =  time() - $lastpaid ;
          $sincelastpaid = number_format(($interval / 3600), 0);
          $mnlastpaid = date($date_format, $mnlist[$i]->{'lastpaid'});
          $mnlastpaid = date($date_format, $mnlist[$i]->{'lastpaid'});
        }

        if($mnlist[$i]->{'activetime'} == 0){
          $mnactivetime = "Not yet";
        }
        else{
          $mnactivetime = number_format(($mnlist[$i]->{'activetime'} / 86400), 0);
        }
      }
    }


$timenow = date($date_format, microtime(true));
?>

<!DOCTYPE html>
<html>
<title>DFTZ NODE MONITOR</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<body class="w3-light-grey">

<!-- Top container -->
<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">

  <span class="w3-bar-item w3-right"><b>dftz_monitor</b> | <?php print $version;?></span>
</div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-top:23px;">

  <!-- Header -->
<?php include 'header.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <div class="w3-row-padding w3-margin-bottom">
    <div id="status" class="w3-quarter">
    </div>
    <div id="mnstatus" class="w3-quarter">

    <?php
    if($mnstatus == "MISSING"){
      echo '<div class="w3-container w3-border-bottom w3-border-white w3-red w3-padding-16"><div class="w3-right">';
      echo '<h3>' . $mnstatus . '</h3>';
      echo "</div>";
      echo '<div class="w3-clear"></div>';
      echo "<h4>Masternode</h4>";
      echo "</div>";
    }
    elseif($mnstatus == "ENABLED"){
      echo '<div class="w3-container w3-border-bottom w3-border-white w3-green w3-padding-16"><div class="w3-right">';
      echo '<h3>Enabled</h3>';
      echo "</div>";
      echo '<div class="w3-clear"></div>';
      echo "<h4>Masternode</h4>";
      echo "</div>";
    }
    ?>

    </div>
    <div id="connectioncount" class="w3-quarter">
    </div>
    <div id="blockcount" class="w3-quarter">
    </div>
    <div id="mnnetwork" class="w3-quarter">

    <?php
    if(empty($mnnetwork)){
    }
    elseif(!empty($mnnetwork)){
      echo '<div class="w3-container w3-border-bottom w3-border-white w3-teal w3-padding-16"><div class="w3-right">';
      echo '<h3>' . $mnnetwork . '</h3>';
      echo "</div>";
      echo '<div class="w3-clear"></div>';
      echo "<h4>Network</h4>";
      echo "</div>";
    }
    ?>

    </div>
    <div id="activetime" class="w3-quarter">

    <?php
    if(!empty($mnactivetime)){
      if($mnactivetime <= 1){
        echo '<div class="w3-container w3-border-bottom w3-border-white w3-purple w3-padding-16"><div class="w3-right">';
        echo '<h3>Fresh</h3>';
        echo "</div>";
        echo '<div class="w3-clear"></div>';
        echo "<h4>Active</h4>";
        echo "</div>";
      }
      elseif($mnactivetime > 1){
        echo '<div class="w3-container w3-border-bottom w3-border-white w3-purple w3-padding-16"><div class="w3-right">';
        echo '<h3>' . $mnactivetime . ' days</h3>';
        echo "</div>";
        echo '<div class="w3-clear"></div>';
        echo "<h4>Active</h4>";
        echo "</div>";
      }
    }


    ?>
    </div>
    <div id="lastpaid" class="w3-quarter">

    <?php
      if(!empty($mnlastpaid)){
        if($sincelastpaid <= 1){
          echo '<div class="w3-container w3-border-bottom w3-border-white w3-blue w3-padding-16"><div class="w3-right">';
          echo '<h3>< 1 hour</h3>';
        }
        elseif($sincelastpaid < 24){
          echo '<div class="w3-container w3-border-bottom w3-border-white w3-green w3-padding-16"><div class="w3-right">';
          echo '<h3>' . $sincelastpaid . ' hours</h3>';
        }
        elseif($sincelastpaid >= 24){
          echo '<div class="w3-container w3-border-bottom w3-border-white w3-orange w3-padding-16"><div class="w3-right">';
          echo '<h3>' . $sincelastpaid . ' hours</h3>';
        }
      echo "</div>";
      echo '<div class="w3-clear"></div>';
      echo "<h4>Last Paid</h4>";
      echo "</div>";
      }
    ?>

    </div>
    <div id="balance" class="w3-quarter">
    </div>
    </div>

  <?php
    echo '</span>';
    echo '<div class="w3-container">';
    echo '<ul class="w3-ul w3-card-4">';

    if(!empty($getnetworkinfo)){
      if(!empty($mnaddress)){
        echo '<li class="w3-padding-16 w3-white">';
        echo '<span class="w3-xlarge">';
        echo 'Masternode Address : <tr><a href=http://' . $dftz_explorer . '/address/' . $mnaddress . '>' . $mnaddress . '</a></tr>';
        echo "<td><a href=http://" . $dftz_explorer . "/address/" . $mnlist[$i]->{'addr'} .">" . $mnlist[$i]->{'addr'} . "</a></td>";
      }
      elseif(empty($mnaddress)){
        echo '<li class="w3-padding-16 w3-orange">';
        echo '<span class="w3-xlarge">';
        echo 'DFTZ Daemon is running but is not a Masternode';
      }
    }
    else{
      echo '<li class="w3-padding-16 w3-orange">';
      echo '<span class="w3-xlarge">';
      echo 'Cannot connect to DFTZ node [url=' . $rpc_url . '] [port=' . $rpc_port . ']';
      echo '</br>';
      echo '- Edit <b>rpc_user</b> and <b>rpc_password</b> in <b>config.php</b> (use the same credentials as dftz.conf)';
      echo '</br>';
      echo '- Maybe your DraftCoinZ "dftzd" daemon is not running';
    }
    echo '</li>';
    echo '</ul>';
    echo '</div>';



  if(!empty($mnstatus)){
        echo '<div id="masternode" class="w3-container">';
        echo '<br>';
        echo '<h3>Masternode Status</h3>';
        echo '<ul class="w3-ul w3-card-4 w3-white">';
        echo '<li class="w3-padding-16"><span class="w3-xlarge">Status : ' . $mnstatus . '</span></li>';
        echo '<li class="w3-padding-16"><span class="w3-xlarge">Time now : ' . $timenow . ' (UTC)</span></li>';
        echo '<li class="w3-padding-16"><span class="w3-xlarge">Last Seen : ' . $mnlastseen . ' (UTC)</span></li>';
        echo '<li class="w3-padding-16"><span class="w3-xlarge">Last Paid : ' . $mnlastpaid . ' (UTC)</span></li>';
        echo '</span>';
        echo '</span>';
        echo '</span>';
        echo '</ul>';
        echo '</div>';
  }
  ?>
  <hr>


  <!-- Footer -->
  <footer class="w3-container w3-padding-16 w3-dark-grey">
    <h3 class="w3-bottombar w3-border-blue">Support DFTZ</h3>
    <p>Source code on <a href="https://github.com/dirtyak/dftz_monitor" target="_blank">GitHub</a></p>
    <p>DFTZ Links :
      <a href="http://dftz-explorer.btcdraft.com">Explorer</a> |
      <a href="https://discord.gg/XD6H8btp2Z">Discord</a> |
      <a href="https://draftcoinz.com/">Website</a>
    </p>
  </footer>

  <script>
    function loadlink(){
      $( "#status" ).load( "status.php" );
      $( "#connectioncount" ).load( "connectioncount.php" );
      $( "#blockcount" ).load( "blockcount.php" );
      $( "#balance" ).load( "balance.php" );
    }

    loadlink(); // This will run on page load
    setInterval(function(){
        loadlink() // this will run after every 5 seconds
    }, <?php echo ($refresh_delay * 1000);?>);
  </script>

  <!-- End page content -->
</div>

</body>
</html>
