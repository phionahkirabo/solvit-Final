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
    public $verificationLink;

    public function __construct($employee, $defaultPassword, $verificationLink)
    {
        $this->employee = $employee;
        $this->defaultPassword = $defaultPassword;
        $this->verificationLink = $verificationLink;
    }

    public function build()
    {
        return $this->view('email.employee-created')
                    ->subject('Welcome to the Company')
                    ->with([
                        'employee' => $this->employee,
                        'defaultPassword' => $this->defaultPassword,
                        'verificationLink' => $this->verificationLink,
                    ]);
    }
}