<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\TokenUser\JwtAuthHelper;
use App\Http\Controllers\UsersController;
use App\AO\Roles\RolesAO;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $response = array(
            'status' => true,
            'codigo' => 500
        );

        try {
            $ipClient = request()->ip();
            $objUser = new UsersController();
            $user     = $request->user;
            $password = $request->password;
            $document = $request->document;
            $validateUser = $objUser->validateUser($user, $document);
            if (!empty($validateUser['data'])) {

                if ($validateUser['data']['deleted'] == 0) {
                    $ldap = [];
                    $nitBusiness = $validateUser['data']['busine']['nit'];
                    $userCity = $validateUser['data']['AdditionalInfo']['city'];
                    $ldap = $this->lpadAuthenticate($user, $password, $nitBusiness, $userCity);
                    if ($ldap[0]) {
                        if($ldap[1] != null){
                            $userData = $objUser->getUserByDocumentLogin($document);
                            if($ldap[1] == 'Admin'){
                                $role = RolesAO::getRoleByName('ADMINISTRADOR');
                            }else{
                                $role = RolesAO::getRoleByName('USUARIO');
                            }
                            $userData['data']->role = $role[0]->id_role;
                            $userData['data']->save();
                            $token = Auth::login($userData['data']);
                            $objJwtAuthHelper = new JwtAuthHelper();

                            $infoToken = [
                                'USER'    => $userData['data']->id,
                                'USER_IP' => $request->ip(),
                                'TOKEN'   => $token
                            ];

                            $objJwtAuthHelper->saveToken($infoToken);

                            $data = [
                                'user' => $userData['data'],
                                'token' => $token,
                            ];

                            $codigo = 200;
                            $response['data'] = $data;

                            activity()
                                ->withProperties(['ip' =>  $ipClient, 'user_name' => $user])
                                ->log('Autenticación exitosa');
                        }else{
                            $message = 'Usuario no tiene permisos en el sistema';
                            $codigo = 500;
                            activity()
                            ->withProperties(['ip' =>  $ipClient, 'user_name' => $user])
                            ->log('Usuario sin permisos.');
                        }
                    }else{
                        $message = 'Credenciales incorrectas';
                        $codigo = 500;
                        activity()
                        ->withProperties(['ip' =>  $ipClient, 'user_name' => $user])
                        ->log('Autenticación fallida contra el directorio activo');
                    }
                } else {
                    $message = 'Usuario deshabilitado en el sistema';
                    $codigo = 500;
                    activity()
                        ->withProperties(['ip' =>  $ipClient, 'user_name' => $user])
                        ->log('Autenticación fallida');
                }

            }else{
                $message = "Datos incorrectos";
                $codigo = 500;
                activity()
                        ->withProperties(['ip' =>  $ipClient, 'user_name' => $user])
                        ->log('Autenticación fallida');
            }

            if(isset($message)){
                $response['message'] = $message;
            }

            $response['codigo'] = $codigo;

        } catch (\Throwable $th) {
            Log::error($th->getMessage() . ' -> function login() AuthController');
            $response = array(
                'status' => false,
                'message' => 'No se pudo comunicar con el servidor, intentelo mas tarde ',
                'codigo' => 500
            );
        }
        return $response;
    }

    public function me(){
        return JWTAuth::parseToken()->authenticate()->load('userRole');
    }

    public function logout()
    {
        session()->forget('userIp');
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function lpadAuthenticate($user,$password,$nitBusiness,$userCity)
    {
        try {
            if($nitBusiness == "900254011-6" || (!is_null($userCity) && (strtoupper($userCity) == 'CALI' ) )){
                $ldap = env('LDAP_AST_SERVER');
                $sufixLdap = env('LDAP_AST_SUFIX');
                $ldapDN = env('LDAP_AST_DN');

                $ldapProvidersAdmin = env('LDAP_ADMIN_AST');
                $ldapProvidersUser = env('LDAP_USER_AST');
            }else{
                $ldap = env('LDAP_TL_AD_MARK_SERVER');
                $sufixLdap = env('LDAP_TL_AD_MARK_SUFIX');
                $ldapDN = env('LDAP_TL_AD_MARK_DN');

                $ldapProvidersAdmin = env('LDAP_ADMIN_TL_AD_MARK');
                $ldapProvidersUser = env('LDAP_USER_TL_AD_MARK');
            }

            $ldapAttributes = env('LDAP_ATTRIBUTES');


            $ldapFilterUser = "(&(objectCategory=person)(objectClass=user)(sAMAccountName=" . $user . ")(memberOf=" . $ldapProvidersUser . "))";
            $ldapFilterAdmin = "(&(objectCategory=person)(objectClass=user)(SAMAccountName=" . $user . ")(memberOf=" . $ldapProvidersAdmin . "))";

            $username = $user;
            $usr = $username . $sufixLdap;
            $ds = ldap_connect($ldap);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

            $ldapBind = ldap_bind($ds, $usr, $password);
            $rol = null;
            if($ldapBind){
                $isAdmin = ldap_search($ds, $ldapDN, $ldapFilterAdmin, array($ldapAttributes));
                $arrDataAdmin = ldap_get_entries($ds, $isAdmin);

                if (isset($arrDataAdmin[0]) && !empty($arrDataAdmin[0])) {
                    $rol = 'Admin';
                }else{
                    $isUser = ldap_search($ds, $ldapDN, $ldapFilterUser);
                    $arrDataUser = ldap_get_entries($ds, $isUser);

                    if (isset($arrDataUser[0]) && !empty($arrDataUser[0])) {
                        $rol = 'User';
                    }
                }
            }
            $dataLdap = [$ldapBind, $rol];
        } catch (\Throwable $th) {
            Log::error($th->getMessage() . ' -> function lpadAuthenticate() AuthController');
            $dataLdap = null;
        }

        return $dataLdap;
    }
}
