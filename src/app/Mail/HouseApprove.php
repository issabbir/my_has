<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HouseApprove extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $name;
    public $emp_code;
    public $root;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $emp_code)
    {
        $this->subject = 'House Approval';
        $this->name = $name;
        $this->emp_code = $emp_code;
        $this->root = request()->root();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $path = $this->root.'/report/render?xdo=/~weblogic/HAS/RPT_Final_Allotment_Letter.xdo&p_EMPL_CODE='.$this->emp_code.'&type=pdf&filename=allotment_letter';

        return $this->subject($this->subject)
            ->view('emails.house_approve')
//            ->attach($path, [
//                'as' => 'name.pdf',
//                'mime' => 'application/pdf',
//            ])
//                ->attach(storage_path('public/' . $filename))
//                ->attachFromStorage($path)
//            ->attachData($path, 'name.pdf', [
//                'mime' => 'application/pdf',
//            ])
            ->with([ 'name' => $this->name, 'path' => $path ]);
    }
}
