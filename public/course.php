<?php
require '../Db.php';

if (!isset($_GET['id'])) {
    die('Lesson not found');
}
$lessonId = (int)$_GET['id'];
$pdo = Db::getConnection();

$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
$sortColumn = ($sortBy === 'date') ? 'files.created_at' : 'files.file_name';

$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM lessons 
    LEFT JOIN files ON lessons.id = files.lesson_id
    WHERE lessons.id = ?
");
$countStmt->execute([$lessonId]);
$totalFiles = $countStmt->fetchColumn();
$totalPages = ceil($totalFiles / $itemsPerPage);

$stmt = $pdo->prepare("
    SELECT lessons.name AS lesson_name, files.file_name, files.file_path 
    FROM lessons 
    LEFT JOIN files ON lessons.id = files.lesson_id
    WHERE lessons.id = ?
    ORDER BY $sortColumn $sortOrder
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $lessonId, PDO::PARAM_INT);
$stmt->bindValue(2, $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$lessonData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$lessonData) {
    die('Lesson not found or no related files.');
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lessonData[0]['lesson_name']) ?></title>
    <link rel="stylesheet" href="public/assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
<div class="slider-wrapper">
    <div class="container">
        <div class="left-side">
            <div class="info-text">
                <h1>Ffdsjf jdsfds jfkdf </h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    Adipisci corporis ducimus earum, eum eveniet labore molestias neque officiis sequi similique tempore
                    veritatis vitae voluptatem!
                    Ab cum est in voluptas voluptatem.</p>
                <div class="buttons">
                    <button id="actual">Актуальне</button>
                    <button id="all">Всі курси</button>
                </div>
            </div>
        </div>
        <div class="right-side">
            <div id="slider">
                <div class="active"><img
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQlygWcz51gyDexlstejSgZZ2LSxqF4rBz3wQ&s"
                            alt=""></div>
                <div class=""><img
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQlygWcz51gyDexlstejSgZZ2LSxqF4rBz3wQ&s"
                            alt=""></div>
                <div class=""><img
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQlygWcz51gyDexlstejSgZZ2LSxqF4rBz3wQ&s"
                            alt=""></div>
            </div>
        </div>
        <div class="controls-slider"></div>
        <div class="arrow"></div>
    </div>
</div>

<div class="container mt-5">
    <h1><?= htmlspecialchars($lessonData[0]['lesson_name']) ?></h1>

    <form method="GET" action="" >
        <input type="hidden" name="id" value="<?= $lessonId ?>">
        <div class="controls">
            <div class="left-side">
                <div class="checkbox">
                    <input type="checkbox" id="select-all">
                </div>
                <div class="buttons">
                    <i class="upload fas fa-upload"></i>
                    <i class="delete fas fa-trash" id="delete-files"></i>
                    <i class="edit fas fa-edit" id="rename-files"></i>
                    <i class="download fas fa-download" id="download-files"></i>
                </div>
                <input type="file" id="file-input" style="display: none;">
            </div>
            <div class="right-side">
                <fieldset class="sort-fieldset">
                    <legend>Sorting</legend>
                    <form method="GET" action="">
                        <input type="hidden" name="id" value="<?= $lessonId ?>">
                        <input type="hidden" name="page" value="<?= $currentPage ?>">
                        <select id="sort-files" name="sort_by" onchange="this.form.submit()">
                            <option value="name" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'name' ? 'selected' : '' ?>>
                                Name
                            </option>
                            <option value="date" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'date' ? 'selected' : '' ?>>
                                Date
                            </option>
                        </select>
                        <input type="hidden" name="order" id="order-input"
                               value="<?= mb_strtolower(isset($_GET['order']) ? htmlspecialchars($_GET['order']) : 'asc') ?>">
                    </form>
                </fieldset>
                <div class="icons">
                    <i class="fas fa-sort-amount-up-alt <?php if(isset($_GET['order']) && $_GET['order'] === 'desc') echo 'rotate' ?>" id="sort"></i>
                    <i class="fas fa-times close-icon"></i>
                </div>
            </div>
        </div>
    </form>

    <div class="row" id="file-list">
        <?php foreach ($lessonData as $file): ?>
            <?php if ($file['file_name']): ?>
                <div class="col-md-3 mb-4 file-item">
                    <div class="file-card" data-file-name="<?= htmlspecialchars($file['file_name']) ?>"
                         data-file-path="<?= htmlspecialchars($file['file_path']) ?>">
                        <div class="show file-actions">
                            <a href="#" class="text-success"><i class="fas fa-check"></i></a>
                            <a href="#" class="text-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <input type="checkbox" class="file-checkbox">
                        <i class="fas fa-file fa-3x"></i>
                        <p class="mt-3"><?= htmlspecialchars($file['file_name']) ?></p>
                        <div class="bottom-buttons show">
                            <a href="<?= htmlspecialchars($file['file_path']) ?>"
                               class="btn btn-outline-secondary btn-sm" download><i class="fas fa-download"></i></a>
                            <a href="#" class="btn btn-outline-secondary btn-sm"
                               onclick="renameFile('<?= htmlspecialchars($file['file_name']) ?>')"><i
                                        class="fas fa-edit"></i></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link"
                               href="?id=<?= $lessonId ?>&page=<?= $i ?>&sort_by=<?= $sortBy ?>&order=<?= $sortOrder ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/assets/js/slider.js"></script>
