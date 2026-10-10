<?php if ($message = session()->getFlashdata('success')): ?><div class="flash-message flash-success" role="status" aria-live="polite" tabindex="0"><?= esc($message) ?></div><?php endif; ?>
<?php if ($message = session()->getFlashdata('error')): ?><div class="flash-message flash-error" role="alert" tabindex="0"><?= esc($message) ?></div><?php endif; ?>
