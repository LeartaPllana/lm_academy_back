

<?php

use Illuminate\Http\Request;

    use App\Http\Controllers\AuthController;
    use App\Mail\TestMail;
    use GuzzleHttp\Middleware;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Route;

    Route::controller(AuthController::class)->prefix("auth")->middleware('api')->group(function () {
        Route::post('login', 'login')->name('auth.login');
        Route::post('register', 'register')->name('auth.register');
        Route::post('refresh', 'refresh')->name('auth.refresh');

        Route::middleware('jwt.auth.token', )->group(function () {
            Route::post('logout', 'logout')->name('auth.logout');
            Route::get('user-profile', 'userProfile')->name('auth.user-profile');
            Route::post('send-registration-invite', 'sendRegistrationInvite')->name('auth.sendRegistrationInvite');

        });

    });

    // Route::get('test-api-endpoint', function(){
    //     return response()->json(['message'=> 'API endpoint is working']);
    // });

    Route::post('test-mail-sent', function (Request $request) {
        try {
            $mailData = [
                'title'  => 'Email Title',
                'message' => 'This is a test e-mail directed to only students of Lutfi Musiqi High School.',
                'session_title'  => $request->session_title
            ];
           
            Mail::to('leartapllana9@gmail.com')->send(new TestMail($mailData));
            return response()->json('success');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => $e->getCode(), 
                    'message' => $e->getMessage(),
                    'type' => class_basename($e)
                ]
            ], 500);
        }

});
