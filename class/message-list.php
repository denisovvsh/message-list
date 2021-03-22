<?php

class MessageList {
    protected $HOST_DB;
    protected $USER_NAME_DB;
    protected $PASS_DB;
    protected $NAME_DB;
    protected $LIMIT_ROW;
    public $lengthRow;
    public $lastPage;

    function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB, $LIMIT_ROW) {
        $this->HOST_DB = $HOST_DB; 
        $this->USER_NAME_DB = $USER_NAME_DB; 
        $this->PASS_DB = $PASS_DB;
        $this->NAME_DB = $NAME_DB;
        $this->LIMIT_ROW = $LIMIT_ROW;
        $this->lengthRow = $this->getCountRow();
    }
    //получаем количество строк в таблице messages
    private function getCountRow() {
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if ($mysqli->connect_errno) {
            printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
            exit();
        }
        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            $query_count = "SELECT COUNT(*) AS _num FROM messages;";
            $stmt->prepare($query_count);
            $stmt->execute();
            $stmt->bind_result($lengthRow);
            $stmt->fetch();
            $stmt->close();
        }
        $mysqli->close();

        $this->lastPage = ceil($lengthRow / $this->LIMIT_ROW);

        return $lengthRow;
    }
    //получаем список сообщений с короткой информацией
    //с условиями пагинации и лимитом строк на странице
    public function getMessgeList($currentPage) {
         
        $mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
        if (mysqli_connect_errno()) {
            return mysqli_connect_error();
            exit();
        }

        $arrForResult = array();

        $stmt = $mysqli->stmt_init();
        if ($stmt) {
            //первая строка для запроса строк
            $numRow = ($currentPage == 1) ? 0 : ($currentPage - 1) * $this->LIMIT_ROW; 
            //до какой строки делать запросы
            $limitRow = (($this->lengthRow - $numRow) < $this->LIMIT_ROW) ? ($numRow + ($this->lengthRow - $numRow)) : ($numRow + $this->LIMIT_ROW);

            $query = "SELECT messages.id_message, messages.title, messages.excerpt 
            FROM messages 
            ORDER BY messages.id_message DESC LIMIT ?, 1;";

            $stmt->prepare($query);
            
            for ( $i = $numRow; $i < $limitRow; $i++ ) {
                $stmt->bind_param('i', $i);
                $stmt->execute();
                $stmt->bind_result($id_message, $title, $excerpt);
                $stmt->fetch();
                
                $data = array(
                    'id' => $id_message, 
                    'title' => $title, 
                    'excerpt' => $excerpt
                );
                
                array_push($arrForResult, $data);
            }
            
            $stmt->close();
        }
        $mysqli->close();

        return $arrForResult;
    }
    //вывод списка строк
    public function renderingMessageList($list) {
        foreach ($list as $key => $value) {
            printf(
                '<li class="list-group-item my-1">
                    <h5>%s</h5>
                    <div class="alert alert-secondary">
                        <p class="m-0">%s</p>
                        <a href="./index.php?mess=%d">читать сообщение</a>
                    </div>
                </li>'
            , $value['title'], $value['excerpt'], $value['id']);
        }   
    }
    //отображение кнопок пагинации - расчет исходя из текущей страницы
    //пагинация предусматривает кнопки:
    //на первую, на последнюю, диапазон из двух соседних, от текущей,
    //переход к следующей или предыдущей от крайнего соседа в диапазоне
    public function renderingPagination($currentPage) {
        //если последняя страница меньше еденицы
        //или текущая больше последней (т.к. стр. передается в $_GET)
        //то пагинация не отображается
        if ($this->lastPage <= 1 || $currentPage > $this->lastPage) exit();

        echo '
            <nav>
                <ul class="pagination justify-content-center my-2">
        ';

        if ($currentPage >= 3) {
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php">%d</a>
                </li>
            ', 1);
        }

        if ($currentPage > 3) {
            $prev = $currentPage - 2; 
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php?page=%d" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                    </a>
                </li>
            ', $prev);
        }

        $p = $currentPage;
        if ($currentPage > 1) {
            $p = ($currentPage <= 1) ? 1 : --$p;
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php?page=%d">%d</a>
                </li>
            ', $p, $p);
        }

        $p = $currentPage;
        printf('
            <li class="page-item active">
                <a class="page-link" href="./index.php?page=%d">%d</a>
            </li>
        ', $p, $p); 

        if ($currentPage < $this->lastPage) {
            $p++;
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php?page=%d">%d</a>
                </li>
            ', $p, $p);
        } 

        $p++;
        if ($p < $this->lastPage) {
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php?page=%d" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                    </a>
                </li>
            ', $p);
        }

        if ($currentPage < ($this->lastPage - 1)) {
            printf('
                <li class="page-item">
                    <a class="page-link" href="./index.php?page=%d">%d</a>
                </li>
            ', $this->lastPage, $this->lastPage);
        }

        echo '
                </ul>
            </nav>
        ';
    }
}


?>