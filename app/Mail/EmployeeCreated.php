<?php
namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $defaultPassword;

    public function __construct(Employee $employee, $defaultPassword)
    {
        $this->employee = $employee;
        $this->defaultPassword = $defaultPassword;
    }

    public function build()
    {
        return $this->view('emails.employeeCreated')
            ->subject('Welcome to the Company')
            ->with([
                'employee' => $this->employee,
                'defaultPassword' => $this->defaultPassword,
                'verificationLink' => url('/api/employee/verify', $this->employee->id)
            ]);
    }
}
