<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      .absolute_mail_body {
        font-family: "Arial", sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
      }
      .absolute_mail_container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }
      .absolute_mail_header {
        text-align: center;
        padding: 5px 0;
        border-bottom: 1px solid #eaeaea;
        height: fit-content;
      }
      .absolute_mail_logo {
        max-width: 180px;
        height: 75px;
        color: #d10026;
        fill: #d10026;
      }
      .absolute_mail_content {
        padding: 30px 20px;
        text-align: center;
      }
      .absolute_mail_code {
        display: inline-block;
        margin: 25px 0;
        padding: 12px 30px;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 2px;
        color: #3182ce;
        background-color: #ebf8ff;
        border-radius: 4px;
        border: 1px solid #bee3f8;
      }
      .absolute_mail_footer {
        text-align: center;
        padding: 20px;
        font-size: 14px;
        color: #718096;
        border-top: 1px solid #eaeaea;
      }
      .absolute_mail_button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #3182ce;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        margin-top: 20px;
      }
    </style>
  </head>
  <body class="absolute_mail_body">
    <div class="absolute_mail_container">
      <div class="absolute_mail_header">
        <img src="{{ asset('images/logo.svg') }}" alt="Абсолют Техно" class="absolute_mail_logo">
      </div>
      <div class="absolute_mail_content">
        <h1>Код подтверждения</h1>
        <p>Для завершения процесса авторизации, пожалуйста, используйте следующий код:</p>
        <div class="absolute_mail_code">{{ $code }}</div>
        <p>Этот код действителен в течение 15 минут.</p>
        <p>Если вы не запрашивали этот код, пожалуйста, проигнорируйте это письмо.</p>
      </div>
      <div class="absolute_mail_footer">
        <p>© {{ date('Y') }} Абсолют Техно. Все права защищены.</p>
      </div>
    </div>
  </body>
</html>
