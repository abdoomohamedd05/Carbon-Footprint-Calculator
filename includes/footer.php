    <script src="<?= defined('APP_BASE') ? APP_BASE : '/CarbonFootprintAI' ?>/assets/js/script.js?v=2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= defined('APP_BASE') ? APP_BASE : '/CarbonFootprintAI' ?>/assets/js/dashboard.js?v=3"></script>
    <?php if (!empty($loadDashboardCss) && isset($chartLabels, $chartValues)): ?>
    <script>
        window.dashboardCharts = {
            carbonLabels: <?= json_encode($chartLabels) ?>,
            carbonValues: <?= json_encode($chartValues) ?>,
            esgValues: <?= json_encode($esgChart ?? [0, 0, 0]) ?>
        };
    </script>
    <?php endif; ?>
</body>
</html>
