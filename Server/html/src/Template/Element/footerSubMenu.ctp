<?php
/**
 * @var \App\Model\Entity\Cultivar[]|\Cake\Collection\CollectionInterface $strains
 */
?>
<ul class='footerSubMenu'>
	<? foreach ($strains as $strain) { ?>
		<li><a href="/strains/view/<?=$strain['Strain']['id']?>"><?=$strain['Strain']['name']?></a></li>
	<? } ?>
</ul>
