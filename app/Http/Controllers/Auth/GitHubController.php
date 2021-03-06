<?php

namespace App\Http\Controllers\Auth;

use App\Contact;
use Auth;
use App\User;
use GrahamCampbell\GitHub\GitHubManager;
use Socialite;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GitHubController extends Controller
{
    protected $github;

    public function __construct(GitHubManager $github)
    {
        $this->github = $github;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->scopes(['read:org', 'user:email'])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $socialiteUser = Socialite::driver('github')->user();

        //If the org filter is set get the users organisations and check them
        if (!empty(env('VALID_GITHUB_ORG'))) {
            $this->github->authenticate(\Github\Client::AUTH_URL_TOKEN, $socialiteUser->token);

            $organizations = $this->github->currentUser()->memberships()->all();

            $loginValid = false;
            foreach ($organizations as $org) {
                if ($org['organization']['login'] === env('VALID_GITHUB_ORG')) {
                    $loginValid = true;
                }
            }

            if (!$loginValid) {
                return redirect('/login')->withError('Not a member of the required organisation');
            }
        }

        //Locate a user or create an account
        $user = User::where('email', $socialiteUser->getEmail())->first();
        if (!$user) {
            $user = User::create(['email' => $socialiteUser->getEmail(), 'name' => $socialiteUser->getName()]);
            //Add a contact record for the user so they receive update notifications
            $contact              = new Contact;
            $contact->name        = $user->name;
            $contact->email       = $user->email;
            $contact->filter_tags = [];
            $contact->active      = true;
            $contact->save();
        }
        Auth::login($user, true);
        return redirect('/pings');
    }
}
