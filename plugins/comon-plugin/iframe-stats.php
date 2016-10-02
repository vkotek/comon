<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
global $wpdb;

	$active_users_id = "
		SELECT user_id
		FROM wp_usermeta
		WHERE wp_usermeta.meta_key = 'wp_user_level' 
		AND wp_usermeta.meta_value != 10
		AND user_id IN (
			SELECT id
			FROM wp_users
		)
	";
	
	// Q1. GENDER
	$queryGender = "
		SELECT COUNT(wp_bp_xprofile_data.value) AS 'Amount', wp_bp_xprofile_data.value AS 'Gender'
		FROM wp_bp_xprofile_data
		WHERE wp_bp_xprofile_data.field_id = 139  AND user_id IN (
			".$active_users_id."
		)
		GROUP BY value
		ORDER BY value";		
	$dataGender = $wpdb->get_results($queryGender);

	// Q2. AGE
	$queryAge = "
		SELECT COUNT(*) as 'Value',
		CASE
			WHEN age <=10 THEN '0-10'
			WHEN age >=11 AND age <=20 THEN '11-20'
			WHEN age >=21 AND age <=30 THEN '21-30'
			WHEN age >=31 AND age <=40 THEN '31-40'
			WHEN age >=41 AND age <=50 THEN '41-50'
			WHEN age >=51 AND age <=60 THEN '51-60'
			WHEN age >=61 THEN '61+'
		END AS Ages
		FROM
			(
				SELECT value AS age
				FROM wp_bp_xprofile_data
				WHERE wp_bp_xprofile_data.field_id = 142  AND user_id IN (
					".$active_users_id."
				)
			) as tbl
		GROUP BY Ages";
	$dataAge = $wpdb->get_results($queryAge);

	// Q3. CITY
	$queryCity = "
		SELECT count(wp_bp_xprofile_data.value) AS 'Quantity', wp_bp_xprofile_data.value AS 'Item'
		FROM wp_bp_xprofile_data
		WHERE wp_bp_xprofile_data.field_id = 143 AND user_id IN (
				".$active_users_id."
		)
		GROUP BY 'Item'";
	$dataCity = $wpdb->get_results($queryCity);

	// Q4. EDUCATION
	$queryEdu = "
		SELECT count(wp_bp_xprofile_data.value) AS 'Quantity', wp_bp_xprofile_data.value AS 'Item'
		FROM wp_bp_xprofile_data
		WHERE wp_bp_xprofile_data.field_id = 186 AND user_id IN (
				".$active_users_id."
		)
		GROUP BY 'Item'";
	$dataEdu = $wpdb->get_results($queryEdu);
	
?>
	
<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<style>
	body {
		font-family: 'Open Sans', sans-serif;
	}
	</style>
	<script type="text/javascript">
	  // Load Charts and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      google.charts.setOnLoadCallback(drawGender);
      google.charts.setOnLoadCallback(drawAge);
      google.charts.setOnLoadCallback(drawCity);
      google.charts.setOnLoadCallback(drawEdu);
	  
	   // GENDER
      function drawGender() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('number', 'Value');
        data.addRows([
		<?php
		foreach($dataGender as $row) {
			printf("['%s', %s],", substr($row->Gender, 3) , $row->Amount);
		}
		
		?>
        ]);

        var options = {title:'Pohlavie',
                       width:300,
                       height:250,
					   colors: ['#a2ad00', '#6a8012', '#646464', '#c4c4c4', '#808080','#999999','#b3b3b3','#cccccc'],
					   chartArea:{
							left:20,
							top: 40,
							width: '100%',
							height: '200px',
						},
						titleTextStyle: { 
							color: '#4a4d4e',
							fontName: 'Roboto',
							fontSize: 16
						}
						};

        var chart = new google.visualization.PieChart(document.getElementById('chartGender'));
        chart.draw(data, options);
      }
	  
	  
      function drawAge() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('number', 'Value');
        data.addRows([
		<?php
		foreach($dataAge as $row) {
			printf("['%s', %s],", $row->Ages , $row->Value);
		}
		
		?>
        ]);

        var options = {title:'Vek',
                       width:300,
                       height:250,
					   colors: ['#a2ad00', '#6a8012', '#646464', '#c4c4c4', '#808080','#999999','#b3b3b3','#cccccc'],
					   chartArea:{
							left:20,
							top: 40,
							width: '100%',
							height: '200',
						},
						titleTextStyle: { 
							color: '#4a4d4e',
							fontName: 'Roboto',
							fontSize: 16
						}};

        var chart = new google.visualization.PieChart(document.getElementById('chartAge'));
        chart.draw(data, options);
      }
	  

      function drawCity() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('number', 'Value');
        data.addRows([
        <?php
		foreach($dataCity as $row) {
			printf("['%s', %s],", substr($row->Item,3), $row->Quantity);
		}
		?>
        ]);

        var options = {title:'Mesto',
                       width:300,
                       height:250,
					   colors: ['#a2ad00', '#6a8012', '#646464', '#c4c4c4', '#808080','#999999','#b3b3b3','#cccccc'],
					   chartArea:{
							left:20,
							top: 40,
							width: '100%',
							height: '200',
						},
						titleTextStyle: { 
							color: '#4a4d4e',
							fontName: 'Roboto',
							fontSize: 16
						}};

        var chart = new google.visualization.PieChart(document.getElementById('chartCity'));
        chart.draw(data, options);
      }
	  

      function drawEdu() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('number', 'Value');
        data.addRows([
        <?php
		foreach($dataEdu as $row) {
			printf("['%s', %s],", substr($row->Item,3), $row->Quantity);
		}
		?>
        ]);

        var options = {title:'Vzdelanie',
                       width:300,
                       height:250,
					   colors: ['#a2ad00', '#6a8012', '#646464', '#c4c4c4', '#808080','#999999','#b3b3b3','#cccccc'],
					   chartArea:{
							left:20,
							top: 40,
							width: '100%',
							height: '200',
						},
						titleTextStyle: { 
							color: '#4a4d4e',
							fontName: 'Roboto',
							fontSize: 16
						}};

        var chart = new google.visualization.PieChart(document.getElementById('chartEdu'));
        chart.draw(data, options);
      }
	  
	</script>
</head>
<body>
<table class="columns">
	<tr>
		<td><div id="chartGender" style="border: 0px solid #ccc"></div></td>
		<td><div id="chartAge" style="border: 0px solid #ccc"></div></td>
	</tr><tr>
		<td><div id="chartCity" style="border: 0px solid #ccc"></div></td>
		<td><div id="chartEdu" style="border: 0px solid #ccc"></div></td>
	</tr>
</table>
</body>