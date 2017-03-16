<?php
$host    = "127.0.0.1";
$port    = 77094;

$menu = array('changement de répertoire', 'lister les fichers du dossier courant', 'envoyer un ficher vers le serveur', 'envoyer un ficher du serveur vers le client','récuperer les informations sur un étudiant','envoyer un dossier du serveur vers le client','arreter le mode ssh');
do
{
	$indice=1;
	foreach($menu as $menu_choisie){ 
		echo($indice); 
		echo ' -';
		echo($menu_choisie); 
		echo "\n";
		$indice++; 
	}

	echo "\n";
	echo "Choisir la commande que vous voulez executer: ";
	$indice=fgets(STDIN);
	//cas cd
	if($indice==1){
		echo "Choisir le nom du fichier: ";
		$content=fgets(STDIN);
		$content="1".$content;
		// create socket
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

		// connect to server
		$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

		// send string to server
		socket_write($socket, $content, strlen($content)) or die("Could not send data to server\n");

		// get server response
		$input = socket_read ($socket, 1024) or die("Could not read server response\n");
		echo "\n".$input."\n";
	}
	//cas ls
	if($indice==2){
		$content='2ls';
		// create socket
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

		// connect to server
		$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

		// send string to server
		socket_write($socket, $content, strlen($content)) or die("Could not send data to server\n");

		// get server response
		$input = socket_read ($socket, 1024) or die("Could not read server response\n");
		echo "Le resultat de cette commande  : \n".$input. "\n";
	}
	//Cas Client Serveur 
	if($indice==3){
		echo "Choisir le nom du fichier: ";
		$filename=fgets(STDIN);
		$filename=trim($filename);
		$file=fopen($filename, "r");
		$content=fread($file,filesize($filename));
		fclose($file);
		$content="3".$content;
		
		// create socket
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

		// connect to server
		$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

		// send string to server
		socket_write($socket, $content, strlen($content)) or die("Could not send data to server\n");

		// get server response
		$input = socket_read ($socket, 1024) or die("Could not read server response\n");
		echo "\n".$input."\n";
	}
	//Cas Serveur Client 
	if($indice==4){
		echo "Choisir le nom du fichier: ";
		$content=fgets(STDIN);
		$content=rtrim($content);
		$content="4".$content;
		// create socket
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
		// connect to server
		$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
		// send string to server
		socket_write($socket, $content, strlen($content)) or die("Could not send data to server\n");
		// get server response
		$input = socket_read ($socket, 1024) or die("Could not read server response\n");
		//Cas Client Serveur 
		$input = trim($input); 
		$filename = 'nouveau_fichier_serveur'; 
		$file = fopen($filename, 'w'); 
		fwrite($file, $input, strlen($input)); 
		fclose($file);
		echo "\n".$input."\n";
	}
		//Cas Client Serveur 
	if($indice==5){
		echo "Choisir le numéro de l'étudiant : ";
        $content=fgets(STDIN);
        $content=rtrim($content);
        $content="5".$content;
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
        // send string to server
        socket_write($socket, $content, strlen($content)) or die("Could not send data to server\n");
        $output = $input = socket_read ($socket, 1024) or die("Could not read server response\n");
        echo "\n".$output."\n";
	}
	if($indice==6){
		// create socket
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
		// connect to server
		$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

		echo "Choisir le nom du dossier: ";
		$dir=fgets(STDIN);
		$dir=trim($dir);
		$dir='6'.$dir;
		//envoie le nom du répertoire
		socket_write($socket, $dir, strlen($dir)) or die("Could not send data to server\n");
		// Ouvre un dossier bien connu, et liste tous les fichiers
		
			
		// recoie tous les fichiers
		$files=socket_read ($socket, 1024) or die("Could not read server response\n");
		if($files!="fail"){
			$files=explode("[delimiter]",$files);
			print_r($files);
			$chemin='server_file_'.$files[0];
			mkdir($chemin);
			for($i=1;$i<count($files);$i=$i+2){
				$j=$i+1;
				$fichier = fopen($chemin.'/'.$files[$i], 'w'); 		
				fwrite($fichier, $files[$j], strlen($chemin.'/'.$files[$j])); 
				fclose($fichier);
				echo("\n");
				
			}
		}
		else
			echo "Une erreur est apparue dans le procesus";
		
	}
	
}
while($indice != 7);
// close socket
socket_close($socket);

