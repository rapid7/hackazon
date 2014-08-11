<script>
    $(function () {
        $("#wishlist_search_form").submit(function(){
            $.ajax({
                url:'/wishlist/search',
                type:"POST",
                data: $("#wishlist_search_form").serialize(),
                dataType:"json",
                success: function(data){
                    var output = '';
                    if (data.length == 0) {
                        output = '<h2>No results</h2>'
                    } else {
                        for (i in data) {
                            var output = output + '<div class="blockShadow">user: <strong>' + data[i].username + '</strong><br />Lists:<br/ >';
                            output = output + '<ul style="list-style:none">';
                            for (j in data[i].wishLists) {
                                output = output + '<li><a href="/wishlist/view/' + data[i].wishLists[j].id + '">' + data[i].wishLists[j].name + '</a></li>';
                            }
                            output = output + '</ul></div>';

                        }
                    }
                    $('.products').empty().append(output);
                },
                fail: function() {
                    alert( "error" );
                }
            });
            return false;
        })
    });
</script>

    <form id="wishlist_search_form" role="search" class="form-inline navbar-form navbar-left search-form">
        <div class="form-group search-field-box">
            <input name="search" type="text" class="form-control search-field"
                   placeholder="Type a person's name or email address"/>
        </div>
        <div class="form-group">
            <button class="btn btn-default" type="submit">Search</button>
        </div>
    </form>
