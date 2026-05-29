<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthLoginSecurityTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_login_uses_remember_me_flag_and_admin_role_alias(): void
    {
        $user = new User(['rules' => 'admin']);

        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'username' => 'admin_test',
                'password' => 'secret123',
            ], true)
            ->andReturn(true);

        Auth::shouldReceive('user')->once()->andReturn($user);

        $request = Request::create('/login', 'POST', [
            'username' => 'admin_test',
            'password' => 'secret123',
            'captcha' => 7,
            'remember' => 'on',
        ]);

        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('captcha_answer', 7);

        $response = (new AuthController())->login($request);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('dashboard_admin', $response->headers->get('Location', ''));
        $this->assertSame('admin', $user->role);
    }
}
