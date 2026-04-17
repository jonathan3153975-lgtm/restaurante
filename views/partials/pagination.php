<?php
$currentPage = (int) ($paginator['page'] ?? 1);
$pages = (int) ($paginator['pages'] ?? 1);
$basePath = $basePath ?? '';
$query = $query ?? [];
?>
<?php if ($pages > 1): ?>
    <nav class="pagination">
        <?php for ($page = 1; $page <= $pages; $page++): ?>
            <?php $queryString = http_build_query(array_merge($query, ['page' => $page])); ?>
            <a href="<?= e($basePath . '?' . $queryString) ?>" class="page-link <?= $page === $currentPage ? 'is-active' : '' ?>"><?= e((string) $page) ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
