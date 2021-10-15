<?php
/**
 * Site Performance Tracker settings page.
 */
?>

<div class="container">
	<h1>Site Performance Tracker Settings</h1>
	<div class="form">
		<form action="/action_page.php" method="POST" class="settings-form">
			<div class="form-element">
				<label for="analytics_types">Analytics Types:</label>
				<select name="analytics_types" id="analytics_types">
					<option value="ga">Google Analytics</option>
					<option value="gtag">Global Site Tag</option>
					<option value="ga4">GA4 Analytics</option>
				</select>
			</div>

			<div class="form-element">
				<label for="analytics_id">Analytics ID:</label>
				<input type="text" id="analytics_id" name="analytics_id" value="" placeholder="UA-XXXXXXXX-Y">
			</div>

			<div class="form-element">
				<label for="custom_dimensions">Custom Dimensions:</label>
				<input type="text" id="custom_dimensions" name="custom_dimensions" value="" placeholder="dimension1">
			</div>

			<div class="form-element">
				<label for="tracking_ratio">Web Vitals Tracking Ratio:</label>
				<input type="text" id="tracking_ratio" name="tracking_ratio" value="" placeholder="Enter between 0 > 1">
			</div>

			<input type="submit" value="Submit">
		</form>
	</div>
</div>
