<?php
/**
*файл с подключенными классами
*параметры базы данных храняться в файле App\Models\databaseConfig.php
*/

//use App\Models\Person as Person;
require "App/Models/Person.php"; //require используется т.к. нет автозагрузки классов
require "App/Models/People.php";

$userData = ['id' => 1, 'firstName' => 'First', 'lastName' => 'Last', 'birthday' => '04.02.1950', 'sex' => 1, 'cityOfBirth' => 'Minsk'];
$userData2 = ['id' => 2, 'firstName' => 'second', 'lastName' => 'Last', 'birthday' => '04.12.1980', 'sex' => 1, 'cityOfBirth' => 'Moskow'];

//создание экземпляра класса с массивом параметров и сохранением в базу данных
$person = new Person($userData);
$person2 = new Person($userData2);

echo "<hr>";
//создание экземпляра класса с поиском id в базе данных
echo "создание экземпляра класса с поиском id в базе данных <br>";
$n2 = new Person(1);
var_dump($n2);

echo "<hr>";

//Удаление человека из БД в соответствии с id объекта
//$n2->delete(1);

echo "<hr>";

//static преобразование даты рождения в возраст (полных лет)
echo Person::birthdayToAge('01.01.1986');

echo "<hr>";

//static преобразование пола из двоичной системы в текстовую (муж, жен)
echo Person::sexToText(0);

echo "<hr>";

//Форматирование человека с преобразованием возраста и (или) пола
echo "Форматирование человека с преобразованием возраста и (или) пола: <br>";
var_dump($person->formatting(true, true));

echo "<hr>";

//класс для работы со списками людей
$people = new People;

//Получение массива экземпляров класса Person из массива с id людей полученного в конструкторе
echo "Получение массива экземпляров класса Person из массива с id людей полученного в конструкторе: <br>";
var_dump($people->getPeople());

//Удаление людей из БД с помощью экземпляров класса 1 в соответствии с массивом, полученным в конструкторе.
//$people->deletePeople();
