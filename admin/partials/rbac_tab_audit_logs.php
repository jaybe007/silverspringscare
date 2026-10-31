<?php
/**
 * Tab 6: Audit Logs & Administrative History
 * Records and displays every administrative action with search, filtering, diff inspection, and CSV export.
 */

$a_search = trim($_GET['aq'] ?? '');
$a_module = trim($_GET['amodule'] ?? '');
$a_action = trim($_GET['aaction'] ?? '');
$a_page = max(1, (int)($_GET['apage'] ?? 1));
$a_per_page = 20;

$a_where = ["1=1"];
$a_params = [];

if ($a_search !== '') {
    $a_where[] = "(actor_username LIKE ? OR target_identifier LIKE ? OR description LIKE ? OR action LIKE ? OR ip_address LIKE ?)";
    $like = "%{$a_search}%";
    array_push($a_params, $like, $like, $like, $like, $like);
}

if ($a_module !== '') {
    $a_where[] = "module = ?";
    $a_params[] = $a_module;
}

if ($a_action !== '') {
    $a_where[] = "action = ?";
    $a_params[] = $a_action;
}

$a_where_sql = implode(' AND ', $a_where);

$cnt_stmt = $pdo->prepare("SELECT COUNT(*) FROM audit_logs WHERE {$a_where_sql}");
$cnt_stmt->execute($a_params);
$total_logs = (int)$cnt_stmt->fetchColumn();

$a_total_pages = max(1, (int)ceil($total_logs / $a_per_page));
$a_page = min($a_page, $a_total_pages);
$a_offset = ($a_page - 1) * $a_per_page;

$log_stmt = $pdo->prepare("SELECT * FROM audit_logs WHERE {$a_where_sql} ORDER BY id DESC LIMIT {$a_per_page} OFFSET {$a_offset}");
$log_stmt->execute($a_params);
$audit_rows = $log_stmt->fetchAll();

$distinct_modules = $pdo->query("SELECT DISTINCT module FROM audit_logs ORDER BY module ASC")->fetchAll(PDO::FETCH_COLUMN);
$distinct_actions = $pdo->query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Filter Form -->
<form method="GET" action="<?= BASE_PATH ?>/admin/users.php" style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); margin-bottom: 2rem;">
  <input type="hidden" name="tab" value="audit_logs">

  <div class="grid-4" style="gap: 1rem; align-items: end;">
    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Search Audit Trail</label>
      <input type="text" name="aq" class="form-control" value="<?= htmlspecialchars($a_search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Actor, user, IP, description..." style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Filter by Module</label>
      <select name="amodule" class="form-control" style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
        <option value="">All Modules</option>
        <?php foreach ($distinct_modules as $dm): ?>
          <option value="<?= htmlspecialchars($dm, ENT_QUOTES, 'UTF-8') ?>" <?= $a_module === $dm ? 'selected' : '' ?>>
            <?= htmlspecialchars($dm, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label">Filter by Action Event</label>
      <select name="aaction" class="form-control" style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
        <option value="">All Actions</option>
        <?php foreach ($distinct_actions as $da): ?>
          <option value="<?= htmlspecialchars($da, ENT_QUOTES, 'UTF-8') ?>" <?= $a_action === $da ? 'selected' : '' ?>>
            <?= htmlspecialchars($da, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn btn-navy btn-sm" style="flex: 1;">Filter Logs</button>
      <a href="?tab=audit_logs" class="btn btn-secondary btn-sm">Reset</a>
    </div>
  </div>
</form>

<!-- Audit Logs Table -->
<section>
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Immutable Administrative Audit Logs (<?= $total_logs ?>)</h2>
    <span style="font-size: 0.8rem; color: var(--color-forest-700); font-weight: 700;">&check; Protected against modification &amp; deletion</span>
  </div>

  <?php if (empty($audit_rows)): ?>
    <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
      <p style="color: var(--color-text-muted); margin: 0;">No audit log entries match the selected filter criteria.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID &amp; Timestamp</th>
            <th>Administrator</th>
            <th>Affected User / Target</th>
            <th>Module &amp; Action</th>
            <th>Description &amp; Value Changes</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($audit_rows as $log): ?>
            <tr>
              <td style="font-size: 0.8rem; white-space: nowrap;">
                <strong>#<?= (int)$log['id'] ?></strong>
                <div style="color: var(--color-text-muted);"><?= format_date($log['created_at']) ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars($log['actor_username'] ?: 'system', ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if (!empty($log['actor_user_id'])): ?>
                  <div style="font-size: 0.75rem; color: var(--color-text-muted);">ID #<?= (int)$log['actor_user_id'] ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($log['target_user_id'])): ?>
                  <a href="?tab=user_detail&amp;id=<?= (int)$log['target_user_id'] ?>" style="font-weight: 700; color: var(--color-forest-700);">
                    <?= htmlspecialchars($log['target_identifier'] ?: ('User #' . (int)$log['target_user_id']), ENT_QUOTES, 'UTF-8') ?>
                  </a>
                <?php else: ?>
                  <span><?= htmlspecialchars($log['target_identifier'] ?: '—', ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge badge-sage" style="font-size: 0.68rem;"><?= htmlspecialchars($log['module'], ENT_QUOTES, 'UTF-8') ?></span>
                <div style="margin-top: 3px;"><code style="font-size: 0.75rem; font-weight: 700; color: var(--color-navy-900);"><?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?></code></div>
              </td>
              <td style="max-width: 440px; font-size: 0.83rem;">
                <div><?= htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($log['old_values']) || !empty($log['new_values'])): ?>
                  <details style="margin-top: 0.35rem; font-size: 0.75rem; color: var(--color-slate-700);">
                    <summary style="cursor: pointer; color: var(--color-forest-700); font-weight: 600;">Inspect Previous / New Values</summary>
                    <div style="background: var(--color-slate-50); padding: 0.5rem 0.75rem; border-radius: var(--radius-xs); border: 1px solid var(--color-slate-200); margin-top: 0.35rem; word-break: break-all;">
                      <?php if (!empty($log['old_values'])): ?>
                        <div><strong>Previous:</strong> <code><?= htmlspecialchars($log['old_values'], ENT_QUOTES, 'UTF-8') ?></code></div>
                      <?php endif; ?>
                      <?php if (!empty($log['new_values'])): ?>
                        <div><strong>New:</strong> <code><?= htmlspecialchars($log['new_values'], ENT_QUOTES, 'UTF-8') ?></code></div>
                      <?php endif; ?>
                    </div>
                  </details>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.8rem; color: var(--color-text-muted);">
                <?= htmlspecialchars($log['ip_address'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($a_total_pages > 1): ?>
      <ul class="pagination" style="margin-top: 1.5rem;">
        <?php for ($p = 1; $p <= $a_total_pages; $p++):
          $qp = $_GET;
          $qp['apage'] = $p;
          $qp['tab'] = 'audit_logs';
        ?>
          <li class="page-item <?= $p === $a_page ? 'is-active' : '' ?>">
            <a class="page-link" href="?<?= htmlspecialchars(http_build_query($qp), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>
</section>

