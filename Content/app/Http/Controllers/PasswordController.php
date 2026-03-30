<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime;


class PasswordController extends Controller
{

    public function forgetPassEmail(Request $request){


        if($request->isMethod("post")){
        $userData = User::where(["email"=>$request->username])->where(["status"=>'Y'])->first();


         if (!$userData) {
            return back()->withErrors(['username' => 'We can\'t find a user with that email address.']);
        }

        // 3. Generate a secure, unique token
        $token = Str::random(60);

        // 4. Store the token in the database
        DB::table('password_resets')->insert([
            'username' => $request->username,
            'token' => $token,
            'token_status' => 'A',
            'created_at' => now(),
        ]);


        $resetUrl = route('password.reset', ['token' => $token]);
        $resetText = "Please click here to reset your password: ";
		
	

        // Configure native PHP SMTP settings
        ini_set("SMTP", "QS4528.pair.com");
        ini_set("sendmail_from", "ahsan.javed@ail.atlas.pk");

		$boundary = md5(time());
		// Email headers
        $headers = "From: AIL - AutoSecure <ahsan.javed@ail.atlas.pk>\r\n";
        $headers .= "Cc: AIL - HO <ahsan.javed@ail.atlas.pk>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n\r\n";

		


        // Build the email body using your string format
        $message = "--" . $boundary . "\r\n";
        $message .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= '<html><body>';
        $message .= "Dear Sir/Ma'am,";
        $message .= "<br><br>Please click the following link to reset your password: </br> <a href='" . $resetUrl . "'>" . $resetUrl . "</a>";
        $message .= "<br><br>If you did not request a password reset, no further action is required.<br>";
        $message .= "<br><br>Regards,<br><br><br>";
        $message .= "</body></html>";




        // Send email
		$subject = "Password Reset Notification";
        $mail_result = mail($userData->email, $subject, $message, $headers);
        // return redirect('/');
        // Return JSON response
        if ($mail_result) {
            return back()->withErrors(['status' => 'Email sent! Please check your email to reset your password.']);
        } else {
            return back()->withErrors(['status' => 'There was a problem sending your email, try again']);
        }
    

        }

    }


    


    public function forgetPass($token)
    {


        // 1. Check if the token exists in the database
        $tokenRecord = DB::table('password_resets')->where('token', $token)->first();

        if (!$tokenRecord) {
            abort(404, 'Invalid password reset token.');
        }

        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            return redirect('/login')->withErrors(['status' => 'The password reset token has expired.']);
        }

        // 3. Render the reset form with the email and token pre-populated
        return view('forgetpassword', ['token' => $token, 'username' => $tokenRecord->username]);
    }


    public function resetPassword(Request $request){

         $request->validate([
            'username' => 'required',
            'token' => 'required',
            'fnew' => 'required|string|min:8',
            'fconf' => 'required|same:fnew', // Use the 'same' rule instead
        ]);
        

        // 2. Verify the token and email combination
        $tokenRecord = DB::table('password_resets')
            ->where('username', $request->username)
            ->where('token', $request->token)
            ->where('token_status', 'A') // Ensure the token is active
            ->first();

        if (!$tokenRecord) {
            return redirect('/login')->withErrors(['status' => 'Invalid username or token.']);
        }


        // 3. Find the user and update the password
        $user = User::where('name', $request->username)->first();
        $userpass = $user->password;
        $prev1 = $user->prev1;
        $prev2 = $user->prev2;
        $prev3 = $user->prev3;
        $prev4 = $user->prev4;
        $prev5 = $user->prev5;

        
            $passwordInHash = Hash::make($request->fnew);


        if(Hash::check($request->fnew, $userpass) || Hash::check($request->fnew, $prev1) || Hash::check($request->fnew, $prev2) 
            || Hash::check($request->fnew, $prev3) || Hash::check($request->fnew, $prev4) || Hash::check($request->fnew, $prev5)){

            echo "<script>";
            echo "alert('Password Exist in History. Try New Combinations.');";
            echo "</script>";

            
            return back()->withErrors(['status' => 'Password exists in history. Try new combinations.']);


        }else{
            User::where('name', $request->username)->update(['password'=>$passwordInHash,
            'prev1'=>$userpass,
            'prev2'=>$prev1,
            'prev3'=>$prev2,
            'prev4'=>$prev3,
            'prev5'=>$prev4]);

            DB::table('password_resets')
            ->where('username', $request->username)
            ->where('token', $request->token)
            ->update(['token_status' => 'I']);

            return redirect('/login')->with('status', 'Password Changed. Login with new credentials');
        }

            
     
        // $user->update(['password' => Hash::make($request->fnew)]);

        // return redirect('/login')->with('status', 'Your password has been reset successfully!');
    }



}
