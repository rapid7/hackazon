<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">REST API Test Client</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li class="active">REST API Test Client</li>
            </ol>
        </div>

        <div>
            <div class="col-lg-6">
                <form action="/restTest/request" role="form" class="js-rest-form rest-form">

                    <div class="form-group">
                        <input type="text" class="form-control js-url-field" placeholder="Resource URL" name="url" required>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control js-name-field" placeholder="Username" name="username" required>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control js-password-field" placeholder="Password" name="password" required>
                    </div>

                    <div class="form-group">
                        <select name="method" id="requestMethod" class="form-control js-method-field">
                            <?php foreach (\App\Rest\Controller::allowedMethods() as $method): ?>
                                <option value="<?php echo $method; ?>"><?php echo $method; ?></option>
                            <?php endforeach; ?>
                            <option value="DISALLOWED">DISALLOWED</option>
                        </select>
                    </div>

                </form>
            </div>

            <div class="col-lg-6">
                <div class="js-output output"></div>
            </div>
        </div>
    </div>
</div>