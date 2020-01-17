@component('mail::message')
@component('mail::panel')
# 工作通温馨提醒:
{{$data['message']}}
感谢使用,<br>
{{ config('app.name') }}
@endcomponent
