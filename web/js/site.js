function bsModalWindow(content, title, footer) {
    title = title || 'Alert!';

    var modal = $('<div class="modal fade bs-example-modal-sm bs-alert-popup" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">\
        <div class="modal-dialog modal-sm">\
            <div class="modal-content">\
                <div class="modal-header">\
                    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>\
                    <h4 id="mySmallModalLabel" class="modal-title">' + title + '</h4>\
                </div>\
                <div class="modal-body">\
                    ' + content + '\
                </div>\
                ' + (footer ? '<div class="modal-footer">' + footer + '</div>' : '') + '\
            </div>\
        </div> \
    </div>');

    modal.appendTo(document.body);
    modal.modal({});

    return modal;
}

function bsAlert(title) {
    var modal = bsModalWindow($('#tplAlertContent').html(), title || 'Alert!'),
        deferred = $.Deferred();

    modal.on('hidden.bs.modal', function () {
        modal.remove();
        deferred.reject();
    });

    modal.on('click', '.js-yes, .js-no', function (ev) {
        ev.preventDefault();
        modal.modal('hide');
        if ($(ev.target).is('.js-yes')) {
            deferred.resolve();
        } else {
            deferred.reject();
        }
    });

    return deferred;
}

function bsEditWishList(wishList) {

    wishList = wishList || {
        name: 'New Wish List',
        type: 'private'
    };


    var isEdit = !!wishList.id;
    var title = isEdit ? 'Edit Wish List' : 'Add Wish List';
    var buttonTitle = isEdit ? 'Save' : 'Add';

    var modal = bsModalWindow($('#tplEditWishListForm').html(), title,
                '<a href="#" class="btn btn-primary js-submit">' + buttonTitle + '</a>'),
        deferred = $.Deferred();



    modal.find('input[name="name"]').val(wishList.name);
    modal.find('select[name="type"]').val(wishList.type);
    modal.find('input[name="id"]').val(wishList.id);

    var form = modal.find('form');
    form.bootstrapValidator({
        exclude: ['_csrf_wishlist_add'],
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        container: 'tooltip',
        fields: {
        }
    });

    modal.on('hidden.bs.modal', function () {
        modal.remove();
        deferred.reject();
    });

    modal.on('click', '.js-submit', function (ev) {
        ev.preventDefault();

        if (!form.bootstrapValidator('isValid')) {
            return;
        }
        var result = {
            id: modal.find('input[name="id"]').val(),
            name: modal.find('input[name="name"]').val(),
            type: modal.find('select[name="type"]').val(),
            _csrf_wishlist_add: modal.find('input[name="_csrf_wishlist_add"]').val()
        };

        if (!result.name || !result.type) {
            return;
        }

        modal.modal('hide');
        deferred.resolve(result);
    });

    return deferred;
}

function getFlashMovie(movieName){
    var isIE = navigator.appName.indexOf("Microsoft") != -1;
    return (isIE) ? window[movieName] : document[movieName];
}

