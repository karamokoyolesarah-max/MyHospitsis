<?php
$content = file_get_contents('isolation_verify.txt');
echo mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
