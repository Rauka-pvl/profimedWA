<?php
chdir(__DIR__);
passthru('php artisan optimize:clear');
echo "Cleared";
