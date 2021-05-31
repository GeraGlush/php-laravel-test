https://laravel.com/
Модули: https://github.com/nWidart/laravel-modules

Версия PHP: 8.0, Laravel 8

Общие требования для заданий:

1. По максимум используем возможности php 8.0 что-бы сократить кол-во кода.
2. По максимум используем возможности laravel ^8.4 что-бы сократить кол-во кода.
3. ./vendor/bin/phpstan analyse не должен ругаться.
4. В каждом нашем файле должен быть включён строгий режим <?php declare(strict_types=1);

   4.1 И в каждом генерируемом нами файле через ``make:`` (только те что используем в проекте, все не надо);
5. Код по возможности требуется покрывать простыми тестами;

**Задание 1. Создать модуль Api.**

У модуля должны быть только папки **Services, Console, Config**. Модуль должен уметь обращаться к
апи https://api.cargo.tech/*, например к https://api.cargo.tech/v1/cargos (реализовать только GET). Данные надо отдавать
ларавел коллекцией. Модуль должен иметь возможность получить данные с помощью limit и offset (все или например первые 3
страницы). Например, если в апи 3000 записей, макс. лимит на страницу 100 записей, нам надо сделать 100 * 30 запросов к
апи что-бы получить все данные.

```php
\Modules\Api\Services\ClientService()->get('/cargos');
```
**Задание 2. Создать модуль Cargo.**

У модуля должны быть только папки **Services, Console, Http, Config, Models, Events, Listeners, Notifications**.

С помощью модуля API раз в 5 минут мы должны обращаться к апи, забирать первые 2 страницы данных, добавлять их в базу
через модель Cargo (нужные поля ниже).

Если груза еще нет, создаем cargo, отправляем событие **CargoCreatedEvent**, слушатель (через очередь) должен отправить
уведомление на email (любой из конфига, а там он из .env). Если груза есть, обновляем cargo, отправляем событие **
CargoUpdatedEvent**, слушатель должен через 5 минут удалить груз и отправить уведомление об этом на тот же email.

Информацию о количестве грузов держать в отдельной таблице (Например Counters, table=cargos, count=10), обновлять инфу
через события модели Cargo (created, deleted), **НЕ ЧЕРЕЗ boot** самой модели.

Создать контроллер для Cargo модели, реализовать простой круд (для store/update использовать FormRequest).

index() crud должен иметь возможность пагинации через offset + limit

Поля из апи которые надо использовать:

- id
- weight
- volume
- truck

Truck должен быть jsonb полем, с кастомным cast https://laravel.com/docs/8.x/eloquent-mutators#custom-casts

Т.е. при записи поля в базу оно должно быть строкой (json_encode), при чтении поле должно возвращать объект TruckCast
или TruckDto (можешь использовать пакет spatie dto).

Пример с кодом:

```php
Cargo::create(['truck' => ['tir' => true]]);// работает, на вход передаем массив, сеттер должен учесть это

$object = new TruckDto(['tir' => true]);
Cargo::create(['truck' => $object]); // работает, на вход передаем объект

class TruckDtoExample
{
	public bool $tir = false;
	public bool $t1 = false;
	public bool $cmr = false;
}
```

**Дополнительно.**

- Нам надо иметь возможность осуществлять операции >< с полем truck.belt_count.
- Нам надо иметь возможность искать какое-либо значение в поле ``truck`` (jsonb) с помощью @>.
- Запаковать приложение в контейнер докера (php-fpm), + сделать контейнер с nginx + конфиг для домена ``test.com``, и
  сделать к ним docker-compose.
