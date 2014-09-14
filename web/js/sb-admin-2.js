$(function() {
    $('#side-menu').metisMenu();
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    $('.file-input').bootstrapFileInput();

    $(document).on('click', '.js-delete-item', function (ev) {
        ev.preventDefault();
        var $link = $(ev.target);
        if (confirm("Are you sure you want to delete this itrm?")) {
            $.ajax({
                url: $link.attr('href'),
                type: 'POST',
                dataType: 'json'
            }).success(function (res) {
                if (res.location) {
                    location.href = res.location;
                }
            }).error(function () {
                alert('Error while deleting the item.')
            });
        }
    });
});
