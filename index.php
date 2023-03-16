<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>

	<?php

		$postsAnonymous = json_decode(file_get_contents('http://localhost/R4.01/Projet/src/api/appServer.php',
			false,
			stream_context_create(array('http' => array('method' => 'GET')))
			), true);

		echo '<table>
				<thead>
					<tr>
						<th>Auteur</th>
						<th>Contenu</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>';
		foreach($postsAnonymous['data'] as $item) {
			echo '<tr>
					<td>'.htmlentities($item['nom']).'</td>
					<td>'.htmlentities($item['contenu']).'</td>
					<td>'.htmlentities($item['date']).'</td>
				</tr>';
		}
		echo '</tbody>
			</table>';
	?>

</body>
</html>