<?php

require_once 'includes/auth.php';
require_once 'includes/functions.php';

$search = trim($_GET['search'] ?? '');

redirect(
    'customers.php?search=' . urlencode($search)
);
