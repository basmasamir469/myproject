<?php

namespace App\Http\Controllers\doctor;
use App\Doctor;
use App\Http\Controllers\Controller;
use App\Recipe;
use App\traits\GeneralTraits;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class docController extends Controller
{
    use GeneralTraits;
    public function show(Request $request){
        $token = $request -> header('auth-token');
        if($token) {
            $doctor_id = $request->user()->id;
            $doctor = Doctor::Find($doctor_id);
            $patients=$doctor->users;
            return $this->returnData('200', $patients, 'patients');
        }
        else{
            $this -> returnError('','some thing went wrongs');
        }

    }
    public function store(Request $request,$id)
    {
        $user = User::Find($id);
        $user_id = $user->id;
        $doctor_id = $request->user()->id;
        $token = $request->header('auth-token');

            $input = $request->all();
            $validator = Validator::make($input, [
                'drug1' => 'required',
                'Dosage1(perday)' => 'required',
            ]);

            if ($validator->fails()) {
                # code...
                return $this->returnError($validator->errors(),'error validation');
            }

            $recipe = Recipe::create($input);
            $recipe->save();
            return $this->returnData(200, $recipe, 'recipe created succesfully');

    }

}
