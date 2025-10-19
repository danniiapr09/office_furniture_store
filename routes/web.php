<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

Route::get('/send-test-mail', function () {
    // Ganti dengan email yang Anda gunakan untuk register (dani09@gmail.com)
    Mail::to('dani09@gmail.com')->send(new TestMail());
    return 'Test email sent (hopefully) to Mailtrap!';
});
