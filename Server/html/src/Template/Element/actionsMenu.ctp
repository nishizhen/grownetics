<div class="btn-group">
  <button type="button" class="btn btn-theme dropdown-toggle" data-toggle="dropdown">
  <i class="fa fa-cog"></i>
    <?php echo isset($label) ? $label : '';?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <?php foreach ($actions as $action) { 
      if ($action) { ?>
      <li><?=$action?></li>
    <?php } else { ?>
      <li class="divider"></li>
    <?php } 
    }?>
  </ul>
</div>