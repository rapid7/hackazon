<?php for($i=1;$i<7;$i++):?>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="thumbnail">
            <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
            <div class="caption">
                <h4 class="pull-right">$<?=(1000*$i)?></h4>
                <h4><a href="/product/view/">Product <?=$i?></a></h4>
                <p>Description Product <?=$i?></p>
            </div>
        </div>
    </div>
<? endfor;?>