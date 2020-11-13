<div class='wrap'>
    <h2><?_e( 'Report Recipient', 'ra7form' )?>: <b><?=$this->content['title']?></b></h2>    
    <input type='hidden' name='action' value='update'>
    <div id='poststuff'>
        <div id='post-body' class='metabox-holder columns-2'>
            <div id='postbox-container-1' class='postbox-container'>
                <div id='submitdiv' class='postbox'>
                    <h2><?_e( 'Report', 'ra7form' )?></h2>
                    <div class='inside'>
                        <div class='submitbox' id='submitpost'>
                            <div id='major-publishing-actions'>
                                <div id='publishing-action'>
                                    <p><?=$this->content['contains']?></p>
                                    <span class='spinner'></span>
                                    <!--<input type='submit' class='button-primary' name='printForm' value='<?_e( 'Generate PDF', 'ra7form' )?>'>-->
                                </div>
                                <div class='clear'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id='postbox-container-2' class='postbox-container'>
                <div id='normal-sortables' class='meta-box-sortables'>
                    <div id='contactnamediv' class='postbox '>
                        <div class='postbox-header'>
                            <h2 class='ui-sortable-handle'><?_e('Default Settings', 'ra7form')?></h2>
                        </div>
                        <div class='inside'>
                            <table class='form-table'>
                                <tbody>
                                    <tr class='recipient-prop'>
                                        <th><?_e('Sender', 'ra7form')?></th>
                                        <td><?=$this->content['_mail'][0]['sender']?></td>
                                    </tr>
                                    <tr class='recipient-prop'>
                                        <th><?_e('Recipient', 'ra7form')?></th>
                                        <td><?=$this->content['_mail'][0]['recipient']?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id='postbox-container-3' class='postbox-container'>
                <div id='normal-sortables' class='meta-box-sortables'>
                    <div id='contactnamediv' class='postbox '>
                        <div class='postbox-header'>
                            <h2 class='ui-sortable-handle'><?_e( 'Multiple Recipients', 'ra7form' )?></h2>
                        </div>
                        <div class='inside'>
                            <table class='form-table'>
                                <tbody>
                                    <?=$this->mount_fields()?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>                    
            </div>
        </div>
    </div>
</div>