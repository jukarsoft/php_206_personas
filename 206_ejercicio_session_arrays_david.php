<?php
	session_start();

	//inicializar variables
	$mensaje=null;
	$datosTabla = null;

	//1.- ALTA DE PERSONAS

	//comprobar si se ha pulsado alta
	if (isset($_POST['alta'])) {
		//recuperar los datos del formulario
		$nif = $_POST['nif'];
		$nombre = $_POST['nombre'];
		$direccion = $_POST['direccion']; 

		//validar los datos: nif, nombre y dirección estén informados
		if (trim($nif)=='' || trim($nombre)=='' || trim($direccion)=='') {
			$mensaje = 'todos los datos son obligatorios';
		} else {
			//validar que la persona no exista en el array
			if (isset($_SESSION['personas']) && array_key_exists($nif, $_SESSION['personas'])) {
				$mensaje = 'persona ya existe';
			} else {
				//dar de alta la persona en la variable de sesión
				//op 1
				$_SESSION['personas'][$nif]['nombre'] = $nombre;
				$_SESSION['personas'][$nif]['direccion'] = $direccion;

				//op 2
				//$_SESSION['personas'][$nif] = ['nombre' => $nombre,  'direccion' => $direccion];

				//print_r($_SESSION['personas']);

				$mensaje = 'alta persona efectuada';
			}
		}
	}

	//3. BAJA DE TODAS LAS PERSONAS

	//comprobar que se ha pulsado el botón de baja
	if (isset($_POST['baja'])) {
		//borrar la variable de sesión
		unset($_SESSION['personas']);
	}

	//4. BAJA DE PERSONA

	//comprobar que se ja pulsado el botón de baja
	if (isset($_POST['bajapersona'])) {
		//recuperar nif a dar de baja
		$nif = $_POST['nifpersona'];

		//borrar de la variable de sesión la fila correspondiente al nif
		unset($_SESSION['personas'][$nif]);

		$mensaje='baja efectuada';
	}

	//5. MODIFICACION DE LA PERSONA
	if (isset($_POST['modificar'])) {
		//recuperar los datos del formulario
		$nif = $_POST['nif'];
		$nombre = $_POST['nombre'];
		$direccion = $_POST['direccion']; 

		//validar nombre y dirección informados
		if (trim($nombre)=='' || trim($direccion)=='') {
			$mensaje = 'todos los datos son obligatorios';
		} else {
			//modificar el nombre y la dirección en el array
			$_SESSION['personas'][$nif]['nombre'] = $nombre;
			$_SESSION['personas'][$nif]['direccion'] = $direccion;

			//mensaje de modificación efectuada
			$mensaje = 'modificación efectuada';
		}

	}

	//2.- MOSTRAR LAS FILAS EN LA PANTALLA

	//comprobar si la variable de sesión existe
	if (isset($_SESSION['personas'])) {
		//ordenar el array por nif
		//ksort($_SESSION['personas']);

		//ordenar por nombre
		//asort($_SESSION['personas']);

		//ordenar el array por dirección
		/*
		$direcciones = array();

		foreach ($_SESSION['personas'] as $clave => $valor) {
			array_push($direcciones, $valor['direccion']);
			//$direcciones[$clave] = $valor['direccion'];
		}

		array_multisort($direcciones, SORT_ASC, $_SESSION['personas']);
		*/

		//creamos un array con las claves del array de personas (el nif)
		$claves = array_keys($_SESSION['personas']);

		//creamos un array con el dato (columna) que queremos ordenar (la dirección)
		$direcciones = array_column($_SESSION['personas'], 'direccion');

		//ordenamos el array de direcciones de forma ascendente y, simultaneamente, se ordenara el array de personas y el de claves por la misma ordenación de claves que el primero
		array_multisort($direcciones, SORT_ASC, $_SESSION['personas'], $claves);

		//substituimos las claves del array de personas por las del array de claves 
		$_SESSION['personas'] = array_combine($claves, $_SESSION['personas']);
		
		//print_r($_SESSION['personas']);

		//recorrer la variable de sesión con un bucle para construir las etiquetas tr de la tabla

		foreach ($_SESSION['personas'] as $nif => $datosPersona) {
			$datosTabla.="<tr>";
			$datosTabla.="<td class='nif'>$nif</td>";
			$datosTabla.="<td><input type='text' value='$datosPersona[nombre]' class='nombre'/></td>";
			$datosTabla.="<td><input type='text' value='$datosPersona[direccion]' class='direccion'/></td>";
			$datosTabla.="<td>";

			$datosTabla.="<form method='post' action='#'>";
			$datosTabla.="<input type='hidden' name='nifpersona' value='$nif'>";
			$datosTabla.="<input type='submit' name='bajapersona' value='baja'>";
			$datosTabla.="</form>";

			$datosTabla.="<input type='button' value='Modificar' class='modificar'>";

			$datosTabla.="</td>";
			$datosTabla.="</tr>";
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
		//cuando se cargue la pàgina activaremos los listeners
		window.onload=function() {
			//recuperar los botones con class modificar
			var botones = document.querySelectorAll('.modificar'); 

			//activar listeners de los botones 'modificar'
			for (i=0; i < botones.length; i++) {
				botones[i].addEventListener('click', modificar)
			}
		}
		//cargamos el formulario hidden con los datos del html seleccionado
		function modificar() {
			//situarnos en la etiqueta tr de la fila sobre la que hemos pulsado el botón de 'modificar'
			var tr = this.closest('tr')

			//recuperar los datos a partir de la tr
			var nif = tr.querySelector('.nif').innerText
			var nombre = tr.querySelector('.nombre').value
			var direccion = tr.querySelector('.direccion').value

			//informar el formulario oculto
			document.getElementById('nif').value = nif
			document.getElementById('nombre').value = nombre
			document.getElementById('direccion').value = direccion

			//enviar el formulario al servidor
			document.getElementById('formModificar').submit()
		}

	</script>
	
</head>
<body>
	<div class='wraper'>
		<form method='post' action='#'>
			<label>NIF</label><input type='text' name='nif'><br>
			<label>Nombre</label><input type='text' name='nombre'><br>
			<label>Dirección</label><input type='text' name='direccion'><br>
			<input type='submit' name='alta' value='alta persona'>
			<span><?=$mensaje?></span>
		</form><br><br>
		<table>
			<tr><th>NIF</th><th>Nombre</th><th>Dirección</th><th></th></tr>
			<?=$datosTabla;?>
		</table><br>
		<form method='post' action='#'>
			<input type='submit' name='baja' value='baja personas'>
		</form>
		<form method="post" action='#' id='formModificar'>
			<input type="hidden" name="nif" id='nif'>
			<input type="hidden" name="nombre" id='nombre'>
			<input type="hidden" name="direccion" id='direccion'>
			<input type="hidden" name="modificar">
		</form>
	</div>
</body>
</html>