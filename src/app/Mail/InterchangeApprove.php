<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterchangeApprove extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $name;
    public $allot_letter_no;
    public $root;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $allot_letter_no)
    {
        $this->subject = 'House Interchange Approval';
        $this->name = $name;
        $this->allot_letter_no = $allot_letter_no;
        $this->root = request()->root();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $path = $this->root.'/report/render?xdo=/~weblogic/HAS/rpt_interchange_allotment_letter.xdo&p_ALLOT_LETTER_NUM='.$this->allot_letter_no.'&type=pdf&filename=allotment_interchange_letter';

        return $this->subject($this->subject)
            ->view('emails.interchange_approve')
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
