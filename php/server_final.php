<?php
// set some variables
$host = "127.0.0.1";
$port = 77094;
$nbMaxClient = 1;

	// don't timeout!
	/*Par défaut, il y a une limite de temps. 
	Au bout de ce temps le programme est arrêté automatiquement.*/
	set_time_limit(0); 

	// create socket //Création socket serveur
	//Un socket = connecteur réseau/interface de connexion
	//Quand ca se passe mal mal, on lève un drapeau
	//or die permet de dire si jamais cette instruction ne se réalise pas on arrête le programme en affichant
	//le message d'erreur associé
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

	// bind socket to port //Enregistrement
	$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
do{

	// start listening for connections 
	//En attente, le serveur est en écoute sur le socket et il accepte un nombre de client maximum
	$result = socket_listen($socket, $nbMaxClient) or die("Could not set up socket listener\n");

	// accept incoming connections
	// spawn another socket to handle communication
	//On accept le client
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");

	// read client input
	//Socket = canal bidirectionnel
	//spawn valeure retourné de l'identifiant que j'ai
	$input = socket_read($spawn, 1024) or die("Could not read input\n");
	$indice=$input[0];
	$input=substr($input,1);

	switch($indice){
		case '1':
		$input=trim($input);
		if(file_exists($input)){
			echo("dossier : ".$input."\n");
			
			chdir($input);
			$reponse="Changement de dossier vers ".$input."\n";
			echo($reponse);
			socket_write($spawn, $reponse, strlen ($reponse)) or die("Could not write output\n");
		}
		else{
			$reponse="fichier/dossier n'existe pas";
			echo($reponse);
			socket_write($spawn, $reponse, strlen ($reponse)) or die("Could not write output\n");
		}
		echo("\n");
		break;
		
		case '2' : //Cas lister les fichiers
		$commande_recuperer = shell_exec($input);
		echo "Le résultat de cette commande  : \n".$commande_recuperer. "\n";
		socket_write($spawn, $commande_recuperer, strlen ($commande_recuperer)) or die("Could not write output\n");
		echo("\n");
		break;
		
		case '3' : //Cas Client Serveur
		$input = trim($input); 
		$filename = 'nouveau_fichier_client'; 
		$file = fopen($filename, 'w'); 
		fwrite($file, $input, strlen($input)); 
		fclose($file);
		$output="fichier transfere";
		socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
		echo("\n");
		break;
		
		case '4' : //Cas Serveur Client
		$filename=$input;
		$file=fopen($input, "r");
		$content=fread($file,filesize($filename));
		fclose($file);
		$output = $content;

		socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
		echo("\n");
		break;

		case '5' : //Cas Client Serveur
		$output="fail";
	    $input = trim($input);
        $file=fopen('base.txt', "r");
        $rlines = file('base.txt');
        foreach($rlines as $rline){
            $maligne= explode(";", $rline);
            $num = trim($maligne[0]);
            if($input==$num){
                $output = $maligne[0]." ".$maligne[1]." ".$maligne[2];
                socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
            }
        }
        if($output=="fail"){
        	$content="aucun resultat trouve";
        	socket_write($spawn,$content, strlen ($content))or die("Could not write output\n");
        }
		break;

		case '6' : //Cas Serveur Client
		if ($dh = opendir($input)) {
			$content=$input;
			$dossier=scandir($input);
			foreach($dossier as $filename){
				if($filename != "." && $filename != ".."){
					$filename=rtrim($filename);
					$fichier=fopen($input."/".$filename,"r");
					$content.="[delimiter]".$filename."[delimiter]".trim(fread($fichier,filesize($input."/".$filename)));
					fclose($fichier);
				}
			}
			closedir($dh);
			socket_write($spawn, $content, strlen ($content)) or die("Could not write output\n");
		}
		else{
			$content="fail";
			echo("Le dossier n'existe pas");
			socket_write($spawn, $content, strlen ($content)) or die("Could not write output\n");
		}
		echo("\n");
		break;
	}

	socket_close($spawn);
}
while($indice != 7);

socket_close($socket);
