<?php

namespace App\Helpers\EmailHelpers;

use App\Mail\BasicMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VerifyUserMailSend
{
    /**
     * Send an onboarding verification code with an explicit delivery outcome.
     *
     * The token update shares the mail attempt's transaction so a rejected
     * delivery rolls back to the previously usable token. Existing callers of
     * sendMail() retain their legacy behavior.
     */
    public static function sendMailForOnboarding(User $user): bool
    {
        try {
            return $user->getConnection()->transaction(function () use ($user) {
                $lockedUser = User::on($user->getConnectionName())
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedUser->email_verified) {
                    return true;
                }

                $token = Str::random(8);
                $lockedUser->forceFill(['email_verify_token' => $token])->save();

                $msg = MarkupGenerator::paragraph(__('Hello'));
                $msg .= MarkupGenerator::paragraph(__('Here is your verification code'));
                $msg .= MarkupGenerator::code($token);
                $subject = sprintf(__('Verify your email address at %s'), site_title());

                Mail::to($lockedUser->email)->send(new BasicMail($msg, $subject));

                return true;
            });
        } catch (\Throwable $exception) {
            try {
                Log::warning('Onboarding verification email could not be sent.', [
                    'user_id' => $user->id,
                    'exception' => get_class($exception),
                    'code' => $exception->getCode(),
                ]);
            } catch (\Throwable) {
                // Delivery failure must still produce a safe false result if logging is unavailable.
            }

            return false;
        }
    }

    public static function sendMail(User $user){
        $token = Str::random(8);
        User::find($user->id)->update(['email_verify_token' => $token ]);
        $msg = MarkupGenerator::paragraph(__('Hello'));
        $msg .= MarkupGenerator::paragraph(__('Here is your verification code'));
        $msg .= MarkupGenerator::code($token);
        $subject = sprintf(__('Verify your email address at %s'),site_title());

        try {
            Mail::to($user->email)->send(new BasicMail($msg, $subject));
        }catch (\Exception $e){
            if ($e->getCode() == 553)
            {
                return redirect()->back()->with(['msg'=> __('Site or server email configuration  is incorrect'), 'type'=> 'danger']);
            }
//            return redirect()->back()->with(['msg'=> $e->getMessage(), 'type'=> 'danger']);
        }
    }

    public static function sendMail_tenant_admin(Admin $user){

        $token = Str::random(8);
        $user_info = tenant()->user()->first();
        $user_info->email_verify_token = $token;
        $user_info->save();
       // Admin::find($user->id)->update(['email_verify_token' => $token ]);
        $msg = MarkupGenerator::paragraph(__('Hello'));
        $msg .= MarkupGenerator::paragraph(__('Here is your verification code'));
        $msg .= MarkupGenerator::code($token);
        $subject = sprintf(__('Verify your email address at %s'),site_title());

        try {
            Mail::to($user->email)->send(new BasicMail($msg,$subject));
        }catch (\Exception $e){
            return redirect()->back()->with(['msg'=> $e->getMessage(), 'type'=> 'danger']);
        }
    }
}
