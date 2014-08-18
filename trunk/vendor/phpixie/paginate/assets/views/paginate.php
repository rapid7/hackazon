<ul class="pagination" data-page="<?=$pager->page?>" data-first-url="<?=$pager->url(1)?>">
	<?php if($pager->num_pages>5):?>
		<?php if($pager->page>1):?>
			<li><a href="<?=$pager->url(1);?>">&laquo;</a></li>
			<li><a href="<?=$pager->url($pager->page-1);?>">&lsaquo;</a></li>
		<?php else:?>
			<li class="disabled"><a>&laquo;</a></li>
			<li class="disabled"><a>&lsaquo;</a></li>
		<?php endif;?>
	<?php endif;?>
	
	<?php if($pager->num_pages>1):
			$start = $pager->page - 2;
			if ($start < 1)
				$start = 1;
				
			$end = $start+4;
			if ($end > $pager->num_pages){ 
				$end = $pager-> num_pages;
				if ($end - 4 > 0)
					$start = $end - 4;
			}
			
			for($i=$start;$i<=$end;$i++): ?>
				<li class="<?=$i==$pager->page?'active':''?>" ><a href="<?=$i==$pager->page?'#':$pager->url($i)?>"><?=$i?></a></li>
			<?php endfor; ?>
	<?php endif;?>
	
	<?php if($pager->num_pages>5):?>
		<?php if($pager->page<$pager->num_pages):?>
			<li><a href="<?=$pager->url($pager->page+1);?>">&rsaquo;</a></li>
			<li><a href="<?=$pager->url($pager->num_pages);?>">&raquo;</a></li>
		<?php else:?>
			<li class="disabled"><a>&rsaquo;</a></li>
			<li class="disabled"><a>&raquo;</a></li>
		<?php endif;?>
	<?php endif;?>
</ul>
