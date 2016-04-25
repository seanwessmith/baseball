<?php
ob_start();
// Output string to overflow browser php.ini output_buffering setting.
echo str_repeat(PHP_EOL, 4097);

for ($i=0; $i<5; $i++) {
  echo PHP_EOL.$i;
  ob_flush();
  flush();
  sleep(1);
}
ob_end_flush();
?>
