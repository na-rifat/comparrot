<div class="cp-admin-body">
    <div class="comparrot-admin-container">
        <div>
            <h1><?php _e( 'Comparrot', 'comparrot' )?></h1>
            <hr />
        </div>
        <!-- Uploader -->
        <div>
            <br>
            <!-- Uploader section -->
            <div class="uploader">
                <div class="csv-uploader">
                    <h2>Import files</h2>
                    <p style="color: #999;">
                        Don't forget to select the images which included inside the CSV file
                    </p>
                    <div class="loader"></div>
                    <div class="button button-large cp-button upload-btn"><i class="fas fa-upload"></i>Upload</div>
                    <div class="button button-large cp-button template-download "><i class="fas fa-download"></i>CSV Template
                    </div>
                    <div class="button button-large cp-button re-generate-btn"><i class="fas fa-sync-alt"></i>Regenerate templates</div>
                    <!-- <div class="button button-large cp-button re-generate-page-btn"><i class="fas fa-sync-alt"></i>Regenerate pages (development only)</div> -->
                    <div class="button button-large cp-button help-btn"><i class="fas fa-question-circle"></i>Help</div>
                </div>
            </div>
            <br>
            <br>
            <hr>
            <div class="cp-admin-status">
                <div class="status-holder"></div>
                <div class="upload-log">
                    <div class="no-items-imported"><?php _e( 'No data imported yet', 'comparrot' )?></div>
                </div>
                <div class="skipped-log">

                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/help.php"?>