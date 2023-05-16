<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;

use Illuminate\Support\Facades\View;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;
class CodeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
           $email_template = "";
        $code  =$this->data['code'];

       ///, "Hola este es tu código ". $code." de verificación Medismart!" );


        $body =  "Hola este es tu código ". $code." de verificación Medismart!";
        ///dd($body);
        $body="<!DOCTYPE html>
        <html>
        <head>
            <title>Mi correo electrónico</title>
        </head>
        <body>
            <h1>Bienvenido a mi sitio web</h1>
            <p>Gracias por registrarte. Espero que disfrutes usando nuestro sitio web.</p>
        </body>
        </html>";

       // $html = view('email.content', $code)->render();

      ///  $body = new TextPart('This is the message body.');

        $mailer->send([], $this->data, function ($message) use($body, $email_template) {
            $message->from('manage@medismart.net', 'Afiliación Medismart');
            $message->to($this->data['email'])->subject('Código de Verificación');
            $view = View::make('email.content', ['codigo' =>  $this->data['code']])->render();

           // $view = view('email.content')->with('codigo', $this->data['code']);
            $message->html($view, 'text/html');


            ///$message->setBody($view->render(), 'text/html');

            ///$message->setBody($body,'text/html');
        });

    }
}

