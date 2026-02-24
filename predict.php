<?php
include 'db.php';

// Collect inputs
$name         = $_POST['name'];
$cgpa         = floatval($_POST['cgpa']);
$coding       = intval($_POST['coding_score']);
$communication= intval($_POST['communication']);
$projects     = intval($_POST['projects']);
$internships  = intval($_POST['internships']);
$aptitude     = intval($_POST['aptitude_score']);
$backlogs     = intval($_POST['backlogs']);

// ── SCORING ALGORITHM ──────────────────────────────────────────
$score = 0;

// CGPA (max 30 points)
if ($cgpa >= 8.5)      $score += 30;
elseif ($cgpa >= 7.5)  $score += 24;
elseif ($cgpa >= 6.5)  $score += 16;
elseif ($cgpa >= 6.0)  $score += 10;
else                   $score += 0;

// Coding (max 20 points)
$score += $coding * 2;

// Communication (max 15 points)
$score += $communication * 1.5;

// Projects (max 10 points, cap at 5 projects)
$score += min($projects, 5) * 2;

// Internships (max 10 points)
$score += min($internships, 2) * 5;

// Aptitude (max 10 points)
$score += $aptitude * 1;

// Backlogs (penalty)
$score -= $backlogs * 5;

$score = max(0, min(100, $score)); // clamp 0-100
$probability = round($score);

// ── STORE IN DB ─────────────────────────────────────────────────
$sql = "INSERT INTO students (name, cgpa, coding_score, communication, projects, internships, aptitude_score, backlogs, probability)
        VALUES ('$name', $cgpa, $coding, $communication, $projects, $internships, $aptitude, $backlogs, $probability)";
mysqli_query($conn, $sql);

// ── PASS TO RESULT PAGE ─────────────────────────────────────────
session_start();
$_SESSION['result'] = [
    'name'        => $name,
    'probability' => $probability,
    'cgpa'        => $cgpa,
    'coding'      => $coding,
    'communication'=> $communication,
    'projects'    => $projects,
    'internships' => $internships,
    'aptitude'    => $aptitude,
    'backlogs'    => $backlogs,
];
header("Location: result.php");
exit;
?>