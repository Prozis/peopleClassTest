<?php
/**
*класс для работы с базой данных людей
*в конструктор должно поступать либо число id либо массив параметров
*свойство birthday принимает дату рождения в формате '%d.%m.%Y и при запросе к базе данных
*преобразуется функцией STR_TO_DATE в формат mysql
*подробное описание каждого метода указано над самим методом
*/

//namespace App\Models;

require "databaseConfig.php";

class Person
{
    private $personData = [
        'id' => null,
        'firstName' => null,
        'lastName' => null,
        'birthday' => null,
        'sex' => null,
        'cityOfBirth' => null,
    ];

    //конструктор класса
    public function __construct($data)
    {
        //проверяем, что пришло в конструктор (число, массив параметров или ничего)
        if (is_int($data)) {
            //подключаемся к базе данных для поиска записи по id
            $mysqli = new \mysqli(HOST, USERNAME, USERPASSWORD, DATABASENAME);
            // проверка соединения
            if ($mysqli->connect_errno) {
                printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
                exit();
            }
            $query = "SELECT * FROM `people` WHERE `id` = '$data'";
            if ($result = $mysqli->query($query)) {
                $res = $result->fetch_assoc();
                $this->personData['id'] = $res['id'];
                $this->personData['firstName'] = $res['firstname'];
                $this->personData ['lastName'] = $res['lastname'];
                $this->personData['birthday'] = $res['birthday'];
                $this->personData['sex'] = $res['sex'];
                $this->personData['cityOfBirth'] = $res['cityofbirth'];
                /* очищаем результирующий набор */
                $result->close();
            } else {
                echo "Пользователь с ID $data не найден";//поступивший id в базе не найден
            }
            $mysqli->close();
        } else if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (array_key_exists($key, $this->personData)) {
                    $this->personData[$key] = $value;
                }
            }
            //сохраняем в базу данных
            $this->save();
        }
    }

    //Сохранение полей экземпляра класса в БД
    public function save()
    {
        //подключаемся к базе данных
        $mysqli = new \mysqli(HOST, USERNAME, USERPASSWORD, DATABASENAME);

        // проверка соединения
        if ($mysqli->connect_errno) {
            printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
            exit();
        }
        //валидация данных
        $id = htmlspecialchars(trim($this->personData['id']));
        $firstName = htmlspecialchars(trim($this->personData['firstName']));
        $lasttName = htmlspecialchars(trim($this->personData['lastName']));
        $birthday = htmlspecialchars(trim($this->personData['birthday']));
        $sex = htmlspecialchars(trim($this->personData['sex']));
        $sityOfBirth = htmlspecialchars(trim($this->personData['cityOfBirth']));

        $query = "INSERT INTO `people` (`id`, `firstname`, `lastname`, `birthday`, `sex`, `cityofbirth`)"
               . "VALUES ('$id', '$firstName', '$lasttName', STR_TO_DATE('$birthday','%d.%m.%Y'), '$sex', '$sityOfBirth')";

        if ($result = $mysqli->query($query)) {
            /* очищаем результирующий набор */
            //$result->close();
        } else {
            printf("Не удалось выполнить запрос: %s\n", $mysqli->error);
        }
        $mysqli->close();
    }

    //Удаление человека из БД в соответствии с id свойством объекта
    public function delete()
    {
      $mysqli = new \mysqli(HOST, USERNAME, USERPASSWORD, DATABASENAME);
      // проверка соединения
      if ($mysqli->connect_errno) {
          printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
          exit();
      }

      $id = $this->personData['id'];
      $query = "DELETE FROM `people` WHERE `id` = '$id'";
      //удаляются все записи с указанным user_id
      if ($result = $mysqli->query($query)) {
          echo "Record deleted successfully<br>";
      } else {
          echo "Error deleting record<br>";
      }
      $mysqli->close();
    }

    //static преобразование даты рождения в возраст (полных лет)
    public static function birthdayToAge($birthday)
    {
      $birthday_timestamp = strtotime($birthday);
      $age = date('Y') - date('Y', $birthday_timestamp);
      if (date('md', $birthday_timestamp) > date('md')) {
        $age--;
      }
      return $age;
    }

    //static преобразование пола из двоичной системы в текстовую (муж, жен)
    public static function sexToText($sex)
    {
        if($sex) {
          return "муж";
        } else {
          return "жен";
        }
    }

    /* метод принимает два параметра типа bool, определяющих, что будет форматироваться: возраст, пол
    или оба свойства. Полученный возраст помещяется в дополнительное свойстово 'age'
    Метод возвращает объект stdClass
    */
    public function formatting($age = false, $sex = false)
    {
      $result = new stdClass();
      $result->personData = $this->personData;
      if ($age) $result->personData['age'] = $this->birthdayToAge($this->personData['birthday']);
      if ($sex) $result->personData['sex'] = $this->sexToText($this->personData['sex']);
      return $result;
    }
}
