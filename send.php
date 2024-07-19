<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключаем PHPMailer
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $firstname = $_POST['firstname'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $involvement = $_POST['involvement'];
    $role = $_POST['role'];
    $statement = $_POST['statement'];
    $custom_statement = isset($_POST['statement_custom']) ? $_POST['statement_custom'] : '';

    // Проверяем, какой тезис был выбран
    switch ($statement) {
        case 'interested-with-experience':
            $statement_text = 'Мне интересна эта сфера, есть опыт предпринимательства, хочу создать свою компанию в ДипТехе';
            break;
        case 'science-business-experience':
            $statement_text = 'У меня есть опыт в наукоёмком бизнесе, заинтересован рассмотреть возможность создания своего высокотехнологического стартапа';
            break;
        case 'no-experience-but-interested':
            $statement_text = 'У меня нет предпринимательского опыта, но очень интересно обучиться и создать свой ДипТех стартап';
            break;
        case 'curiosity':
            $statement_text = 'Меня сюда привело любопытство, хочу разобраться, что к чему';
            break;
        case 'custom-option':
            $statement_text = 'Другое: ' . $custom_statement;
            break;
        default:
            $statement_text = '';
            break;
    }

    // Проверяем, какой уровень вовлечённости был выбран
    switch ($involvement) {
        case 'less-than-10':
            $involvement_text = 'Готов уделять менее 10 часов в неделю';
            break;
        case '10-30-hours':
            $involvement_text = '10-30 часов в неделю';
            break;
        case 'around-30-hours':
            $involvement_text = 'Около 30 часов в неделю';
            break;
        case 'as-needed':
            $involvement_text = 'Столько, сколько окажется необходимым';
            break;
        default:
            $involvement_text = '';
            break;
    }

    // Проверяем, какая роль была   
    switch ($role) {
        case 'co-founder':
            $role_text = 'Со-основатель, участник команды';
            break;
        case 'advisor':
            $role_text = 'Советник';
            break;
        case 'leader':
            $role_text = 'Лидер';
            break;
        // case 'as-needed':
        //     $role_text = 'Столько, сколько окажется необходимым';
        //     break;
        default:
            $role_text = '';
            break;
    }

    // Создаем новое сообщение
    $mail = new PHPMailer(true);

    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.mail.ru';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'deeptech@rusnatt.ru';
        $mail->Password   = 'A2hBMKsNB0Dcz6c23u2g';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Устанавливаем кодировку
        $mail->CharSet = 'UTF-8';

        // Адрес и имя отправителя
        $mail->setFrom('deeptech@rusnatt.ru', 'Имя отправителя');

        // Получатель письма
        $mail->addAddress('deeptech@rusnatt.ru');

        // Тема письма
        $mail->Subject = 'Заявка на бронирование места';

        // Тело письма
        $mailContent = "
            <h2>Забронировать место</h2>
            <p><strong>Фамилия:</strong> $firstname</p>
            <p><strong>Имя:</strong> $name</p>
            <p><strong>E-mail:</strong> $email</p>
            <p><strong>Телефон:</strong> $phone</p>
            <p><strong>Уровень вовлечённости:</strong> $involvement_text</p>
            <p><strong>Роль:</strong> $role_text</p>
            <p><strong>Тезис:</strong> $statement_text</p>
        ";
        file_put_contents('secret/list.php', '<hr>' . date(DATE_RFC822) . '<br>' . $mailContent, FILE_APPEND);
        $mail->Body = $mailContent;
        $mail->isHTML(true);

        // Отправка письма
        $mail->send();

        // Отправляем ответ клиенту в виде JSON
        $response = array('status' => 'success', 'message' => 'Письмо отправлено');
        echo json_encode($response);
    } catch (Exception $e) {
        // Ошибка при отправке
        $response = array('status' => 'error', 'message' => "Письмо не может быть отправлено. Ошибка: {$mail->ErrorInfo}");
        echo json_encode($response);
    }
} else {
    // Некорректный метод отправки формы
    $response = array('status' => 'error', 'message' => 'Некорректный метод отправки формы');
    echo json_encode($response);
}
?>
