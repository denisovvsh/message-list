<?php
class MessageAdd extends MessageDetale {
    
    function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
    }
    //отображение формы для добавлнеия нового сообщения
    public function renderingFormMessgeAdd($data=false, $err=false) {
        echo '<div class="mx-auto col-12 text-center alert alert-primary">
                Новое сообщение
            </div>';

        if ($err) {
            echo '<div class="mx-auto col-12 text-center alert alert-warning">
                '.$err.'
            </div>';
        }
        //по умолчанию данные в форме пусты
        //если при попытке отправить данные, они не пройдут валидацию
        //то форма заполнится теми данными
        if (!$date) { 
            $date = array('title' => '', 'name' => '', 'lastname' => '', 'excerpt' => '', 'text' => ''); 
        }

        printf('
            <div class="mx-auto col-12 m-2">
                <form class="col-12 m-2" action="./index.php?add=true" method="post">
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
                    
                    <button type="submit" class="btn btn-primary m-1">Отправить</button>
                    <a class="btn btn-secondary m-1" href="./index.php">Отмена</a>
                </form>
            </div>
        ', $data['title'], $data['name'], $data['lastname'], $data['excerpt'], $data['text']);
    
    }
    //подготовка данных для сохранения в базе
    public function prepareMessgeAdd($data) {
        $title = trim($data['title']);
        if (empty($title)) {
            $err = 'Поле Заголовок пустое!';
            $this->renderingFormMessgeAdd($data, $err);
            exit();
        }
        $name = trim($data['name']);
        if (empty($name)) {
            $err = 'Поле Имя автора пустое!';
            $this->renderingFormMessgeAdd($data, $err);
            exit();
        }
        $lastname = trim($data['lastname']);
        if (empty($lastname)) {
            $err = 'Поле Фамилия автора пустое!';
            $this->renderingFormMessgeAdd($data, $err);
            exit();
        }
        $excerpt = trim($data['excerpt']);
        if (empty($excerpt)) {
            $err = 'Поле Краткий текст пустое!';
            $this->renderingFormMessgeAdd($data, $err);
            exit();
        }
        $text = trim($data['text']);
        if (empty($text)) {
            $err = 'Поле Полный текст пустое!';
            $this->renderingFormMessgeAdd($data, $err);
            exit();
        }

        $prepareArr = array(
            'title' => $title,
            'name' => $name,
            'lastname' => $lastname,
            'excerpt' => $excerpt,
            'text' => $text,
        ); 
        //если сохранение успешно, то отображаем список сообщений на первой странице
        //чтобы показать новое сообщение
        if ($this->insertMessge($prepareArr)) {
            $list = $this->getMessgeList(1);
            $this->renderingMessageList($list);
            $this->renderingPagination(1);
        } else {
            $err = 'Ошибка записи сообщения!';
            $this->renderingFormMessgeAdd($data, $err);
        }

    }
    //сохранение данных в базе
    private function insertMessge($data) {
        //проверка автора и получение идентификатора
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
            $query_insert_messages = "INSERT INTO messages VALUES (NULL, ?, ?, ?, ?)";
            $stmt->prepare($query_insert_messages);
            $stmt->bind_param('siss', $data['title'], $id_author, $data['excerpt'], $data['text']);
            $stmt->execute();
            $res = $stmt->affected_rows;
            $stmt->close();
        }
        $mysqli->close();

        return ($res) ? $res : false;
    }
    
}


?>