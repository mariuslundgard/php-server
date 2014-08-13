<div class="article">
  <header class="article-header">
    <h1 class="article-title"><?php echo $message ?></h1>
  </header>

  <form class="article-content" action="<?php echo $app->getRealPath($loginUri) ?>" method="post">
    <div class="field<?php if (isset($fieldErrors['username'])): ?> error<?php endif ?>">
      <label>Username</label>
      <input class="text-field" name="username" type="text"<?php if ($data): ?> value="<?php echo $data['username'] ?>"<?php endif ?>>
<?php if (isset($fieldErrors['username'])): ?>
      <p class="note"><?php echo implode('<br>', $fieldErrors['username']) ?></p>
<?php endif ?>
    </div>

    <div class="field">
      <label>Password</label>
      <input class="text-field" name="password" type="password"<?php if ($data): ?> value="<?php echo $data['password'] ?>"<?php endif ?>>
<?php if (isset($fieldErrors['password'])): ?>
      <p class="note"><?php echo implode('<br>', $fieldErrors['password']) ?></p>
<?php endif ?>
    </div>

    <div class="button-group">
      <input class="button" type="submit" value="Log in">
    </div>
  </form>
</div>

