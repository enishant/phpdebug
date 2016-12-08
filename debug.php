<?php // Referenece URL : http://stackoverflow.com/questions/159393/how-can-i-parse-apaches-error-log-in-php
date_default_timezone_set('Asia/Calcutta');
$application = 'Application Name';
$command = 'cat error_log'; // error_log file here
exec($command, $output);
$yesterday = date('d-M-Y',strtotime("-1 days"));
$table_started = false;
$table_rows = 0;
  ob_start();
  foreach($output as $key => $line) {
    preg_match('~^\[(.*?)\]~', $line, $date);
    if(empty($date[1])) {
      continue;
    }
    $date_to_check = date('d-M-Y',strtotime($date[1]));
    if($date_to_check != $yesterday) {
      continue;  
    }
    if($table_started == false) { ?>
      <table border="1" style="margin:0 auto;width:50%">
      <tr>
        <th>Date</th>
        <th>Message</th>
      </tr>
    <?php
      $table_started = true;
    }
    preg_match('~\] \[([a-z]*?)\] \[~', $line, $type);
    preg_match('~\] \[client ([0-9\.]*)\]~', $line, $client);
    preg_match('~\] (.*)$~', $line, $message);
    $table_rows++;
  ?>
  <tr>
    <td style="width:200px;text-align:center;"><?=$date[1]?></td>
    <td style="width:*;padding:5px;"><?=$message[1]?></td>
  </tr>
<?php if(count($output) == ($key+1)) { ?>
  </table>
  <?php }
  }
  $error_message = ob_get_contents();
  ob_end_clean();
  $content = '<h2 style="text-align:center;">' . $table_rows . ' Errors found !</h2>';
  if($table_rows > 0) {
    $content .= $error_message;
  } else {
    $content .= '<p style="text-align:center;">Congratulations ! for error free application :-)</p>';
  }
  $content .= '<hr><p style="text-align:center;">'.$command.'</p>';
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  mail("user@example.com",$application . " : Errors occured on " .  date('d-M-Y h:i:sa'),$content,$headers);
?>