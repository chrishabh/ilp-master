<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordFormRequest;
use App\Http\Requests\GetPayToDetailsFormRequest;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Http\Requests\LinkUserAndFloorsFormRequest;
use App\Http\Requests\LinkUserAndProjectsFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Http\Requests\UpdateUserRoleFormRequest;
use App\Mail\TestEmail;
use App\Models\LookUpValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class UserController extends Controller
{

    public static function userSignUp(SignUpFormRequest $request)
    {
         $requestData = $request->validated();

            $user = new UserServices();
            $user->register($request);

		return  response()->data([],'Registration Success');
    }

    public static function userLogin(LoginFormRequest  $request)
    {

        $requestData = $request->validated();
        $user = new UserServices();
        $data = $user->login($request);
        return  response()->data(['user'=>$data]);

    }

    public static function lookUpValue()
    {
        $data = LookUpValue::getLookUpValue();
        return  response()->data(['look_up'=>$data]);
    }

    public static function test()
    {
        UserServices::cleanServerDirectory();
    }

    public static function wagesPortalController(Request $request)
    {
        UserServices::wagesPortalController($request);
    }


    public static function getUserList()
    {
        $user = new UserServices();
        $data = $user->getUserList();
        return  response()->data(['user_list'=>$data]);
    }

    public static function updateUser(UpdateUserRoleFormRequest  $request)
    {
        $user = new UserServices();
        $data = $user->updateUserRole($request);
        return  response()->success();
    }

    public static function forgotPassword(ForgotPasswordFormRequest $request)
    {
        $user = new UserServices();
        $data = $user->forgotPassword($request);
        return  response()->success();
    }

    public static function decryptPassword(ForgotPasswordFormRequest $request)
    {
        $user = new UserServices();
        $data = $user->decryptPassword($request);
        return  response()->data($data);
    }
    public static function getUserProjectLinkingDetails(GetPayToDetailsFormRequest $request)
    {
        $requestData    =   $request->validated();
        $user           =   new UserServices();
        $data           = $user->getUserProjectLinkingDetails($request);

        return  response()->data($data);
    }

    public static function linkUserAndProjects(LinkUserAndProjectsFormRequest $request)
    {
        $requestData    =   $request->validated();
        $user           =   new UserServices();
        $data           = $user->linkUserAndProjects($request);

        return  response()->success();
    }

    public static function linkUserAndFloor(LinkUserAndFloorsFormRequest $request)
    {
        $requestData    =   $request->validated();
        $user           =   new UserServices();
        $data           = $user->linkUserAndfloor($request);

        return  response()->success();
    }
    
    public static function smtpHandshake(Request $request)
    {
        $email = $request['email'];
        $domain = substr(strrchr($email, "@"), 1); // Extract domain
        $mxRecords = dns_get_record($domain, DNS_MX);

        if (empty($mxRecords)) {
            return "No MX records found for domain $domain.";
        }

        // Use the highest priority MX server
        usort($mxRecords, function ($a, $b) {
            return $a['pri'] - $b['pri'];
        });
        $mxHost = $mxRecords[0]['target'];

        // Connect to the SMTP server
        $connection = fsockopen($mxHost, 25, $errno, $errstr, 10);
        if (!$connection) {
            return "Failed to connect to SMTP server: $errstr ($errno)";
        }

        // Perform SMTP handshake
        $responses = [];
        fwrite($connection, "HELO " . gethostname() . "\r\n");
        $responses[] = fgets($connection, 1024);

        // Specify the sender email
        fwrite($connection, "MAIL FROM: <test@example.com>\r\n");
        $responses[] = fgets($connection, 1024);

        // Specify the recipient email
        fwrite($connection, "RCPT TO: <$email>\r\n");
        $response = fgets($connection, 1024);
        $responses[] = $response;

        // Close the connection
        fwrite($connection, "QUIT\r\n");
        fclose($connection);

        // Check the response for recipient validation
        if (strpos($response, '250') !== false) {
            return "Email address is valid.";
        } elseif (strpos($response, '550') !== false) {
            return "Email address is invalid.";
        }

        return "Unable to verify the email address.";
    }

    public static function testEmail(Request $request)
    {
        $email = $request['email'];
        $details = [
            'message' => 'This is a test email sent from a IPL-WAGES application.'
        ];
    
        Mail::to($email)->send(new TestEmail($details));
    
        return 'Test email sent successfully!';
       
    }
}
