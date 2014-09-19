<script>
    $(function () {
        $(".remove_follower").click(function(){
            var block = $(this).parents('.collapsible-block');
            var follower_id = block.attr('data-id');
            $.ajax({
                url:'/wishlist/remove_follower',
                type:"POST",
                data: {follower_id : follower_id},
                dataType:"json",
                success: function(data){
                    if (data.success) {
                        block.remove();
                    }
                }
            });
        });
        $(".toggle").click(function(){
            $(this).parents('.collapsible-block').children(".block-content").slideToggle();
        });
        $("#wishlist_search_form").submit(function(){
            $.ajax({
                url:'/wishlist/search',
                type:"POST",
                data: $("#wishlist_search_form").serialize(),
                dataType:"json",
                success: function(data){
                    var output = '';
                    if (data.length == 0) {
                        output = '<div class="alert alert-danger text-center" style="margin-top: 10px;" role="alert"><h2>No results</h2></div>'
                    } else {
                        var img = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAIAAAHDVQljAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA7NJREFUeNpiXHHi/o/ffxnAgAnI+vvvv5mSMJDBBBT4/fcfRIZFgItNTYIXyLJTFwMIIMYFh+8wwABID1CDGB8HVA8Q3H/9BUgyXn/2/v9/EP/7778AAQTSAzHdUVMcKHTu4bsvP/6A9EO0cLExQxi6MgJQgyHU119Qx1189B7CYLz5/APQHAYMABBAKO6CAxaI95w0xf/8+8/CxLjv+kuE6X/BDvwDM40JTS/E+VDRe6++AklmJkZGRpgoUF5WmBPIABoAMQSL44B2MmG6F6gcIICgYQIXAioCBqOsMNev3/8uP/kAtAnFGGQOGwuTpYoIxHkMHAzAMH3x8cetF58Q0YJi19//UEfjACiqf//79/vPP0SoMjK8+vQDp2pmRsYjt17D3fr47bf3336huxvoM252Fn1ZQS52ZiAbHhoyQlxA7wKNuPv6y91XX0Bhe+v5hz/YIhcrAAgg7DGOFXCwMrMgBzbYZ4wqYjy8nKwP33wFehFfeIvwsuvICPwDO0xbmh+YSPddf8GEFKZMyGlGXYLvH5IfgOb+/487vP8zoHv3P87YYWT4hRQ1EKWMuFT///+fnZUZWQ7oKGDKwa4a6JvbLz4jJxNg7v+NahuK1mcfvrMgBdm1px+ZUEMQPZ28/vITymZifPf1J840+AeUWpgFudjg2cJGTQwoiByILP/BEspiPMqiPMBcjCLHxOiqLfHt598Lj99/+wkqZBjP3n/Dy8GCP2EBvQ4Mg+vPPrFwsjETTINAC4HWaknxMTEQDYBGAgRotcpyGoaBaLxlaVrSQgCpSCyH4Lochj8ugJBAiB9AVC2kVbpkdZ8TBFVSW61UK/KP7fF43jIhd/fPDQjbm0AeqSqmcqqJ1OBmewBWTvUaR6mGA+869Ds2J9Udsqo4ZhjHZ7T8nqe6KxCWG5K9vTkJOmJT1Ypo1Xzac4Z992WktM014beXDwXwBINblFIaEOCU7upqm89BP1qkBbFMNV2k+d6hkaxv867DpT7rQlpXIcyQ6rbos05z1WL1TABJkqyAanU8oAYYYeJv4zlnW44Cusf36OF1YiCYCQfGyCROslyS1puiZTaaJYLtD+NvxUsZeAJZyxYSfU9cDLy8MGmNN84of2Qk7DrngXfUEYLSrT8auTKe3mXozxbZ13Q5jpO0kLSyj//QNXOPffsscMOuC92VpfVHZwOvsQSzHPg2PkQExdNcjuPVaLqCSrFKnj5+0LuKndvkLgPkAWvpwePW5oOwnCmxltahB8KuAf+pyQJVxN7hAAAAAElFTkSuQmCC">';
                        for (i in data) {
                            var output = output + '<div class="panel panel-primary js-people-box" data-id="'+data[i].id+'"' + img + '<div class="panel-heading">' + data[i].username + '</div>';
                            output = output + '<div class="panel-body"><ul style="list-style:none">';
                            var cntList = data[i].wishLists.length;
                            var c = 0;
                            for (j in data[i].wishLists) {
                                if (c < 3) {
                                    output = output + '<li><a href="/wishlist/view/' + data[i].wishLists[j].id + '">' + data[i].wishLists[j].name + '</a></li>';
                                }
                                c = c + 1;
                            }
                            if (cntList > 3) {
                                output = output + '<li>Total: ' + cntList + ' lists</li>';
                            }
                            if (data[i].remembered) {
                                var remember = '<div class="remembered">Remembered</div>';
                            } else {
                                var remember = '<button class="btn btn-primary remember" onclick="remember(this)">Remember</button>';
                            }
                            output = output + '</ul><div class="remember">'+remember+'</div></div></div>';

                        }
                    }
                    $('.product-list').empty().append(output);
                },
                fail: function() {
                    alert( "error" );
                }
            });
            return false;
        });
    });
    function remember(el) {
        <?php if (is_null($this->pixie->auth->user())): ?>
            window.location.href = "<?php echo '/user/login?return_url=' . rawurlencode('/wishlist/');?>"
        return false;
        <?php endif?>
        var userId = $(el).parents('.js-people-box').attr('data-id');
        $.ajax({
            url:'/wishlist/remember',
            type:"POST",
            data: {user_id: userId},
            dataType:"json",
            success: function(data){
                if (data.success) {
                    $(el).parent('.remember').empty().append('<div class="remembered">Remembered!</div>');
                }
            }
        });
    }
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
