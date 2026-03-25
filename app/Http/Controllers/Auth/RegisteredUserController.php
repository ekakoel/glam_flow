<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SubscriptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $plan = request()->query('plan', Subscription::PLAN_FREE);
        $allowedPlans = [Subscription::PLAN_FREE, Subscription::PLAN_PRO, Subscription::PLAN_PREMIUM];
        if (! in_array($plan, $allowedPlans, true)) {
            $plan = Subscription::PLAN_FREE;
        }

        return view('auth.register', [
            'selectedPlan' => $plan,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $plan = $request->input('plan', $request->query('plan', Subscription::PLAN_FREE));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => ['required', Rule::in([
                Subscription::PLAN_FREE,
                Subscription::PLAN_PRO,
                Subscription::PLAN_PREMIUM,
            ])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $subscription = app(SubscriptionService::class)->createTrialForUser($user->id, $plan, 7);
        app(NotificationService::class)->sendWelcomeMessage($user, $subscription);

        return redirect('/onboarding');
    }
}
