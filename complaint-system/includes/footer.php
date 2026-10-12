<?php
/** Reusable app layout — footer (closes <main>, renders toasts, loads JS). */
$__flash = null;
if (function_exists('get_flash')) { $__flash = get_flash(); }
?>
    </main><!-- /.content -->
    <footer class="app-footer">
      <span>ResolveDesk · College Complaint Management System</span>
      <span class="dot">·</span>
      <span><?= date('Y') ?> · For demonstration &amp; campus use</span>
    </footer>
  </div><!-- /.main-col -->
</div><!-- /.app-shell -->

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap" aria-live="polite"></div>
<?php if ($__flash): ?>
<script>window.__FLASH__ = <?= json_encode($__flash, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;</script>
<?php endif; ?>
<script src="<?= BASE_URL ?>/js/app.js"></script>
<script src="<?= BASE_URL ?>/js/validation.js"></script>
</body>
</html>
