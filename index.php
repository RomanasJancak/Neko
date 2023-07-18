<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
?>
<?php
	header('Location: '.$uri.'/public/index.php');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/003 Paskaita');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/004 Paskaita');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/005 Paskaita');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/006 Paskaita Uzduotis');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/007 Paskaita');
	//header('Location: '.$uri.'/RenkuosiProgramuoti/Projektas_Biudzetas');
	//header('Location: '.$uri.'/Jarta');
	/*
$port = '8000';
header('Location: '
    . ($_SERVER['HTTPS'] ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . ':' . $port
    . $_SERVER['REQUEST_URI']);
exit;
*/
	exit;
	
?>
Something is wrong with the XAMPP installation :-(
