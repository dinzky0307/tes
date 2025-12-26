<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
   public function index()
{
    $employees = Employee::where('validation_status', 'Not Validated')
        ->orderBy('lastname')
        ->orderBy('firstname')
        ->limit(50)
        ->get([
            'id',
            'payroll_number',
            'lastname',
            'firstname',
            'middle_initial',
            'grant_type_batch',
            'validation_status',
        ]);

    return view('employees.index', compact('employees')); // ✅ IMPORTANT
}

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['file' => 'Unable to read the uploaded file.']);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'The file is empty or invalid CSV.']);
        }

        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $expected = ['payroll number', 'lastname', 'firstname', 'middle initial', 'type of grant and batch number'];
        foreach ($expected as $col) {
            if (!in_array($col, $header, true)) {
                fclose($handle);
                return back()->withErrors([
                    'file' => "Missing required column: {$col}. Expected: payroll number, lastname, firstname, middle initial."
                ]);
            }
        }

        $idx = array_flip($header);

        $count = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if (count(array_filter($data, fn($v) => trim((string)$v) !== '')) === 0) {
                continue;
            }

            $payroll  = $this->toUtf8((string)($data[$idx['payroll number']] ?? ''));
$lastname = $this->toUtf8((string)($data[$idx['lastname']] ?? ''));
$firstname= $this->toUtf8((string)($data[$idx['firstname']] ?? ''));
$mi       = $this->toUtf8((string)($data[$idx['middle initial']] ?? ''));
$grantTypeBatch = $this->toUtf8((string)($data[$idx['type of grant and batch number']] ?? ''));



            if ($payroll === '' || $lastname === '' || $firstname === '') {
                continue;
            }

           Employee::create([
    'payroll_number' => $payroll,
    'lastname' => $lastname,
    'firstname' => $firstname,
    'middle_initial' => $mi !== '' ? $mi : null,
    'grant_type_batch' => $grantTypeBatch !== '' ? $grantTypeBatch : null,
    'validation_status' => 'Not Validated', // ✅ IMPORTANT
]);



            $count++;
        }

        fclose($handle);

        return redirect()->route('employees.index')
            ->with('success', "Upload successful. Imported/updated {$count} record(s).");
    }

    public function liveSearch(Request $request)
{
    $q = trim((string) $request->query('q', ''));

    $query = Employee::where('validation_status', 'Not Validated'); // ✅ ONLY not validated

    if ($q !== '') {
        $query->where(function ($qq) use ($q) {
            $qq->where('lastname', 'like', "%{$q}%")
               ->orWhere('firstname', 'like', "%{$q}%");
        });
    }

    $employees = $query->orderBy('lastname')
        ->orderBy('firstname')
        ->limit(50)
        ->get([
            'id',
            'payroll_number',
            'lastname',
            'firstname',
            'middle_initial',
            'grant_type_batch',
            'validation_status',
        ]);

    return response()->json([
        'data' => $employees,
        'count' => $employees->count(),
    ]);
}

    private function toUtf8(string $value): string
{
    $value = trim($value);

    // If it's already valid UTF-8, keep it
    if (mb_check_encoding($value, 'UTF-8')) {
        return $value;
    }

    // Convert common Windows CSV encodings to UTF-8
    $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1252, ISO-8859-1, UTF-16, UTF-8');

    // Remove any remaining invalid UTF-8 bytes
    return mb_convert_encoding($converted ?? $value, 'UTF-8', 'UTF-8');
}

public function updateValidation(Request $request, Employee $employee)
{
    $request->validate([
        'validation_status' => ['required', 'in:Not Validated,Validate'],
    ]);

    $employee->update([
        'validation_status' => $request->validation_status,
    ]);

    return back()->with('success', 'Validation status updated.');
}


}
