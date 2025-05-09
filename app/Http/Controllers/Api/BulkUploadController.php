<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAcademics;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class BulkUploadController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validFields = $request->input('valid_fields', []);
    //     $studentDatas = $request->input('student_datas', []);
    //     $academicInfo = $request->input('acadamic_info', []);

    //     $createdUsers = [];
    //     $errors = [];

    //     $academicValidator = Validator::make($academicInfo, [
    //         'college_id' => 'required|integer|exists:colleges,id',
    //         'batch_id' => 'required|integer|exists:batches,id',
    //         'department_id' => 'required|integer|exists:departments,id',
    //         'section_id' => 'required|integer|exists:sections,id',
    //     ]);

    //     if ($academicValidator->fails()) {
    //         return response()->json([
    //             'message' => 'Academic info validation failed.',
    //             'errors' => $academicValidator->errors(),
    //             'status' => 422
    //         ], 422);
    //     }

    //     foreach ($studentDatas as $index => $student) {
    //         $rules = [];

    //         if (!empty($validFields['name'])) {
    //             $rules['name'] = 'required|string|max:255';
    //         }
    //         if (!empty($validFields['roll_no'])) {
    //             $rules['roll_no'] = 'required|string|unique:users,roll_no';
    //         }
    //         if (!empty($validFields['email'])) {
    //             $rules['email'] = 'required|email|unique:users,email';
    //         }
    //         if (!empty($validFields['contact_number'])) {
    //             $rules['contact_number'] = 'required|digits:10|unique:users,contact_number';
    //         }
    //         if (!empty($validFields['dob'])) {
    //             $rules['dob'] = 'required|date';
    //         }
    //         if (!empty($validFields['password'])) {
    //             $rules['password'] = 'required|min:6';
    //         }
    //         if (!empty($validFields['gender'])) {
    //             $rules['gender'] = 'required';
    //         }

    //         $validator = Validator::make($student, $rules);

    //         if ($validator->fails()) {
    //             $errors[$index] = $validator->errors();
    //             continue;
    //         }

    //         $userData = [];
    //         foreach ($validFields as $field => $shouldUse) {
    //             if ($shouldUse && isset($student[$field])) {
    //                 $userData[$field] = $field === 'password'
    //                     ? bcrypt($student[$field])
    //                     : $student[$field];
    //             }
    //         }

    //         $userData['role'] = 2;
    //         $userData['status'] = 1;
    //         $userData['remember_token'] = Str::random(10);


    //         $user = User::create($userData);
    //         $createdUsers[] = $user;

    //         UserAcademics::create([
    //             'user_id' => $user->id,
    //             'college_id' => $academicInfo['college_id'],
    //             'batch_id' => $academicInfo['batch_id'],
    //             'department_id' => $academicInfo['department_id'],
    //             'section_id' => $academicInfo['section_id'],
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Upload processed.',
    //         'inserted_count' => count($createdUsers),
    //         'errors' => $errors,
    //         'status' => empty($errors) ? 201 : 207
    //     ]);
    // }

    // public function store(Request $request)
    // {
    //     $validFields = $request->input('valid_fields', []);
    //     $studentDatas = $request->input('student_datas', []);
    //     $academicInfo = $request->input('acadamic_info', []);

    //     $errors = [];
    //     $bulkUsers = [];
    //     $now = now();

    //     $academicValidator = Validator::make($academicInfo, [
    //         'college_id' => 'required|integer|exists:colleges,id',
    //         'batch_id' => 'required|integer|exists:batches,id',
    //         'department_id' => 'required|integer|exists:departments,id',
    //         'section_id' => 'required|integer|exists:sections,id',
    //     ]);

    //     if ($academicValidator->fails()) {
    //         return response()->json([
    //             'message' => 'Academic info validation failed.',
    //             'errors' => $academicValidator->errors(),
    //             'status' => 422
    //         ]);
    //     }

    //     $emails = [];
    //     $rollNos = [];
    //     $phones = [];

    //     foreach ($studentDatas as $student) {
    //         if (!empty($validFields['email']) && isset($student['email'])) {
    //             $emails[] = $student['email'];
    //         }
    //         if (!empty($validFields['roll_no']) && isset($student['roll_no'])) {
    //             $rollNos[] = $student['roll_no'];
    //         }
    //         if (!empty($validFields['contact_number']) && isset($student['contact_number'])) {
    //             $phones[] = $student['contact_number'];
    //         }
    //     }

    //     $existingUsers = User::query()
    //         ->when($emails, fn($q) => $q->orWhereIn('email', $emails))
    //         ->when($rollNos, fn($q) => $q->orWhereIn('roll_no', $rollNos))
    //         ->when($phones, fn($q) => $q->orWhereIn('contact_number', $phones))
    //         ->get(['email', 'roll_no', 'contact_number']);

    //     $existingEmails = $existingUsers->pluck('email')->filter()->unique()->toArray();
    //     $existingRollNos = $existingUsers->pluck('roll_no')->filter()->unique()->toArray();
    //     $existingPhones = $existingUsers->pluck('contact_number')->filter()->unique()->toArray();

    //     foreach ($studentDatas as $index => $student) {
    //         $rules = [];

    //         if (!empty($validFields['name'])) {
    //             $rules['name'] = 'required|string|max:255';
    //         }
    //         if (!empty($validFields['roll_no'])) {
    //             $rules['roll_no'] = 'required|string|not_in:' . implode(',', $existingRollNos);
    //         }
    //         if (!empty($validFields['email'])) {
    //             $rules['email'] = 'required|email|not_in:' . implode(',', $existingEmails);
    //         }
    //         if (!empty($validFields['contact_number'])) {
    //             $rules['contact_number'] = 'required|digits:10|not_in:' . implode(',', $existingPhones);
    //         }
    //         if (!empty($validFields['dob'])) {
    //             $rules['dob'] = 'required|date';
    //         }
    //         if (!empty($validFields['password'])) {
    //             $rules['password'] = 'required|min:6';
    //         }
    //         if (!empty($validFields['gender'])) {
    //             $rules['gender'] = 'required';
    //         }

    //         $validator = Validator::make($student, $rules);
    //         if ($validator->fails()) {
    //             $errors[$index] = $validator->errors();
    //             continue;
    //         }

    //         $userData = [];

    //         foreach ($validFields as $field => $use) {
    //             if ($use && isset($student[$field])) {
    //                 $userData[$field] = $field === 'password'
    //                     ? bcrypt($student[$field])
    //                     : $student[$field];
    //             }
    //         }

    //         $userData['role'] = 2;
    //         $userData['status'] = 1;
    //         $userData['remember_token'] = Str::random(10);
    //         $userData['created_at'] = $now;
    //         $userData['updated_at'] = $now;

    //         $bulkUsers[] = $userData;
    //     }

    //     if (!empty($bulkUsers)) {
    //         User::insert($bulkUsers);
    //     }

    //     return response()->json([
    //         'message' => 'Upload processed.',
    //         'inserted_count' => count($bulkUsers),
    //         'errors' => $errors,
    //         'status' => empty($errors) ? 201 : 207
    //     ]);
    // }

    public function store(Request $request)
    {
        $validFields = $request->input('valid_fields', []);
        $studentDatas = $request->input('student_datas', []);
        $academicInfo = $request->input('acadamic_info', []);

        $errors = [];
        $bulkUsers = [];
        $now = now();

        $emails = collect($studentDatas)->pluck('email')->filter()->all();
        $rollNos = collect($studentDatas)->pluck('roll_no')->filter()->all();
        $phones = collect($studentDatas)->pluck('contact_number')->filter()->all();

        $existingUsers = User::where(function ($q) use ($emails, $rollNos, $phones) {
            if ($emails) $q->orWhereIn('email', $emails);
            if ($rollNos) $q->orWhereIn('roll_no', $rollNos);
            if ($phones) $q->orWhereIn('contact_number', $phones);
        })->get(['email', 'roll_no', 'contact_number']);

        $existingEmails = $existingUsers->pluck('email')->toArray();
        $existingRollNos = $existingUsers->pluck('roll_no')->toArray();
        $existingPhones = $existingUsers->pluck('contact_number')->toArray();

        foreach ($studentDatas as $index => $student) {
            $rules = [];

            if (!empty($validFields['name'])) {
                $rules['name'] = 'required|string|max:255';
            }
            if (!empty($validFields['roll_no'])) {
                $rules['roll_no'] = ['required', 'string', Rule::notIn($existingRollNos)];
            }
            if (!empty($validFields['email'])) {
                $rules['email'] = ['required', 'email', Rule::notIn($existingEmails)];
            }
            if (!empty($validFields['contact_number'])) {
                $rules['contact_number'] = [
                    'required',
                    'regex:/^[6-9][0-9]{9}$/',
                    Rule::notIn($existingPhones),
                ];
            }
            if (!empty($validFields['dob'])) {
                $rules['dob'] = 'required|date';
            }
            if (!empty($validFields['password'])) {
                $rules['password'] = 'required|min:6';
            }
            if (!empty($validFields['gender'])) {
                $rules['gender'] = 'required';
            }

            $validator = Validator::make($student, $rules, [
                'email.not_in' => 'The email already exists.',
                'roll_no.not_in' => 'The roll number already exists.',
                'contact_number.not_in' => 'The contact number already exists.',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors();
                continue;
            }

            $userData = collect($validFields)
                ->filter(fn($use, $field) => $use && isset($student[$field]))
                ->mapWithKeys(fn($use, $field) => [
                    $field => $field === 'password' ? bcrypt($student[$field]) : $student[$field]
                ])
                ->toArray();

            $userData['role'] = 2;
            $userData['status'] = 1;
            $userData['remember_token'] = Str::random(10);
            $userData['created_by'] = Auth::id() ?? null;

            $bulkUsers[] = $userData;
        }

        if (empty($bulkUsers)) {
            return response()->json([
                'message' => 'No valid student data to process.',
                'errors' => $errors,
                'status' => 422
            ]);
        }

        $academicValidator = Validator::make($academicInfo, [
            'college_id' => 'required|integer|exists:colleges,id',
            'batch_id' => 'required|integer|exists:batches,id',
            'department_id' => 'required|integer|exists:departments,id',
            'section_id' => 'required|integer|exists:sections,id',
        ]);

        if ($academicValidator->fails()) {
            return response()->json([
                'message' => 'Academic info validation failed.',
                'errors' => $academicValidator->errors(),
                'status' => 422
            ]);
        }

        User::insert($bulkUsers);

        $insertedUsers = User::whereIn('email', array_column($bulkUsers, 'email'))->get();

        $academicRecords = $insertedUsers->map(fn($user) => [
            'user_id' => $user->id,
            'college_id' => $academicInfo['college_id'],
            'batch_id' => $academicInfo['batch_id'],
            'department_id' => $academicInfo['department_id'],
            'section_id' => $academicInfo['section_id'],
        ])->toArray();

        UserAcademics::insert($academicRecords);

        return response()->json([
            'message' => 'Upload processed.',
            'inserted_count' => count($bulkUsers),
            'errors' => $errors,
            'status' => empty($errors) ? 201 : 207
        ]);
    }
}
