/**
 * Single page application for Account section
 */

function js2xml2(obj) {
    var xw = new XMLWriter('UTF-8');
    xw.formatting = 'indented';//add indentation and newlines
    xw.indentChar = ' ';//indent with spaces
    xw.indentation = 4;//add 2 spaces per level

    xw.writeStartDocument();
    js2xmlWalker(xw, obj);
    xw.writeEndDocument();
    return xw.flush();
}

function js2xmlWalker(xw, obj, name) {
    name = name || 'root';

    if (obj instanceof Object && obj !== null) {
        xw.writeStartElement(name);

        if ($.isArray(obj)) {
            $.each(obj, function (key, value) {
                js2xmlWalker(xw, value, 'item' + key)
            });

        } else {
            $.each(obj, function (key, value) {
                js2xmlWalker(xw, value === null || typeof value == 'undefined' ? '' : value, key);
            });
        }

        xw.writeEndElement();

    } else {
        xw.writeElementString(name, obj);
    }
}



/**
 * Changes XML to JSON
 * fixed some bugs from http://davidwalsh.name/convert-xml-json
 * October 9, 2012
 * Brian Hurlow
 * @param xml
 * @return Object
 */
function xmlToJson(xml) {
    // Create the return object
    var obj = {};

    if (xml.nodeType == 1) { // element
        // do attributes
        if (xml.attributes.length > 0) {
            obj["@attributes"] = {};
            for (var j = 0; j < xml.attributes.length; j++) {
                var attribute = xml.attributes.item(j);
                obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
            }
        }

    } else if (xml.nodeType == 4) { // cdata section
        obj = xml.nodeValue;
    }

    // do children
    if (xml.hasChildNodes()) {
        for(var i = 0; i < xml.childNodes.length; i++) {
            var item = xml.childNodes.item(i);
            var nodeName = item.nodeName;
            if (item.nodeType == 3) {
                obj = item.textContent;
                break;
            } else if (typeof(obj[nodeName]) == "undefined") {
                obj[nodeName] = xmlToJson(item);
            } else {
                if (typeof(obj[nodeName].length) == "undefined") {
                    var old = obj[nodeName];
                    obj[nodeName] = [];
                    obj[nodeName].push(old);
                }
                if (typeof(obj[nodeName]) === 'object') {
                    obj[nodeName].push(xmlToJson(item));
                }
            }
        }
    } else {
        obj = null;
    }
    return obj;
}

function prepareResourceAccessor(dataType, type, path) {
    if (dataType == 'xml') {
        var requester = function (params) {
            var prepPath = path;
            $.each(params, function (key, value) {
                prepPath = prepPath.replace(new RegExp('{' + key + '}'), value);
            });

            var def = $.Deferred();
            var asPayload = type == 'POST' || type == 'PUT';
            delete params['id'];
            var encodedParams = $.param(params);
            encodedParams = encodedParams ? '?' + encodedParams : '';

            $.ajax(prepPath + (asPayload ? '' : encodedParams), {
                data: asPayload ? js2xml2(params) : '',
                dataType: dataType,
                contentType: 'application/xml',
                type: type,
                timeout: 15000
            }).success(function (res) {
                var data = xmlToJson(res),
                    keys = Object.getOwnPropertyNames(data);

                // Exclude root element from result
                if (keys.length) {
                    data = data[keys[0]];
                }
                def.resolve(data);
            }).fail(function () {
                def.reject();
            });
            return def;
        };

        if (type == 'PUT' || type == 'DELETE') {
            return function (id, params) {
                $.extend(params || {}, {id: id});
                return requester.call(null, params)
            };
        } else {
            return requester;
        }

    } else {
        return type + ' ' + path;
    }
}

var RestModel = can.Model.extend({
}, {});

var resourceDataType = App && App.config && App.config.dataType;
resourceDataType = resourceDataType || 'xml';