<script src="public/assets/js/script.js"></script>
<script>
    $('.file-card').on('click', function () {
        $(this).toggleClass('selected');
        const checkbox = $(this).find('.file-checkbox');
        checkbox.prop('checked', !checkbox.prop('checked'));
    });

    $('#select-all').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.file-card').each(function () {
            $(this).toggleClass('selected', isChecked);
            $(this).find('.file-checkbox').prop('checked', isChecked);
        });
    });

    $('.upload').on('click', function () {
        const fileInput = $('#file-input')[0].files[0];

        if (!fileInput) {
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput);

        $.ajax({
            url: '/upload-file.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function (response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    alert(jsonResponse.message);
                    location.reload();
                } else {
                    alert('Error: ' + jsonResponse.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error uploading file: ' + textStatus + ' - ' + errorThrown);
            }
        });
    });


    $('.upload').on('click', function () {
        $('#file-input').click();
    });
    $('#rename-files').on('click', function () {
        let selectedFiles = [];

        $('.file-checkbox:checked').each(function () {
            const fileCard = $(this).closest('.file-card');
            const oldFileName = fileCard.data('file-name');
            const filePath = fileCard.data('file-path');

            const newFileName = prompt(`Enter new name for file: ${oldFileName}`, oldFileName);

            if (newFileName && newFileName !== oldFileName) {
                selectedFiles.push({oldFileName, filePath, newFileName});
            } else if (!newFileName) {
                alert('Rename cancelled.');
                return;
            }
        });
        if (selectedFiles.length > 0) {
            $.ajax({
                url: '/rename-file.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({files: selectedFiles}),
                success: function (response) {
                    alert('Files renamed successfully');
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error renaming files:', textStatus, errorThrown);
                    alert('Error renaming files. Please check logs.');
                }
            });
        } else {
            alert('No files selected for renaming');
        }
    });

    function renameFile(oldFileName) {
        const newFileName = prompt('Enter new file name:', oldFileName);

        if (newFileName && newFileName !== oldFileName) {
            const filePath = $('[data-file-name="' + oldFileName + '"]').data('file-path');

            $.ajax({
                url: '/rename-file.php',
                type: 'POST',
                data: {oldFileName, newFileName, filePath},
                success: function (response) {
                    alert('File renamed successfully');
                    location.reload();
                },
                error: function () {
                    alert('Error renaming file');
                }
            });
        } else if (!newFileName) {
            alert('Rename cancelled.');
        }
    }

    $('#file-input').on('change', function () {
        const fileInput = $('#file-input')[0];

        if (fileInput.files.length > 0) {
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('lessonId', <?= $lessonId ?>);

            $.ajax({
                url: '/upload-file.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    alert('File uploaded successfully');
                    location.reload();
                },
                error: function () {
                    alert('Error uploading file');
                }
            });
        }
    });

    $('#delete-files').on('click', function () {
        let selectedFiles = [];
        $('.file-checkbox:checked').each(function () {
            const fileCard = $(this).closest('.file-card');
            selectedFiles.push(fileCard.data('file-path'));
        });

        if (selectedFiles.length > 0 && confirm('Are you sure to delete selected files?')) {
            $.ajax({
                url: '/delete-files.php',
                type: 'POST',
                data: {files: selectedFiles},
                success: function (response) {
                    alert('Files deleted successfully');
                    location.reload();
                },
                error: function () {
                    alert('Error deleting files');
                }
            });
        }
    });

    $('#sort-files').on('change', function () {
        const sortBy = $(this).val();
        const files = $('.file-item').get();

        files.sort(function (a, b) {
            const fileA = $(a).find('.file-card').data('file-name').toLowerCase();
            const fileB = $(b).find('.file-card').data('file-name').toLowerCase();

            if (sortBy === 'name') {
                return fileA < fileB ? -1 : fileA > fileB ? 1 : 0;
            }
            return 0;
        });

        $('#file-list').append(files);
    });
    $('.file-card').on('dblclick', function () {
        const filePath = $(this).data('file-path');
        window.open(filePath, '_blank');
    });
</script>
</body>
</html>