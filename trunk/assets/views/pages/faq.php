<div class="container">
    <div class="row">
        <div class="col-lg-12">
			<h1>Frequently Asked Questions</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">Frequently Asked Questions</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?php if ($success = $this->pixie->session->flash('success')): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($entries) && !is_null($entries)): ?>
                <div class="panel-group" id="accordion"  id="dataGrid">
                    <?php foreach ($entries as $obj): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $obj->faqID; ?>" >
                                        <?php $_($obj->question); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?php echo $obj->faqID; ?>" class="panel-collapse collapse">
                                <div class="panel-body" >
                                    <?php if (!empty($obj->answer)) echo $obj->answer;
                                    else "Not answered yet." ?>
                                </div>
                            </div>
                        </div>
                <?php endforeach; ?>    
                </div>
            <?php endif ?>     
            <div style="display: none" class="alert alert-success"></div>
            <div class="panel-group" id="accordion1" data-bind="foreach: data" style='display:none'>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1"  data-bind="text: question, attr: { href: '#collapse' + faqID }">
                            </a>
                        </h4>
                    </div>
                    <div data-bind="attr: { id: 'collapse' + faqID }" class="panel-collapse collapse">
                        <div class="panel-body" data-bind="text: answer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section">
        <form role="form" method="post" action="/faq" id="faqForm">
            <div class="form-group">
                <label for="userEmail">Email address</label>
                <input type="email" class="form-control" name="userEmail" id="userEmail" placeholder="Enter email" required data-validation="email">
            </div>
            <div class="form-group">
                <label for="userQuestion">Question</label>
                <textarea class="form-control" name="userQuestion" id="userQuestion" placeholder="Type your question here..." required></textarea>
            </div>
            <?php echo $_token('faq'); ?>
            <button id="form-submit" type="submit" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">Submit</span></button>
        </form>
    </div>
</div>

<script>
    function AppViewModel() {
        var self = this;
        self.data = ko.observableArray([]);
    }
    var model = new AppViewModel();

    $(function() {

        $('#faqForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            container: 'tooltip'
        }).on('success.form.bv', function(e) {
            var l = Ladda.create(document.querySelector( '#form-submit' ));
            l.start();
            $.ajax({
                url: '/faq',
                type: "POST",
                dataType: "json",
                data: $("#faqForm").serialize(),
                success: function(data) {
                    location.reload();
//                    $(".alert").empty().append('Thank you for your question. We will contact you as soon.').show();
//                    if (data.length) {
//                        ko.applyBindings(model);
//                        model.data(data);
//                        $('#accordion1').css('display', 'block');
//                    }
//                    else {
//                        $(".alert").empty().append('There is some error happened during processing your request.').show();
//                    }
                },
                fail: function() {
                    $(".alert").empty().append('There is some error happened during processing your request.').show();
                }
            }).always(function() { l.stop(); });
            return false;
        });
    });
</script>