var typedAcc = function () {
    var args = Array.prototype.slice.call(arguments, 0);
    args.unshift(resourceDataType);
    return prepareResourceAccessor.apply(null, args);
};

var User = RestModel({
    findAll: typedAcc('GET', '/api/user'),
    findOne: typedAcc('GET', '/api/user/{id}'),
    create:  typedAcc('POST', '/api/user'),
    update:  typedAcc('PUT', '/api/user/{id}'),
    destroy: typedAcc('DELETE', '/api/user/{id}')
}, {});

var Order = RestModel({
    findAll: typedAcc('GET', '/api/order'),
    findOne: typedAcc('GET', '/api/order/{id}'),
    create:  typedAcc('POST', '/api/order'),
    update:  typedAcc('PUT', '/api/order/{id}'),
    destroy: typedAcc('DELETE', '/api/order/{id}')
}, {
});

var Product = RestModel({
    findAll: typedAcc('GET', '/api/product'),
    findOne: typedAcc('GET', '/api/product/{id}'),
    create:  typedAcc('POST', '/api/product'),
    update:  typedAcc('PUT', '/api/product/{id}'),
    destroy: typedAcc('DELETE', '/api/product/{id}')
}, {

});

Product.List = Product.List.extend({
    getById: function (id) {
        for (var i = 0; i < this.length; i++) {
            if (this[i].productID == id) {
                return this[i];
            }
        }
        return null;
    }
});

/**
 * Controls Account page with tabs
 */
var Account = can.Control('Account', {
}, {
    orders: null,
    profile: null,

    init: function () {
        this.showAccountLayout();
        this.showTab('my-orders');
        this.element.on('select_tab', this.proxy(this.select_tab));
    },

    'select_tab': function (ev, el, tab) {
        this.showTab(tab);
        var tabEl = this.element.find('#'+tab),
            template,
            control = this;

        if (tab == 'my-orders' && !this.orders) {
            Order.findAll({customer_id: App.liveConfig.user.attr('id'), order: 'desc', order_by: 'created_at'}, function (orders) {
                template = can.view("#tpl_order_list", {orders: orders, paging: false});
                tabEl.find('.js-order-list').html('').append(template);
                control.orders = orders;
            });
        }
    },

    showAccountLayout: function () {
        this.showLayout('layout_account', {
            user: App.liveConfig.attr('user'),
            baseImgPath: App.config.baseImgPath
        });
    },

    showLayout: function (template, data) {
        if (this.element.data('template') != template) {
            data = data || {};
            this.element.html(can.view(template, data));
            this.element.data('template', template);
        }
    },
    showTab: function (id) {
        var selected = this.element.find('.tab-pane').filter('[id="' + id + '"]');
        selected.show();
        selected.siblings().hide();
        var li = this.element.find('.nav-tabs > li').filter('[data-id="' + id + '"]');
        li.addClass('active');
        li.siblings().removeClass('active');
    }
});

/**
 * Controls list of orders
 */
var OrdersController = can.Control('OrdersController', {
    data: new can.Map({
        orders: null,
        paging: true,
        perPage: 10
    }),

    init: function () {
        this.loadPage(can.route.attr('page') || 1);
    },

    "{can.route} change": function (data, ev, attr, how, newVal/*, oldVal*/) {
        if (data.route == 'orders' && attr == 'page') {
            this.loadPage(newVal);
        }
    },

    loadPage: function (page) {
        Order.findAll({customer_id: App.config.user.id, per_page: this.data.attr('perPage'), page: page}, this.proxy(function (orders) {
            this.data.attr('orders', orders, true);
            var template = can.view("#tpl_order_list", this.data);
            this.element.html('').append(template);
        }));
    }
});

