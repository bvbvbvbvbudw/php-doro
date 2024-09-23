<?php
require '../Db.php';

$pdo = Db::getConnection();

$stmt = $pdo->query("
    SELECT courses.id AS course_id, courses.title, lessons.id AS lesson_id, lessons.name AS lesson_name 
    FROM courses 
    LEFT JOIN lessons ON courses.id = lessons.course_id
");
$coursesData = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="nav-left">
            <div id="logo">
                <img src="public/assets/img/browser.png" alt="Logo site">
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="#">Курси</a></li>
                    <li><a href="#">Тренiнги</a></li>
                    <li><a href="#">Про нас</a></li>
                </ul>
            </nav>
        </div>
        <div class="nav-right">
            <div class="phone">
                <i></i>
                <p></p>
            </div>
            <div class="socials">
                <a href=""><i></i></a>
                <a href=""><i></i></a>
                <a href=""><i></i></a>
            </div>
            <a href="">Login</a>
        </div>
    </div>
</header>

<div class="wrapper">
    <div class="row courses">
        <?php foreach ($coursesData as $courseId => $course): ?>
            <div class="item-course col-md-4 mb-4">
                <div class="p-3 course-card">
                    <h5 class="course-title text-success"><span class="">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24"
                                 viewBox="0 0 48 48">
                                <path fill="#4caf50"
                                      d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path>
                                <path fill="#ccff90"
                                      d="M34.602,14.602L21,28.199l-5.602-5.598l-2.797,2.797L21,33.801l16.398-16.402L34.602,14.602z"></path>
                            </svg>
                        </span><?= $course[0]['title'] ?></h5>
                    <ul class="course-list">
                        <?php foreach ($course as $lesson): ?>
                            <li>
                                <span class="arrow">→</span> <?= $lesson['lesson_name'] ?>
                                <button class="btn course-btn">
                                    <a href="course.php?id=<?= $lesson['lesson_id'] ?>">Йде набір</a>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
