<?php include get_template_directory() . '/header.php'?>
<?php
    $cmp_theme_name= wp_get_theme();
    if($cmp_theme_name->Name ==  'Astra'){
        echo "</div>";
    }
?>
<div class="comparrot-layout">
    <?php echo get_the_content() ?>
</div>
<?php include get_template_directory() . '/footer.php'?>