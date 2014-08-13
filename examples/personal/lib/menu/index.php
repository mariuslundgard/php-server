<div class="sm-max-size">
<header class="nav trinity rule-after">
  <div class="nav-left">
<?php foreach ($menuItems as $menuItem): ?>
    <a class="button" href="<?php echo $app->getRealPath($menuItem['uri']) ?>"><?php echo $menuItem['label'] ?></a>    
<?php endforeach ?>
  </div>

  <div class="nav-right">
<?php foreach ($actions as $menuItem): ?>
    <a class="button" href="<?php echo $app->getRealPath($menuItem['uri']) ?>"><?php echo $menuItem['label'] ?></a>    
<?php endforeach ?>
  </div>
</header>
</div>
