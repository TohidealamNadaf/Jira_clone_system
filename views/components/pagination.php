<?php
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$perPage = $pagination['per_page'] ?? 15;
$total = $pagination['total'] ?? 0;
$from = $pagination['from'] ?? 0;
$to = $pagination['to'] ?? 0;
$baseUrl = $pagination['base_url'] ?? '';
$queryParams = $pagination['query_params'] ?? [];

if ($lastPage <= 1) {
    return;
}

function buildPaginationUrl($page, $baseUrl, $queryParams)
{
    $queryParams['page'] = $page;
    return $baseUrl . '?' . http_build_query($queryParams);
}

$range = 2;
$start = max(1, $currentPage - $range);
$end = min($lastPage, $currentPage + $range);
?>

<nav aria-label="Pagination" class="d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing <?= e($from) ?> to <?= e($to) ?> of <?= e($total) ?> results
    </div>

    <ul class="pagination mb-0">
        <!-- First Page -->
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(buildPaginationUrl(1, $baseUrl, $queryParams)) ?>" aria-label="First"
                <?= $currentPage <= 1 ? 'tabindex="-1"' : '' ?>>
                <i class="bi bi-chevron-double-left"></i>
            </a>
        </li>

        <!-- Previous Page -->
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(buildPaginationUrl($currentPage - 1, $baseUrl, $queryParams)) ?>"
                aria-label="Previous" <?= $currentPage <= 1 ? 'tabindex="-1"' : '' ?>>
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <!-- Page Numbers -->
        <?php if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= e(buildPaginationUrl(1, $baseUrl, $queryParams)) ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($page = $start; $page <= $end; $page++): ?>
            <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= e(buildPaginationUrl($page, $baseUrl, $queryParams)) ?>">
                    <?= $page ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $lastPage): ?>
            <?php if ($end < $lastPage - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link"
                    href="<?= e(buildPaginationUrl($lastPage, $baseUrl, $queryParams)) ?>"><?= $lastPage ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Page -->
        <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(buildPaginationUrl($currentPage + 1, $baseUrl, $queryParams)) ?>"
                aria-label="Next" <?= $currentPage >= $lastPage ? 'tabindex="-1"' : '' ?>>
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>

        <!-- Last Page -->
        <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(buildPaginationUrl($lastPage, $baseUrl, $queryParams)) ?>"
                aria-label="Last" <?= $currentPage >= $lastPage ? 'tabindex="-1"' : '' ?>>
                <i class="bi bi-chevron-double-right"></i>
            </a>
        </li>
    </ul>

    <div class="d-flex align-items-center">
        <label class="me-2 text-muted small">Per page:</label>
        <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
            <?php foreach ([10, 15, 25, 50, 100] as $option): ?>
                <?php
                $params = array_merge($queryParams, ['per_page' => $option, 'page' => 1]);
                $optionUrl = $baseUrl . '?' . http_build_query($params);
                ?>
                <option value="<?= e($optionUrl) ?>" <?= $perPage == $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</nav>