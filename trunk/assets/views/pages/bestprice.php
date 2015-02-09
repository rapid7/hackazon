<div class="container">
    <div class="row">
        <div class="col-lg-12">
			<h1>Get the best price</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">Get the best price</li>
            </ol>
        </div>
    </div>

    <div class="section">
        <form role="form" method="post" action="/bestprice" id="bestpriceForm">
            <div class="form-group">
                <label for="userEmail">Email address</label>
                <input type="email" class="form-control" name="userEmail" id="userEmail" placeholder="Enter email" required data-validation="email">
            </div>
            <?php echo $_token('bestprice'); ?>
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
        var alertBlock = $(".alert");

        $('#bestpriceForm').bootstrapValidator({
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
                url: '/bestprice',
                type: "POST",
                dataType: "json",
                data: $("#bestpriceForm").serialize(),
                success: function(data) {
                    alertBlock.empty().append('Thank you for your question. We will contact you as soon.').show();
                    if (data.length) {
                        ko.applyBindings(model);
                        model.data(data);
                        $('#accordion1').css('display', 'block');
                    }
                    else {
                        alertBlock.empty().append('There is some error happened during processing your request.').show();
                    }
                },
                fail: function() {
                    alertBlock.empty().append('There is some error happened during processing your request.').show();
                }
            }).always(function() { l.stop(); });
            return false;
        });
    });
</script>