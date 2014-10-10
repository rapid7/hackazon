<div style="position:absolute; width: 0;height:0;">
    <div id="couponWidget" >
        <script type="text/javascript">
            var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://");
            document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='"
                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" );
        </script>
    </div>
    <script type="text/javascript">
        var flashvars = {
            host: "<?php echo $_SERVER['HTTP_HOST'] ? 'http://'.$_SERVER['HTTP_HOST'] : $this->pixie->config->get('parameters.host'); ?>"
        };
        var params = {};
        params.quality = "high";
        params.bgcolor = "#ffffff";
        params.allowscriptaccess = "always";
        params.allowfullscreen = "true";
        var attributes = {};
        attributes.id = "coupon_as";
        attributes.name = "coupon_as";
        attributes.align = "middle";
        swfobject.embedSWF(
            "/swf/coupon_as.swf", "couponWidget",
            "0", "0",
            swfVersionStr, xiSwfUrlStr,
            flashvars, params, attributes);
        // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
        swfobject.createCSS("#couponWidget", "display:block;text-align:left;");
    </script>
</div>