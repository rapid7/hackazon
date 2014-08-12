
    <div class="container">

        <div class="row">

            <div class="col-lg-12">
                <h1 class="page-header">FAQ
                    <small>Frequently Asked Questions</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a>
                    </li>
                    <li class="active">FAQ</li>
                </ol>
            </div>

        </div>

        
        <?php if (isset($entries) && !is_null($entries)): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group" id="accordion">
                        <?php foreach($entries as $obj): ?>
                           <div class="panel panel-default">
                               <div class="panel-heading">
                                   <h4 class="panel-title">
                                       <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $obj->faqID; ?>">
                                         <?php echo $_($obj->question, 'userQuestion'); ?>
                                       </a>
                                  </h4>
                               </div>
                               <div id="collapse<?php echo $obj->faqID; ?>" class="panel-collapse collapse">
                                   <div class="panel-body">
                                       <?php if(!empty ($obj->answer)) echo $obj->answer; else "Not answered yet."?>
                                   </div>
                               </div>
                           </div>
                        <?php endforeach; ?>    
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if (isset($this->errorMessage)):  ?>
            <div class="panel panel-default">
                <?php echo $this->errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="section">
                <form role="form" method="post" action="/faq/add" id="faqForm">
                  <div class="form-group">
                    <label for="userEmail">Email address</label>
                    <input type="email" class="form-control" name="userEmail" id="userEmail" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="userQuestion">Question</label>
                    <textarea class="form-control" name="userQuestion" id="userQuestion" placeholder="Type your question here..."></textarea>
                  </div>
                    <?php echo $_token('faq'); ?>
                  <button type="submit" class="btn btn-default">Submit</button>
                </form>
        </div>
    </div>
  <script>

  // When the browser is ready...
  $(function() {

    // Setup form validation on the #register-form element
    $("#faqForm").validate({
        // Specify the validation rules
        rules: {
            userEmail: {
                required: true,
                email: true
            },
            userQuestion: "required",
        },
        // Specify the validation error messages
        messages: {
            email: "Please enter a valid email address",
            userQuestion: "Please enter your question"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });

  });
  
  </script>
