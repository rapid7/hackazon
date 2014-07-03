<form class="navbar-form navbar-left" role="search">
    <span style="font-size: 16px; color: #d9534f">Search</span>
    <div class="input-group">
        <div class="input-group-btn">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?=$search_category?> <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
                <?php foreach($search_subcategories as $key=>$value):?>
                    <li><a href="#"><?=$value?></a></li>
                <?php endforeach?>
            </ul>
        </div><!-- /btn-group -->
        <input type="text" class="form-control" style="width: 170px;">
    </div><!-- /input-group -->
    <button type="submit" class="btn btn-default">Go</button>
</form>