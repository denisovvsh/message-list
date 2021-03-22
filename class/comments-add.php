<?php
class CommentsAdd extends CommentsList {
    
    function __construct($idMessage, $HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($idMessage, $HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
    }
    //отображение формы добавления комментария
    public function renderingFormCommentAdd($data=false, $err=false) {
        echo '<div class="mx-auto col-12 text-center alert alert-success my-1">
                Новоый комментарий
            </div>';

        if ($err) {
            echo '<div class="mx-auto col-12 text-center alert alert-warning">
                '.$err.'
            </div>';
        }
        
        if (!$date) { 
            $date = array('name' => '', 'lastname' => '', 'text' => ''); 
        }

        printf('
            <div class="mx-auto col-12 m-2">
                <form class="col-12 m-2" action="./index.php?mess=%d&addComment=true" method="post">
                    <div class="form-group">
                        <label>Имя автора</label>
                        <input type="text" class="form-control" name="name" value="%s">
                    </div>
                    <div class="form-group">
                        <label>Фамилия автора</label>
                        <input type="text" class="form-control" name="lastname" value="%s">
                    </div>
                    <div class="form-group">
                        <label>Комментарий</label>
                        <textarea class="form-control" name="text" rows="3">%s</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary m-1">Отправить</button>
                </form>
            </div>
        ', $this->idMessage, $data['name'], $data['lastname'], $data['text']);
    
    }
    //подготовка данных нового комментария
    public function prepareCommentAdd($data) {
        $name = trim($data['name']);
        if (empty($name)) {
            $commentsList = $this->getCommentsList();
            $this->renderingCommentsList($commentsList);
            $err = 'Поле Имя автора пустое!';
            $this->renderingFormCommentAdd($data, $err);
            exit();
        }
        
        $lastname = trim($data['lastname']);
        if (empty($lastname)) {
            $commentsList = $this->getCommentsList();
            $this->renderingCommentsList($commentsList);
            $err = 'Поле Фамилия автора пустое!';
            $this->renderingFormCommentAdd($data, $err);
            exit();
        }

        $text = trim($data['text']);
        if (empty($text)) {
            $commentsList = $this->getCommentsList();
            $this->renderingCommentsList($commentsList);
            $err = 'Поле Комментарий пустое!';
            $this->renderingFormCommentAdd($data, $err);
            exit();
        }

        $prepareArr = array(
            'name' => $name,
            'lastname' => $lastname,
            'text' => $text,
        ); 
        
        if ($this->insertComment($prepareArr)) {
            $this->lengthComments = $this->getCountComments();
            $commentsList = $this->getCommentsList();
            $this->renderingCommentsList($commentsList);
            $this->renderingFormCommentAdd();
        } else {
            $commentsList = $this->getCommentsList();
            $this->renderingCommentsList($commentsList);
            $err = 'Ошибка записи комментария!';
            $this->renderingFormCommentAdd($data, $err);
        }

    }
    //запись в базу нового комментария
    private function insertComment($data) {
        $id_author = $this->checkAuthor($data['name'], $data['lastname']);
        if (!$id_author) {
            return false;
            exit();
        }
        
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }
        
        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query_insert_comments = "INSERT INTO comments VALUES (NULL, ?, ?, ?)";
            $stmt->prepare($query_insert_comments);
            $stmt->bind_param('iis', $this->idMessage, $id_author, $data['text']);
            $stmt->execute();
            $res = $stmt->affected_rows;
            $stmt->close();
        }
        $mysqli->close();

        return ($res) ? $res : false;
    }
    
}


?>