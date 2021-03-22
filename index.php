<?php
    require_once './settings.php';
    require_once './class/message-list.php';
    require_once './class/message-detale.php';
    require_once './class/message-add.php';
    require_once './class/message-edit.php';
    require_once './class/message-remove.php';
    require_once './class/comments-list.php';
    require_once './class/comments-add.php';
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <title>Message list</title>
    </head>

    <body>
        <div class="container mx-auto col-12 col-md-8">
        <!-- меню -->
            <div class="mx-auto col-12 mt-2">
                <ul class="nav justify-content-start">
            <?php 
                if (!empty($_GET) && empty($_GET['remove'])) {
                    echo '
                    <li class="nav-item">
                        <a class="btn text-primary" onclick="history.back();">
                            <svg width="1.8em" height="1.8em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.854 4.646a.5.5 0 0 1 0 .708L3.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>
                                <path fill-rule="evenodd" d="M2.5 8a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </a>    
                    </li>
                    ';
                }
            ?>
                    <li class="nav-item">
                        <a class="btn" href="./index.php">
                            <svg width="1.8em" height="1.8em" viewBox="0 0 16 16" class="bi bi-house-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 3.293l6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                                <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn text-success" href="./index.php?add=true">
                            <svg width="1.8em" height="1.8em" viewBox="0 0 16 16" class="bi bi-file-earmark-plus-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M2 3a2 2 0 0 1 2-2h5.293a1 1 0 0 1 .707.293L13.707 5a1 1 0 0 1 .293.707V13a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3zm7 2V2l4 4h-3a1 1 0 0 1-1-1zm-.5 2a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V11a.5.5 0 0 0 1 0V9.5H10a.5.5 0 0 0 0-1H8.5V7z"/>
                            </svg>
                        </a>    
                    </li>
                </ul>
            </div>
        <!-- конец меню -->
        
            <div class="mx-auto p-1 col-12">
        <?php  
            //если есть переменная $_GET['mess'] и она имеет числовое значение
            //то отобразиться страница с деталями собщения
            //иначе отображается список сообщений с короткой информацией
            if (preg_match("/^[\d]+$/i", $_GET['mess'])) {
                //наличие переменной $_GET['edit'] подключит форму редактирования сообщения
                if (preg_match("/^[\d]+$/i", $_GET['edit'])) {

                    $messageEdit = new MessageEdit(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                    //наличие массива $_POST определяет, нужно ли обрабатывать данные
                    if (!$_POST) {
                        $messageEdit->renderingFormMessgeEdit($_GET['edit']);
                    } else {
                        $messageEdit->prepareMessgeEdit($_POST);
                    }

                } else {
                    //$_GET['remove'] - определяет производится ли удаление сообщения
                    if (preg_match("/^[\d]+$/i", $_GET['remove'])) {

                        $messageRem = new MessageRemove(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                        //если существует $_POST['removeid'] то удаление уже подтверждено
                        //иначе предупреждение о том что производится удаление
                        if (!$_POST['removeid']) {
                            $messageRem->renderingMessgeRemove($_GET['remove']);
                        } else {
                            $messageRem->actionMessgeRemove($_POST['removeid']);
                        }

                    } else {
                        //если есть переменная $_GET['mess']
                        //и нет переменных для редактирования или удаления сообщения
                        //то отображается сообщение с детальной информацией
                        $message = new MessageDetale(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                        $messageData = $message->getMessgeDetale($_GET['mess']);
                        $message->renderingMessgeDetale($messageData);        
                        //если сообщение найдено в базе и оно отображено
                        //отображаем комментарии и форму для ввода нового комментария
                        if ($messageData['id']) {
                            echo '
                                <div class="alert alert-info text-center my-2">
                                    Коммнтарии:
                                </div>
                            ';

                            $comments = new CommentsAdd($messageData['id'], HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                            //если есть переменные $_GET['addComment'] && $_POST
                            //то производится добавление нового комментария
                            if ($_GET['addComment'] && $_POST) {
                                $comments->prepareCommentAdd($_POST);                            
                            } else {
                                $commentsList = $comments->getCommentsList();
                                $comments->renderingCommentsList($commentsList);
                                $comments->renderingFormCommentAdd();
                            }
                        }
    
                    }

                }   

            } else {
                //если есть переменная $_GET['add']
                //то отображается форма для ввода нового сообщения
                //иначе отображается список сообщений с короткой информацией
                if ($_GET['add']) {

                    $messageAdd = new MessageAdd(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                    //если есть массив $_POST то данные уже введены
                    //производится добавление нового сообщения
                    if (!$_POST) {
                        $messageAdd->renderingFormMessgeAdd();
                    } else {
                        $messageAdd->prepareMessgeAdd($_POST);
                    }

                } else {
                    //отображается список сообщений с короткой информацией
                    $messages = new MessageList(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB, LIMIT_ROW);
                    //текущая страница пагинации
                    $currentPage = preg_match("/^[\d]+$/i", $_GET['page']) ? $_GET['page'] : 1;
                    $list = $messages->getMessgeList($currentPage);
                    echo '<ul class="list-group">';
                    //если в базе есть сообщения то они тображаются
                    //инче сообщение о том что нет данных
                    if ($messages->lengthRow > 0 && $messages->lastPage >= $currentPage) {
                        $messages->renderingMessageList($list);
                        //вывод пагинации сообщений
                        $messages->renderingPagination($currentPage);
                    } else {
                        echo '
                            <li class="list-group-item">
                                <div class="mx-auto col-12 text-center alert alert-warning">
                                    Нет данных!
                                </div>
                            </li>
                        ';
                    }

                    echo '</ul>';

                }

            }
        ?>
            </div>
        </div>
    </body>
</html>