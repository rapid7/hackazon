<div class="container">

    <div class="row">
        <div class="col-md-9 col-sm-8">
            <h1 class="page-header">Sidebar Page
                <small>For Deeper Customization</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.html">Home</a>
                </li>
                <li class="active">Full Width Page</li>
            </ol>
            <p><?= $this->pixie->session->flash('added_product_name')?></p>
        </div>

        <div class="col-md-3 col-sm-4 sidebar">
            <ul class="nav nav-stacked nav-pills">
                <li><a href="index.html">Home</a>
                </li>
            </ul>
        </div>

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->
