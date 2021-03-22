<?php
class MessageRemove extends MessageDetale {
    
    function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
    }
    //отображение формы подтверждающей удаление сообщения
    public function renderingMessgeRemove($id, $err=false, $success=false) {
        if ($err) {
            echo '<div class="mx-auto col-12 text-center alert alert-warning">
                '.$err.'
            </div>';
        }
        
        if ($success) {
            echo '<div class="mx-auto col-12 text-center alert alert-success">
                '.$success.'<br>
                <a class="btn btn-primary" href="./index.php">Вернуться к списку сообщений</a>
            </div>';
        } else {
            $data = $this->getMessgeDetale($id);
            if ($data) {
                printf('
                    <div class="mx-auto col-12 m-2">
                        <div class="mx-auto col-12 text-center alert alert-warning">
                            Подтвердите Уделние сообщения - %s
                        </div>
                        <form class="col-12 m-2" action="./index.php?mess=%d&remove=%d" method="post">
                            <input type="hidden" name="removeid" value="%d">
                            <button type="submit" class="btn btn-danger m-1">Удалить</button>
                            <a class="btn btn-secondary m-1" href="index.php?mess=%d">Отмена</a>
                        </form>
                    </div>
                ', $data['title'], $id, $id, $id, $id);
            } else {
                printf('
                    <div class="mx-auto col-12 text-center alert alert-warning">
                        Нет данных!
                    </div>
                ');
            }   
        }
    }
    //удаление сообщения
    public function actionMessgeRemove($id) {
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }

        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query = "DELETE FROM messages WHERE messages.id_message = ?";
            $stmt->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->affected_rows;
            $stmt->close();
        }
        $mysqli->close();
        
        if ($res) {
            $success = 'Сообщение удалено успешно!';
            $this->renderingMessgeRemove($id, false, $success);
        } else {
            $err = 'Ошибка удаления сообщения!';
            $this->renderingMessgeRemove($id, $err, false);
        }
    
    }
    
}


?>