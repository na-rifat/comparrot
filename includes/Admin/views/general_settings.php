<div class="cp-admin-body">
    <?php
        $schema = new \Comparrot\Schema\Schema();
    ?>
    <div class="comparrot-admin-container">
        <h1><?php _e( 'Comparrot - general settings', 'comparrot' )?></h1>
        <hr>
        <div class="comparrot-admin-row">
            <?php
                echo $schema::submit(
                    [
                        'label' => __( 'Reset settings', 'comparrot' ),
                        'class' => ['comparrot-settings-reset-button'],
                    ]
                );

            ?>

            <?php
                echo $schema::create_settings_form(
                    [
                        'settings_key' => 'header',
                        'class'        => ['comparrot-settings-form'],
                        'admin'        => true,
                    ]
                );
                echo $schema::submit(
                    [
                        'label' => __( 'Save', 'comparrot' ),
                        'class' => ['comparrot-save-settings'],
                    ]
                );

            ?>
        </div>
    </div>
</div>
