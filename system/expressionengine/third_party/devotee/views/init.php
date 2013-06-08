<div class="border">
	<div class="border">
		<p class="error-message">Loading&hellip;</p>

		<div id="devotee-footer">
			<p class="logos">
				<a href="<?php echo $cp->masked_url('http://dvt.ee/mtr-acc-lnk-lgo'); ?>" target="_blank" class="first">devot:ee</a>
				<a href="<?php echo $cp->masked_url('http://eecoder.com'); ?>" target="_blank" class="last">eecoder</a>
			</p>
			<p>
				<small>
					EE Add-on Monitor is proudly powered by
					<a href="<?php echo $cp->masked_url('http://dvt.ee/mtr-acc-lnk-ttl'); ?>" target="_blank">devot:ee</a>
					in partnership with
					<a href="<?php echo $cp->masked_url('http://eecoder.com'); ?>" target="_blank">eecoder</a>.
					Designed by
					<a href="<?php echo $cp->masked_url('http://antistaticdesign.com'); ?>" target="_blank">Antistatic</a>
				</small>
			</p>
		</div>
	</div><!-- /.border -->
</div><!-- /.border -->

<script type="text/javascript">
	$(document).ready(function() {
		$.ajax({
			cache: false,
			data: {},
			dataType: 'html',
			success: function(data) {
				$('#devot-ee .accessorySection').html(data);
			},
			type: 'GET',
			url: '<?php echo $link ?>'
		});
	});
</script>
