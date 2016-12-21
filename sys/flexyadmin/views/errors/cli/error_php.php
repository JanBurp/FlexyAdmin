<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

A PHP Error was encountered

Severity:     <?php echo "$severity\n"; ?>
Message:      <?php echo "$message\n"; ?>
Filename:     <?php echo "$filepath\n"; ?>
Line Number:  <?php echo "$line\n" ;?>
<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
Backtrace:
<?php foreach (debug_backtrace(0,10) as $error): ?>
<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
 - File: <?php echo $error['file'];?> - Line: <?php echo $error['line'];?> - Function: <?php echo $error['function']; echo "\n"; ?>
<?php endif ?>
<?php endforeach ?>
<?php endif ?>