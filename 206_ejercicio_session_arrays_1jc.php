<?php
	//sesion
	session_start();
	//variables
	$error='<br>';
	$nif='';
	$nombre='';
	$direccion='';
	$datosPersona=null;
	$datosTabla=null;
	
	//alta de personas
	//comprobar si hemos pulsado alta
		if (isset($_POST['alta'])) {
			//recuperar datos del formularil
			$nif=$_POST['nif']; 
			$nombre=$_POST['nombre'];
			$direccion=$_POST['direccion'];
			$error='<br>';
			//echo "$nif $nombre $direccion";
			//validar los datos: NIF, Nombre, Dirección, esten informados.
			if (trim($nif)=='') {
				$error=$error.'NIF no informado';
			}
			if (trim($nombre)=='') {
				$error=$error.'// nombre no informado';
			}
			if (trim($direccion)=='') {
				$error=$error.' // dirección no informada';
			}
			if (trim($nif)=='' || trim($nombre)=='' || trim($direccion)=='') {
				$error=$error.'--> DATOS OBLIGATORIOS';
			} else {
				//validar que la persona (nif) no exista en el array
				if (isset ($_SESSION['personas']) && array_key_exists($nif, $_SESSION['personas'])) {
					$error=$nif.' nif ya existente';
				} else {
					//dar de alta la persona en la variable de sesion
					//forma1
						//$_SESSION['personas'][$nif]['nombre']=$nombre;
						//$_SESSION['personas'][$nif]['direccion']=$direccion;
					//forma2
						$_SESSION['personas'][$nif] = array('nombre'=>$nombre, 'direccion'=>$direccion);
					//$_SESSION['personas'][$nif] = ['nombre'=>$nombre, 'direccion'=>$direccion];
						$error='<br>nif:'.$nif.' alta persona efectuada';
					//print_r($_SESSION['personas']);
				}
			}
		}
		//borrar nif de la lista
		if (isset($_POST['bajapersona'])) {
			//echo "entro";
			//recuperear nif a dar de baja
			$nifpersona=$_POST['nifpersona'];
			//borrar de la variable de sesion la fila correspondiente al nif
			//echo $nifpersona;
			unset($_SESSION['personas'][$nifpersona]);
			$error='<br>nif:'.$nif.' baja persona efectuada';
		}

		//borra la sesion 
		//3.-baja de todas las personas
		//comprobar si se ha pulsado el boton de de borrado
		if (isset($_POST['baja'])) {
			unset($_SESSION['personas']);
			$datosPersona=null;
		}

		//Modificación------------------------------------------------------------------------
		//modificacion de la persona
		if (isset($_POST['modificar'])) {
			echo "entro en modificar";
			//recuperar  info
			$nif=$_POST['nif']; 
			$nombre=$_POST['nombre'];
			$direccion=$_POST['direccion'];
			$error='<br>';
			// validar si esta informado
			if (trim($nif)=='') {
				$error=$error.'NIF no informado';
			}
			if (trim($nombre)=='') {
				$error=$error.'// nombre no informado';
			}
			if (trim($direccion)=='') {
				$error=$error.' // dirección no informada';
			}
			if (trim($nif)=='' || trim($nombre)=='' || trim($direccion)=='') {
				$error=$error.'--> DATOS OBLIGATORIOS';
			} else {
				//si informado modificar en el array utilizando el nif como clave
				$_SESSION['personas'][$nif] = array('nombre'=>$nombre, 'direccion'=>$direccion);
				//mensaje de modificación realizada
				$error='<br>nif:'.$nif.' Modificación persona efectuada';
			}				
		}

		// 2.- Mostrar las filas en la pantalla (tabla)
		//comprobar si la variable de sesion existe
		if (isset($_SESSION['personas'])){
			//ordenar el array por nif (clave principal)
			ksort($_SESSION['personas']);
			//ordenar el array por primera clave del segundo array (ordenar por nombre)
			//asort($_SESSION['personas']);
			//multisort (por direccion)  - extraer valor 
			//creamos un array con las claves del array de personas (el nif)
			$claves = array_keys($_SESSION['personas']);
			//creamos un array con el dato (columna) que queremos ordenar (la dirección)
			$direcciones = array_column($_SESSION['personas'], 'direccion');
			//ordenamos el array de direcciones de forma ascendente y, simultaneamente, se ordenara el array de personas y el de claves por la misma ordenación de claves que el primero
			array_multisort($direcciones, SORT_ASC, $_SESSION['personas'], $claves);
			//substituimos las claves del array de personas por las del array de claves 
			$_SESSION['personas'] = array_combine($claves, $_SESSION['personas']);
			// print_r($_SESSION['personas']);
			foreach ($_SESSION['personas'] as $nif => $datosPersona) {
				$datosTabla.="<tr>";
					$datosTabla.="<td class='nif' >$nif</td>";
		
					$datosTabla.="<td><input type='text' value='$datosPersona[nombre]' class='nombre' /> </td>";
					$datosTabla.="<td><input type='text' value='$datosPersona[direccion]' class='direccion' /> </td>";
					$datosTabla.="<td>";
						$datosTabla.="<form method='post' action='#'>";
							$datosTabla.="<input type='hidden' name='nifpersona' value='$nif'>";
							$datosTabla.="<input type='submit' name='bajapersona' value='baja'>";
						$datosTabla.="</form>";
						$datosTabla.="<input type='button' value='Modificar' class='modificar'>";
					$datosTabla.="</td>";
				$datosTabla.="</tr>";
				//echo $nif;
				//print_r($datosPersona);
				//echo "<br>";
			}
		} 

		