var OrderController = can.Control('OrderController', {
    data: new can.Map({
        order: null,
        items: null,
        products: []
    }),

    init: function () {
        var control = this;
        window.items = control.items;
        Order.findOne({id: this.options.id}, function (order) {
            control.data.attr('order', order);
            var template = can.view("#layout_order", control.data);
            control.element.html('').append(template);

            var productIds = $.map(order.orderItems, function (item) { return item.product_id; }).join(',');

            Product.findAll({productID: productIds}, function (products) {
                control.data.attr('products', products);
            });
        });
    }
});


ProfileEditController = can.Control('ProfileEditController', {}, {
    data: new can.Map({
        user: null,
        userForm: null,
        baseImgPath: App.config.baseImgPath
    }),

    init: function () {
        var template = can.view("#layout_profile_edit", this.data);
        this.element.html('').append(template);
        this.element.find('input[type="file"]').bootstrapFileInput();
        this.buttons = this.element.find('input[type="submit"]');
        this.data.attr('successMessage', '');

        var control = this;
        User.findOne({id: App.config.user.id}, function (user) {
            control.data.attr('user', user);
            window.userForm = new can.Map(user.attr());
            control.data.attr('userForm', userForm);
            window.user = user;
        });
    },

    '#photo change': function (/*el, ev*/) {
        var form = $('#uploadProfilePhotoForm');
        var control = this;
        form.ajaxSubmit({
            url: '/account/add_photo',
            success: function (res) {
                if (res.photo) {
                    control.data.attr('userForm').attr('photo', res.photo);
                    control.data.attr('userForm').attr('photoUrl', res.photoUrl || res.photo);
                    control.element.find('[name="remove_photo"]').removeAttr('checked');

                } else if (res.errors) {
                    bsModalWindow(res.errors, 'Error');
                }
            }
        });
    },

    '.js-save-button click': function () {
        var control = this;
        this.save(function () {
            control.data.attr('successMessage', 'You have successfully updated your profile.');
        });
    },

    '.js-save-and-exit-button click': function () {
        this.save(function () {
            location.hash = '#!profile';
        });
    },

    save: function (callback) {
        if (!this.data.user.attr().id) {
            return;
        }

        this.data.user.attr('first_name', this.element.find('input[name="first_name"]').val());
        this.data.user.attr('last_name', this.element.find('input[name="last_name"]').val());
        this.data.user.attr('user_phone', this.element.find('input[name="user_phone"]').val());

        if (this.element.find('[name="remove_photo"]').is(':checked')) {
            this.data.userForm.attr('photo', '');
            this.data.user.attr('photo', '');
        } else if (this.data.userForm.attr('photo')) {
            this.data.user.attr('photo', this.data.userForm.attr('photo'));
        }


        this.data.user.save(function (user) {
            App.liveConfig.attr('user', user);
            callback && callback();
        });
    }
});

/**
 * Main routing controller
 */
