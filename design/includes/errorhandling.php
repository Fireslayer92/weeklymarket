<?php
if (!empty($errt)){
			?>
			<div id="error" class="modal fade" role="dialog"> <!-- Error handling -->
				<div class="modal-dialog">
					<div class="modal-content">
					<div class="modal-header">
					<p class="text-danger"><b>Fehler</b></p>
					</div>
					<div class="modal-body">
						<?php echo($errt); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" onclick="window.location = window.location.href;">Schliessen</button>
					</div>
					</div>

				</div>
			</div> <!-- Error handling -->
			<?php
        }
        ?>