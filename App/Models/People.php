<?php
/**
*класс для работы со списками людей
*конструктор заполняет свойство $peopleID значениями ID из базы данных
*подробное описание каждого метода указано над самим методом
*/

require "databaseConfig.php";

class People
{
  private $peopleID = [];

  public function __construct()
  {
    //подключаемся к базе данных для поиска записи по id
    $mysqli = new \mysqli(HOST, USERNAME, USERPASSWORD, DATABASENAME);
    // проверка соединения
    if ($mysqli->connect_errno) {
      printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
      exit();
    }
    $query = "SELECT `id` FROM `people`";
    if ($result = $mysqli->query($query)) {
      while ($row = $result->fetch_assoc()) {
        //заполняем свойство полученными id с приведением к типу integer
        $this->peopleID[] = (int) $row['id'];
      }
      /* очищаем результирующий набор */
      $result->close();
    } else {
      echo "База данных не содержит пользователей";
    }
    $mysqli->close();
    //var_dump($this->peopleID);
  }

  //возвращает массив с объектами Person, по полученным в конструкторе ID
  public function getPeople()
  {
    $result = [];
    foreach ($this->peopleID as $value) {
      $result[] = new Person($value);
    }
    return $result;
  }

  /*удаление пользователей. Получаем массив с объектами Person,
  затем удаляем каждого отдельно
  */
  public function deletePeople()
  {
    $people = $this->getPeople();
    foreach ($people as $person) {
      $person->delete();
    }
  }

}
