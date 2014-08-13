<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>

  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=2.0">
  <meta name="author" content="Marius Lundgård">
  <meta name="generator" content="Sketch 1.0.0-alpha">

<link rel="stylesheet" href="http://localhost/~mariuslundgard/body/dist/body.css">
</head>

<body class="no-margin xs-max-size">
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<div class="page-content" id="content">
    <?php
    if ( ! isset($err)) {
        die('<h1>The error object is not defined</h1><pre>' . print_r(debug_backtrace(), true) . '</pre>');
    }
    if ( ! $err) {
        die('<h1>The error object is empty</h1><pre>' . debug_backtrace() . '</pre>');
    }
    ?>
    
    <div class="nav trinity">
      <div class="nav-left">
        <h1 class="nav-title"><?php echo $err->getMessage(); ?></h1>
      </div>
    </div>

    <div class="note">The error was thrown in <?php echo substr($err->getFile(), 0); ?> on line <?php echo $err->getLine(); ?>.</div>

        <h2 class="label">Backtrace</h2>

<?php foreach ($err->getTrace() as $trace):
            extract($trace + [
                'class' => '',
                'type' => '',
                'function' => '',
                'file' => '',
                'line' => ''
            ]); ?>
        <details class="details trace rule-before">
            <summary class="details-title"><?php echo $class; ?><?php echo $type; ?><?php echo $function; ?>()</summary>

            <div class="note">Called in <?php echo $file ? substr($file, 0) : 'a closure object'; ?><?php echo $line ? ' on line ' . $line : ''; ?>.</div>

        </details>
<?php endforeach; ?>
</div>

<script src="http://localhost/~mariuslundgard/body/dist/body.js"></script>
</body>
</html>