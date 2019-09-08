<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller
{
    public function login()
    {
        return $this->redirectToProvider();
    }

    /**
     * Redirect the user to the Aurora authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        if (Auth::check()) {
            return redirect("home");
        }

        return Socialite::driver('ipscommunity')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $socialite_user = Socialite::driver('ipscommunity')->user();

        try {
            $linkedAccounts = $socialite_user->linkedAccounts;
            $byond_key = $linkedAccounts[config('aurora.forum_byond_attribute')]['value'];
        } catch (\ErrorException $e) {
            $byond_key = null;
        }

        //Update or create the user details in the db
        $user = User::updateOrCreate(
            ['id' => $socialite_user->id],
            [
                'name' => $socialite_user->nickname,
                'formatted_name' => $socialite_user->formattedName,
                'email' => $socialite_user->email,
                'password' => $socialite_user->token,
                'refresh_token' => $socialite_user->refreshToken,
                'photo_url' => $socialite_user->avatar,
                'linked_accounts' => $socialite_user->linkedAccounts,
                'primary_group' => $socialite_user->primaryGroup,
                'secondary_groups' => $socialite_user->secondaryGroups,
                'byond_key' => $byond_key,
            ]
        );

        //Sync the groups
        if ($user->sync_groups) {
            //Get a complete list of IPB Group IDs
            $ipb_groups = array();
            foreach ($socialite_user->secondaryGroups as $ipb_group) {
                $ipb_groups[] = $ipb_group["id"];
            }
            $ipb_groups[] = $socialite_user->primaryGroup["id"];

            try {
                $wi_roles_old = array_unique($user->roles()->get()->toArray());
            } catch (\Exception $e){
                $wi_roles_old = "";
                Log::debug("login.wi_roles_old.invalid_format", ['user_id' => $user->id, 'wi_roles_old' => $user->roles()->get()]);
            }

            //Map the IPB Roles to WI roles
            $wi_roles_new = array();
            $groupmap = config("aurora.group_mappings");
            if (is_array($groupmap)) {
                foreach ($groupmap as $ipb_group => $wi_role) {
                    //Check if the user has the IPB Group
                    if (in_array($ipb_group, $ipb_groups)) {
                        $wi_roles_new[] = $wi_role;
                    }
                }
            }
            $wi_roles_new = array_unique($wi_roles_new);

            $user->roles()->sync($wi_roles_new);

            Log::debug('login.permsync - Synchronizing permissions', ['user_id' => $user->id, 'wi_roles_old' => $wi_roles_old, 'wi_roles_new' => $wi_roles_new, 'ipb_groups' => $ipb_groups]);
        }

        //Login the user and remember them.
        Auth::login($user, TRUE);

        // If user was trying to auth, then we send them to finish authing.
        if($request->session()->has('server_client_token')) {
            return redirect()->route('server.login.begin');
        }
        return redirect('home');
    }

    public function logout()
    {
        Auth::logout();
        return redirect("/");
    }
}
