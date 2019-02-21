@component('mail::message')
# Change password request

Click on the button below to change password.

@component('mail::button', ['url' => 'http://localhost:4200/reset/password/form?token='.$token])
Change Password
@endcomponent

Thanks,<br>
The Streamline Team
@endcomponent
