<?php
session_start();
if (!isset($_SESSION['result'])) { header("Location: form.php"); exit; }
$r = $_SESSION['result'];
$p = $r['probability'];

// Determine category
if ($p >= 80)      { $category = "Highly Likely ✅"; $color = "#22c55e"; }
elseif ($p >= 60)  { $category = "Good Chances 🟡"; $color = "#f59e0b"; }
elseif ($p >= 40)  { $category = "Moderate 🟠"; $color = "#f97316"; }
else               { $category = "Needs Improvement ❌"; $color = "#ef4444"; }

// Tips
$tips = [];
if ($r['cgpa'] < 7)        $tips[] = "📚 Focus on improving your CGPA above 7.";
if ($r['coding'] < 6)      $tips[] = "💻 Practice more coding (LeetCode, HackerRank).";
if ($r['communication'] < 6) $tips[] = "🗣️ Work on communication & soft skills.";
if ($r['projects'] < 2)    $tips[] = "🛠️ Build at least 2-3 good projects.";
if ($r['internships'] == 0)$tips[] = "🏢 Try to get at least one internship.";
if ($r['backlogs'] > 0)    $tips[] = "⚠️ Clear your backlogs ASAP!";
if ($r['aptitude'] < 5)    $tips[] = "🧮 Practice aptitude tests regularly.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Result</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container result-page">
  <h2>Hello, <?= htmlspecialchars($r['name']) ?>! 👋</h2>
  <div class="circle" style="border-color: <?= $color ?>; color: <?= $color ?>">
    <span class="percent"><?= $p ?>%</span>
    <small>Placement Chance</small>
  </div>
  <h3 style="color: <?= $color ?>"><?= $category ?></h3>

  <?php if (!empty($tips)): ?>
  <div class="tips">
    <h4>🔧 Areas to Improve:</h4>
    <ul>
      <?php foreach ($tips as $tip): ?>
        <li><?= $tip ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php else: ?>
    <p class="success">You're well-prepared! Keep it up! 🎉</p>
  <?php endif; ?>

  <a href="form.php" class="btn">Try Again</a>
</div>
</body>
</html>