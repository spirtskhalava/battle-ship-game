<!DOCTYPE html>
<html ng-app>
<head>
	<title>Task</title>
	<script language="javascript" type="text/javascript" src="js/lib/angular.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/game.js"></script>
	<script language="javascript" type="text/javascript" src="js/app.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div ng-controller="GameController" id="container">
		<table cellpadding="5" cellspacing="5" border="0" id="gameTable">
			<thead>
				<td>&nbsp;</td>
				<td>1</td>
				<td>2</td>
				<td>3</td>
				<td>4</td>
				<td>5</td>
				<td>6</td>
				<td>7</td>
				<td>8</td>
				<td>9</td>
				<td>10</td>
			</thead>
			<tbody>
				<tr ng-repeat="row in data.map">
					<td class="index">{{$index}}</td>
					<td ng-repeat="state in row track by $index" class="gameCell state_{{state}}" ng-click="shootAt($parent.$index,$index)">
						{{renderState(state)}}
					</td>
				</tr>
			</tbody>
		</table>
		<div class="shots"><strong>Shot count:</strong> {{data.shots}}</div>

		<div class="buttons">
			<button class="btn toggleHidden" ng-click="toggleHidden()">{{hiddenState ? 'hide ships' : 'show ships'}}</button>
			<button class="btn newGame" ng-click="newGame()">new game</button>
		</div>
	</div>
</body>
</html>
