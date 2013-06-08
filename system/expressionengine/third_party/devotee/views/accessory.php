<div class="border">
	<div class="border">
		<?php if( ! empty($updates->error)) : ?>
			<p><?php echo $updates->error ?></p>
		<?php else : ?>
			<p class="last-check">Last Add-on Check: <?php echo date('l, M. j, Y @ g:ia.', $last_check) ?>&nbsp;&nbsp;<a href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_refresh' ?>" class="available refresh">Check Now</a>&nbsp;<img class="monitor-loading" src="<?php echo URL_THIRD_THEMES; ?>/devotee/images/loader.gif" alt="Loading..." /></p>
			<table>
				<thead>
					<tr class="first">
						<th class="addon-notes">&nbsp;</th>
						<th class="addon-name">Add-On Name</th>
						<th class="addon-type">Type</th>
						<th class="addon-installed">Installed</th>
						<th class="addon-current">Latest</th>
						<th class="addon-status"><span>Status</span></th>
						<th class="addon-link">&nbsp;</th>
						<!--<th class="addon-link">Link</th>-->
					</tr>
				</thead>
				<tbody>
					<?php foreach($updates as $package => $addon) : ?>
						<?php
							if(in_array($package, $hidden_addons) && $show_hidden == FALSE)
							{
								continue;
							}

							// convert to object if need be on some php versions
							if( ! is_object($addon))
							{
								$addon = (object) $addon;
							}
						?>
						<?php if($addon->update_available) : ?>
							<tr class="update<?php if($addon->notes) : ?> notes<?php endif ?><?php if (in_array($package, $hidden_addons)): ?> hidden_addon<?php endif ?>">
						<?php else : ?>
							<tr class="<?php if (in_array($package, $hidden_addons)) echo 'hidden_addon'; ?>" >
						<?php endif ?>
							<!-- notes -->
							<?php if($addon->update_available && $addon->notes) : ?>
								<td class="addon-notes"><a href="#" class="toggle"></a></td>
							<?php else : ?>
								<td class="addon-notes">&nbsp;</td>
							<?php endif ?>

							<?php $commercial_designation = isset($addon->license) && $addon->license == 'Commercial' ? "&nbsp;<span class='commercial_designation' >$</span> "  : '' ?>

							<!-- name -->
							<td class="addon-name">
								<?php if (! in_array($package, $hidden_addons) ): ?>
									<?php echo $addon->name ?><span><?php echo $commercial_designation ?>&nbsp;<a class="hide_addon" href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_hide_addon' . AMP . 'package=' . $package ?>">Hide this</a></span>
								<?php else: ?>
									<?php echo $addon->name ?><span><?php echo $commercial_designation ?>&nbsp;<a class="unhide_addon" href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_unhide_addon' . AMP . 'package=' . $package ?>">Unhide this</a></span>
								<?php endif; ?>
							</td>

							<!-- types -->
							<td class="addon-type">
								<ul>
									<?php foreach($addon->types as $key => $val) : ?>
										<li class="<?php echo ($val) ? 'highlight' : '' ?>"><?php echo $key ?></li>
									<?php endforeach ?>
								</ul>
							</td>

							<!-- local version -->
							<td class="addon-installed"><?php echo $addon->version ?></td>

							<!-- devot:ee version -->
							<td class="addon-current">
								<?php if($addon->current_version != '') : ?>
									<?php if($addon->update_available) : ?>
										<span class="available"><?php echo $addon->current_version ?></span>
									<?php else : ?>
										<?php echo $addon->current_version ?>
									<?php endif ?>
								<?php else : ?>
									&ndash;
								<?php endif ?>
							</td>

							<!-- status and link -->
							<?php if($addon->devotee_link != '') : ?>
								<?php if($addon->update_available) : ?>
									<td class="addon-status">
										<a href="<?php echo $cp->masked_url($addon->devotee_link.'?utm_source=site%26utm_medium=acc_link_addon%26utm_campaign=monitor'); ?>" class="available update" target="_blank">Update Available</a>
									</td>
								<?php else : ?>
									<?php if($addon->current_version == '') : ?>
										<td class="addon-status">
											<span class="warning">Version Info Unavailable - <a href="<?php echo $cp->masked_url($addon->devotee_link.'?utm_source=site%26utm_medium=acc_link_addon%26utm_campaign=monitor'); ?>" target="_blank">Details</a></span>
										</td>
									<?php elseif($addon->version != $addon->current_version) : ?>
										<td class="addon-status">
											<span class="warning">Installed version higher than devot:ee's</span>
										</td>
									<?php else : ?>
										<td class="addon-status">
											<span class="check">Up-to-date</span>
										</td>
									<?php endif ?>
								<?php endif ?>
								<td class="addon-link<?php if($addon->update_available) : ?> link-present<?php endif ?>">
									<a href="<?php echo $cp->masked_url($addon->devotee_link.'?utm_source=site%26utm_medium=acc_link_addon%26utm_campaign=monitor'); ?>" class="available" target="_blank">
										<span></span>
									</a>
								</td>
							<?php else : ?>
								<td class="addon-status">
									<span class="warning">Not Found - <a href="<?php echo $cp->masked_url('http://devot-ee.com/search/results?keywords='.rawurlencode(htmlspecialchars_decode($addon->name)).'%26collection=addons%26addon_version_support=ee2%26utm_source=site%26utm_medium=acc_link_search%26utm_campaign=monitor'); ?>" target="_blank">Search</a></span>
								</td>
								<td class="addon-link">
									<a href="<?php echo $cp->masked_url('http://devot-ee.com/search/results?keywords='.rawurlencode(htmlspecialchars_decode($addon->name)).'%26collection=addons%26addon_version_support=ee2%26utm_source=site%26utm_medium=acc_link_search%26utm_campaign=monitor'); ?>" class="available" target="_blank">
										<span></span>
									</a>
								</td>
							<?php endif ?>
						</tr>
						<?php if($addon->update_available && $addon->notes) : ?>
							<tr class="notes">
								<td class="notes" colspan="7">
									<h6>Release Notes</h6>
									<ul>
									<?php
										foreach (explode("\n", $addon->notes) as $note)
										{
											if ($note != '')
												echo "<li>".stripslashes($note)."</li>\n";
										}
									?>
									</ul>
								</td>
							</tr>
						<?php endif ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>

		<div id="devotee-footer">
			<?php if( ! empty($hidden_addons) && $show_hidden == FALSE) : ?>
				<p class="hidden"><a href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_display_hidden_addons' ?>" class="show-hidden-addons">Show Hidden Add-ons (<?php echo count($hidden_addons) ?>)</a></p>
			<?php elseif ( ! empty($hidden_addons) && $show_hidden == TRUE) : ?>
				<p class="hidden"><a href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_refresh' ?>" class="show-hidden-addons">Hide Previously Hidden Add-ons (<?php echo count($hidden_addons) ?>)</a></p>
			<?php endif ?>
			<p>Last Add-on Check: <?php echo date('l, M. j, Y @ g:ia.', $last_check) ?>&nbsp;&nbsp;<a href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_refresh' ?>" class="available refresh">Check Now</a>&nbsp;<img class="monitor-loading" src="<?php echo URL_THIRD_THEMES; ?>/devotee/images/loader.gif" alt="Loading..." /></p>
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