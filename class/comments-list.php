<?php

class CommentsList extends MessageDetale {
    
    protected $lengthComments = 0;
    protected $idMessage;

    function __construct($idMessage, $HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW);
        $this->idMessage = $idMessage;
        $this->lengthComments = $this->getCountComments();
    }
    //получает количество комментариев, которые относятся к сообщению
    protected function getCountComments() {
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if ($mysqli->connect_errno) {
            printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
            exit();
        }

        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query_count = "SELECT COUNT(*) AS _num FROM comments WHERE comments.id_message = ?;";
            $stmt->prepare($query_count);
            $stmt->bind_param('i', $this->idMessage);
            $stmt->execute();
            $stmt->bind_result($lengthRow);
            $stmt->fetch();
            $stmt->close();
        }
        $mysqli->close();

        return $lengthRow;
    }
    //получает список всех комментариев, которые относятся к сообщению
    //в комментариях не реализована пагинация, поэтому нет условий
    public function getCommentsList() {
         
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }

        $arrForResult = array();

        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query = "SELECT comments.text, authors.name, authors.lastname 
            FROM comments 
            INNER JOIN authors 
            ON comments.id_author = authors.id_author 
            INNER JOIN messages 
            ON comments.id_message = messages.id_message 
            WHERE comments.id_message = ? 
            ORDER BY comments.id_comment DESC LIMIT ?, 1;";

            $stmt->prepare($query);
            
            for ( $i = 0; $i < $this->lengthComments; $i++ ) {
                $stmt->bind_param('ii', $this->idMessage, $i);
                $stmt->execute();
                $stmt->bind_result($text, $name, $lastname);
                $stmt->fetch();
                
                $data = array(
                    'text' => $text, 
                    'name' => $name, 
                    'lastname' => $lastname
                );
                
                array_push($arrForResult, $data);
            }
            
            $stmt->close();
        }
        $mysqli->close();

        return ($this->lengthComments > 0) ? $arrForResult : false;
    }
    //отображение списка комментариев
    public function renderingCommentsList($list) {
        echo '<ul class="list-group">';
                                
            if ($list) {
                foreach ($list as $key => $value) {
                    printf(
                        '<li class="list-group-item my-1">
                            <div class="alert alert-info">
                                <small class="text-muted">
                                    <b>Автор: </b>%s %s
                                </small>
                                <p class="m-0">%s</p>
                            </div>
                        </li>'
                    , $value['name'], $value['lastname'], $value['text']);
                } 
            } else {
                echo '
                    <li class="list-group-item">
                        <div class="mx-auto col-12 text-center alert alert-warning">
                            Нет комментариев!
                        </div>
                    </li>
                ';
            }
            
        echo '</ul>';
          
    }

}


?>