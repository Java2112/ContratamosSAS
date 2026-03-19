<?php
$output = shell_exec('npm run build 2>&1');
file_put_contents('build_error.log', $output);
echo "Written\n";
