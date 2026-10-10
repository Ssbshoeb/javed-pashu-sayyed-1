<?php
/*
| One media row: text on the left (source, headline, summary line, link), the
| article's own picture on the right. Expects $m (see 'media' in
| config/practice.php). External link, opens in a new tab.
*/

defined('APP_RUNNING') or exit('No direct access.');

$src = (string) ($m['source'] ?? 'Media');
$brand = practice('mediaSources.' . $src);
$mark = is_array($brand) ? $brand['mark'] : strtoupper(substr($src, 0, 2));
$color = is_array($brand) ? $brand['color'] : '#8A6A23';

// Readable form of the address: host + path, without scheme, query or trailing slash.
$parts = parse_url((string) $m['url']) ?: [];
$shown = preg_replace('#^www\.#', '', (string) ($parts['host'] ?? '')) . rtrim((string) ($parts['path'] ?? ''), '/');
?>
<a class="mrow" href="<?= e($m['url']) ?>" target="_blank" rel="noopener noreferrer"
   style="--mc:<?= e($color) ?>" data-topic="<?= e($m['topic'] ?? '') ?>">
    <span class="mrow__body">
        <span class="mrow__meta">
            <span class="mrow__mark" aria-hidden="true"><?= e($mark) ?></span>
            <strong><?= e($src) ?></strong>
            <?php if (!empty($m['date'])): ?><span class="mrow__dot" aria-hidden="true"></span><span><?= e(long_date($m['date'])) ?></span><?php endif; ?>
            <?php if (!empty($m['topic'])): ?><span class="mrow__tag"><?= e($m['topic']) ?></span><?php endif; ?>
        </span>
        <h3 class="mrow__title"><?= e($m['title']) ?></h3>
        <?php if (!empty($m['excerpt'])): ?><span class="mrow__excerpt"><?= e($m['excerpt']) ?></span><?php endif; ?>
        <span class="mrow__link">
            <span class="mrow__url"><?= e($shown) ?></span>
            <span class="mrow__cta">Read article</span>
        </span>
    </span>
    <span class="mrow__img<?= empty($m['image']) ? ' mrow__img--blank' : '' ?>">
        <?php if (!empty($m['image'])): ?>
            <img src="<?= e($m['image']) ?>" alt="<?= e($src . ': ' . $m['title']) ?>" loading="lazy" width="400" height="260">
        <?php else: ?>
            <span aria-hidden="true"><?= e($mark) ?></span>
        <?php endif; ?>
    </span>
</a>
