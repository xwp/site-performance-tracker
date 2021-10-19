<?php
/**
 * Site Performance Tracker settings page.
 */
?>

<div class="container">
	<h1>Site Performance Tracker Settings</h1>
	<div class="form">
		<form action="" method="POST" class="settings-form">
			<div class="form-element">
				<label for="analytics_types">Analytics Types:</label>
				<select name="analytics_types" id="analytics_types" required>
					<option value="ga">Google Analytics</option>
					<option value="gtag">Global Site Tag</option>
					<option value="ga4">GA4 Analytics</option>
				</select>
			</div>

			<div class="form-element">
				<label for="analytics_id">Analytics ID:</label>
				<input type="text" id="analytics_id" name="analytics_id" pattern="[UA|GTM|G]+-[A-Z|0-9]+.*" value="" placeholder="UA-XXXXXXXX-Y" aria-label="analytics id" required>
			</div>

			<div class="form-element">
				<label for="measurement_version_dimension">Measurement Version Dimension:</label>
				<input type="text" id="measurement_version_dimension" name="measurement_version_dimension" pattern="[dimension]+[0-9]{1,2}" value="" placeholder="dimension1" aria-label="measurement version dimension" required>
			</div>

			<div class="form-element">
				<label for="event_meta_dimension">Event Meta Dimension:</label>
				<input type="text" id="event_meta_dimension" name="event_meta_dimension" pattern="[dimension]+[0-9]{1,2}" value="" placeholder="dimension2" aria-label="event meta dimension" required>
			</div>

			<div class="form-element">
				<label for="event_debug_dimension">Event Debug Dimension:</label>
				<input type="text" id="event_debug_dimension" name="event_debug_dimension" pattern="[dimension]+[0-9]{1,2}" value="" placeholder="dimension3" aria-label="event debug dimension" required>
			</div>

			<div class="form-element">
				<label for="tracking_ratio">Web Vitals Tracking Ratio:</label>
				<input type="number" id="tracking_ratio" name="tracking_ratio" min="0" max="1" step="any" value="" placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio" required>
			</div>

			<input type="submit" value="Submit">
		</form>
	</div>

	<div class="content">
		<p>You can get the <a href="https://web-vitals-report.web.app/" target="_blank">Web Vitals Report here</a>. Ensure that the date range starts from when the Web Vitals data is being sent.</p>
	</div>
</div>
