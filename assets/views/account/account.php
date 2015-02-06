    <!-- Page Content -->
    <div class="container js-container account-page <?php if ($useRest): ?>js-disabled-hashchange<?php endif; ?>">
        <div class="row">
            <div class="col-lg-12" id="header_block">
                <h1 class="page-header">My Account</h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li class="active">My Account</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->
        <!-- Service Tabs -->
        <div class="row">
            <div class="col-lg-12">
                <?php if ($useRest): ?>
                    <div class="js-account" id="account_block"></div>
                <?php else: ?>
                    <?php if ($success = $this->pixie->session->flash('success')): ?>
                        <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <ul id="myTab" class="nav nav-tabs" role="tablist">
                        <li class="active"><a href="#my-orders" data-toggle="tab">My Latest Orders</a></li>
                        <li><a href="#profile" data-toggle="tab">Profile</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade in active latest-orders" id="my-orders">
                            <?php include __DIR__.'/_order_list.php'; ?>
                            <p class="text-right">
                                <a href="/account/orders" id="order_link" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">Go to my orders</span></a>
                            </p>
                        </div>
                        <div class="tab-pane fade profile-show" id="profile">
                            <?php include __DIR__ . '/_profile_info.php'; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->

<?php if ($useRest): ?>
    <script type="text/javascript">
        var orderStatusLabelMapping = <?php echo json_encode($this->getHelper()->getOrderStatusLabelMapping()); ?>;

        function order_status(status)
        {
            var canonicalStatus = $.trim(status).toLowerCase(),
                label = orderStatusLabelMapping[canonicalStatus]
                    ? orderStatusLabelMapping[canonicalStatus] : 'label-default';
            return $('<span class="label ' + label + '"></span>').text(status)[0].outerHTML;
        }
    </script>

    <script type="text/javascript" charset="utf-8" src="/js/can.custom.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/js/jquery.form.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/js/XMLWriter.js"></script>
    <script type="text/javascript" charset="utf-8" src="/js/account.js"></script>

    <script type="text/x-handlebars" charset="utf-8" id="layout_header">
        <h1 class="page-header">{{ title }}</h1>
        <ol class="breadcrumb">
            {{#each breadcrumbs }}
                <li {{#if active }}class="active"{{/if}}>
                    {{#if url}}
                        <a href="{{ url }}">{{ name }}</a>
                    {{else}}
                        {{ name }}
                    {{/if}}
                </li>
            {{/each}}
        </ol>
    </script>

    <script type="text/x-handlebars" id="layout_account">
        <ul id="myTab" class="nav nav-tabs" role="tablist">
            <li data-id="my-orders"><a href="#!">My Latest Orders</a></li>
            <li data-id="profile"><a href="#!profile">Profile</a></li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane latest-orders" id="my-orders">
                <div class="js-order-list"></div>
                <p class="text-right">
                    <a href="/account#!orders" id="order_link" class="btn btn-primary ladda-button"
                        data-style="expand-right"><span class="ladda-label">Go to my orders</span></a>
                </p>
            </div>
            <div class="tab-pane profile-show js-profile" id="profile">
                {{>tpl_user_profile}}
            </div>
        </div>
    </script>

    <script type="text/x-handlebars" charset="utf-8" id="tpl_order_list">
        <div class="row">
            <div class="col-xs-12">
                {{#if orders.length }}
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order №</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Shipping Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{#each orders}}
                        <tr>
                            <td><a href="/account#!orders/{{ increment_id }}">{{ increment_id }}</a></td>
                            <td>{{formatDate created_at }}</td>
                            <td>{{ payment_method }}</td>
                            <td>{{ shipping_method }}</td>
                            <td>{{order_status status}}</td>
                        </tr>
                        {{/each}}
                    </tbody>
                </table>
                {{/if}}
                {{#if paging }}
                    {{pager orders}}
                {{/if}}
            </div>
        </div>
    </script>

    <script type="text/x-handlebars" charset="utf-8" id="tpl_user_profile">
        <div class="row">
            <div class="col-xs-8">
                <table class="table profile-table table-striped">
                    <thead>
                    <tr>
                        <td>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Username:</td>
                        <td>{{ user.username }}</td>
                    </tr>
                    <tr>
                        <td>E-mail:</td>
                        <td>{{ user.email }}</td>
                    </tr>
                    <tr>
                        <td>First Name:</td>
                        <td>{{ user.first_name }}</td>
                    </tr>
                    <tr>
                        <td>Last Name:</td>
                        <td>{{ user.last_name }}</td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td>{{ user.user_phone }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-4">
                {{#if user.photoUrl }}
                    <img src="{{ baseImgPath }}{{ user.photoUrl}}" alt="" class="profile-picture img-responsive img-bordered img-thumbnail" />
                {{else}}
                    {{#if user.photo }}
                        <img src="{{ baseImgPath }}{{ user.photo}}" alt="" class="profile-picture img-responsive img-bordered img-thumbnail" />
                    {{/if}}
                {{/if}}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <p class="text-right buttons-row">
                    <a href="/account#!profile/edit" id="profile_link" class="btn btn-primary">Edit Profile</a>
                </p>
            </div>
        </div>
    </script>

    <script type="text/x-handlebars" charset="utf-8" id="layout_order">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Overview</h3>
                    </div>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <dt>Date</dt>
                            <dd>{{formatDate order.created_at }}</dd>

                            <dt>Status</dt>
                            <dd>{{order_status order.status }}</dd>

                            <dt>Total</dt>
                            <dd><span class="label label-danger">${{ order.total_price }}</span></dd>
                        </dl>
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th colspan="2">Items</th>
                        <th width="50">count</th>
                        <th width="70">total</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{#each order.orderItems }}
                            <tr>
                                <td class="product-image">
                                    <div class="img-thumbnail-wrapper">
                                        {{#if products.length}}
                                        <a href="/product/view?id={{ product_id }}"><img src="/products_pictures/{{product_picture this products}}" alt=""/></a>
                                        {{/if}}
                                    </div>
                                </td>
                                <td><a href="/product/view?id={{ product_id }}">{{ name }}</a></td>
                                <td align="center">{{ qty }}</td>
                                <td align="right">${{orderItemTotalPrice this}}</td>
                            </tr>
                        {{/each}}
                    <tr>
                        <th colspan="4">Services</th>
                    </tr>
                    <tr class="info">
                        <td colspan="2">Shipping:{{ order.shipping_method }}</td>
                        <td align="right" colspan="2">$0</td>
                    </tr>
                    <tr class="info">
                        <td colspan="2">Payment: {{ order.payment_method }}</td>
                        <td align="right" colspan="2">$0</td>
                    </tr>
                    <tr class="danger">
                        <td align="right" colspan="4"><strong>${{ order.total_price }}</strong></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </script>

    <script type="text/x-handlebars" charset="utf-8" id="layout_profile_edit">
        <div class="row">
            <div class="col-xs-12">
                {{#if successMessage }}
                    <div class="alert alert-success" role="alert">{{ successMessage }}</div>
                {{/if}}
                {{#if errorMessage }}
                    <div class="alert alert-danger" role="alert">{{ errorMessage }}</div>
                {{/if}}

                <!--form role="form" method="post" class="profile-edit-form" action="#" id="editProfileForm" enctype="multipart/form-data"-->
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group">
                                <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="First Name" tabindex="1" value="{{ userForm.first_name }}">
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group">
                                <input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="Last Name" tabindex="2" value="{{ userForm.last_name }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="text" name="user_phone" id="user_phone" class="form-control input-lg" placeholder="Phone" tabindex="3" value="{{ userForm.user_phone }}">
                    </div>

                    <form role="form" method="post" class="upload-profile-photo-form" action="#" id="uploadProfilePhotoForm" enctype="multipart/form-data">
                        {{#if userForm.photo }}
                        <div class="form-group">
                            {{#if userForm.photoUrl }}
                                <img src="{{ baseImgPath }}{{ userForm.photoUrl }}" alt="" class="profile-picture" /> <br>
                            {{else}}
                                <img src="{{ baseImgPath }}{{ userForm.photo }}" alt="" class="profile-picture" /> <br>
                            {{/if}}
                            <label><input type="checkbox" name="remove_photo" /> Remove photo</label>
                        </div>
                        {{/if}}

                        <div class="form-group">
                            <input type="file" name="photo" id="photo" class="file-input btn btn-default btn-primary btn-lg"
                                   title="Select avatar image" tabindex="4" value="">
                        </div>
                    </form>

                    <hr class="colorgraph">
                    <div class="row">
                        <div class="col-xs-6 col-md-6">
                            <input type="submit" name="_submit" value="Save" class="btn btn-block btn-lg js-save-button" tabindex="7"
                                {{^userForm.id}}disabled{{/userForm.id}}>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <input type="submit" name="_submit" value="Save and Exit" class="btn btn-primary btn-block btn-lg js-save-and-exit-button"
                                {{^userForm.id}}disabled{{/userForm.id}} tabindex="8">
                        </div>
                    </div>
                <!--/form-->
            </div>
        </div>
    </script>

<?php else: ?>
    <script>
        $(function() {
            Ladda.bind( '#order_link' );

            $('#order_link').on('click', function(e) {
                var l = Ladda.create(document.querySelector( '#order_link' ));
                l.start();
                window.location.href = "/account/orders";
                return false; // Will stop the submission of the form
            });
        });
    </script>
<?php endif; ?>