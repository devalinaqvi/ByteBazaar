<section class="shell page-section">
  <div class="page-heading">
    <span class="eyebrow">THE PEOPLE BEHIND THE POSSIBILITIES</span>
    <h1>
      Customers<span class="orange-text">.</span>
    </h1>
    <p><?= (int) $total ?> accounts in your community.</p>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (
     $users
     as $user
 ): ?>
        <tr>
          <td><strong><?= e($user["name"]) ?></strong></td>
          <td><?= e(
    $user["email"],
) ?></td>
          <td><span class="status-badge"><?= $user["is_admin"]
    ? "Administrator"
    : "Customer" ?></span></td>
          <td><?= e(
    date("M j, Y", strtotime($user["created_at"])),
) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php require base_path(
    "app/Views/partials/pagination.php",
); ?>
</section>
