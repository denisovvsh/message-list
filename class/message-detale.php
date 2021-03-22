<?php
class MessageDetale extends MessageList {
    
    function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
    }
    //получение подробных данных сообщения
    public function getMessgeDetale($id) {
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }

        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query = "SELECT messages.id_message, messages.title, messages.excerpt, messages.text, authors.name, authors.lastname 
            FROM messages 
            INNER JOIN authors 
            ON messages.id_author = authors.id_author 
            WHERE messages.id_message = ? LIMIT 0, 1;";
            $stmt->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($id_message, $title, $excerpt, $text, $name, $lastname);
            $stmt->fetch();
            $data = array(
                'id' => $id_message, 
                'title' => $title, 
                'excerpt' => $excerpt, 
                'text' => $text, 
                'name' => $name, 
                'lastname' => $lastname
            );
            $stmt->close();
        }
        $mysqli->close();
        return $id_message ? $data : false;
    }
    //проверка автора сообщения - используется при добавлении, редактировании сообщения
    //а также при добавлении комментария
    //т.к. нет аутентификации, то проверяется имя и фамилия на уникальность
    //один и тот же автор может писать сообщения и добавлять комментарии
    protected function checkAuthor($name, $lastname) {
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }
        
        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query_select_authors = "SELECT authors.id_author FROM authors WHERE authors.name = ? AND authors.lastname = ? LIMIT 0, 1";
            $stmt->prepare($query_select_authors);
            $stmt->bind_param('ss', $name, $lastname);
            $stmt->execute();
            $stmt->bind_result($id_author);
            $stmt->fetch();
            if ($id_author) {
                $res = $id_author;
            } else {
                $query = "INSERT INTO authors VALUES (NULL, ?, ?)";
                $stmt->prepare($query);
                $stmt->bind_param('ss', $name, $lastname);
                $stmt->execute();
                $res = $stmt->affected_rows;
                if ($res) {
                    $stmt->prepare($query_select_authors);
                    $stmt->bind_param('ss', $name, $lastname);
                    $stmt->execute();
                    $stmt->bind_result($id_author);
                    $stmt->fetch();
                    $res = $id_author;
                }
            }
                  
            $stmt->close();
        }
        $mysqli->close();

        return ($res) ? $res : false;
    }
    //отображение карточки сообщения с детальной информацией
    public function renderingMessgeDetale($data) {
        if ($data) {
            printf('
                <div class="card mx-auto col-12">
                    <div class="card-header">%s</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <p>%s</p>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted">
                                <b>Автор: </b>%s %s
                            </small>
                        </li>
                    </ul>
                    <div class="card-footer">
                        <a href="./index.php?mess=%d&edit=%d" class="btn btn-primary m-1">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>
                        <a href="./index.php?mess=%d&remove=%d" class="btn btn-danger m-1">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            ', $data['title'], $data['text'], $data['name'], $data['lastname'], $data['id'], $data['id'], $data['id'], $data['id']);
        } else {
            printf('
                <div class="mx-auto col-12 text-center alert alert-warning">
                    Нет данных!
                </div>
            ');
        }
        
    }

    
}


?>