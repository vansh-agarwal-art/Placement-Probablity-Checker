<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Your Skills</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>📋 Enter Your Details</h2>
  <form action="predict.php" method="POST">

    <label>Full Name</label>
    <input type="text" name="name" required placeholder="Your name">

    <label>CGPA (0 - 10)</label>
    <input type="number" step="0.1" min="0" max="10" name="cgpa" required>

    <label>Coding Skills (0 - 10)</label>
    <input type="range" name="coding_score" min="0" max="10" value="5" oninput="this.nextElementSibling.value=this.value">
    <output>5</output>

    <label>Communication Skills (0 - 10)</label>
    <input type="range" name="communication" min="0" max="10" value="5" oninput="this.nextElementSibling.value=this.value">
    <output>5</output>

    <label>Number of Projects Done</label>
    <input type="number" name="projects" min="0" max="20" value="0">

    <label>Internships Done</label>
    <input type="number" name="internships" min="0" max="10" value="0">

    <label>Aptitude Score (0 - 10)</label>
    <input type="range" name="aptitude_score" min="0" max="10" value="5" oninput="this.nextElementSibling.value=this.value">
    <output>5</output>

    <label>Number of Active Backlogs</label>
    <input type="number" name="backlogs" min="0" max="20" value="0">

    <button type="submit" class="btn">Predict My Placement 🚀</button>
  </form>
</div>
</body>
</html>