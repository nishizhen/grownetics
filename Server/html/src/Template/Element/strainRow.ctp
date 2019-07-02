<div class="col-lg-4 col-md-4 col-sm-12">
    <? $photo = '';
    if (!empty($strain['photo'])) {
				$photo = $strain['photo'];
			} ?>
	<div class="row centered">
		<img src='/thumbs/?src=<?=$photo?>&h=150&w=150' class='photo img-circle' height=150 width=150 />
	</div>						

    <h4><?=$strain['name']?></h4>
    <p><?=$strain['description']?></p>
</div>

