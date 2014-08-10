<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="<?php if (isset($htmlClassNames)): echo implode(' ', $htmlClassNames); endif; ?> no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>         <html class="<?php if (isset($htmlClassNames)): echo implode(' ', $htmlClassNames); endif; ?> no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>         <html class="<?php if (isset($htmlClassNames)): echo implode(' ', $htmlClassNames); endif; ?> no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="<?php if (isset($htmlClassNames)): echo implode(' ', $htmlClassNames); endif; ?> no-js" lang="en"> <!--<![endif]-->
<html class="<?php if (isset($htmlClassNames)): echo implode(' ', $htmlClassNames); endif; ?> no-js">
<head>
  <meta charset="utf-8">

  <title><?php if (isset($title)): echo $title.' – '; endif; echo $app->config['title'] ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0">
  <meta name="author" content="Marius Lundgård">

<?php if (isset($styleSheets)): foreach ($styleSheets as $styleSheet): ?>
  <link rel="stylesheet" href="<?php echo $app->staticUrl($styleSheet) ?>">
<?php endforeach; endif ?>

  <link rel="icon" type="image/png" href="<?php echo $app->staticUrl('favicon.png') ?>">

  <script src="<?php echo $app->staticUrl('vendor/modernizr/modernizr.js' ) ?>"></script>
</head>

<body<?php if (isset($bodyClassNames)): ?> class="<?php echo implode(' ', $bodyClassNames) ?>"<?php endif ?>>

<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<?php echo $menu->render(); ?>
<?php echo $view; ?>

<?php if (isset($scripts)): ?>
<?php foreach ($scripts as $script): ?>
<script src="<?php echo $app->staticUrl($script) ?>"></script>
<?php endforeach ?>
<?php endif ?>
</body>
</html>
