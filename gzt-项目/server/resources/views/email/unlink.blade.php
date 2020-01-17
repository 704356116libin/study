@component('mail::message')
@component('mail::panel')
# 邮箱解绑
您收到此电子邮件，因为您注册了我们的平台。
@endcomponent
邮箱解绑链接请点击

@component('mail::button', ['url' => route('email_unlink', $token)])
点击解绑邮箱
@endcomponent

如果没有注册我们的账户，请忽略。
@component('mail::panel')
如果您点击“激活邮箱”按钮无效，复制并粘贴下面的网址到您的网页浏览器：
{{ route('email_unlink',$token) }}
@endcomponent
感谢使用,<br>
{{ config('app.name') }}
@endcomponent
