<div class="wrap">
    <h2><?=__( 'Recipients for Contact Form 7', 'ra7form' )?></h2>
    <div id="poststuff">
    <?php
    if ( !( in_array( 'contact-form-7/wp-contact-form-7.php',
        apply_filters( 'active_plugins' , get_option( 'active_plugins' ) ) ) ) ) : ?>
            <div class="error"><p><?=__( 'The plugin Contact Form 7 was not installed or active.', 'ra7form')?></p></div>
    <?php else : ?>
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $this->recipients_obj->prepare_items();
                        $this->recipients_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    <?php endif; ?>
    </div>
</div>