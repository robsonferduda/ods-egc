<?php

namespace App\Http\Controllers;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ODSController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function discovery()
    {
        $process = new Process(['python3', base_path().'/ods.py']);

        $process->run(function ($type, $buffer){

            
            if (Process::ERR === $type) {
               echo 'ERR > '.$buffer.'<br />';
            } else {

                if(trim($buffer) == 'END') {
                    echo 'OUT > '.$buffer.'<br />';
                }

            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}