?>
<html>
<head>
	<title></title>
	<meta charset='UTF-8'>
	<style type="text/css">
		label {width: 150px; display: inline-block;}
		table {border: 1px solid blue;}
		td, th {border: 1px solid blue; width: 250px;}
		.wraper {margin: auto; border: 3px ridge blue;width: 650px;padding: 15px;}
		form {margin: 0px; display: inline-block;}
	</style>
	<script type="text/javascript">
		//no ejecuta hasta que no cargue la pagina
		window.onload = function() {
			//recuperar botones a recuperar
			var botones=document.querySelectorAll('.modificar');
			//activar listener para modificar (tipo de boton y función a lanzar)
			for (i=0; i< botones.length; i++) {
				botones[i].addEventListener('click', modificar);
			}
		}

		function modificar() {
			alert ("modificar");
			//situarnos en la etiqueta TR de la fila sobre la que hemos pulsado el boton de modificar
			// opcion 1 >>>>> var tr=this.parentNode.parentNode;
			//opcion 2 >>>>>> closest busca la etiqueta más cercana del tipo que se indique
			var tr=this.closest('tr');
			//recuperar los datos a partir de la etiqueta TR
			var nif=tr.querySelector('.nif').innerText;
			var nom=tr.querySelector('.nombre').value;
			var dir=tr.querySelector('.direccion').value;
			//informar el formulario oculto
			document.getElementById('nif').value=nif;
			document.getElementById('nombre').value=nom;
			document.getElementById('direccion').value=dir;

			//enviar formulario al servidor
			document.getElementById('formModificar').submit();
			
		}

	</script>
</head>
<body>
	<div class='wraper'>
		<form method='post' action='#'>
			<label>NIF</label>
			<input type='text' name='nif'><br>
			<label>Nombre</label>
			<input type='text' name='nombre'><br>
			<label>Dirección</label>
			<input type='text' name='direccion'><br>
			<input type='submit' name='alta' value='alta persona'>
			<span><?=$error?></span>
		</form><br><br>
		<table>
			<tr><th>NIF</th>
				<th>Nombre</th>
				<th>Dirección</th>
				<th></th>
				<?=$datosTabla;?>;
			</tr>
			
		</table><br>
		<form method='post' action='#'>
			<input type='submit' name='baja' value='baja personas'>
		</form>
		<form method="post" action="#" id="formModificar">
			<input type="hidden" name="nif" id="nif">
			<input type="hidden" name="nombre" id="nombre">
			<input type="hidden" name="direccion" id="direccion">
			<input type="hidden" name="modificar">
		</form>
	</div>
</body>
</html>