$(document).ready(function () {

    $('a.login-window').click(function () {

        //Getting the variable's value from a link 
        var loginBox = $(this).attr('href');

        //Fade in the Popup
        $(loginBox).fadeIn(300);

        //Set the center alignment padding + border see css style
        var popMargTop = ($(loginBox).height() + 24) / 2;
        var popMargLeft = ($(loginBox).width() + 24) / 2;

        $(loginBox).css({
            'margin-top': -popMargTop,
            'margin-left': -popMargLeft
        });

        // Add the mask to body
        $('body').append('<div id="mask"></div>');
        $('#mask').fadeIn(300);

        return false;
    });

// When clicking on the button close or the mask layer the popup closed
    $('a.close, #mask').on('click', function () {
        $('#mask , .login-popup').fadeOut(300, function () {
            $('#mask').remove();
        });
        return false;
    });

    /*
     var $dropdowns = $('.dropdown-submenu');

     $dropdowns.click(function() {
     alert('rre');
     $('.dropdown-menu').css('display','block');
     /*
     if ( $(this).hasClass('active') ){
     alert('yes');
     $(this).toggleClass('active');
     } else {
     alert('no');
     $dropdowns.removeClass('active');
     $(this).toggleClass('active');
     }
     */

    /* Popup Image */
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    // Run the slideshows on the page
    $('.nivoslider').nivoSlider();

    // Controls product detail widget
    $('.product-detail').each(function () {
        var $productBlock = $(this);

        $productBlock.on('click', '.js-add-to-wish-list', function (ev) {
            var title, $link, $dropdown, $button;
            $link = $(ev.target);
            $dropdown = $link.closest('.dropdown');
            $button = $dropdown.find('.dropdown-toggle'); // Button in dropdown
            var $textLink = $button.length ? $button : $link;
            title = $textLink.text(); //

            ev.preventDefault();
            var id = $link.data('id');
            var wishListId = $link.data('wishlist-id');

            if (!id) {
                return;
            }

            $.ajax('/wishlist/add-product/' + id, {
                data: {
                    wishlist_id: wishListId
                },
                dataType: 'json',
                timeout: 10000,
                type: 'POST',
                beforeSend: function () {
                    $textLink.attr('disabled', 'disabled');
                    $textLink.text('Adding to Wish List...')
                },
                complete: function () {
                    $textLink.removeAttr('disabled');
                    $textLink.text(title);
                }

            }).success(function () {
                var result = $('<div class="alert alert-success" role="alert">Successfully added to your wishlist.</div>');
                if ($dropdown.length) {
                    $dropdown.replaceWith(result);
                } else {
                    $link.replaceWith(result);
                }

            }).error(function () {
                $link.replaceWith($('<div class="alert alert-danger" role="alert">Error while adding to wishlist.</div>'));
            });
        });

        $productBlock.on('click', '.js-remove-from-wish-list', function (ev) {
            var $link, id, wishListItemId, title;

            ev.preventDefault();
            $link = $(ev.target);
            title = $link.text();

            id = $link.data('id');
            wishListItemId = $link.data('wish-list-item-id');

            $.ajax('/wishlist/remove-product/' + id, {
                data: {
                    wish_list_item_id: wishListItemId
                },
                dataType: 'json',
                timeout: 10000,
                type: 'POST',
                beforeSend: function () {
                    $link.attr('disabled', 'disabled');
                    $link.text('Removing from your Wish List...')
                },
                complete: function () {
                    $link.removeAttr('disabled');
                    $link.text(title);
                }

            }).success(function () {
                var result = $('<div class="alert alert-success" role="alert">Successfully removed from your wishlist.</div>');
                $link.replaceWith(result);

            }).error(function () {
                $link.replaceWith($('<div class="alert alert-danger" role="alert">Error while removing product from wishlist.</div>'));
            });
        });

    });

    $('.wishlist').each(function () {
        var $wishlist = $(this),
            buttonTemplate = $(
                '<div class="item-actions">' +
                '<a class="item-action-icon remove-from-list js-remove-from-list" title="Remove from Wish List"><span class="glyphicon glyphicon-remove-circle"></span></a>' +
                '</div>'
            );


        if ($wishlist.data('access') == 'owner') {
            $wishlist.find('.product-item').each(function () {
                var $product = $(this);
                $product.append(buttonTemplate.clone());
            });
        }

        $wishlist.on('click', '.js-remove-from-list', function (ev) {
            var $link, id;

            ev.preventDefault();
            $link = $(ev.target);

            id = $link.closest('.product-item').data('id');

            $.ajax('/wishlist/remove-product/' + id, {
                dataType: 'json',
                timeout: 10000,
                type: 'POST',
                beforeSend: function () {
                    $link.remove();
                },
                complete: function () {
                }

            }).success(function (response) {
                if (response.success) {
                    location.reload();
                }

            }).error(function () {
            });
        });

        $wishlist.on('click', '.js-add-wish-list', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target);

            bsEditWishList()
            .then(function (result) {
                $.ajax('/wishlist/new', {
                    data: result,
                    dataType: 'json',
                    timeout: 10000,
                    type: 'POST',
                    beforeSend: function () {
                        $link.attr('disabled', 'disabled');
                    },
                    complete: function () {
                        $link.removeAttr('disbled');
                    }

                }).success(function (response) {
                    if (response.success) {
                        location.pathname = '/wishlist/view/' + response.id;
                    }
                });
            });
        });

        $wishlist.on('click', '.js-delete-wish-list', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target);

            bsAlert("Are you sure?")
            .then(function () {
                var id = $link.closest('.wishlist').data('id');
                if (!id) {
                    return;
                }

                $.ajax('/wishlist/delete/' + id, {
                    data: {
                        token: $wishlist.data('token')
                    },
                    dataType: 'json',
                    timeout: 10000,
                    type: 'POST',
                    beforeSend: function () {
                        $link.attr('disabled', 'disabled');
                    },
                    complete: function () {
                        $link.removeAttr('disbled');
                    }

                }).success(function (response) {
                    if (response.success) {
                        location.pathname = '/wishlist';
                    }
                });
            });
        });

        $wishlist.on('click', '.js-edit-wish-list', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                id = $wishlist.data('id');

            bsEditWishList({
                id: id,
                name: $wishlist.data('name'),
                type: $wishlist.data('type')
            })
                .then(function (result) {
                    $.ajax('/wishlist/edit/' + id, {
                        data: result,
                        dataType: 'json',
                        timeout: 10000,
                        type: 'POST',
                        beforeSend: function () {
                            $link.attr('disabled', 'disabled');
                        },
                        complete: function () {
                            $link.removeAttr('disbled');
                        }

                    }).success(function (response) {
                        if (response.success) {
                            location.pathname = '/wishlist/view/' + response.id;
                        }
                    });
                });
        });


    });

    /* Search Input */
    $('#searchForm').on('click', '#searchValue a', function(e){
       e.preventDefault();
       $('#searchLabel').text($(this).text());
       $('#searchForm input[type="hidden"][name="id"]').val($(this).data('item-id'));
    });

    if ($('.js-disabled-hashchange').length == 0) {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var href = $(e.target).attr('href');
            if (href.substr(0, 1) == '#') {
                location.hash = href;
            }
        });

        // Check hash for tabs
        (function () {
            var hash = location.hash;
            $('.nav').each(function () {
                var nav = $(this);
                nav.find('a[data-toggle="tab"]').filter('[href="' + hash + '"]').tab('show');
            });

            $(window).on('hashchange', function () {
                $('.nav').find('a[data-toggle="tab"]').filter('[href="' + location.hash + '"]').tab('show');
            });
        })();
    }

    (function () {
        var counter = 0;

        window.addTopCartItem = function (cartItem) {
            var list = $('.js-cart-top-list');
            list.find('li[data-id="' + cartItem.productId + '"]').remove();

            var itemListId = 'cartTopListItem' + counter++;
            var html = '<li id="' + itemListId + '" data-id="' + cartItem.productId + '">'
                + '<a href="/product/view?id=' + cartItem.productId + '"><span class="pull-left product-name"><small>'
                + cartItem.item.qty + 'x</small> ' + cartItem.product.name + '</span> &nbsp; <small class="pull-right label label-info">$'
                + cartItem.product.Price + '</small></a></li>';

            list.prepend(html);
            var listItem = list.find('#' + itemListId);
            if (list.is(':hidden')) {
                $('.js-cart-top-icon').dropdown('toggle');
            }
            listItem.modernBlink({
                iterationCount: 3
            });
        };

        $(document).on('click', '.js-add-to-cart-shortcut', function (ev) {
            ev.preventDefault();

            var link = $(this),
                l = link.ladda();

            link.blur();

            $.ajax('/cart/add/', {
                data: {
                    product_id: link.data('product-id'),
                    shortcut: true
                },
                dataType: 'json',
                timeout: 15000,
                type: 'POST',
                beforeSend: function () {
                    link.attr('disabled', 'disabled');
                    l.ladda('start');
                },
                complete: function () {
                    link.removeAttr('disbled');
                    l.ladda('stop');
                }

            }).success(function (res) {
                link.removeClass('js-add-to-cart-shortcut btn-primary');
                link.addClass('added-to-cart btn-success');
                link.html('Added to Cart');
                link.attr('title', 'Go to Cart');
                link.blur();

                if (!(res && res.newProduct && res.product)) {
                    return;
                }
                addTopCartItem(res);
            });
        });
    })();

    $('.file-input').bootstrapFileInput();

    // Coupon processing
    var slider = $('#slider2'),
        sliderControl = slider.next('.nivo-controlNav');
    slider.on('click', 'img', function (ev) {
        if (!amfphp) {
            return;
        }
        var activeImageIndex = sliderControl.find('.active').attr('rel'),
            image = slider.children('img').get(activeImageIndex),
            dayOfWeek = $(image).data('day-of-week');

//        amfphp.services.CouponService.registerVoucher(function (res) {
//            alert('The Day of the week is ' + res.dayOfWeek + '\nYour discount is: ' + res.discount);
//        }, function () {
//
//        }, new Date(), dayOfWeek);
    });
});


