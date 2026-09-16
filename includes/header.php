<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . " | " : "" ?>Carbon Footprint AI</title>
    <meta name="description" content="Carbon Footprint AI Sustainability Management Platform">
    <meta name="theme-color" content="#10B981">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/style.css?v=2">
    <?php if (!empty($loadDashboardCss)): ?>
    <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/dashboard.css?v=3">
    <?php endif; ?>
</head>
<body>
