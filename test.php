<?php

	//ob_start();
	//shell_exec("cd");
	//shell_exec("cd article-extract");
	//$result2 = shell_exec("cd ~; cd ../../home; cd ~; ls");
	$result = shell_exec("/home/matchme/article-extract/run 'http://is.gd/k0N9TE' '**&**' 'fast' 2>/dev/null");
    //$result = shell_exec("nohup $script >>log.txt 2>>&1 & echo $!");
    //return $pid;
	//$res  = ob_get_contents();
	echo $result;
?>