var Routing = can.Control({}, {
    controller: null,

    init: function () {
        $('#header_block').html('').append(can.view('layout_header', this.options.header));
        this.container = this.element.closest('.js-container');
    },

    'route': function() {
        if (!this.controller || this.controller.constructor.fullName != 'Account') {
            this.setControl(Account);
        }
        if (this.controller) {
            this.controller.element.trigger('select_tab', [this.controller.element, 'my-orders']);
        }
        this.options.header.attr({
            title: 'My Account',
            breadcrumbs: [
                {name: 'Home', url: '/'},
                {name: 'My Account', active: true}
            ]
        }, true);
        this.container.attr('class', 'container js-container account-page');
    },

    'profile route': function () {
        if (!this.controller || this.controller.constructor.fullName != 'Account') {
            this.setControl(Account);
        }
        if (this.controller) {
            this.controller.element.trigger('select_tab', [this.controller.element, 'profile']);
        }
        this.options.header.attr({
            title: 'My Profile',
            breadcrumbs: [
                {name: 'Home', url: '/'},
                {name: 'My Profile', active: true}
            ]
        });
        this.container.attr('class', 'container js-container account-page');
    },

    'profile/edit route': function () {
        this.setControl(ProfileEditController);

        this.options.header.attr({
            title: 'Edit Profile',
            breadcrumbs: [
                {name: 'Home', url: '/'},
                {name: 'Profile', url: '#!profile'},
                {name: 'Edit', active: true}
            ]
        });

        this.container.attr('class', 'container js-container profile-edit');
    },

    'orders route': function() { //data) {
        // Matches routes like #!todos/5,
        // and will get passed {id: 5} as data.

        if (!this.controller || this.controller.constructor.fullName != 'OrdersController') {
            this.setControl(OrdersController);
        }

        this.options.header.attr({
            title: 'My Orders',
            breadcrumbs: [
                {name: 'Home', url: '/'},
                {name: 'My Account', url: '#!'},
                {name: 'My Orders', active: true}
            ]
        }, true);
        this.container.attr('class', 'container js-container account-page');
    },

    'orders/:id route': function(data) {
        // Matches routes like #!recipes/5,
        // and will get passed {id: 5, type: 'recipes'} as data.

        this.setControl(OrderController, {id: data.id});

        this.options.header.attr({
            title: 'Order №' + data.id,
            breadcrumbs: [
                {name: 'Home', url: '/'},
                {name: 'My Account', url: '#!'},
                {name: 'Orders', url: '#!orders'},
                {name: 'Order №' + data.id, active: true}
            ]
        });

        this.container.attr('class', 'container js-container order-page');
    },

    setControl: function (Control, options) {
        this.controller = null;
        this.element.html('');
        this.element.append($('<div>'));
        var el = this.element.find('div');
        this.controller = new Control(el, options);
    }
});

/**
 * Useful helpers
 */
can.mustache.registerHelper('order_status', function(status){
    return function (el) {
        return $(el).html(order_status(status()));
    }
});

can.mustache.registerHelper('orderItemTotalPrice', function(orderItem){
    return function (el) {
        return $(el).html(orderItem.attr('qty') * orderItem.attr('price'));
    }
});

can.mustache.registerHelper('formatDate', function(date){
    return function (el) {
        return $(el).html(moment(date()).format('MMMM Do, YYYY'));
    }
});

can.mustache.registerHelper('product_picture', function(item, products) {
    if (!products().getById) {
        return;
    }
    var prod = products().getById(item.product_id);
    return prod ? prod.attr('picture') : '';
});

/**
 * Pagination helper
 */
can.mustache.registerHelper('pager', function(list){
    return function (el) {
        var result = [];
        var url = function (p) {
            return can.route.url({page: p}, 'orders');
        };
        if (list() && list().attr('pages') > 1) {
            var page = parseInt(list().page, 10),
                pages = parseInt(list().pages, 10);
            result.push('<ul class="pagination pull-right clearfix">');
            result.push('<li class="previous ' + (page == 1 ? 'disabled' : '') + '">');
            result.push('<a href="' + url(page > 1 ? page - 1 : 1) + '">&laquo;</a></li>');
            for (var p = 1; p <= list().attr('pages'); p++) {
                result.push('<li ' + (p == page ? 'class="active"' : '') + '><a href="' + url(p) + '">' + p + '</a></li>');
            }
            result.push('<li class="next ' + (page == pages ? 'disabled' : '') + '">');
            result.push('<a href="' + url(page < pages ? page + 1 : pages) + '">&raquo;</a></li>');
            result.push('</ul>');
        }

        $(el).html('').append(result.join(''));
    }
});

/**
 * Entry point
 */
jQuery(function () {
    var App = window.App;

    App.liveConfig = new can.Map(App.config);

    window.header = new can.Map({
        title: 'My Account',
        breadcrumbs: [
            {name: 'Home', url: '/'},
            {name: 'My Account', active: true}
        ]
    });

    App.controller = new Routing($('#account_block'), {
        header: header
    });
    can.route.ready();
});
