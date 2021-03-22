<?php
class MessageEdit extends MessageDetale {
    
    function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
    }
    //отображается форма редактирования сообщения
    public function renderingFormMessgeEdit($id, $data=false, $err=false, $success=false) {
        echo '<div class="mx-auto col-12 text-center alert alert-primary">
                Редактирование сообщения
            </div>';

        if ($err) {
            echo '<div class="mx-auto col-12 text-center alert alert-warning">
                '.$err.'
            </div>';
        }
        
        if ($success) {
            echo '<div class="mx-auto col-12 text-center alert alert-success">
                '.$success.'
            </div>';
        }
        //если была нажата кнопка Сохранить и не прошла валидация
        //то в форме отображаются переданные данные
        //если переданных данных нет, то происходит запрос из базы
        //чтобы заполнить форму редактирования
        if (!$data) {
            $data = $this->getMessgeDetale($id);
        }

        if ($data) {
            printf('
                <div class="mx-auto col-12 m-2">
                    <form class="col-12 m-2" action="./index.php?mess=%d&edit=%d" method="post">
                        <div class="form-group">
                            <label>Заголовок</label>
                            <input type="text" class="form-control" name="title" value="%s">
                        </div>
                        <div class="form-group">
                            <label>Имя автора</label>
                            <input type="text" class="form-control" name="name" value="%s">
                        </div>
                        <div class="form-group">
                            <label>Фамилия автора</label>
                            <input type="text" class="form-control" name="lastname" value="%s">
                        </div>
                        <div class="form-group">
                            <label>Краткий текст</label>
                            <input type="text" class="form-control" name="excerpt" value="%s">
                        </div>
                        <div class="form-group">
                            <label>Полный текст</label>
                            <textarea class="form-control" name="text" rows="3">%s</textarea>
                        </div>
                        <input type="hidden" name="id" value="%d">
                        
                        <button type="submit" class="btn btn-primary m-1">Сохранить</button>
                        <a class="btn btn-secondary m-1" href="index.php?mess=%d">Отмена</a>
                    </form>
                </div>
            ', $data['id'], $data['id'], $data['title'], $data['name'], $data['lastname'], $data['excerpt'], $data['text'], $data['id'], $data['id']);
        } else {
            printf('
                <div class="mx-auto col-12 text-center alert alert-warning">
                    Нет данных!
                </div>
            ');
        }
        
    }
    //подготовка данных перед сохранением в базу
    public function prepareMessgeEdit($data) {
        $title = trim($data['title']);
        if (empty($title)) {
            $err = 'Поле Заголовок пустое!';
            $this->renderingFormMessgeEdit($data['id'], $data, $err);
            exit();
        }

        $name = trim($data['name']);
        if (empty($name)) {
            $err = 'Поле Имя автора пустое!';
            $this->renderingFormMessgeEdit($data['id'], $data, $err);
            exit();
        }
        
        $lastname = trim($data['lastname']);
        if (empty($lastname)) {
            $err = 'Поле Фамилия автора пустое!';
            $this->renderingFormMessgeEdit($data['id'], $data, $err);
            exit();
        }
        
        $excerpt = trim($data['excerpt']);
        if (empty($excerpt)) {
            $err = 'Поле Краткий текст пустое!';
            $this->renderingFormMessgeEdit($data['id'], $data, $err);
            exit();
        }
        
        $text = trim($data['text']);
        if (empty($text)) {
            $err = 'Поле Полный текст пустое!';
            $this->renderingFormMessgeEdit($data['id'], $data, $err);
            exit();
        }

        $prepareArr = array(
            'title' => $title,
            'name' => $name,
            'lastname' => $lastname,
            'excerpt' => $excerpt,
            'text' => $text,
            'id' => $data['id']
        );

        if ($this->updateMessge($prepareArr)) $success = 'Данные успешно сохранены!';

        $this->renderingFormMessgeEdit($data['id'], false, false, $success);

    }
    //сохранение данных в базу
    private function updateMessge($data) {
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
            $query = "UPDATE messages SET messages.title=?, messages.id_author=?, messages.excerpt=?, messages.text=? WHERE messages.id_message=?";
            $stmt->prepare($query);
            $stmt->bind_param('sissi', $data['title'], $id_author, $data['excerpt'], $data['text'], $data['id']);
            $stmt->execute();
            $res = $stmt->affected_rows;
            $stmt->close();
        }
        $mysqli->close();

        return ($res) ? $res : false;
    }
    
}


?>