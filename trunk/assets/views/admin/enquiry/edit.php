<div class="panel panel-default enquiry-page">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body helpdesk">
        <div class="col-xs-6 col-md-6">
            <?php
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderForm();
            ?>
        </div>
        <div class="col-xs-6 col-md-6">
            <div id="helpdesk">
                <h4>Messages:</h4>
                <br/>
                <div class="messages-panel">
                    <div class="gwt-HTML">
                        <ul class="chat" id="enquiry_messages">
                            <?php foreach ($enquiryMessages as $eMessage): ?>
                                <?php include __DIR__.'/_enquiry_message.php'; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div aria-hidden="true" style="display: none;" class="gwt-HTML errors alert alert-danger js-add-enquiry-message-errors"></div>
                <form action="/admin/enquiry/<?php echo $item->id(); ?>/add-message" method="POST" class="js-add-enquiry-message-form">
                    <table class="add-message-form" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="vertical-align: top;" align="left">
                                <div class="gwt-Label">Message:</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;" align="left">
                                <textarea required="required" class="form-control" name="message"></textarea></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;" align="left">
                                <table class="buttons-panel" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;" align="right">
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>

