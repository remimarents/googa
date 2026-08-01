<?php declare(strict_types=1); session_name('googa'); session_start(); if (empty($_SESSION['googa_email'])) { require __DIR__ . '/login.php'; exit; } require __DIR__ . '/index.html';
