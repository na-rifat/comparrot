<?php wp_head();?>

<body class="<?php body_class()?>">
    <div class="comparrot-layout">
        <?php echo get_the_content() ?>
    </div>
</body>

<?php wp_footer();?>