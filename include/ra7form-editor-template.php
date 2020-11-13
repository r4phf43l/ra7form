<div class='wrap'>
    <h2><?_e( 'Edit Recipient', 'ra7form' )?>: <b><?=$this->content['title']?></b></h2>
    <form method='post'>      
        <div id='poststuff'>
            <div id='post-body' class='metabox-holder columns-2'>                
                <div id='postbox-container-1' class='postbox-container'>
                    <div id='submitdiv' class='postbox'>
                        <h2><?_e( 'Status', 'ra7form' )?></h2>
                        <div class='inside'>
                            <div class='submitbox' id='submitpost'>
                                <div id='major-publishing-actions'>
                                    <div id='publishing-action'>
                                        <p><?_e( $this->content['contains'], 'ra7form' )?></p>
                                        <span class='spinner'></span>
                                        <input disabled id='saveButton' type='button' class='button-primary' value='<?_e( 'Save recipient', 'ra7form' )?>'>
                                        <input type='hidden' value='<?=$this->content[ '_form' ][ 0 ]?>' id='hiddeninside'>
                                        <input type='hidden' id='newhiddeninside'>
                                    </div>
                                    <div class='clear'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id='postbox-container-2' class='postbox-container'>
                    <div>
                        <div class='postbox '>
                            <div class='postbox-header'>
                                <h2><?_e('Default Settings', 'ra7form')?></h2>
                            </div>
                            <div class='inside'>
                                <table class='form-table'>
                                    <tbody>
                                        <tr class='recipient-prop'>
                                            <th><label for='sender'><?_e( 'Sender', 'ra7form' )?></label></th>
                                            <td><input type='text' id='sender' value='<?=$this->content['_mail'][0]['sender']?>' class='widefat' ></td>
                                        </tr>
                                        <tr class='recipient-prop'>
                                            <th>
                                                <label for='recipient'><?_e( 'Recipient', 'ra7form' )?></label>                                                
                                            </th>
                                            <td>
                                                <input type='text' id='recipient' value='<?=$this->content['_mail'][0]['recipient']?>' class='widefat' >
                                                <p><?_e( 'To use Multiple recipients [your-recipient] is mandatory', 'ra7form' )?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                            <label for='required'><?_e( 'Required', 'ra7form' )?></label>
                                            </th>                                            
                                            <td>
                                                <input type='checkbox' id='required' <?=$this->content['_required']==1?' checked':''?>>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id='postbox-container-3' class='postbox-container'>
                    <div>
                        <div class='postbox '>
                            <div class='postbox-header'>
                                <h2><?_e('Multiple Recipients', 'ra7form')?></h2>                                
                            </div>
                            <input id='addRecipient' type='button' class='button-secondary' value='<?_e( 'New recipient', 'ra7form' )?>'>
                            <div class='inside'>
                                <table class='widefat'>
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
    </form>
</div>