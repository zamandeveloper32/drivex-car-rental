<?php

require_once 'includes/auth.php';
require_once 'includes/functions.php';

$search = trim($_GET['search'] ?? '');

redirect(
    'rentals.php?search=' . urlencode($search)
);
